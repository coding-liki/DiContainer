<?php

namespace CodingLiki\DiContainer\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id)
    {
        parent::__construct("service or singleton with id '$id' was not found");
    }
}