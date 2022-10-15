<?php

namespace CodingLiki\DiContainer\Services;

interface ServiceInterface
{
    public function build(): mixed;

    public function getClass(): string;
}