<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainSplice
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Удаляет часть массива с начала массива и заменяет её новыми элементами.
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $ch = Ch::from($arr);
     *
     * $ch->splice->head(2, 'item');
     * // [1, 2]
     *
     * $ch->toArray();
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
     * $ch = Ch::from($arr);
     *
     * $ch->elems->elems->splice->head(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $ch->toArray();
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
     * @return Chain|array
     *
     * @see Func::spliceHead()
     * @see ChainFuncSplice::head()
     */
    public function head(?int $length = null, $replacement = [])
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceHead($this->array, $length, $replacement);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceHead'], $length, $replacement);
        $this->chain->resetElemsLevel();

        return $res;
    }

    /**
     * Удаляет часть массива с конца массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $ch = Ch::from($arr);
     *
     * $ch->splice->tail(2, 'item');
     * // [1, 2]
     *
     * $ch->toArray();
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
     * $ch = Ch::from($arr);
     *
     * $ch->elems->elems->splice->tail(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $ch->toArray();
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
     * @return Chain|array
     *
     * @see Func::spliceTail()
     * @see ChainFuncSplice::tail()
     */
    public function tail(?int $length = null, $replacement = [])
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::spliceTail($this->array, $length, $replacement);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->chain->elemsLevel, $store, [Func::class, 'spliceTail'], $length, $replacement);
        $this->chain->resetElemsLevel();

        return $res;
    }

}