<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncFilter
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Оставить только элементы коллекции с указанными ключами. Используется строгое сравнение `===`.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->keys('a', 'b');
     * // ['a' => 1, 'b' => 2]
     * ```
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainFilter::keys()
     * @see ChainFuncFilter::keys()
     */
    public function keys(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterKeys'], ...$keys);
    }

    /**
     * Оставить только элементы из коллекции с указанными значениями. Используется строгое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->values(1, '2');
     * // ['a' => 1, 'b' => 2]
     * ```
     *
     * @param mixed ...$values
     * @return array
     *
     * @see ChainFilter::values()
     * @see Func::filterValues()
     */
    public function values(...$values): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterValues'], ...$values);
    }
}