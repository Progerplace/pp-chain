<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

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
     * Подробности {@see Func::isEmpty()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isNotEmpty()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isEvery()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isNone()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isAny()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isList()}
     *
     * @return bool|Chain
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
     * Подробности {@see Func::isHasValue()}
     *
     * @param mixed ...$values
     * @return bool|Chain
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
     * Подробности {@see Func::isFieldHasValue()}
     *
     * @param string|int $field
     * @param mixed ...$values
     * @return bool|Chain
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
     * Подробности {@see Func::isHasKey()}
     *
     * @param mixed ...$key
     * @return bool|Chain
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