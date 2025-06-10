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
     * Подробности {@see Func::isEmpty()}
     *
     * @return bool|array
     */
    public function empty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEmpty($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEmpty']);
    }

    /**
     * Подробности {@see Func::isNotEmpty()}
     *
     * @return bool|array
     */
    public function notEmpty()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNotEmpty($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNotEmpty']);
    }

    /**
     * Подробности {@see Func::isEvery()}
     *
     * @return bool|array
     */
    public function every(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isEvery($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isEvery'], $callback);
    }

    /**
     * Подробности {@see Func::isNone()}
     *
     * @return bool|array
     */
    public function none(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isNone($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isNone'], $callback);
    }

    /**
     * Подробности {@see Func::isAny()}
     *
     * @return bool|array
     */
    public function any(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isAny($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isAny'], $callback);
    }

    /**
     * Подробности {@see Func::isList()}
     *
     * @return bool|array
     */
    public function list()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isList($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isList']);
    }

    /**
     * Подробности {@see Func::isHasValue()}
     *
     * @param mixed ...$values
     * @return bool|array
     */
    public function hasValue(...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasValue($this->array, ...$values);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasValue'], ...$values);
    }

    /**
     * Подробности {@see Func::isFieldHasValue()}
     *
     * @param string|int $field
     * @param mixed ...$values
     * @return bool|array
     */
    public function fieldHasValue($field, ...$values)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isFieldHasValue($this->array, $field, ...$values);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isFieldHasValue'], $field, ...$values);
    }

    /**
     * Подробности {@see Func::isHasKey()}
     *
     * @param mixed ...$keys
     * @return bool|array
     */
    public function hasKey(...$keys)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::isHasKey($this->array, ...$keys);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'isHasKey'], ...$keys);
    }
}