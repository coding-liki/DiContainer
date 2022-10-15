<?php
namespace CodingLiki\DiContainer\Registrar;

use CodingLiki\DiContainer\DiContainerInterface;

interface DiContainerRegistrarInterface
{
    public function registerServices(DiContainerInterface $diContainer, array $serviceConfigurations): self;
}