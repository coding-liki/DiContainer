<?php

namespace CodingLiki\DiContainer\Services;

use CodingLiki\DiContainer\Exceptions\NoSetterException;
use CodingLiki\DiContainer\Exceptions\PublicFieldNotSetException;
use CodingLiki\DiContainer\Exceptions\SetterErrorException;

class ClassService implements ServiceInterface
{
    /**
     * @param array<string, mixed> $constructParams
     * @param array<string, mixed> $setValues
     * @param array<string, mixed> $publicFieldValues
     */
    public function __construct(private string $class,private array $constructParams, private array $setValues, private array $publicFieldValues)
    {
    }

    public function build(): mixed
    {

        $instance = new $this->class(...$this->constructParams);

        foreach ($this->setValues as $fieldName => $value){
            $setterName = "set".ucfirst($fieldName);
            if(method_exists($instance, $setterName)) {
                try {
                    $instance->$setterName($value);
                } catch (\Throwable $t){
                    throw new SetterErrorException($this->class, $setterName, $t->getMessage());
                }
            } else {
                throw new NoSetterException($this->class, $fieldName);
            }
        }

        foreach ($this->publicFieldValues as $fieldName => $value){
            if(property_exists($instance, $fieldName)){
                try{
                    $instance->$fieldName = $value;
                } catch (\Throwable $t){
                    throw new PublicFieldNotSetException($this->class, $fieldName);
                }
            } else {
                throw new PublicFieldNotSetException($this->class, $fieldName);

            }
        }

        return $instance;
    }

    public function getClass(): string
    {
        return $this->class;
    }


}