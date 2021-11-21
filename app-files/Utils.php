<?php

namespace Ru\Progerplace\Chain;

class Utils
{
    public static function argumentsAsArray(array $args): array
    {
        $res = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $res = [...$res, ...$arg];
            } else {
                $res[] = $arg;
            }
        }

        return $res;
    }
}