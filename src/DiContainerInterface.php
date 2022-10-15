<?php
namespace CodingLiki\DiContainer;

use CodingLiki\DiContainer\Middlewares\MiddlewareInterface;
use Psr\Container\ContainerInterface;

interface DiContainerInterface extends ContainerInterface
{
    public function add(string $id, mixed $configuration): static;

    public function registerMiddleware(MiddlewareInterface $middleware): static;
}