<?php

namespace CodingLiki\DiContainer\Services;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Services\SingletonService;

class ServiceFactory
{
    public const SINGLETON_KEY = 'singleton';
    public const CLASS_KEY = 'class';
    public const CONSTRUCT_KEY = 'construct';
    public const SET_KEY = 'set';
    public const PUBLIC_KEY = 'public';
    public const SERVICE_LINK_PREFIX = '@';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function build(string $id, mixed $configuration): ServiceInterface
    {
        if (!is_array($configuration) || !isset($configuration[self::CLASS_KEY])) {
            return new ParameterService($id, $configuration);
        }

        $isSingleton = $configuration[self::SINGLETON_KEY] ?? false;

        if ($isSingleton) {
            return $this->buildSingleton($configuration);
        }

        return $this->buildService($configuration);
    }

    private function buildSingleton(array $configuration): SingletonInterface
    {
        $class = $configuration[self::CLASS_KEY];

        $constructParams = $this->buildParams($configuration[self::CONSTRUCT_KEY] ?? []);
        $setParams = $this->buildParams($configuration[self::SET_KEY] ?? []);
        $publicParams = $this->buildParams($configuration[self::PUBLIC_KEY] ?? []);

        return new SingletonService($class, $constructParams, $setParams, $publicParams);
    }

    private function buildService(array $configuration)
    {
        $class = $configuration[self::CLASS_KEY];

        $constructParams = $this->buildParams($configuration[self::CONSTRUCT_KEY] ?? []);

        $setParams = $this->buildParams($configuration[self::SET_KEY] ?? []);
        $publicParams = $this->buildParams($configuration[self::PUBLIC_KEY] ?? []);

        return new ClassService($class, $constructParams, $setParams, $publicParams);
    }

    /**
     * @param array<string, mixed> $paramsDefinition
     * @return array<string, mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function buildParams(array $paramsDefinition): array
    {
        return array_map(function (mixed $parameter): mixed {
            if (is_array($parameter)) {
                return array_map([$this, 'buildParameter'], $parameter);
            }
            return $this->buildParameter($parameter);
        }, $paramsDefinition);
    }

    /**
     * @param mixed $parameter
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function buildParameter(mixed $parameter): mixed
    {
        if (is_string($parameter) && str_starts_with($parameter, self::SERVICE_LINK_PREFIX)) {
            return $this->container->get(trim($parameter, self::SERVICE_LINK_PREFIX));
        }
        return $parameter;
    }
}