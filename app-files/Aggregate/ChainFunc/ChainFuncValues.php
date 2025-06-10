<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncValues
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::valuesGetList()}
     *
     * @return array
     */
    public function getList(): array
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::valuesGetList($this->array);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'valuesGetList']);
    }
}