<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Exception\NotFoundException;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainGet
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - вернётся $val.
     *
     * ```
     * Ch::from([1, 2, 3])->get->orElse(1, 'else');
     * // 2
     *
     * Ch::from([1, 2, 3])->get->orElse(10, 'else');
     * // 'else'
     * ```
     *
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
     *
     * @see Func::getOrElse()
     * @see ChainFuncGet::orElse()
     */
    public function orElse($key, $val)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getOrElse($this->array, $key, $val);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrElse'], $key, $val);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Ch::from([1, 2, 3])->get->orElse(1);
     * // 2
     *
     * Func::getOrElse([1, 2, 3], 10);
     * // NotFoundException
     * ```
     *
     * @param string|int $key
     * @return mixed
     * @throws NotFoundException
     *
     * @see Func::getOrException()
     * @see ChainFuncGet::orException()
     */
    public function orException($key)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getOrException($this->array, $key);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrException'], $key);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `null`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumber(1);
     * // 2
     *
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumber(10);
     * // null
     * ```
     *
     * @param int $number
     * @return mixed|null
     *
     * @see ChainFuncGet::byNumber()
     * @see Func::getByNumber()
     */
    public function byNumber(int $number)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getByNumber($this->array, $number);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumber'], $number);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }


    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `$val`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumberOrElse(1, 'else');
     * // 2
     *
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumberOrElse(10, 'else');
     * // 'else'
     * ```
     *
     * @param int $number
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainFuncGet::byNumberOrElse()
     * @see Func::getByNumberOrElse()
     */
    public function byNumberOrElse(int $number, $val)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getByNumberOrElse($this->array, $number, $val);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrElse'], $number, $val);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }


    /**
     * Получить элемент с ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Ch::from([1, 2, 3])->get->byNumberOrException(1);
     * // 2
     *
     * Ch::from([1, 2, 3])->get->byNumberOrException(10);
     * // NotFoundException
     * ```
     *
     * @param int $number
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainFuncGet::byNumberOrException()
     * @see Func::getByNumberOrException()
     */
    public function byNumberOrException(int $number)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getByNumberOrException($this->array, $number);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrException'], $number);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }


    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->first();
     * // 1
     *
     * Ch::from([])->get->first();
     * // null
     * ```
     *
     * @return mixed|null
     *
     * @see ChainFuncGet::first()
     * @see Func::getFirst()
     */
    public function first()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getFirst($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirst']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->firstOrElse('else');
     * // 1
     *
     * Ch::from([])->get->firstOrElse('else');
     * // 'else'
     * ```
     *
     * @param mixed $val
     * @return mixed|null
     *
     * @see Func::getFirstOrElse()
     * @see ChainFuncGet::firstOrElse()
     */
    public function firstOrElse($val)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getFirstOrElse($this->array, $val);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrElse'], $val);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Ch::from([1, 2, 3])->get->firstOrException();
     * // 1
     *
     * Ch::from([])->get->firstOrException();
     * // NotFoundException
     * ```
     *
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainFuncGet::firstOrException()
     * @see Func::getFirstOrException()
     */
    public function firstOrException()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getFirstOrException($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrException']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }


    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->last();
     * // 3
     *
     * Ch::from([])->get->last();
     * // null
     * ```
     *
     * @return mixed|null
     *
     * @see Func::getLast()
     * @see ChainFuncGet::last()
     */
    public function last()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getLast($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLast']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->lastOrElse('else');
     * // 3
     *
     * Ch::from([])->get->lastOrElse('else');
     * // 'else'
     * ```
     *
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainFuncGet::lastOrElse()
     * @see Func::getLastOrElse()
     */
    public function lastOrElse($val)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getLastOrElse($this->array, $val);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrElse'], $val);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Ch::from([1, 2, 3])->get->lastOrException();
     * // 3
     *
     * Ch::from([])->get->lastOrException();
     * // NotFoundException
     * ```
     *
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainFuncGet::lastOrException()
     * @see Func::getLastOrException()
     */
    public function lastOrException()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::getLastOrException($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrException']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

}