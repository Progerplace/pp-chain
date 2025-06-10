<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncFilter
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::filterKeys()}
     *
     * @param string|int ...$keys
     * @return array
     */
    public function keys(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterKeys'], ...$keys);
    }

    /**
     * Подробности {@see Func::filterValues()}
     *
     * @param mixed ...$values
     * @return array
     */
    public function values(string ...$values): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterValues'], ...$values);
    }
}