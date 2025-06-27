<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncIs
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Проверка на пустой массив.
     *
     * ```
     * Cf::from([])->isEmpty()
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => []
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->empty();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::empty()
     * @see Func::isEmpty()
     */
    public function empty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEmpty($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEmpty']);
    }

    /**
     * Проверка на непустой массив.
     *
     * ```
     * Cf::from([])->isNotEmpty()
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => []
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->notEmpty();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::notEmpty()
     * @see Func::isNotEmpty()
     */
    public function notEmpty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNotEmpty($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNotEmpty']);
    }

    /**
     * Проверка "все элементы удовлетворяют условию". Вернёт true, если для каждого элемента функция callback вернёт true.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Cf::from($arr)->is->every(fn(int $item) => $item > 0);
     * // true
     *
     * Cf::from($arr)->is->every(fn(int $item) => $item > 1);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::every()
     * @see Func::isEvery()
     */
    public function every(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEvery($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEvery'], $callback);
    }

    /**
     * Проверка "все элементы не удовлетворяют условию". Вернёт `true`, если для каждого элемента функция `callback` вернёт `false`.
     *
     * ```
     * Cf::from([1, 2, 3])->is->none(fn(int $item) => $item < 0);
     * // true
     *
     * Cf::from([1, 2, 3])->is->none(fn(int $item) => $item > 0);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->none(fn(int $item) => $item < 0);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::none()
     * @see Func::isNone()
     */
    public function none(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNone($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNone'], $callback);
    }

    /**
     * Проверка "хотя бы один элемент удовлетворяют условию". Вернёт `true`, если хотя бы для одного элемента функция `callback` вернёт `true`.
     *
     * ```
     * Cf::from([1, 2, 3])->is->any(fn(int $item) => $item >= 3);
     * // true
     *
     * Cf::from([1, 2, 3])->is->any(fn(int $item) => $item > 10);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->any(fn(int $item) => $item > 1);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::any()
     * @see Func::isAny()
     */
    public function any(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isAny($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isAny'], $callback);
    }

    /**
     * Проверяет, является ли массив списком.
     *
     * ```
     * Cf::from([0 => 1, 1 => 2, 2 => 3])->is->list();
     * // true
     *
     * Cf::from([10 => 1, 11 => 2, 12 => 3])->is->list();
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [0 => 1, 1 => 2, 2 => 3]
     *   ]
     * ];
     * Cf::from($arr)->elems->elems->is->list();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|array
     *
     * @see ChainIs::list()
     * @see Func::isList()
     */
    public function list()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isList($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isList']);
    }

    /**
     * Проверки, есть ли хотя бы одно из переданных значений в массиве. Используется строгое сравнение `===`.
     *
     * ```
     * Cf::from([1, 2, 3])->is->hasValue(3, 4);
     * // true
     * ```
     *
     * @param mixed ...$values
     * @return bool|array
     *
     * @see ChainIs::hasValue()
     * @see Func::isHasValue()
     */
    public function hasValue(...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasValue($this->array, ...$values);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasValue'], ...$values);
    }

    /**
     * Проверка, что значение поля `field` равно хотя бы одному из переданных значений `values`. Используется строгое сравнение `===`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->is->fieldHasValue('a', 1, 10);
     * // true
     * ```
     *
     * @param string|int $field
     * @param mixed ...$values
     * @return bool|array
     *
     * @see ChainIs::fieldHasValue()
     * @see Func::isFieldHasValue()
     */
    public function fieldHasValue($field, ...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isFieldHasValue($this->array, $field, ...$values);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isFieldHasValue'], $field, ...$values);
    }

    /**
     * Проверка, присутствует ли в массиве хотя бы один ключ из `keys`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->is->hasKey('a', 'd');
     * // true
     * ```
     *
     * @param mixed ...$keys
     * @return bool|array
     *
     * @see ChainIs::hasKey()
     * @see Func::isHasKey()
     */
    public function hasKey(...$keys)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasKey($this->array, ...$keys);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasKey'], ...$keys);
    }
}