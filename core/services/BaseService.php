<?php

namespace App\Core\Services;

abstract class BaseService
{
    private static array $instances = [];

    public static function getInstance(): static
    {
        $class = static::class;

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }
}