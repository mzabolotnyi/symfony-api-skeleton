<?php

namespace App\Constant;

abstract class BaseConstant
{
    public static function getConstants() : array
    {
        return array_values((new \ReflectionClass(static::class))->getConstants());
    }
}
