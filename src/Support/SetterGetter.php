<?php

namespace ZerosDev\MCPayment\Support;

trait SetterGetter
{
    public function __call($name, $arguments)
    {
        $type = strtolower(substr($name, 0, 3));
        $property = substr($name, 3);
        $property = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));

        switch ($type) {
            case 'set':
                $this->{$property} = isset($arguments[0]) ? $arguments[0] : null;
                break;

            case 'get':
                return property_exists($this, $property) ? $this->{$property} : null;
                break;
        }
    }

    public function __get($property)
    {
        return property_exists($this, $property) ? $this->{$property} : null;
    }

    public function __set($property, $value)
    {
        $this->{$property} = $value;
    }
}
