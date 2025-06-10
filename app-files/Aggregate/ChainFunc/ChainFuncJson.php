<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncJson
{
    protected array     $array;
    protected ChainFunc $chain;

    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::jsonEncodeFields())}
     *
     * @param string|int ...$keys
     * @return array
     */
    public function encodeFields(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeFields'], ...$keys);
    }

    /**
     * Подробности {@see Func::jsonEncodeBy())}
     *
     * @param callable $callback
     * @return array
     */
    public function encodeBy(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeBy'], $callback);
    }

    /**
     * Подробности {@see Func::jsonDecodeFields())}
     *
     * @param string|int ...$keys
     * @return array
     */
    public function decodeFields(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeFields'], ...$keys);
    }

    /**
     * Подробности {@see Func::jsonDecodeBy())}
     *
     * @param callable $callback
     * @return array
     */
    public function decodeBy(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeBy'], $callback);
    }
}