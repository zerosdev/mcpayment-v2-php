<?php

namespace ZerosDev\MCPayment\Support;

use ZerosDev\MCPayment\Exception\ValidationException;

trait Validator
{
    public function requires(array $keys)
    {
        foreach ($keys as $key) {
            if (! property_exists($this, $key)) {
                throw new ValidationException('The property `' . $key . '` must be filled');
            } elseif (empty($this->{$key})) {
                throw new ValidationException('The property `' . $key . '` must be filled');
            }
        }
    }
}
