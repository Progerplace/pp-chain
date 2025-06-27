<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncGroup;
use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainGroup
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Сгруппировать элементы на основе значений поля `$field`. Если указанного поля в элементе нет, то он попадёт в группу с пустым ключом `''`.
     *
     * ```
     * $arr = [
     *   ['a' => 1],
     *   ['a' => 1],
     *   ['a' => 3],
     * ];
     *
     * Ch::from($arr)->group->byField('a')->toArray();
     * // [
     * //    1 => [
     * //      ['a' => 1],
     * //      ['a' => 1],
     * //    ],
     * //    3 => [
     * //      ['a' => 3],
     * //    ],
     * //  ],
     * ```
     * Отсутствующее поле:
     * ```
     * $arr = [
     *   ['a' => 1],
     *   ['a' => 1],
     *   ['b' => 2],
     * ];
     *
     * Ch::from($arr)->group->byField('a')->toArray();
     * // [
     * //    1 => [
     * //      0 => ['a' => 1],
     * //      1 => ['a' => 1],
     * //    ],
     * //    '' => [
     * //      0 => ['b' => 2],
     * //    ],
     * //  ],
     * ```
     *
     * @param string|int $field
     * @return Chain
     *
     * @see ChainFuncGroup::byField()
     * @see Func::groupByField()
     */
    public function byField($field): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupByField'], $field);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Сгруппировать элементы на основе значений, которые вернёт callback функция, и привести к структуре вида
     *
     * `['key' => ..., 'items' => [...]]`.
     *
     * Актуально, если возвращаемое значение не является валидным ключом массива (например, объекты). Сравнение ключей производится через сериализацию (функция `serialize`).
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
     * $arr = [$first, $second, $third, $fourth];
     *
     * Ch::from($arr)->group->toStruct(fn($item) => $item)->toArray();
     * // [
     * //   ['key' => $first, 'items' => [$first, $third]],
     * //   ['key' => $second, 'items' => [$second]],
     * //   ['key' => $fourth, 'items' => [$fourth]],
     * // ],
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFuncGroup::toStruct()
     * @see Func::groupToStruct()
     */
    public function toStruct(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupToStruct'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}