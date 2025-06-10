<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainJson
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::jsonEncodeFields())}
     *
     * @param string|int ...$keys
     * @return Chain
     */
    public function encodeFields(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeFields'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::jsonEncodeBy())}
     *
     * @param callable $callback
     * @return Chain
     */
    public function encodeBy(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeBy'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::jsonDecodeFields())}
     *
     * @param string|int ...$keys
     * @return Chain
     */
    public function decodeFields(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeFields'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::jsonDecodeBy())}
     *
     * @param callable $callback
     * @return Chain
     */
    public function decodeBy(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeBy'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}