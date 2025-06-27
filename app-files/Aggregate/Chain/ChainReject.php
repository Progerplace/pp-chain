<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncReject;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainReject
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Убрать null элементы из коллекции. Проверка осуществляется методом `is_null`. Ключи сохраняются.
     *
     * ```
     * Ch::from([null, 'foo', ''])->reject->null()->toArray();
     * // [1 => 'foo', 2 => '']
     *
     * Ch::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject->null()->toArray();
     * // ['b' => 'foo', 'c' => '']
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncReject::null()
     * @see Func::rejectNull()
     */
    public function null(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectNull']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Убрать пустые элементы из коллекции. Проверка осуществляется методом `empty`. Ключи сохраняются.
     *
     * ```
     * Ch::from([null, 'foo', ''])->reject->empty()->toArray();
     * // [1 => 'foo']
     *
     * Ch::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject->empty()->toArray();
     * // ['b' => 'foo']
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncReject::empty()
     * @see Func::rejectEmpty()
     */
    public function empty(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectEmpty']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Убрать элементы из коллекции с указанными ключами. Используется строгое сравнение `===`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->keys('b', 'c')->toArray();
     * // ['a' => 1]
     * ```
     *
     * @param string|int ...$keys
     * @return Chain
     *
     * @see ChainFuncReject::keys()
     * @see Func::rejectKeys()
     */
    public function keys(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectKeys'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Убрать элементы из коллекции с указанными значениями. Используется строгое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->values(1, '2')->toArray()
     * // ['c' => 3]
     * ```
     *
     * @param mixed ...$values
     * @return Chain
     *
     * @see ChainFuncReject::values()
     * @see Func::rejectValues()
     */
    public function values(...$values): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectValues'], ...$values);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}