<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainReplace
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Заменяет рекурсивно элементы массива элементами других массивов.
     *
     * ```
     * $arr = [
     *   [1, 2, 3],
     *   [4, 5, 6],
     * ];
     * $arrReplace1 = [
     *   1 => [1 => 7, 2 => 8]
     * ];
     * $arrReplace2 = [
     *   1 => [2 => 9]
     * ];
     *
     * Ch::from($arr)->replace->recursive($arrReplace1, $arrReplace2)->toArray();
     * // [
     * //   [1, 2, 3],
     * //   [4, 7, 9],
     * // ]
     * ```
     *
     * @param mixed ...$replacement
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-replace-recursive.php Php.net - array_replace_recursive
     * @see ChainFuncReplace::recursive
     * @see Func::replaceRecursive
     */
    public function recursive(array ...$replacement): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'replaceRecursive'], ...$replacement);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}