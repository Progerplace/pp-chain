<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;

class ChainOuter
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Заменить массив целиком
     *
     * ```
     * $ch = Chain::from([1,2])
     * $ch->toArray()
     * // [1,2]
     * $ch->outer->replaceWith([3,4])->toArray()
     * // [3,4]
     * ```
     *
     * @param array $arr
     * @return Chain
     */
    public function replaceWith(array $arr): Chain
    {
        return $this->chain->replaceWith($arr);
    }

    /**
     * Проверить логическое условие ко всему массиву.
     *
     * Параметр `callback` - функции - `$array`
     *
     * @param callable $callback
     * @return bool
     */
    public function is(callable $callback): bool
    {
        return $callback($this->array);
    }

    /**
     * Изменить массив целиком.
     *
     * Параметр `callback` - функции - `$array`
     *
     * @param callable $callback
     * @return Chain
     */
    public function change(callable $callback): Chain
    {
        $this->array = $callback($this->array);

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Выполнить действие с массивом целиком без его изменения (например, для точки остановки в дебагере).
     *
     * Параметр `callback` - функции - `$array`
     *
     * @param callable $callback
     * @return Chain
     */
    public function check(callable $callback): Chain
    {
        $callback($this->array);

        return $this->chain;
    }

    /**
     * Вывести массив через `print_r` (с тегами `pre`)
     *
     * @return Chain
     */
    public function print(): Chain
    {
        echo '<pre>';
        print_r($this->array);
        echo '</pre>';

        return $this->chain;
    }
}