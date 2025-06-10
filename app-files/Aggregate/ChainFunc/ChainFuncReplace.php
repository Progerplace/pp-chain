<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncReplace
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::replaceRecursive()}
     *
     * @param array ...$replacement
     * @return array
     */
    public function recursive(array ...$replacement): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'replaceRecursive'], ...$replacement);
    }
}