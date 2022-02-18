<?php

namespace Ru\Progerplace\Chain\ChainFunc\Aggregate;

use Ru\Progerplace\Chain\Utils;

class Sort
{
    public static function asc(array $array): array
    {
        sort($array);

        return $array;
    }

    public static function desc(array $array): array
    {
        sort($array);

        return array_reverse($array);
    }

    public static function natsort(array $array, $isCase = false): array
    {
        if ($isCase) {
            natsort($array);
        } else {
            natcasesort($array);
        }

        return $array;
    }
}