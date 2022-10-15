<?php

namespace CodingLiki\DiContainer\Middlewares;

use CodingLiki\DiContainer\DiContainerInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    protected DiContainerInterface $container;

    public function setContainer(DiContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }
}