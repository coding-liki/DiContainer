<?php

namespace CodingLiki\DiContainer\Middlewares;

use CodingLiki\DiContainer\DiContainerInterface;

interface MiddlewareInterface
{
    public function prepareConfiguration(mixed $configuration): mixed;

    public function get(string $name): mixed;

    public function setContainer(DiContainerInterface $container): static;
}