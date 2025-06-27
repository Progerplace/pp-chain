<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncChunk
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Разбивает массив на массивы с заданным в параметре `size` количеством элементов. Количество элементов в последней части будет равняться или окажется меньше заданной длины.
     *
     * Если аргумент `isPreserveKeys` равен `true`, ключи оригинального массива будут сохранены. По умолчанию - `false`, что переиндексирует части числовыми ключами. Если массив не лист - то ключи сохраняются всегда.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->chunk->bySize($arr, 2);
     * // [
     * //   [1, 2],
     * //   [3, 4],
     * //   [5]
     * // ];
     *
     * Cf::from([1, 2, 3, 4, 5])->chunk->bySize($arr, 2, true);
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ]
     * ```
     *
     * @param int $size
     * @param bool $isPreserveKeys
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-chunk.php Php.net - array_chunk
     * @see Func::chunkBySize()
     * @see ChianChunk::bySize()
     */
    public function bySize(int $size, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkBySize'], $size, $isPreserveKeys);
    }

    /**
     * Разбивает массив на заданное в параметре `count` количество массивов. Если `count` больше длины массива, то будут дописаны пустые массивы.
     *
     * Если аргумент `isPreserveKeys` равен `true`, ключи оригинального массива будут сохранены. По умолчанию - `false`, что переиндексирует части числовыми ключами. Если массив не лист - то ключи сохраняются всегда.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->chunk->byCount(3);
     * // [
     * //  [1, 2],
     * //  [3, 4],
     * //  [5]
     * // ]
     *
     * Cf::from([1, 2, 3, 4, 5])->chunk->byCount(3, true);
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ],
     * ```
     *
     * @param int $count
     * @param bool $isPreserveKeys
     * @return array
     *
     * @see ChianChunk::byCount()
     * @see Func::chunkByCount()
     */
    public function byCount(int $count, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkByCount'], $count, $isPreserveKeys);
    }
}