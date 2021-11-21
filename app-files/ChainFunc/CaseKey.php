<?php

namespace Ru\Progerplace\Chain\ChainFunc;

use CaseConverter\CaseString;

class CaseKey
{
    public static function toLower(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyRes = mb_strtolower($key);
            $res[$keyRes] = $item;
        }

        return $res;
    }

    public static function toUpper(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyRes = mb_strtoupper($key);
            $res[$keyRes] = $item;
        }

        return $res;
    }

    public static function snakeToCamel(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyRes = CaseString::snake($key)->camel();
            $res[$keyRes] = $item;
        }

        return $res;
    }

    public static function camelToSnake(array $array): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyRes = CaseString::camel($key)->snake();
            $res[$keyRes] = $item;
        }

        return $res;
    }
}