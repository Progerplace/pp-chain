<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncPrepend
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::prependMerge()}
     *
     * @param mixed ...$items
     * @return array
     */
    public function merge(...$items): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMerge'], ...$items);
    }

    /**
     * Подробности {@see Func::prependMergeFromJson()}
     *
     * @param string $json
     * @return array
     */
    public function mergeFromJson(string $json): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromJson'], $json);
    }

    /**
     * Подробности {@see Func::prependMergeFromString()}
     *
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public function mergeFromString(string $str, string $delimiter): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromString'], $str, $delimiter);
    }
}