<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncSlice
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::sliceHead()}
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     */
    public function head(int $length, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceHead'], $length, $isPreserveKeys);
    }

    /**
     * Подробности {@see Func::sliceTail()}
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     */
    public function tail(int $length, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceTail'], $length, $isPreserveKeys);
    }
}