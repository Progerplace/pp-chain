<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncIs;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainIs
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Проверка на пустой массив.
     *
     * ```
     * Ch::from([])->isEmpty()
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => []
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->empty()->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::empty()
     * @see Func::isEmpty()
     */
    public function empty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEmpty($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEmpty']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка на непустой массив.
     *
     * ```
     * Ch::from([])->isNotEmpty()
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => []
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->notEmpty()->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::notEmpty()
     * @see Func::isNotEmpty()
     */
    public function notEmpty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNotEmpty($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNotEmpty']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка "все элементы удовлетворяют условию". Вернёт `true`, если для каждого элемента функция `callback` вернёт `true`.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Ch::from($arr)->is->every(fn(int $item) => $item > 0);
     * // true
     *
     * Ch::from($arr)->is->every(fn(int $item) => $item > 1);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0)->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::every()
     * @see Func::isEvery()
     */
    public function every(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEvery($this->array, $callback);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEvery'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка "все элементы не удовлетворяют условию". Вернёт `true`, если для каждого элемента функция `callback` вернёт `false`.
     *
     * ```
     * Ch::from([1, 2, 3])->is->none(fn(int $item) => $item < 0);
     * // true
     *
     * Ch::from([1, 2, 3])->is->none(fn(int $item) => $item > 2);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->none(fn(int $item) => $item < 0)->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::none()
     * @see Func::isNone()
     */
    public function none(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNone($this->array, $callback);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNone'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка "хотя бы один элемент удовлетворяют условию". Вернёт `true`, если хотя бы для одного элемента функция `callback` вернёт `true`.
     *
     * ```
     * Ch::from([1, 2, 3])->is->any(fn(int $item) => $item >= 3);
     * // true
     *
     * Ch::from([1, 2, 3])->is->any(fn(int $item) => $item > 10);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3]
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->any(fn(int $item) => $item > 1)->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::any()
     * @see Func::isAny()
     */
    public function any(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isAny($this->array, $callback);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isAny'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверяет, является ли массив списком.
     *
     * ```
     * Ch::from([0 => 1, 1 => 2, 2 => 3])->is->list();
     * // true
     *
     * Ch::from([10 => 1, 11 => 2, 12 => 3])->is->list();
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [0 => 1, 1 => 2, 2 => 3]
     *   ]
     * ];
     * Ch::from($arr)->elems->elems->is->list()->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     *
     * @return bool|Chain
     *
     * @see ChainFuncIs::list()
     * @see Func::isList()
     */
    public function list()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isList($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isList']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверки, есть ли хотя бы одно из переданных значений в массиве. Используется строгое сравнение `===`.
     *
     * ```
     * Ch::from([1, 2, 3])->is->hasValue(3, 4);
     * // true
     * ```
     *
     * @param mixed ...$values
     * @return bool|Chain
     *
     * @see ChainFuncIs::hasValue()
     * @see Func::isHasValue()
     */
    public function hasValue(...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasValue($this->array, ...$values);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasValue'], ...$values);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка, что значение поля `field` равно хотя бы одному из переданных значений `values`. Используется строгое сравнение `===`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->is->fieldHasValue('a', 1, 10);
     * // true
     * ```
     *
     * @param string|int $field
     * @param mixed ...$values
     * @return bool|Chain
     *
     * @see ChainFuncIs::fieldHasValue()
     * @see Func::isFieldHasValue()
     */
    public function fieldHasValue($field, ...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isFieldHasValue($this->array, $field, ...$values);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasValue'], $field, ...$values);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Проверка, присутствует ли в массиве хотя бы один ключ из `keys`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->is->hasKey('a', 'd');
     * // true
     * ```
     *
     * @param mixed ...$key
     * @return bool|Chain
     *
     * @see ChainFuncIs::hasKey()
     * @see Func::isHasKey()
     */
    public function hasKey(...$key)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasKey($this->array, ...$key);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasKey'], ...$key);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}