<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainAppend
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
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
     * Ch::from($arr)->append->merge(3, [4, 5])->toArray();
     * // [1, 2, 3, 4, 5]
     *
     * Ch::from($arr)->append->merge(3, [4, 5, [6, 7]])->toArray();
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param mixed ...$items
     * @return Chain
     *
     * @see ChainAppend::merge()
     * @see Func::mergeAppend()
     */
    public function merge(...$items): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMerge'], ...$items);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Декодировать json и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Ch::from($arr)->append->mergeFromJson('[3, 4, 5, [6, 7]]')->toArray();
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param string $json
     * @return Chain
     *
     * @see ChainFuncAppend::mergeFromJson()
     * @see Func::appendMergeFromJson()
     */
    public function mergeFromJson(string $json): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromJson'], $json);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Конвертировать строку в массив и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Ch::from($arr)->append->mergeFromString('3,4,5', ',')->toArray();
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param string $str
     * @param string $delimiter
     * @return Chain
     *
     * @see ChainFuncAppend::mergeFromString()
     * @see Func::appendMergeFromString()
     */
    public function mergeFromString(string $str, string $delimiter): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'appendMergeFromString'], $str, $delimiter);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}