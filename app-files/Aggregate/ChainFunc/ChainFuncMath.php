<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\Aggregate\Chain\ChainMathAction;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncMath
{
    protected array     $array;
    protected ChainFunc $chain;

    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;

        $this->min = new ChainFuncMathAction($this->array, $this->chain, 'min');
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

    public ChainFuncMathAction $min;


    protected function doAction(callable $action)
    {
        if ($this->chain->elemsLevel == 0) {
            return $action($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, $action);
    }
}