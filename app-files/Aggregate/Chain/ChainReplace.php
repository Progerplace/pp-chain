<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainReplace
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::replaceRecursive()}
     *
     * @param mixed ...$replacement
     * @return Chain
     */
    public function recursive(array ...$replacement): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'replaceRecursive'], ...$replacement);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}