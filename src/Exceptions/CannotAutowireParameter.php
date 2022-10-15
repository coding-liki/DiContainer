<?php

namespace CodingLiki\DiContainer\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class CannotAutowireParameter extends \Exception implements ContainerExceptionInterface
{

    public function __construct(string $class, string $fieldName)
    {
        parent::__construct("Cannot autowire constructor parameter `$fieldName` in $class");
    }
}