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

        return $this->normaliseConstructorConfiguration($configuration);
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

            if (!$nullable && $configurationValue === null) {
                $lastError = NULL;
                foreach ($classes as $class) {
                    try {
                        $configurationValue = $this->autowire($class);
                    } catch (\Exception $e) {
                        $lastError = $e;
                        continue;
                    }
                }
                if ($configurationValue === null) {
                    throw $lastError ?? new CannotAutowireParameter($classRef->getName(), $name);
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

        $constructor = $classRef->getConstructor();
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
        if (!$this->container->has($class)) {
            $this->container->add($class, [
                ServiceFactory::CLASS_KEY => $class
            ]);
        }

        return $this->container->get($class);
    }


    public function get(string $name): mixed
    {
        if (!$this->container->has($name) && class_exists($name)) {
            return $this->autowire($name);
        }

        return NULL;
    }
}