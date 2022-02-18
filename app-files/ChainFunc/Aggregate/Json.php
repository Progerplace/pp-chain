<?php

namespace Ru\Progerplace\Chain\ChainFunc\Aggregate;

use Ru\Progerplace\Chain\Utils;

class Json
{
    public static function encodeFields(array $array, ...$fields): array
    {
        $fields = Utils::argumentsAsArray($fields);

        foreach ($array as $key => $item) {
            if (in_array($key, $fields)) {
                $array[$key] = json_encode($item);
            }
        }

        return $array;
    }

    public static function encodeBy(array $array, callable $callback): array
    {
        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                $array[$key] = json_encode($item);
            }
        }

        return $array;
    }

    public static function decodeFields(array $array, ...$fields): array
    {
        $fields = Utils::argumentsAsArray($fields);

        foreach ($array as $key => $item) {
            if (in_array($key, $fields)) {
                $array[$key] = json_decode($item, true);
            }
        }

        return $array;
    }

    public static function decodeBy(array $array, callable $callback): array
    {
        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                $array[$key] = json_decode($item, true);
            }
        }

        return $array;
    }
}