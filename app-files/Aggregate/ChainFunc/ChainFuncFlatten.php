<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncFlatten
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Полностью убрать вложенность массива.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Cf::from($arr)->flatten->all();
     * // [1, 2, 3, 4, 5];
     * ```
     *
     * @return array
     *
     * @see Func::flattenAll()
     * @see ChainFlatten::all()
     */
    public function all(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'flattenAll']);
    }
}