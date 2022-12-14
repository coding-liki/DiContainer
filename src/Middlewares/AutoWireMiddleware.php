<?php

namespace CodingLiki\DiContainer\Middlewares;


use CodingLiki\DiContainer\Exceptions\CannotAutowireParameter;
use CodingLiki\DiContainer\Exceptions\NotFoundException;
use CodingLiki\DiContainer\Services\ServiceFactory;

class AutoWireMiddleware extends AbstractMiddleware
{

    public function prepareConfiguration(mixed $configuration): mixed
    {

        if (
            !is_array($configuration)
            || !isset($configuration[ServiceFactory::CLASS_KEY])
        ) {
            return $configuration;
        }

//        try {
            return $this->normaliseConstructorConfiguration($configuration);
//        } catch(CannotAutowireParameter $e){
//
//        }
    }


    private function normaliseConstructorConfiguration(array $configuration): array
    {
        $classRef = new \ReflectionClass($configuration[ServiceFactory::CLASS_KEY]);
        $constructConfiguration = $configuration[ServiceFactory::CONSTRUCT_KEY] ?? [];

        $realConstructConfiguration = $this->extractConstructorConfiguration($classRef);

        $normalisedConstructionConfiguration = [];
        foreach ($realConstructConfiguration as $parameter) {
            $name = $parameter['name'];
            $default = $parameter['default'];
            $classes = $parameter['classes'];
            $nullable = $parameter['nullable'];

            $configurationValue = $constructConfiguration[$name] ?? $default;

            if (!$nullable) {
                foreach ($classes as $class) {
                    try {
                        $configurationValue = $this->autowire($class);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                if ($configurationValue === null) {
                    throw new CannotAutowireParameter($classRef->getName(), $name);
                }
            }

            $normalisedConstructionConfiguration[] = $configurationValue;
        }

        $configuration[ServiceFactory::CONSTRUCT_KEY] = $normalisedConstructionConfiguration;
        return $configuration;
    }

    private function extractConstructorConfiguration(\ReflectionClass $classRef): array
    {
        $constructorConfiguration = [];

        $constructor =  $classRef->getConstructor();
        $constructParams = $constructor ? $constructor->getParameters() : [];

        foreach ($constructParams as $parameter) {
            $type = $parameter->getType();

            $classes = $type instanceof \ReflectionNamedType ?
                [$type->getName()] :
                array_map(function (\ReflectionNamedType $type): string {
                    return $type->getName();
                }, $type->getTypes());

            $constructorConfiguration[] = [
                'name' => $parameter->getName(),
                'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                'nullable' => $type->allowsNull(),
                'classes' => $classes
            ];
        }

        return $constructorConfiguration;
    }

    private function autowire(string $class): mixed
    {
        if (!$this->container->has($class)){
            $this->container->add($class, [
                ServiceFactory::CLASS_KEY => $class
            ]);
        }

        return $this->container->get($class);
    }


}