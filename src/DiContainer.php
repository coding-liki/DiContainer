<?php

namespace CodingLiki\DiContainer;


use CodingLiki\DiContainer\Exceptions\ContainerException;
use CodingLiki\DiContainer\Exceptions\NotFoundException;
use CodingLiki\DiContainer\Exceptions\ServiceAlreadyExist;
use CodingLiki\DiContainer\Middlewares\MiddlewareInterface;
use CodingLiki\DiContainer\Services\ServiceFactory;
use CodingLiki\DiContainer\Services\ServiceInterface;
use CodingLiki\DiContainer\Services\SingletonInterface;
use Psr\Container\ContainerExceptionInterface;

class DiContainer implements DiContainerInterface
{

    /**
     * @var array<string, ServiceInterface>
     */
    private array $services = [];


    /**
     * @var array<string, SingletonInterface>
     */
    private array $singletonServices = [];

    /**
     * @var array<string, string>
     */
    private array $classNameToServiceId = [];

    /**
     * @var MiddlewareInterface[]
     */
    private array $middlewares = [];

    public function get(string $id): mixed
    {
        foreach ($this->middlewares as $middleware) {
            $result = $middleware->get($id);
            if($result !== NULL){
                return $result;
            }
        }
        if (isset($this->classNameToServiceId[$id])) {
            $id = $this->classNameToServiceId[$id];
        }

        try {
            if (isset($this->services[$id])) {
                return $this->services[$id]->build();
            }

            if (isset($this->singletonServices[$id])) {
                return $this->singletonServices[$id]->get();
            }
        } catch (\Throwable $throwable){
            throw new ContainerException($id, 0,  $throwable);
        }

        throw new NotFoundException($id);
    }

    public function has(string $id): bool
    {
        if (isset($this->classNameToServiceId[$id])) {
            $id = $this->classNameToServiceId[$id];
        }

        if (isset($this->services[$id])) {
            return true;
        }

        if (isset($this->singletonServices[$id])) {
            return true;
        }

        return false;
    }

    public function add(string $id, mixed $configuration): static
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware->prepareConfiguration($configuration);
        }

        if ($this->has($id)) {
            throw new ServiceAlreadyExist($id);
        }

        $factory = new ServiceFactory($this);

        $service = $factory->build($id, $configuration);

        if ($service instanceof SingletonInterface) {
            $this->singletonServices[$id] = $service;
        } else {
            $this->services[$id] = $service;
        }

        $this->classNameToServiceId[$service->getClass()] = $id;


        return $this;

    }

    public function registerMiddleware(MiddlewareInterface $middleware): static
    {
        if (!in_array($middleware, $this->middlewares)) {
            $this->middlewares[] = $middleware;
            $middleware->setContainer($this);
        }

        return $this;
    }
}