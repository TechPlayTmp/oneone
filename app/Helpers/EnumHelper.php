<?php

namespace App\Helpers;

use InvalidArgumentException;
use ReflectionClass;

class EnumHelper
{

    public static function toAssociativeArray(string $class): array
    {
        return array_combine(self::toArray($class), self::toArray($class));
    }

    public static function toArray(string $class): array
    {
        $reflection = new ReflectionClass($class);
        if (!$reflection->isEnum()) {
            throw new InvalidArgumentException("Not enum class.");
        }

        return array_map(fn($case) => $case->value, $class::cases()); //reformular esta classe
    }


}