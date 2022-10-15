<?php

namespace CodingLiki\DiContainer\Services;

interface SingletonInterface extends ServiceInterface
{
    public function get(): mixed;
}