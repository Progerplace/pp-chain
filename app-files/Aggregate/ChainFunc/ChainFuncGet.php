<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;
use Throwable;

class ChainFuncGet
{
    protected array     $array;
    protected ChainFunc $chain;

    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - вернётся $val.
     *
     * ```
     * Cf::from([1, 2, 3])->get->orElse(1, 'else');
     * // 2
     *
     * Cf::from([1, 2, 3])->get->orElse(10, 'else');
     * // 'else'
     * ```
     *
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::orElse()
     * @see Func::getOrElse()
     */
    public function orElse($key, $val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrElse'], $key, $val);
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Cf::from([1, 2, 3])->get->orElse(1);
     * // 2
     *
     * Func::getOrElse([1, 2, 3], 10);
     * // NotFoundException
     * ```
     *
     * @param string|int $key
     * @return mixed
     *
     * @see ChainGet::orException()
     * @see Func::getOrException()
     */
    public function orException($key)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrException'], $key);
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `null`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumber(1);
     * // 2
     *
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumber(10);
     * // null
     * ```
     *
     * @param int $number
     * @return mixed
     *
     * @see ChainGet::byNumber()
     * @see Func::getByNumber()
     */
    public function byNumber(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumber'], $number);
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `$val`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumberOrElse(1, 'else');
     * // 2
     *
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->byNumberOrElse(10, 'else');
     * // 'else'
     * ```
     *
     * @param int $number
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::byNumberOrElse()
     * @see Func::getByNumberOrElse()
     */
    public function byNumberOrElse(int $number, $val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrElse'], $number, $val);
    }

    /**
     * Получить элемент с ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Cf::from([1, 2, 3])->get->byNumberOrException(1);
     * // 2
     *
     * Cf::from([1, 2, 3])->get->byNumberOrException(10);
     * // NotFoundException
     * ```
     *
     * @param int $number
     * @return mixed
     *
     * @see ChainGet::byNumberOrException()
     * @see Func::getByNumberOrException()
     */
    public function byNumberOrException(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrException'], $number);
    }


    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->first();
     * // 1
     *
     * Cf::from([])->get->first();
     * // null
     * ```
     *
     * @return mixed
     *
     * @see Func::getFirst()
     * @see ChainGet::first()
     */
    public function first()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirst']);
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->firstOrElse('else');
     * // 1
     *
     * Cf::from([])->get->firstOrElse('else');
     * // 'else'
     * ```
     *
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::firstOrElse()
     * @see Func::getFirstOrElse()
     */
    public function firstOrElse($val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrElse'], $val);
    }


    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Cf::from([1, 2, 3])->get->firstOrException();
     * // 1
     *
     * Cf::from([])->get->firstOrException();
     * // NotFoundException
     * ```
     *
     * @return mixed
     *
     * @see ChainGet::firstOrException()
     * @see Func::getFirstOrException()
     */
    public function firstOrException()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrException']);
    }


    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->last();
     * // 3
     *
     * Cf::from([])->get->last();
     * // null
     * ```
     *
     * @return mixed
     *
     * @see ChainGet::last()
     * @see Func::getLast()
     */
    public function last()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLast']);
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->get->lastOrElse('else');
     * // 3
     *
     * Cf::from([])->get->lastOrElse('else');
     * // 'else'
     * ```
     *
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::lastOrElse()
     * @see Func::getLastOrElse()
     */
    public function lastOrElse($val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrElse'], $val);
    }


    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Cf::from([1, 2, 3])->get->lastOrException();
     * // 3
     *
     * Cf::from([])->get->lastOrException();
     * // NotFoundException
     * ```
     *
     * @return mixed
     *
     * @see ChainGet::lastOrException()
     * @see Func::getLastOrException()
     */
    public function lastOrException()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrException']);
    }
}