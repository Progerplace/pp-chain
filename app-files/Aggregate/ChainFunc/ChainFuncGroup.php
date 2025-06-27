<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncGroup
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
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
     * Cf::from($arr)->group->byField('a');
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
     * Cf::from($arr)->group->byField($arr, 'a')
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
     * @return array
     *
     * @see ChainGroup::byField()
     * @see Func::groupByField()
     */
    public function byField($field): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupByField'], $field);
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
     * Cf::from($arr)->group->toStruct(fn($item) => $item);
     * // [
     * //   ['key' => $first, 'items' => [$first, $third]],
     * //   ['key' => $second, 'items' => [$second]],
     * //   ['key' => $fourth, 'items' => [$fourth]],
     * // ],
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see ChainGroup::toStruct()
     * @see Func::groupToStruct()
     */
    public function toStruct(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'groupToStruct'], $callback);
    }
}