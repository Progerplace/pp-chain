<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainKeys
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->case = new ChainKeysCase($array, $chain);
    }

    public ChainKeysCase $case;

    /**
     * Изменить значения ключей. Повторяющиеся значения будут молча перезаписаны.
     *
     * Параметры callback функции - `$key`, `$element`
     *
     * ```
     * Ch::from(['a' =>1, 'b' => 2, 'c' => 3])->keys->map(fn(string $key, int $item) => $key . $item)->toArray();
     * // ['a1' =>1, 'b2' => 2, 'c3' => 3];
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFuncKeys::map()
     * @see Func::keysMap()
     */
    public function map(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysMap'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Заполнить ключи из значений поля. Повторяющиеся значения будут молча перезаписаны.
     *
     * ```
     * $arr = [
     *   ['id' => 10, 'val' => 'a'],
     *   ['id' => 20, 'val' => 'b'],
     * ]
     *
     * Ch::from($arr)->keys->fromField('id')->toArray();
     * // $arr = [
     * //   10 => ['id' => 10, 'val' => 'a'],
     * //   20 => ['id' => 20, 'val' => 'b'],
     * // ]
     * ```
     *
     * @param string|int $field
     * @return Chain
     *
     * @see ChainFuncKeys::fromField()
     * @see Func::keysFromField()
     */
    public function fromField($field): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysFromField'], $field);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить ключ по номеру в массиве. Нумерация начинается с 0.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->keys->get(1);
     * // 'b'
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => ['a' => 10, 'b' => 'a'],
     *     'a.b' => ['a' => 10, 'b' => 'a']
     *   ],
     * ];
     *
     * Ch::from($arr)->elems->elems->keys->get(1)->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     *
     * @param int $number
     * @return int|string|null|Chain
     *
     * @see ChainFuncKeys::get()
     * @see Func::keysGet()
     */
    public function get(int $number)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGet($this->array, $number);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGet'], $number);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить первый ключ массива.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->keys->getFirst();
     * // 'a'
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => ['a' => 10, 'b' => 'a'],
     *     'a.b' => ['a' => 10, 'b' => 'a']
     *   ],
     * ];
     *
     * Ch::from($arr)->elems->elems->keys->getFirst->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => 'a',
     * //     'a.b' => 'a',
     * //   ]
     * // ],
     * ```
     *
     * @return int|string|null|Chain
     *
     * @link https://www.php.net/manual/ru/function.array-key-first.php php.net - Php.net - array_key_first
     * @see ChainFuncKeys::getFirst()
     * @see Func::keysGetFirst()
     */
    public function getFirst()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGetFirst($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetFirst']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Получить последний ключ массива.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->keys->getLast();
     * // 'b'
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => ['a' => 10, 'b' => 'a'],
     *     'a.b' => ['a' => 10, 'b' => 'a']
     *   ],
     * ];
     *
     * Ch::from($arr)->elems->elems->keys->getLast->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     *
     * @return int|string|null|Chain
     *
     * @link https://www.php.net/manual/ru/function.array-key-last.php php.net - Php.net - array_key_last
     * @see ChainFuncKeys::getLast()
     * @see Func::keysGetLast()
     */
    public function getLast()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGetLast($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetLast']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}