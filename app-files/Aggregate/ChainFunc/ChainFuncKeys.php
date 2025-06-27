<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncKeys
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->case = new ChainFuncKeysCase($array, $chain);
    }

    public ChainFuncKeysCase $case;

    /**
     * Изменить значения ключей. Повторяющиеся значения будут молча перезаписаны.
     *
     * Параметры callback функции - `$key`, `$element`
     *
     * ```
     * Cf::from(['a' =>1, 'b' => 2, 'c' => 3])->keys->map(fn(string $key, int $item) => $key . $item);
     * // ['a1' =>1, 'b2' => 2, 'c3' => 3];
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see ChainKeys::map()
     * @see Func::keysMap()
     */
    public function map(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysMap'], $callback);
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
     * Cf::from($arr)->keys->fromField('id');
     * // $arr = [
     * //   10 => ['id' => 10, 'val' => 'a'],
     * //   20 => ['id' => 20, 'val' => 'b'],
     * // ]
     * ```
     *
     * @param string|int $field
     * @return array
     *
     * @see ChainFuncKeys::fromField()
     * @see Func::keysFromField()
     */
    public function fromField($field): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysFromField'], $field);
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
     * Cf::from($arr)->elems->elems->keys->get(1);
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     *
     * @param int $number
     * @return int|string|null|array
     *
     * @see ChainKeys::get()
     * @see Func::keysGet()
     */
    public function get(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGet'], $number);
    }

    /**
     * Получить первый ключ массива.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->keys->getFirst();
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
     * Cf::from($arr)->elems->elems->keys->getFirst();
     * // [
     * //   'a' => [
     * //     'a.a' => 'a',
     * //     'a.b' => 'a',
     * //   ]
     * // ],
     * ```
     *
     * @return string|int|null|array
     *
     * @link https://www.php.net/manual/ru/function.array-key-first.php php.net - Php.net - array_key_first
     * @see ChainKeys::getFirst()
     * @see Func::keysGetFirst()
     */
    public function getFirst()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetFirst']);
    }

    /**
     * Получить последний ключ массива.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->keys->getLast();
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
     * Cf::from($arr)->elems->elems->keys->getLast();
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     *
     * @return int|string|null|array
     *
     * @link https://www.php.net/manual/ru/function.array-key-last.php php.net - Php.net - array_key_last
     * @see ChainKeys::getLast()
     * @see Func::keysGetLast()
     */
    public function getLast()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetLast']);
    }
}