<?php

namespace CodingLiki\DiContainer\Services;

use CodingLiki\DiContainer\Services\SingletonInterface;

class ParameterService implements SingletonInterface
{
    public function __construct(private mixed $value)
    {
    }

    public function build(): mixed
    {
        return $this->value;
    }

    public function get(): mixed
    {
        return $this->value;
    }
}