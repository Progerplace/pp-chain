<?php

namespace Ru\Progerplace\Chain\ChainFunc\Aggregate;

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