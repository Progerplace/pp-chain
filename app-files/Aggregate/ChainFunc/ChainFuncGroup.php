<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncGroup
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::groupByField()}
     *
     * @param string|int $field
     * @return array
     */
    public function byField($field): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupByField'], $field);
    }

    /**
     * Подробности {@see Func::groupToStruct()}
     *
     * @param callable $callback
     * @return array
     */
    public function toStruct(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupToStruct'], $callback);
    }
}