<?php

namespace Ru\Progerplace\Chain\Utils;

class ArrayAction
{
    /**
     * Рекурсивное выполнение операции над элементами массива
     *
     * @param array $array
     * @param int $elemsLevel
     * @param $targetAction
     * @param ...$params
     * @return array|mixed
     */
    public static function doAction(array &$array, int $elemsLevel, $targetAction, ...$params)
    {
        if ($elemsLevel === 0) {
            return $targetAction($array, ...$params);
        }

        foreach ($array as &$child) {
            $child = self::doAction($child, $elemsLevel - 1, $targetAction, ...$params);
        }

        return $array;
    }

    /**
     * Рекурсивное выполнение мутабельной операции над элементами массива и возвратом значения это операции
     *
     * @param array $array
     * @param int $elemsLevel
     * @param $store
     * @param $targetAction
     * @param ...$params
     * @return mixed
     */
    public static function doActionMutableReturn(array &$array, int $elemsLevel, &$store, $targetAction, ...$params)
    {
        if ($elemsLevel === 0) {
            $store[] = $targetAction($array, ...$params);

            return $store;
        }

        foreach ($array as &$child) {
            self::doActionMutableReturn($child, $elemsLevel - 1, $store, $targetAction, ...$params);
        }

        return $store;
    }
}