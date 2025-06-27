<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncReject
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Убрать null элементы из коллекции. Проверка осуществляется методом `is_null`. Ключи сохраняются.
     *
     * ```
     * Cf::from([null, 'foo', ''])->reject->null();
     * // [1 => 'foo', 2 => '']
     *
     * Cf::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject->null();
     * // ['b' => 'foo', 'c' => '']
     * ```
     *
     * @return array
     *
     * @see ChainReject::null()
     * @see Func::rejectNull()
     */
    public function null(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectNull']);
    }

    /**
     * Убрать пустые элементы из коллекции. Проверка осуществляется методом `empty`. Ключи сохраняются.
     *
     * ```
     * Cf::from([null, 'foo', ''])->reject->empty();
     * // [1 => 'foo']
     *
     * Cf::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject->empty();
     * // ['b' => 'foo']
     * ```
     *
     * @return array
     *
     * @see ChainReject::empty()
     * @see Func::rejectEmpty()
     */
    public function empty(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectEmpty']);
    }

    /**
     * Убрать элементы из коллекции с указанными ключами. Используется нестрогое сравнение `===`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->keys('b', 'c');
     * // ['a' => 1]
     * ```
     *
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainReject::keys()
     * @see Func::rejectKeys()
     */
    public function keys(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectKeys'], ...$keys);
    }

    /**
     * Убрать элементы из коллекции с указанными значениями. Используется нестрогое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->values(1, '2')
     * // ['c' => 3]
     * ```
     *
     * @param mixed ...$values
     * @return array
     *
     * @see ChainReject::values()
     * @see Func::rejectValues()
     */
    public function values(...$values): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectValues'], ...$values);
    }
}