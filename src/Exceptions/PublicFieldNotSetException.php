<?php

namespace CodingLiki\DiContainer\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class PublicFieldNotSetException extends \Exception implements ContainerExceptionInterface
{

    public function __construct(string $class, string $fieldName)
    {
        parent::__construct("Cannot set public field `$fieldName` in $class. Is it public?");
    }
}