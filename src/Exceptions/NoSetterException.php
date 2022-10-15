<?php

namespace CodingLiki\DiContainer\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class NoSetterException extends \Exception implements ContainerExceptionInterface
{

    public function __construct(string $class, string $fieldName)
    {
        parent::__construct("There is no setter for field `$fieldName` in $class");
    }
}