<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncSplice
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::spliceHead()}
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     */
    public function head(?int $length = null, $replacement = []): array
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceHead($this->array, $length, $replacement);
        }

        $store = [];
        return ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceHead'], $length, $replacement);
    }

    /**
     * Подробности {@see Func::spliceTail()}
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     */
    public function tail(?int $length = null, $replacement = []): array
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceTail($this->array, $length, $replacement);
        }

        $store = [];
        return ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceTail'], $length, $replacement);
    }
}