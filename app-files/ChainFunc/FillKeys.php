<?php

namespace Ru\Progerplace\Chain\ChainFunc;

class FillKeys
{
    public static function fromField(array $array, $field): array
    {
        $res = [];

        foreach ($array as $item) {
            $res[$item[$field]] = $item;
        }

        return $res;
    }
}