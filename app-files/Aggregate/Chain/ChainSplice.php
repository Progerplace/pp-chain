<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainSplice
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::spliceHead()}
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return Chain|array
     */
    public function head(?int $length = null, $replacement = [])
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceHead($this->array, $length, $replacement);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceHead'], $length, $replacement);
        $this->chain->resetElemsLevel();

        return $res;
    }

    /**
     * Подробности {@see Func::spliceTail()}
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return Chain|array
     */
    public function tail(?int $length = null, $replacement = [])
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceTail($this->array, $length, $replacement);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceTail'], $length, $replacement);
        $this->chain->resetElemsLevel();

        return $res;
    }

}