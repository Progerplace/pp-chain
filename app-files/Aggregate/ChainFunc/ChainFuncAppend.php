<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncAppend
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Добавить элементы в конец коллекции. Если элемент итерируемый - то будет выполнено слияние. Неитерируемые элементы будут добавлены как есть.
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->append->merge(3, [4, 5]);
     * // [1, 2, 3, 4, 5]
     *
     * Ch::from($arr)->append->merge(3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param mixed ...$items
     * @return array
     *
     * @see ChainFuncAppend::merge()
     * @see Func::mergeAppend()
     */
    public function merge(...$items): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMerge'], ...$items);
    }

    /**
     * Декодировать json и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->append->mergeFromJson('[3, 4, 5, [6, 7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param string $json
     * @return array
     *
     * @see ChainFuncAppend::mergeFromJson()
     * @see Func::appendMergeFromJson()
     */
    public function mergeFromJson(string $json): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromJson'], $json);
    }

    /**
     * Конвертировать строку в массив и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Cf::from($arr)->append->mergeFromString('3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param string $str
     * @param string $delimiter
     * @return array
     *
     * @see ChainAppend::mergeFromString()
     * @see Func::appendMergeFromString()
     */
    public function mergeFromString(string $str, string $delimiter): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromString'], $str, $delimiter);
    }
}