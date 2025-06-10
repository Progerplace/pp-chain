<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainPrepend
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::prependMerge()}
     *
     * @param mixed ...$items
     * @return Chain
     */
    public function merge(...$items): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMerge'], ...$items);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::prependMergeFromJson()}
     *
     * @param string $json
     * @return Chain
     */
    public function mergeFromJson(string $json): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromJson'], $json);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::prependMergeFromString()}
     *
     * @param string $str
     * @param string $delimiter
     * @return Chain
     */
    public function mergeFromString(string $str, string $delimiter): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromString'], $str, $delimiter);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}