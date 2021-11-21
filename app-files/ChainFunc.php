<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\ChainFunc\CaseKey;
use Ru\Progerplace\Chain\ChainFunc\FillKeys;
use Ru\Progerplace\Chain\ChainFunc\Json;

class ChainFunc
{
    /**
     * @var CaseKey
     */
    public static $caseKey = CaseKey::class;

    /**
     * @var FillKeys
     */
    public static $fillKeys = FillKeys::class;

    /**
     * @var Json
     */
    public static $json = Json::class;

    public static function map(array $array, callable $callback): array
    {
        foreach ($array as $key => $item) {
            $array[$key] = $callback($item, $key);
        }

        return $array;
    }

    public static function keys(array $array): array
    {
        return array_keys($array);
    }

    public static function values(array $array): array
    {
        return array_values($array);
    }

    public static function filter(array $array, callable $callback): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                $res[$key] = $item;
            }
        }

        return $res;
    }

    public static function reject(array $array, callable $callback): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            if ($callback($item, $key) === false) {
                $res[$key] = $item;
            }
        }

        return $res;
    }

    public static function sort(array $array, callable $callback): array
    {
        usort($array, $callback);

        return $array;
    }

    public static function find(array $array, callable $callback)
    {
        foreach ($array as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    public static function reduce(array $array, callable $callback, $startVal = [])
    {
        $res = $startVal;

        foreach ($array as $key => $item) {
            $res = $callback($res, $item, $key);
        }

        return $res;
    }

    public static function fillKeys(array $array, callable $callback): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyNew = $callback($item, $key);
            $res[$keyNew] = $item;
        }

        return $res;
    }

    public static function group(array $array, callable $callback): array
    {
        $res = [];

        foreach ($array as $key => $item) {
            $keyTarget = $callback($item, $key);

            if (!isset($res[$keyTarget])) {
                $res[$keyTarget] = [];
            }

            $res[$keyTarget][] = $item;
        }

        return $res;
    }

    public static function column(array $array, $field): array
    {
        return array_column($array, $field);
    }

    public static function unique(array $array): array
    {
        return array_unique($array);
    }

    public static function reverse(array $array): array
    {
        return array_reverse($array);
    }

    public static function count(array $array): int
    {
        return count($array);
    }

    public static function isEmpty(array $array): bool
    {
        return empty($array);
    }
}