<?php

namespace Services;

use CodingLiki\DiContainer\Services\ClassService;
use CodingLiki\DiContainer\Services\SingletonInterface;

class SingletonService extends ClassService implements SingletonInterface
{
    private mixed $singleton = null;
    public function get(): mixed
    {
        if($this->singleton === null){
            $this->singleton = $this->build();
        }

        return $this->singleton;
    }
}