<?php

namespace Ru\Progerplace\Chain\Utils;

class CaseKey
{
    public static function toCamel(string $key): string
    {
        $parts = static::buildParts($key);
        $encoding = mb_detect_encoding($key);

        $res = [];
        foreach ($parts as $index => $part) {
            $firstSymbol = $index === 0
                ? mb_strtolower(mb_substr($part, 0, 1, $encoding))
                : mb_strtoupper(mb_substr($part, 0, 1, $encoding));

            $res[] = $firstSymbol . mb_strtolower(mb_substr($part, 1, null, $encoding));
        }

        return implode('', $res);
    }

    public static function toPaskal(string $key): string
    {
        $parts = static::buildParts($key);
        $encoding = mb_detect_encoding($key);

        $res = [];
        foreach ($parts as $index => $part) {
            $firstSymbol = mb_strtoupper(mb_substr($part, 0, 1, $encoding));

            $res[] = $firstSymbol . mb_strtolower(mb_substr($part, 1, null, $encoding));
        }

        return implode('', $res);
    }

    public static function toSnake(string $key): string
    {
        $parts = static::buildParts($key);

        $res = [];
        foreach ($parts as $part) {
            $res[] = mb_strtolower($part);
        }

        return implode('_', $res);
    }

    public static function toKebab(string $key): string
    {
        $parts = static::buildParts($key);

        $res = [];
        foreach ($parts as $part) {
            $res[] = mb_strtolower($part);
        }

        return implode('-', $res);
    }

    public static function toScreamSnake(string $key): string
    {
        $parts = static::buildParts($key);

        $res = [];
        foreach ($parts as $part) {
            $res[] = mb_strtoupper($part);
        }

        return implode('_', $res);
    }

    public static function toScreamKebab(string $key): string
    {
        $parts = static::buildParts($key);

        $res = [];
        foreach ($parts as $part) {
            $res[] = mb_strtoupper($part);
        }

        return implode('-', $res);
    }

    protected static function buildParts(string $key): array
    {
        if (preg_match('/(_)/', $key)) {
            return explode('_', $key);
        } elseif (preg_match('/-/', $key)) {
            return explode('-', $key);
        }

        $regUpper = '/([A-Z])/';
        $parts = preg_split($regUpper, $key, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        $res = [];
        $added = '';
        foreach ($parts as $part) {
            if (strlen($part) === 1 && preg_match($regUpper, $part)) {
                $added .= $part;
                continue;
            }

            $res[] = $added . $part;
            $added = '';
        }

        if (!empty($added)) {
            $res[] = $added;
        }

        return $res;
    }
}