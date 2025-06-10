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
     * Подробности {@see Func::getOrElse()}
     *
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
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
     * Подробности {@see Func::getOrException()}
     *
     * @param string|int $key
     * @return mixed
     * @throws NotFoundException
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
     * Подробности {@see Func::getByNumber()}
     *
     * @param int $number
     * @return mixed|null
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
     * Подробности {@see Func::getByNumberOrElse()}
     *
     * @param int $number
     * @param mixed $val
     * @return mixed|null
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
     * Подробности {@see Func::getByNumberOrException()}
     *
     * @param int $number
     * @return mixed
     * @throws NotFoundException
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
     * Подробности {@see Func::getFirst()}
     *
     * @return mixed|null
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
     * Подробности {@see Func::getFirstOrElse()}
     *
     * @param mixed $val
     * @return mixed|null
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
     * Подробности {@see Func::getFirstOrException()}
     *
     * @return mixed
     * @throws NotFoundException
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
     * Подробности {@see Func::getLast()}
     *
     * @return mixed|null
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
     * Подробности {@see Func::getlastOrElse()}
     *
     * @param mixed $val
     * @return mixed|null
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
     * Подробности {@see Func::getLastOrException()}
     *
     * @return mixed
     * @throws NotFoundException
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