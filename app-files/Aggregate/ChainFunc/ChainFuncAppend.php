<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncAppend
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::appendMerge()}
     *
     * @param mixed ...$items
     * @return array
     */
    public function merge(...$items): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMerge'], ...$items);
    }

    /**
     * Подробности {@see Func::appendMergeFromJson()}
     *
     * @param string $json
     * @return array
     */
    public function mergeFromJson(string $json): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromJson'], $json);
    }

    /**
     * Подробности {@see Func::appendMergeFromString()}
     *
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public function mergeFromString(string $str, string $delimiter): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromString'], $str, $delimiter);
    }
}