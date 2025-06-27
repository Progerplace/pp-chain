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
     * Выбирает срез массива - `length` элементов с начала массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Ch::from($arr)->slice->head(2)->toArray();
     * // [1, 2]
     *
     * Ch::from($arr)->slice->head(2, true)->toArray();
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return Chain
     *
     * @see ChainFuncSlice::head()
     * @see Func::sliceHead()
     */
    public function head(int $length, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceHead'], $length, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Выбирает срез массива - `length` элементов с конца массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Ch::from($arr)->slice->tail(2)->toArray();
     * // [4, 5]
     *
     * Ch::from($arr)->slice->tail(2, true)->toArray();
     * // [13 => 4, 14 => 5]
     * ```
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return Chain
     *
     * @see ChainFuncSlice::tail()
     * @see Func::sliceTail()
     */
    public function tail(int $length, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceTail'], $length, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}