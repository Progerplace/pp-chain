<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainUnique
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Удалить повторяющиеся элементы на основе возвращаемых функцией `$callback` значений. Ключи сохраняются.
     *
     * ```
     * $first = new stdClass();
     * $first->value = 1;
     *
     * $second = new stdClass();
     * $second->value = 2;
     *
     * $third = new stdClass();
     * $third->value = 1;
     *
     * $fourth = new stdClass();
     * $fourth->value = 3;
     *
     * $arr = ['a' => $first, 'b' => $second, 'c' => $third, 'd' => $fourth];
     *
     * Ch::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value)->toArray();
     * // ['a' => $first, 'b' => $second, 'd' => $fourth],
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFuncUnique::by()
     * @see Func::uniqueBy()
     */
    public function by(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'uniqueBy'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}