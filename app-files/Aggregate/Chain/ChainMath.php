<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainMath
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;

        $this->min = new ChainMathAction($this->array, $this->chain, 'min');
    }

    /**
     * Подробности {@see Func::mathMin()}
     *
     * @return Chain|mixed
     */
    public function min()
    {
        return $this->doAction([Func::class, 'mathMin']);
    }

    public ChainMathAction $min;


    protected function doAction(callable $action)
    {
        if ($this->chain->elemsLevel == 0) {
            return $action($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, $action);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}