<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFilter
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Оставить только элементы коллекции с указанными ключами. Используется строгое сравнение `===`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->keys('a', 'b')->toArray();
     * // ['a' => 1, 'b' => 2]
     * ```
     * @param string|int ...$keys
     * @return Chain
     *
     * @see ChainFilter::keys()
     * @see Func::filterKeys()
     */
    public function keys(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterKeys'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Оставить только элементы из коллекции с указанными значениями. Используется строгое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->values(1, '2')->toArray();
     * // ['a' => 1, 'b' => 2]
     * ```
     *
     * @param mixed ...$values
     * @return Chain
     *
     * @see ChainFuncFilter::values()
     * @see Func::filterValues()
     */
    public function values(...$values): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'filterValues'], ...$values);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}