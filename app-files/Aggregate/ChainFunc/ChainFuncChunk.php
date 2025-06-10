<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncChunk
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::chunkBySize()}
     *
     * @param int $size
     * @param bool $isPreserveKeys
     * @return array
     */
    public function bySize(int $size, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkBySize'], $size, $isPreserveKeys);
    }

    /**
     * Подробности {@see Func::chunkByCount()}
     *
     * @param int $count
     * @param bool $isPreserveKeys
     * @return array
     */
    public function byCount(int $count, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkByCount'], $count, $isPreserveKeys);
    }
}