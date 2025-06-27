<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncReplace
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
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
     * Cf::from($arr)->replace->recursive($arrReplace1, $arrReplace2);
     * // [
     * //   [1, 2, 3],
     * //   [4, 7, 9],
     * // ]
     * ```
     *
     * @param array ...$replacement
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-replace-recursive.php Php.net - array_replace_recursive
     * @see ChainReplace::recursive()
     * @see Func::replaceRecursive()
     */
    public function recursive(array ...$replacement): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'replaceRecursive'], ...$replacement);
    }
}