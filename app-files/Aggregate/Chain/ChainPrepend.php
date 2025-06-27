<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainPrepend
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
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
     * Ch::from($arr)->prepend->merge(3, [4, 5, [6, 7]])->toArray();
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param mixed ...$items
     * @return Chain
     *
     * @see ChainFuncPrepend::merge()
     * @see Func::prependMerge()
     */
    public function merge(...$items): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMerge'], ...$items);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Декодировать json и добавить в начало массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Ch::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]')->toArray();
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param string $json
     * @return Chain
     *
     * @see ChainFuncPrepend::mergeFromJson()
     * @see Func::prependMergeFromJson()
     */
    public function mergeFromJson(string $json): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromJson'], $json);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Конвертировать строку в массив и добавить в начало массива (с распаковкой итерируемых элементов)
     *
     * ```
     * $arr = [1, 2];
     *
     * Ch::from($arr)->prepend->mergeFromString('3,4,5', ',')->toArray();
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param string $str
     * @param string $delimiter
     * @return Chain
     *
     * @see ChainFuncPrepend::mergeFromString()
     * @see Func::prependMergeFromString()
     */
    public function mergeFromString(string $str, string $delimiter): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'prependMergeFromString'], $str, $delimiter);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}