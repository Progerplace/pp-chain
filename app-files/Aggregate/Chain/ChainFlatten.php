<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFlatten
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
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
     * Ch::from($arr)->flatten->all()->toArray();
     * // [1, 2, 3, 4, 5];
     * ```
     *
     * @return Chain
     *
     * @see ChainFLatten::all()
     * @see Func::flattenAll()
     */
    public function all(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'flattenAll']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}