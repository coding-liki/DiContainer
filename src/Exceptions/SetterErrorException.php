<?php

namespace CodingLiki\DiContainer\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class SetterErrorException extends \Exception implements ContainerExceptionInterface
{

    public function __construct(string $class, string $setter, $exceptionMessage)
    {
        parent::__construct("Error while calling $setter in $class. `$exceptionMessage`");
    }
}