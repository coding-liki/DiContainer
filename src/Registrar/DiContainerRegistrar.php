<?php
namespace CodingLiki\DiContainer\Registrar;

use CodingLiki\DiContainer\DiContainerInterface;

class DiContainerRegistrar implements DiContainerRegistrarInterface
{
    public function registerServices(DiContainerInterface $diContainer, array $serviceConfigurations): static {
        foreach ($serviceConfigurations as $id => $configuration){
            $diContainer->add($id, $configuration);
        }
        return $this;
    }
}