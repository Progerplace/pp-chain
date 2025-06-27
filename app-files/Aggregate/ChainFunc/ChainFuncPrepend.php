<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncPrepend
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Добавить элементы в начало массива. Если элемент итерируемый - то будет выполнено слияние. Неитерируемые элементы будут добавлены как есть.
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->prepend->merge(3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param mixed ...$items
     * @return array
     *
     * @see ChainFuncPrepend::merge()
     * @see Func::prependMerge()
     */
    public function merge(...$items): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMerge'], ...$items);
    }

    /**
     * Декодировать json и добавить в начало массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param string $json
     * @return array
     *
     * @see ChainFuncPrepend::mergeFromJson()
     * @see Func::prependMergeFromJson()
     */
    public function mergeFromJson(string $json): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromJson'], $json);
    }

    /**
     * Конвертировать строку в массив и добавить в начало массива (с распаковкой итерируемых элементов)
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->prepend->mergeFromString('3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param string $str
     * @param string $delimiter
     * @return array
     *
     * @see ChainFuncPrepend::mergeFromString()
     * @see Func::prependMergeFromString()
     */
    public function mergeFromString(string $str, string $delimiter): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromString'], $str, $delimiter);
    }
}