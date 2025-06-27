<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainChunk
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
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
     * Ch::from([1, 2, 3, 4, 5])->chunk->bySize($arr, 2)->toArray();
     * // [
     * //   [1, 2],
     * //   [3, 4],
     * //   [5]
     * // ];
     *
     * Ch::from([1, 2, 3, 4, 5])->chunk->bySize($arr, 2, true)->toArray();
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ]
     * ```
     *
     * @param int $size
     * @param bool $isPreserveKeys
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-chunk.php Php.net - array_chunk
     * @see ChainFuncChunk::bySize()
     * @see Func::chunkBySize()
     */
    public function bySize(int $size, bool $isPreserveKeys = false ): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkBySize'], $size, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Разбивает массив на заданное в параметре `count` количество массивов. Если `count` больше длины массива, то будут дописаны пустые массивы.
     *
     * Если аргумент `isPreserveKeys` равен `true`, ключи оригинального массива будут сохранены. По умолчанию - `false`, что переиндексирует части числовыми ключами. Если массив не лист - то ключи сохраняются всегда.
     *
     * ```
     * Ch::from([1, 2, 3, 4, 5])->chunk->byCount(3)->toArray();
     * // [
     * //  [1, 2],
     * //  [3, 4],
     * //  [5]
     * // ]
     *
     * Ch::from([1, 2, 3, 4, 5])->chunk->byCount(3, true)->toArray();
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ],
     * ```
     *
     * @param int $count
     * @param bool $isPreserveKeys
     * @return Chain
     *
     * @see ChainFuncChunk::byCount()
     * @see Func::chunkByCount()
     */
    public function byCount(int $count, bool $isPreserveKeys = false ): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'chunkByCount'], $count, $isPreserveKeys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}