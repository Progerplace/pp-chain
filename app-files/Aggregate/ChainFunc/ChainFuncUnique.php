<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncUnique
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
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
     * Cf::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value);
     * // ['a' => $first, 'b' => $second, 'd' => $fourth],
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see ChainUnique::by()
     * @see Func::uniqueBy()
     */
    public function by(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'uniqueBy'], $callback);
    }
}