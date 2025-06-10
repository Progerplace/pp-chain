<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFilter
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::filterKeys()}
     *
     * @param string|int ...$keys
     * @return Chain
     */
    public function keys(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterKeys'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::filterValues()}
     *
     * @param mixed ...$values
     * @return Chain
     */
    public function values(...$values): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterValues'], ...$values);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}