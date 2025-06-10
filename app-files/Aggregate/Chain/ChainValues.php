<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainValues
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::valuesGetList()}
     *
     * @return Chain|array
     */
    public function getList()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::valuesGetList($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'valuesGetList']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}