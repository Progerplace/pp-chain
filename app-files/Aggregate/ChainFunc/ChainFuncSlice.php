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
     * Выбирает срез массива - `length` элементов с начала массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Cf::from($arr)->slice->head(2);
     * // [1, 2]
     *
     * Cf::from($arr)->slice->head(2, true);
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     *
     * @see ChainSlice::head()
     * @see Func::sliceHead()
     */
    public function head(int $length, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceHead'], $length, $isPreserveKeys);
    }

    /**
     * Выбирает срез массива - `length` элементов с конца массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Cf::from($arr)->slice->tail(2);
     * // [4, 5]
     *
     * Cf::from($arr)->slice->tail(2, true);
     * // [13 => 4, 14 => 5]
     * ```
     *
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     *
     * @see ChainSlice::tail()
     * @see Func::sliceTail()
     */
    public function tail(int $length, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'sliceTail'], $length, $isPreserveKeys);
    }
}