<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainChunk
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::chunkBySize()}
     *
     * @param int $size
     * @param bool $isPreserveKeys
     * @return Chain
     */
    public function bySize(int $size, bool $isPreserveKeys = false ): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkBySize'], $size, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::chunkByCount()}
     *
     * @param int $count
     * @param bool $isPreserveKeys
     * @return Chain
     */
    public function byCount(int $count, bool $isPreserveKeys = false ): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkByCount'], $count, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}