<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainAppend
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::appendMerge()}
     *
     * @param mixed ...$items
     * @return Chain
     */
    public function merge(...$items): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMerge'], ...$items);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::appendMergeFromJson()}
     *
     * @param string $json
     * @return Chain
     */
    public function mergeFromJson(string $json): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromJson'], $json);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::appendMergeFromString()}
     *
     * @param string $str
     * @param string $delimiter
     * @return Chain
     */
    public function mergeFromString(string $str, string $delimiter): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromString'], $str, $delimiter);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}