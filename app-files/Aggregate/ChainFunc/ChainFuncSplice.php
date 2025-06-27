<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncSplice
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Удаляет часть массива с начала массива и заменяет её новыми элементами.
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $cf = Cf::from($arr);
     *
     * $cf->splice->head(2, 'item');
     * // [1, 2]
     *
     * $cf->toArray();
     * // ['item', 3, 4]
     * ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3, 4],
     *     'a.b' => [5, 6, 7, 8],
     *   ]
     * ];
     * $cf = Cf::from($arr);
     *
     * $cf->elems->elems->splice->head(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => ['item', 3, 4],
     * //     'a.b' => ['item', 7, 8],
     * //   ]
     * // ]
     * ```
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @see Func::spliceHead()
     * @see ChainSplice::head()
     */
    public function head(?int $length = null, $replacement = []): array
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceHead($this->array, $length, $replacement);
        }

        $store = [];

        return ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceHead'], $length, $replacement);
    }

    /**
     * Удаляет часть массива с конца массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $cf = Cf::from($arr);
     *
     * $cf->splice->tail(2, 'item');
     * // [1, 2]
     *
     * $arr;
     * $cf->toArray();
     * // ['item', 3, 4]
     * ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3, 4],
     *     'a.b' => [5, 6, 7, 8],
     *   ]
     * ];
     * $cf = Cf::from($arr);
     *
     * $cf->elems->elems->splice->tail(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => ['item', 3, 4],
     * //     'a.b' => ['item', 7, 8],
     * //   ]
     * // ],
     * ```
     *
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @see Func::spliceTail()
     * @see ChainSplice::tail()
     */
    public function tail(?int $length = null, $replacement = []): array
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceTail($this->array, $length, $replacement);
        }

        $store = [];

        return ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceTail'], $length, $replacement);
    }
}