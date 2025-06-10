<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainSlice
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::sliceHead()}
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return Chain
     */
    public function head(int $length, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceHead'], $length, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::sliceTail()}
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return Chain
     */
    public function tail(int $length, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceTail'], $length, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}