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
     * Подробности {@see Func::getOrElse()}
     *
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
     */
    public function orElse($key, $val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrElse'], $key, $val);
    }

    /**
     * Подробности {@see Func::getOrException()}
     *
     * @param string|int $key
     * @return mixed
     */
    public function orException($key)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getOrException'], $key);
    }

    /**
     * Подробности {@see Func::getByNumber()}
     *
     * @param int $number
     * @return mixed
     */
    public function byNumber(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumber'], $number);
    }

    /**
     * Подробности {@see Func::getByNumberOrElse()}
     *
     * @param int $number
     * @param mixed $val
     * @return mixed|null
     */
    public function byNumberOrElse(int $number, $val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrElse'], $number, $val);
    }

    /**
     * Подробности {@see Func::getByNumberOrException()}
     *
     * @param int $number
     * @return mixed
     */
    public function byNumberOrException(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getByNumberOrException'], $number);
    }


    /**
     * Подробности {@see Func::getFirst()}
     *
     * @return mixed
     */
    public function first()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirst']);
    }

    /**
     * Подробности {@see Func::getFirstOrElse()}
     *
     * @param mixed $val
     * @return mixed|null
     */
    public function firstOrElse($val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrElse'], $val);
    }


    /**
     * Подробности {@see Func::getFirstOrException()}
     *
     * @return mixed
     */
    public function firstOrException()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getFirstOrException']);
    }


    /**
     * Подробности {@see Func::getLast()}
     *
     * @return mixed
     */
    public function last()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLast']);
    }

    /**
     * Подробности {@see Func::getLastOrElse()}
     *
     * @param mixed $val
     * @return mixed|null
     */
    public function lastOrElse($val)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrElse'], $val);
    }


    /**
     * Подробности {@see Func::getLastOrException()}
     *
     * @return mixed
     */
    public function lastOrException()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'getLastOrException']);
    }
}