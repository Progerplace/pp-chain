<?php

namespace Ru\Progerplace\Chain;

use Error;
use Ru\Progerplace\Chain\Exception\NotFoundException;
use Ru\Progerplace\Chain\Utils\CaseKey;
use Ru\Progerplace\Chain\Utils\MathAction;
use Throwable;

class Func
{
    /**
     * Изменить элементы коллекции. Ключи сохраняются.
     *
     * Параметры callback - `$element`, `$key`
     *
     * ```
     * Func::map(['a' => 1, 'b' => 2], fn(int $item, string $key) => $key . $item)
     * // ['a' => 'a1', 'b' => 'b2']
     *
     * ChainFunc::from([1,2,3]))->map(fn(int $item) => $item + 5);
     * Chain::from([1,2,3]))->map(fn(int $item) => $item + 5)->toArray();
     * Func::map([1,2,3], fn(int $item) => $item + 5);
     * // [6,7,8]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function map(array $arr, callable $callback): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $res[$key] = $callback($item, $key);
        }

        return $res;
    }

    /**
     * Убрать элементы из коллекции, для которых функция $callback вернула true. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`
     *
     * ```
     * Func::reject(['a' => null, 'b' => 'foo', 'c' => ''], fn(?string $item, string $key) => $key === 'a' || $item === 'foo')
     * // ['c' => '']
     *
     * ChainFunc::from([1,2,3,4,5])->reject(fn(int $item) => $item < 4)
     * Chain::from([1,2,3,4,5])->reject(fn(int $item) => $item < 4)->toArray()
     * Func::reject([1,2,3,4,5], fn(int $item) => $item < 4)
     * // [3 => 4, 4 => 5]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function reject(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            if ($callback($item, $key) === false) {
                $res[$key] = $item;
            }
        }

        return $res;
    }

    /**
     * Убрать null элементы из коллекции. Проверка осуществляется методом `is_null`. Ключи сохраняются.
     *
     * ```
     * Func::rejectNull(['a' => null, 'b' => 'foo', 'c' => ''])
     * // ['b' => 'foo', 'c' => '']
     *
     * ChainFunc::from([null, 'foo', ''])->reject->null()
     * Chain::from([null, 'foo', ''])->reject->null()->toArray()
     * Func::rejectNull([null, 'foo', ''])
     * // [1 => 'foo', 2 => '']
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function rejectNull(array $arr): array
    {
        return array_filter($arr, fn($item) => !is_null($item));
    }

    /**
     * Убрать пустые элементы из коллекции. Проверка осуществляется методом `empty`. Ключи сохраняются.
     *
     * ```
     * Func::rejectEmpty(['a' => null, 'b' => 'foo', 'c' => ''])
     * // ['b' => 'foo']
     *
     * ChainFunc::from([null, 'foo', ''])->reject->empty()
     * Chain::from([null, 'foo', ''])->reject->empty()->toArray()
     * Func::rejectEmpty([null, 'foo', ''])
     * // [1 => 'foo']
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function rejectEmpty(array $arr): array
    {
        return array_filter($arr, fn($item) => !empty($item));
    }

    /**
     * Убрать элементы из коллекции с указанными ключами. Используется нестрогое сравнение `==`.
     *
     * ```
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->keys('b', 'c')
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->keys('b', 'c')->toArray()
     * Func::rejectKeys(['a' => 1, 'b' => 2, 'c' => 3], 'b', 'c')
     * // ['a' => 1]
     * ```
     *
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     */
    public static function rejectKeys(array $arr, ...$keys): array
    {
        return array_filter($arr, fn($key) => !in_array($key, $keys), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Убрать элементы из коллекции с указанными значениями. Используется нестрогое сравнение `==`. Ключи сохраняются.
     *
     * ```
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->values(1, '2')
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->reject->values(1, '2')->toArray()
     * Func::rejectValues(['a' => 1, 'b' => 2, 'c' => 3], 1, '2')
     * // ['c' => 3]
     * ```
     *
     * @param array $arr
     * @param mixed ...$values
     * @return array
     */
    public static function rejectValues(array $arr, ...$values): array
    {
        return array_filter($arr, fn($value) => !in_array($value, $values));
    }

    /**
     * Оставить только значения массива.
     *
     * ```
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->values()
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->values()->toArray()
     * Func::values(['a' => 1, 'b' => 2, 'c' => 3])
     * // [1,2,3]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function values(array $arr): array
    {
        return array_values($arr);
    }

    /**
     * Получить значения массива. Для вложенных элементов работает аналогично `values`
     *
     * ```
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->values->getList();
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->values->getList();
     * Func::valuesGetList(['a' => 1, 'b' => 2, 'c' => 3]);
     * // [1,2,3]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function valuesGetList(array $arr): array
    {
        return array_values($arr);
    }

    /**
     * Элементы массива в обратном порядке.
     *
     * Если `$preserveNumericKeys` установлено в true, то числовые ключи будут сохранены. Нечисловые ключи не подвержены этой опции и всегда сохраняются.
     *
     * ```
     * ChainFunc::from([1,2,3])->reverse()
     * Chain::from([1,2,3])->reverse()->toArray()
     * Func::reverse([1,2,3])
     * // [3,2,1]
     *
     * Func::reverse([1,2,3], true)
     * [2 => 3, 1 => 2, 0 => 1]
     * ```
     *
     * @param array $arr
     * @param bool $preserveNumericKeys
     * @return array
     */
    public static function reverse(array $arr, bool $preserveNumericKeys = false): array
    {
        return array_reverse($arr, $preserveNumericKeys);
    }


    /**
     * Проверка на пустой массив.
     *
     * ```
     * ChainFunc::from([])->isEmpty()
     * Chain::from([])->isEmpty()
     * Func::isEmpty([])
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *     'a' => [
     *       'a.a' => []
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->empty()->toArray();
     * ChainFunc::from($arr)->elems->elems->is->empty();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @return bool
     */
    public static function isEmpty(array $arr): bool
    {
        return empty($arr);
    }

    /**
     * Проверка на непустой массив.
     *
     * ```
     * ChainFunc::from([])->isNotEmpty()
     * Chain::from([])->isNotEmpty()
     * Func::isNotEmpty([])
     * // true
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *     'a' => [
     *       'a.a' => []
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->notEmpty()->toArray();
     * ChainFunc::from($arr)->elems->elems->is->notEmpty();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @return bool
     */
    public static function isNotEmpty(array $arr): bool
    {
        return !empty($arr);
    }

    /**
     * Проверка "все элементы удовлетворяют условию". Вернёт true, если для каждого элемента функция callback вернёт true.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Chain::from($arr)->is->every(fn(int $item) => $item > 0);
     * ChainFunc::from($arr)->is->every(fn(int $item) => $item > 0);
     * Func::isEvery($arr, fn(int $item) => $item > 0);
     * // true
     *
     * Func::isEvery($arr, fn(int $item) => $item > 1);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1,2,3]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0)->toArray();
     * ChainFunc::from($arr)->elems->elems->is->every(fn(int $item) => $item > 0);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @param callable $callback
     * @return bool
     */
    public static function isEvery(array $arr, callable $callback): bool
    {
        foreach ($arr as $item) {
            if (!$callback($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверка "все элементы не удовлетворяют условию". Вернёт true, если для каждого элемента функция callback вернёт false.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Chain::from($arr)->is->none(fn(int $item) => $item < 0);
     * ChainFunc::from($arr)->is->none(fn(int $item) => $item < 0);
     * Func::isNone($arr, fn(int $item) => $item < 0);
     * // true
     *
     * Func::isNone($arr, fn(int $item) => $item > 2);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1,2,3]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->none(fn(int $item) => $item < 0)->toArray();
     * ChainFunc::from($arr)->elems->elems->is->none(fn(int $item) => $item < 0);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @param callable $callback
     * @return bool
     */
    public static function isNone(array $arr, callable $callback): bool
    {
        foreach ($arr as $item) {
            if ($callback($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Проверка "хотя бы один элемент удовлетворяют условию". Вернёт true, если хотя бы для одного элемента функция callback вернёт true.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Chain::from($arr)->is->any(fn(int $item) => $item >= 3);
     * ChainFunc::from($arr)->is->any(fn(int $item) => $item >= 3);
     * Func::isAny($arr, fn(int $item) => $item >= 3);
     * // true
     *
     * Func::isAny($arr, fn(int $item) => $item > 10);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1,2,3]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->any(fn(int $item) => $item > 1)->toArray();
     * ChainFunc::from($arr)->elems->elems->is->any(fn(int $item) => $item > 1);
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @param callable $callback
     * @return bool
     */
    public static function isAny(array $arr, callable $callback): bool
    {
        foreach ($arr as $item) {
            if ($callback($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверяет, является ли массив списком.
     *
     * ```
     * $arr = [0 => 1, 1 => 2, 2 => 3];
     *
     * Chain::from($arr)->is->list();
     * ChainFunc::from($arr)->is->list();
     * Func::isList($arr);
     * // true
     *
     * $arr = [10 => 1, 11 => 2, 12 => 3]
     *
     * Chain::from($arr)->is->list();
     * ChainFunc::from($arr)->is->list();
     * Func::isList($arr);
     * // false
     * ```
     * Дочерние элементы:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [0 => 1, 1 => 2, 2 => 3]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->is->list()->toArray();
     * ChainFunc::from($arr)->elems->elems->is->list();
     * // [
     * //   'a' => [
     * //     'a.a' => true
     * //   ]
     * // ]
     * ```
     * @param array $arr
     * @return bool
     * @link https://www.php.net/manual/ru/function.array-is-list.php php.net - array_is_list
     */
    public static function isList(array $arr): bool
    {
        return array_is_list($arr);
    }

    /**
     * Проверки, есть ли хотя бы одно из переданных значений в массиве. Используется нестрогое сравнение `==`.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Chain::from($arr)->is->hasValue(3, 4);
     * ChainFunc::from($arr)->is->hasValue(3, 4);
     * Func::isHasValue($arr, 3, 4)
     * // true
     * ```
     * @param array $arr
     * @param mixed ...$values
     * @return bool
     */
    public static function isHasValue(array $arr, ...$values): bool
    {
        foreach ($arr as $item) {
            if (in_array($item, $values)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка, что значение поля `field` равно хотя бы одному из переданных значений `values`.
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->is->fieldHasValue('a', 1, 10);
     * ChainFunc::from($arr)->is->fieldHasValue('a', 1, 10);
     * Func::isFieldHasValue($arr, 'a', 1, 10);
     * // true
     * ```
     * @param array $arr
     * @param int|string $field
     * @param mixed ...$values
     * @return bool
     */
    public static function isFieldHasValue(array $arr, $field, ...$values): bool
    {
        if (in_array($arr[$field], $values)) {
            return true;
        }

        return false;
    }

    /**
     * Проверка, присутствует ли в массиве хотя бы один ключ из `keys`.
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->is->hasKey('a', 'd');
     * ChainFunc::from($arr)->is->hasKey('a', 'd');
     * Func::isHasKey($arr, 'a', 'd');
     * // true
     * ```
     * @param array $arr
     * @param int|string ...$keys
     * @return bool
     */
    public static function isHasKey(array $arr, ...$keys): bool
    {
        foreach (array_keys($arr) as $item) {
            if (in_array($item, $keys)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Удалить повторяющиеся значения. Ключи сохраняются.
     *
     * ```
     * ChainFunc::from([1,1,2])->unique()
     * Chain::from([1,1,2])->unique()->toArray()
     * Func::unique([1,1,2])
     * // [0 => 1, 2 => 2]
     * ```
     *
     * @param array $arr
     * @return array
     * @link https://www.php.net/manual/ru/function.array-unique.php php.net - array_unique
     */
    public static function unique(array $arr): array
    {
        return array_unique($arr);
    }

    /**
     * Удалить повторяющиеся элементы, на основе возвращаемых функцией `$callback` значений. Ключи сохраняются.
     *
     * ```
     * $first = new stdClass();
     * $first->value = 1;
     *
     * $second = new stdClass();
     * $second->value = 2;
     *
     * $third = new stdClass();
     * $third->value = 1;
     *
     * $fourth = new stdClass();
     * $fourth->value = 3;
     *
     * $arr = ['a' => $first, 'b' => $second, 'c' => $third, 'd' => $fourth];
     *
     * Chain::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value)->toArray();
     * ChainFunc::from($arr)->unique->by(fn(stdClass $item, string $key) => $item->value);
     * Func::uniqueBy($arr, fn(stdClass $item, string $key) => $item->value);
     * // ['a' => $first, 'b' => $second, 'd' => $fourth],
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function uniqueBy(array $arr, callable $callback): array
    {
        $existing = [];
        $res = [];
        foreach ($arr as $key => $item) {
            $val = $callback($item, $key);
            if (isset($existing[$val])) {
                continue;
            }

            $existing[$val] = true;
            $res[$key] = $item;
        }

        return $res;
    }

    /**
     * Параметры callback функции - `$result`, `$element`, `$key`.
     *
     * ```
     * ChainFunc::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * Chain::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * Func::reduce([1, 2, 3], fn(int $res, int $item) => $res + $item, 0)
     * // 6
     *
     * Func::reduce(['a' => 1, 'b' => 2], fn(array $res, int $item, string $key) => [...$res, $key, $item])
     * // [ 'a', 1, 'b', 2]
     * ```
     *
     * Для Chain, если аргумент `$startVal` имеет тип `array`, то `Chain->reduce` вернёт `Chain` и цепочку можно продолжить. В ином случае вернётся само значение.
     * ```
     * Chain::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * // 0
     * Chain::from([1, 2, 3])->reduce(fn(int $res, int $item) => [...$res, $item])->toArray()
     * // [1, 2, 3]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @param mixed $startVal
     * @return array|mixed
     */
    public static function reduce(array $arr, callable $callback, $startVal = [])
    {
        $res = $startVal;

        foreach ($arr as $key => $item) {
            $res = $callback($res, $item, $key);
        }

        return $res;
    }

    /**
     * Пройти по массиву и выполнить функцию. Не предназначен для изменения массива, только для сайд-эффектов.
     *
     * ```
     * ChainFunc::from([1,2,3])->each(fn(int $item, string $key) => echo $key . $item)
     * Chain::from([1,2,3])->each(fn(int $item, string $key) => echo $key . $item)->toArray()
     * Func::each([1,2,3], fn(int $item, string $key) => echo $key . $item)
     * // [1,2,3]
     * ```
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function each(array $arr, callable $callback): array
    {
        foreach ($arr as $key => $item) {
            $callback($item, $key);
        }

        return $arr;
    }

    /**
     * Получить количество элементов.
     *
     * ```
     * ChainFunc::from([1,2,3])->count()
     * Chain::from([1,2,3])->count()
     * Func::count([1,2,3])
     * // 3
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a.a' => [
     *     'a.a.a' => [1, 2, 3],
     *     'a.a.b' => [1, 2]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->count()->toArray();
     * ChainFunc::from($arr)->elems->elems->count();
     * // $arr = [
     * //  'a.a' => [
     * //    'a.a.a' => 3
     * //    'a.a.b' => 2
     * //  ]
     * // ];
     * ```
     *
     * @param array $arr
     * @return int
     */
    public static function count(array $arr): int
    {
        return count($arr);
    }

    /**
     * Кодировать в json поля с перечисленными ключами. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * ChainFunc::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeFields('a', 'b')
     * Chain::from(['a'=>['f'=>1],'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeFields('a', 'b')->toArray()
     * Func::jsonEncodeFields(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]], 'a', 'b')
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     */
    public static function jsonEncodeFields(array $arr, ...$keys): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $res[$key] = in_array($key, $keys)
                ? json_encode($item, JSON_UNESCAPED_UNICODE)
                : $item;
        }

        return $res;
    }

    /**
     * Кодировать в json поля, для который `$callback` вернул `true`. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * ChainFunc::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeBy(fn(string $item, string $key) => $item === ['f' => 1] || $key === 'b')
     * Chain::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeBy(fn(string $item, string $key) => $item === ['f' => 1] || $key === 'b')->toArray()
     * Func::jsonEncodeBy(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]], fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b')
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function jsonEncodeBy(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $res[$key] = $callback($item, $key)
                ? json_encode($item, JSON_UNESCAPED_UNICODE)
                : $item;
        }

        return $res;
    }

    /**
     * Декодировать из json поля с перечисленными ключами.
     *
     * ```
     * ChainFunc::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeFields('a', 'b')
     * Chain::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeFields('a', 'b')->toArray()
     * Func::jsonDecodeFields(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'], 'a', 'b')
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param array $arr
     * @param ...$keys
     * @return array
     */
    public static function jsonDecodeFields(array $arr, ...$keys): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $res[$key] = in_array($key, $keys)
                ? json_decode($item, true)
                : $item;
        }

        return $res;
    }

    /**
     * Декодировать из json поля, для которых `$callback` вернул `true`.
     *
     * ```
     *  ChainFunc::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')
     *  Chain::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')->toArray()
     *  Func::jsonDecodeBy(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'], fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')
     *  // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function jsonDecodeBy(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $res[$key] = $callback($item, $key)
                ? json_decode($item, true)
                : $item;
        }

        return $res;
    }


    /**
     * Добавить элементы в конец массива.
     *
     * ```
     * ChainFunc::from([1, 2])->append(3, 4)
     * Chain::from([1,2])->append(3, 4)->toArray()
     * Func::append([1,2], 3, 4)
     * // [1, 2, 3, 4]
     * ```
     *
     * @param array $arr
     * @param array|mixed ...$items
     * @return array
     */
    public static function append(array $arr, ...$items): array
    {
        return [...$arr, ...$items];
    }

    /**
     * Добавить элементы в конец массива. Если элемент итерируемый - то будет выполнено слияние. Неитерируемые элементы будут добавлены как есть.
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->append->merge(3, [4, 5, [6, 7]])->toArray();
     * ChainFunc::from($arr)->append->merge(3, [4, 5, [6, 7]]);
     * Func::appendMerge($arr, 3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param ...$items
     * @return array
     */
    public static function appendMerge(array $arr, ...$items): array
    {
        $res = [];
        foreach ($items as $item) {
            if (is_iterable($item)) {
                $res = [...$res, ...$item];
            } else {
                $res[] = $item;
            }
        }

        return [...$arr, ...$res];
    }

    /**
     * Декодировать json и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->append->mergeFromJson('[3,4,5,[6,7]]')->toArray();
     * ChainFunc::from($arr)->append->mergeFromJson('[3,4,5,[6,7]]');
     * Func::appendMergeFromJson($arr, '[3,4,5,[6,7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param string $json
     * @return array
     */
    public static function appendMergeFromJson(array $arr, string $json): array
    {
        $array = json_decode($json, true);

        return static::appendMerge($arr, $array);
    }

    /**
     * Конвертировать строку в массив и добавить в конец массива (с распаковкой итерируемых элементов)
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->append->mergeFromString('3,4,5', ',')->toArray();
     * ChainFunc::from($arr)->append->mergeFromString('3,4,5', ',');
     * Func::appendMergeFromJson($arr, '3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     * @param array $arr
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public static function appendMergeFromString(array $arr, string $str, string $delimiter): array
    {
        $array = explode($delimiter, $str);

        return static::appendMerge($arr, $array);
    }


    /**
     * Добавить элементы в начало массива.
     *
     * ```
     * ChainFunc::from([3, 4])->prepend(1,2)
     * Chain::from([3, 4])->prepend(1,2)->toArray()
     * Func::prepend([3, 4], 1, 2)
     * // [1, 2, 3, 4]
     * ```
     *
     * @param array $arr
     * @param array|mixed ...$items
     * @return array
     */
    public static function prepend(array $arr, ...$items): array
    {
        return [...$items, ...$arr];
    }

    /**
     * Добавить элементы в начало массива. Если элемент итерируемый - то будет выполнено слияние. Неитерируемые элементы будут добавлены как есть.
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->prepend->merge(3, [4, 5, [6, 7]])->toArray();
     * ChainFunc::from($arr)->prepend->merge(3, [4, 5, [6, 7]]);
     * Func::prependMerge($arr, 3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param ...$items
     * @return array
     */
    public static function prependMerge(array $arr, ...$items): array
    {
        $res = [];
        foreach ($items as $item) {
            if (is_iterable($item)) {
                $res = [...$res, ...$item];
            } else {
                $res[] = $item;
            }
        }

        return [...$res, ...$arr];
    }

    /**
     * Декодировать json и добавить в начало массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]')->toArray();
     * ChainFunc::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]');
     * Func::prependMergeFromJson($arr, '[3,4,5,[6,7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param string $json
     * @return array
     */
    public static function prependMergeFromJson(array $arr, string $json): array
    {
        $array = json_decode($json, true);

        return static::prependMerge($arr, $array);
    }

    /**
     * Конвертировать строку в массив и добавить в начало массива (с распаковкой итерируемых элементов)
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->prepend->mergeFromString('3,4,5', ',')->toArray();
     * ChainFunc::from($arr)->prepend->mergeFromString('3,4,5', ',');
     * Func::prependMergeFromJson($arr, '3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     * @param array $arr
     * @param string $str
     * @param string $delimiter
     * @return array
     */
    public static function prependMergeFromString(array $arr, string $str, string $delimiter): array
    {
        $array = explode($delimiter, $str);

        return static::prependMerge($arr, $array);
    }


    /**
     * Оставить элементы в массиве, для которых $callback вернёт true. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`
     *
     * ```
     * ChainFunc::from([1,2,3])->filter(fn(int $item) => $item > 2)
     * Chain::from([1,2,3])->filter(fn(int $item) => $item > 2)->toArray()
     * Func::filter([1,2,3], fn(int $item) => $item > 2)
     * // [2 => 3]
     *
     * Func::filter(['a' => 1, 'b' => 2, 'c' => 3], fn(int $item, string $key) => $item > 1 && $key !== 'b' )
     * // ['c' => 3]
     * ````
     *
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function filter(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            if ($callback($item, $key)) {
                $res[$key] = $item;
            }
        }

        return $res;
    }

    /**
     * Оставить только элементы коллекции с указанными ключами. Используется нестрогое сравнение `==`.
     *
     * ```
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->keys('a', 'b')
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->keys('a', 'b')->toArray()
     * Func::filterKeys(['a' => 1, 'b' => 2, 'c' => 3], 'a', 'b')
     * // ['a' => 1, 'b' => 2]
     * ```
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     */
    public static function filterKeys(array $arr, ...$keys): array
    {
        return array_filter($arr, fn($key) => in_array($key, $keys), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Оставить только элементы из коллекции с указанными значениями. Используется нестрогое сравнение `==`. Ключи сохраняются.
     *
     * ```
     * ChainFunc::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->values(1, '2')
     * Chain::from(['a' => 1, 'b' => 2, 'c' => 3])->filter->values(1, '2')->toArray()
     * Func::filterValues(['a' => 1, 'b' => 2, 'c' => 3], 1, '2')
     * // ['a' => 1, 'b' => 2]
     * ```
     *
     * @param array $arr
     * @param mixed ...$values
     * @return array
     */
    public static function filterValues(array $arr, ...$values): array
    {
        return array_filter($arr, fn($value) => in_array($value, $values));
    }

    /**
     * Преобразовать стиль ключей к "camelCase".
     *
     * ```
     * $arr = ['var_first' => 1, 'var_second' => 2];
     *
     * Chain::from($arr)->keys->case->toCamel()->toArray()
     * ChainFunc::from($arr)->keys->case->toCamel()
     * Func::keysCaseToCamel($arr)
     * // ['varFirst' => 1, 'varSecond' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToCamel(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toCamel($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }


    /**
     * Преобразовать стиль ключей к "PaskalCase".
     *
     * ```
     * $arr = ['var_first' => 1, 'var_second' => 2];
     *
     * Chain::from($arr)->keys->case->toPaskal()->toArray()
     * ChainFunc::from($arr)->keys->case->toPaskal()
     * Func::keysCaseToPaskal($arr)
     * // ['VarFirst' => 1, 'VarSecond' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToPaskal(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toPaskal($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }

    /**
     * Преобразовать стиль ключей к "snake_case".
     *
     * ```
     * $arr = ['varFirst' => 1, 'varSecond' => 2];
     *
     * Chain::from($arr)->keys->case->toSnake()->toArray();
     * ChainFunc::from($arr)->keys->case->toSnake();
     * Func::keysCaseToSnake($arr);
     * // ['var_first' => 1, 'var_second' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToSnake(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toSnake($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }

    /**
     * Преобразовать стиль ключей к "kebab-case".
     *
     * ```
     * $arr = ['varFirst' => 1, 'varSecond' => 2];
     *
     * Chain::from($arr)->keys->case->toKebab()->toArray();
     * ChainFunc::from($arr)->keys->case->toKebab();
     * Func::keysCaseToKebab($arr);
     * // ['var-first' => 1, 'var-second' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToKebab(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toKebab($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }

    /**
     * Преобразовать стиль ключей к "SCREAM_SNAKE_CASE".
     *
     * ```
     * $arr = ['varFirst' => 1, 'varSecond' => 2];
     *
     * Chain::from($arr)->keys->case->toScreamSnake()->toArray();
     * ChainFunc::from($arr)->keys->case->toScreamSnake();
     * Func::keysCaseToScreamSnake($arr);
     * // ['VAR_FIRST' => 1, 'VAR_SECOND' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToScreamSnake(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toScreamSnake($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }

    /**
     * Преобразовать стиль ключей к "SCREAM-KEBAB-CASE".
     *
     * ```
     * $arr = ['varFirst' => 1, 'varSecond' => 2];
     *
     * Chain::from($arr)->keys->case->toScreamKebab()->toArray();
     * ChainFunc::from($arr)->keys->case->toScreamKebab();
     * Func::keysCaseToScreamKebab($arr);
     * // ['VAR-FIRST' => 1, 'VAR-SECOND' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     */
    public static function keysCaseToScreamKebab(array $arr): array
    {
        $res = [];
        foreach ($arr as $key => $item) {
            $keyMod = CaseKey::toScreamKebab($key);
            $res[$keyMod] = $item;
        }

        return $res;
    }

    /**
     * Найти первое значение, для которого callback функция вернёт `true`.
     *
     * Параметры callback функции - `$element`, `$key`.
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->find(fn(int $item, string $key) => $item == 1);
     * ChainFunc::from($arr)->find(fn(int $item, string $key) => $item == 1);
     * Func::find($arr, fn(int $item, string $key) => $item == 1);
     * // 1
     *
     * Func::find($arr, fn(int $item, string $key) => $key == 'a');
     * // 1
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [
     *       'a.a.a' => 1,
     *       'a.a.b' => 2
     *     ],
     *   'b' => [5, 6]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->find(fn(int $item) => $item == 1);
     * ChainFunc::from($arr)->elems->elems->find(fn(int $item) => $item == 1);
     * // [
     * //   'a' => [
     * //     'a.a' => 1
     * //   ]
     * //   'b' => null
     * // ]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return mixed
     */
    public static function find(array $arr, callable $callback)
    {
        foreach ($arr as $key => $item) {
            if ($callback($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Сгруппировать элементы на основе значений, которые вернёт callback функция.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * $arr = [1,2,3,4,5];
     *
     * Chain::from($arr)->group(fn(int $item) => $item > 3 ? 'more' : 'less')->toArray();
     * ChainFunc::from($arr)->group(fn(int $item) => $item > 3 ? 'more' : 'less');
     * Func::group($arr, fn(int $item) => $item > 3 ? 'more' : 'less')
     * // [
     * //   'less' => [1,2,3],
     * //   'more' => [4,5]
     * // ]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function group(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $keyTarget = $callback($item, $key);
            $res[$keyTarget] ??= [];
            $res[$keyTarget][] = $item;
        }

        return $res;
    }

    /**
     * Сгруппировать элементы на основе значений поля $field. Если указанного поля в элементе нет, то он попадёт в группу с пустым ключом `''`.
     *
     * ```
     * $arr = [
     *   ['a' => 1],
     *   ['a' => 1],
     *   ['a' => 3],
     * ];
     *
     * Chain::from($arr)->group->byField('a')->toArray();
     * ChainFunc::from($arr)->group->byField('a');
     * Func::groupByField($arr, 'a');
     * // [
     * //    1 => [
     * //      ['a' => 1],
     * //      ['a' => 1],
     * //    ],
     * //    3 => [
     * //      ['a' => 3],
     * //    ],
     * //  ],
     * ```
     * Отсутствующее поле:
     * ```
     * $arr = [
     *   ['a' => 1],
     *   ['a' => 1],
     *   ['b' => 2],
     * ];
     *
     * Func::groupByField($arr, 'a')
     * // [
     * //    1 => [
     * //      0 => ['a' => 1],
     * //      1 => ['a' => 1],
     * //    ],
     * //    '' => [
     * //      0 => ['b' => 2],
     * //    ],
     * //  ],
     * ```
     *
     * @param array $arr
     * @param string|int $field
     * @return array
     */
    public static function groupByField(array $arr, $field): array
    {
        $res = [];

        foreach ($arr as $item) {
            $keyTarget = $item[$field] ?? '';
            $res[$keyTarget] ??= [];
            $res[$keyTarget][] = $item;
        }

        return $res;
    }

    /**
     * Сгруппировать элементы на основе значений, которые вернёт callback функция, и привести к структуре
     *
     * `['key' => ..., 'items' => [...]]`.
     *
     * Актуально, если возвращаемое значение не является валидным ключом массива (например, объекты). Сравнение ключей производится через сериализацию (метод `serialize`).
     *
     * ```
     * $first = new stdClass();
     * $first->value = 1;
     *
     * $second = new stdClass();
     * $second->value = 2;
     *
     * $third = new stdClass();
     * $third->value = 1;
     *
     * $fourth = new stdClass();
     * $fourth->value = 3;
     *
     * $arr = [$first, $second, $third, $fourth];
     *
     * Chain::from($arr)->group->toStruct(fn($item) => $item)->toArray();
     * ChainFunc::from($arr)->group->toStruct(fn($item) => $item);
     * Func::groupToStruct($arr);
     * // [
     * //   ['key' => $first, 'items' => [$first, $third]],
     * //   ['key' => $second, 'items' => [$second]],
     * //   ['key' => $fourth, 'items' => [$fourth]],
     * // ],
     * ```
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function groupToStruct(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $keyEntity = $callback($item, $key);
            $keyString = serialize($keyEntity);
            $res[$keyString] ??= ['key' => $keyEntity, 'items' => []];
            $res[$keyString]['items'][] = $item;
        }

        return array_values($res);
    }

    /**
     * Заменить массив списком ключей.
     *
     * ```
     * $arr = ['a' =>1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->keys()->toArray();
     * ChainFunc::from($arr)->keys();
     * Func::keys($arr);
     * // ['a', 'b', 'c']
     * ```
     * @param array $arr
     * @return array
     */
    public static function keys(array $arr): array
    {
        return array_keys($arr);
    }

    /**
     * Получить список ключей.
     *
     * ```
     * $arr = ['a' =>1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->keys->getList();
     * ChainFunc::from($arr)->keys->getList();
     * Func::keysGetList($arr);
     * // ['a', 'b', 'c']
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [
     *       'a.a.a' => 1,
     *       'a.a.b' => 2,
     *     ]
     *   ]
     * ];
     * Chain::from($arr)->elems->elems->keys->getList()->toArray();
     * ChainFunc::from($arr)->elems->elems->keys->getList();
     * // [
     * //   'a' => [
     * //     'a.a' => [
     * //       'a.a.a',
     * //       'a.a.b',
     * //    ]
     * //   ]
     * // ],
     * ```
     * @param array $arr
     * @return array
     */
    public static function keysGetList(array $arr): array
    {
        return array_keys($arr);
    }

    /**
     * Изменить значения ключей. Повторяющиеся значения будут молча перезаписаны.
     *
     * Параметры callback функции - `$key`, `$element`
     *
     * ```
     * $arr = ['a' =>1, 'b' => 2, 'c' => 3];
     * Chain::from($arr)->keys->map(fn(string $key, int $item) => $key . $item)->toArray();
     * ChainFunc::from($arr)->keys->map(fn(string $key, int $item) => $key . $item);
     * Func::keysMap($arr, fn(string $key, int $item) => $key . $item);
     * // ['a1' =>1, 'b2' => 2, 'c3' => 3];
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function keysMap(array $arr, callable $callback): array
    {
        $res = [];

        foreach ($arr as $key => $item) {
            $keyTarget = $callback($key, $item);
            $res[$keyTarget] = $item;
        }

        return $res;
    }

    /**
     * Заполнить ключи из значений поля. Повторяющиеся значения будут молча перезаписаны.
     *
     * ```
     * $arr = [
     *  ['id' => 10, 'val' => 'a'],
     *  ['id' => 20, 'val' => 'b'],
     * ]
     *
     * Chain::from($arr)->keys->fromField('id')->toArray();
     * ChainFunc::from($arr)->keys->fromField('id');
     * Func::keysFromField($arr, 'id');
     * // $arr = [
     * //   10 => ['id' => 10, 'val' => 'a'],
     * //   20 => ['id' => 20, 'val' => 'b'],
     * // ]
     * ```
     *
     * @param array $arr
     * @param string|int $field
     * @return array
     */
    public static function keysFromField(array $arr, $field): array
    {
        $res = [];

        foreach ($arr as $item) {
            $res[$item[$field]] = $item;
        }

        return $res;
    }

    /**
     * Получить ключ по номеру в массиве. Нумерация начинается с 0.
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->keys->get(1);
     * ChainFunc::from($arr)->keys->get(1);
     * Func::keysGet($arr, 1);
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
     * Chain::from($arr)->elems->elems->keys->get(1)->toArray();
     * ChainFunc::from($arr)->elems->elems->keys->get(1);
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     * @param array $arr
     * @param int $number
     * @return int|string|null
     */
    public static function keysGet(array $arr, int $number)
    {
        $keys = array_keys($arr);

        return $keys[$number] ?? null;
    }

    /**
     * Получить первый ключ массива.
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->keys->getFirst();
     * ChainFunc::from($arr)->keys->getFirst();
     * Func::keysGetFirst($arr);
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
     * Chain::from($arr)->elems->elems->keys->getFirst->toArray();
     * ChainFunc::from($arr)->elems->elems->keys->getFirst();
     * // [
     * //   'a' => [
     * //     'a.a' => 'a',
     * //     'a.b' => 'a',
     * //   ]
     * // ],
     * ```
     * @link https://www.php.net/manual/ru/function.array-key-first.php php.net - array_key_first
     * @param array $arr
     * @return int|string|null
     */
    public static function keysGetFirst(array $arr)
    {
        return array_key_first($arr);
    }


    /**
     * Получить последний ключ массива.
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2];
     *
     * Chain::from($arr)->keys->getLast();
     * ChainFunc::from($arr)->keys->getLast();
     * Func::keysGetLast($arr);
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
     * Chain::from($arr)->elems->elems->keys->getLast->toArray();
     * ChainFunc::from($arr)->elems->elems->keys->getLast();
     * // [
     * //   'a' => [
     * //     'a.a' => 'b',
     * //     'a.b' => 'b',
     * //   ]
     * // ],
     * ```
     * @link https://www.php.net/manual/ru/function.array-key-last.php php.net - array_key_last
     * @param array $arr
     * @return int|string|null
     */
    public static function keysGetLast(array $arr)
    {
        return array_key_last($arr);
    }


    /**
     * Сортировка массива. Используется функция usort.
     *
     * ```
     * $arr = [3,1,2];
     *
     * Chain::from($arr)->sort(fn(int $a, int $b) => $a <=> $b)->toArray();
     * ChainFunc::from($arr)->sort(fn(int $a, int $b) => $a <=> $b);
     * Func::sort($arr, fn(int $a, int $b) => $a <=> $b));
     * // [1,2,3]
     * ```
     *
     * Сортировка строк:
     * ```
     * $arr = [1, 3, 11, 2];
     *
     * Func::sort($arr, fn(int $a, int $b) => strcmp($a, $b));
     * // [1, 11, 2, 3]
     *
     * Func::sort($arr, fn(int $a, int $b) => strnatcmp($a, $b));
     * // [1, 2, 3, 11]
     * ```
     *
     * ### Компараторы для строк:
     * - `strcasecmp` — сравнивает строки без учёта регистра в бинарно безопасном режиме, [подробнее](https://www.php.net/manual/ru/function.strcasecmp.php)
     * - `strcmp` — сравнивает строки в бинарно-безопасном режиме: как последовательности байтов [подробнее](https://www.php.net/manual/ru/function.strcmp.php)
     * - `strnatcasecmp` — сравнивает строки без учёта регистра по алгоритму natural order [подробнее](https://www.php.net/manual/ru/function.strnatcasecmp.php)
     * - `strnatcmp` — сравнивает строк алгоритмом natural order [подробнее](https://www.php.net/manual/ru/function.strnatcmp.php)
     * - `strncasecmp` — сравнивает первые n символов строк без учёта регистра в бинарно-безопасном режиме [подробнее](https://www.php.net/manual/ru/function.strncasecmp.php)
     * - `strncmp` — сравнивает первые n символов строк в бинарно безопасном режиме [подробнее](https://www.php.net/manual/ru/function.strncmp.php)
     *
     * @link https://www.php.net/manual/ru/function.usort.php функция usort
     * @link https://www.php.net/manual/ru/ref.strings.php методы строк (в том числе компараторы)
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function sort(array $arr, callable $callback): array
    {
        usort($arr, $callback);

        return $arr;
    }

    /**
     * Очистить массив.
     *
     * ```
     * Chain::from([1,2,3])->clear()->toArray()
     * ChainFunc::from([1,2,3])->clear()->toArray()
     * Func::clear([1,2,3])
     * // []
     *
     * $arr = [
     *  'a' => [1,2],
     *  'b' => [3,4]
     * ]
     * ChainFunc::from($arr)->elems->clear()
     * // [
     * //   'a' => [],
     * //   'b' => []
     * // ]
     * ```
     *
     *
     * @param array $arr
     * @return array
     */
    public static function clear(array $arr): array
    {
        return [];
    }

    /**
     * Разбивает массив на массивы с заданным в параметре `size` количеством элементов. Количество элементов в последней части будет равняться или окажется меньше заданной длины.
     *
     * Если аргумент `isPreserveKeys` равен `true`, ключи оригинального массива будут сохранены. По умолчанию - `false`, что переиндексирует части числовыми ключами. Если массив не лист - то ключи сохраняются всегда.
     *
     * ```
     * $arr = [1, 2, 3, 4, 5];
     *
     * Chain::from($arr)->chunk->bySize($arr, 2)->toArray();
     * ChainFunc::from($arr)->chunk->bySize($arr, 2);
     * Func::chunkBySize($arr, 2);
     * // [
     * //   [1, 2],
     * //   [3, 4],
     * //   [5]
     * // ];
     *
     * Func::chunkBySize($arr, 2, true);
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ]
     * ```
     *
     * @param array $arr
     * @param int $size
     * @param bool $isPreserveKeys
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-chunk.php php.net - array_chunk
     */
    public static function chunkBySize(array $arr, int $size, bool $isPreserveKeys = false): array
    {
        return array_chunk($arr, $size, $isPreserveKeys);
    }

    /**
     * Разбивает массив на заданное в параметре `count` количество массивов. Если `count` больше длины массива, то будут дописаны пустые массивы.
     *
     * Если аргумент `isPreserveKeys` равен `true`, ключи оригинального массива будут сохранены. По умолчанию - `false`, что переиндексирует части числовыми ключами. Если массив не лист - то ключи сохраняются всегда.
     *
     * ```
     * $arr = [1, 2, 3, 4, 5];
     *
     * Chain::from($arr)->chunk->byCount(3)->toArray();
     * ChainFunc::from($arr)->chunk->byCount(3);
     * Func::chunkByCount($arr, 3);
     * // [
     * //  [1, 2],
     * //  [3, 4],
     * //  [5]
     * // ]
     *
     * Func::chunkByCount($arr, 3, true);
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ],
     * ```
     * @param array $arr
     * @param int $count
     * @param bool $isPreserveKeys
     * @return array
     */
    public static function chunkByCount(array $arr, int $count, bool $isPreserveKeys = false): array
    {
        if ($count < 1) {
            throw new Error("Количество частей не может быть меньше 1");
        }

        $res = [];
        $target = $arr;
        $size = ceil(count($arr) / $count);
        for ($i = 0; $i < $count; $i++) {
            $tailChunksCount = $count - $i;
            $tailElemsCount = count($target);
            $ratio = $tailElemsCount / $tailChunksCount;

            if ($ratio <= 1 && $size > 1) {
                $size--;
            }

            $chunk = array_slice($target, 0, $size, $isPreserveKeys);
            $target = array_slice($target, $size, null, $isPreserveKeys);
            $res[] = $chunk;
        }

        return $res;
    }

    /**
     * Меняет местами ключи с их значениями в массиве. Повторяющиеся ключи будут молча перезаписаны. Если значение не является корректным ключом (`string` или `int`), будет выдано предупреждение и данная пара ключ/значение не будет включена в результат.
     *
     * ```
     * $arr = ['a' => 10, 'b' => 20, 'c' => 30];
     *
     * Chain::from($arr)->flip()->toArray();
     * ChainFunc::from($arr)->flip();
     * Func::flip($arr);
     *
     * // ['10' => 'a', '20' => 'b', '30' => 'c'];
     * ```
     * @link https://www.php.net/manual/ru/function.array-flip.php php.net - array_flip
     * @param array $arr
     * @return array
     */
    public static function flip(array $arr): array
    {
        return array_flip($arr);
    }

    /**
     * Извлекает и возвращает первое значение массива array, сокращает массив array на один элемент и сдвигает остальные элементы в начало. Числовые ключи массива изменятся так, чтобы нумерация начиналась с нуля, тогда как литеральные ключи не изменятся.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым
     *
     * ```
     * $arr = [1, 2, 3];
     * $arrFunc = [1, 2, 3];
     * $ch = Chain::from([1, 2, 3]);
     * $cf = ChainFunc::from([1, 2, 3]);
     *
     * $ch->shift();
     * $cf->shift();
     * Func::shift($arrFunc);
     * // 1
     *
     * $arr;
     * $arrFunc
     * $ch->toArray();
     * $cf->toArray();
     * // [2, 3]
     * ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3],
     *     'a.b' => [4, 5, 6],
     *   ]
     * ];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->elems->elems->shift();
     * $cf->elems->elems->shift();
     * // [1, 4];
     *
     * $ch->toArray();
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => [2, 3],
     * //     'a.b' => [5, 6],
     * //   ]
     * // ],
     * ```
     * @link https://www.php.net/manual/ru/function.array-shift.php php.net - array_shift
     * @param array $arr
     * @return mixed|null
     */
    public static function shift(array &$arr)
    {
        return array_shift($arr);
    }

    /**
     * Извлекает и возвращает последнее значение массива array, сокращает массив array на один элемент.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым
     *
     * ```
     * $arr = [1, 2, 3];
     * $arrFunc = [1, 2, 3];
     * $ch = Chain::from([1, 2, 3]);
     * $cf = ChainFunc::from([1, 2, 3]);
     *
     * $ch->pop();
     * $cf->pop();
     * Func::pop($arrFunc);
     * // 3
     *
     * $arr;
     * $arrFunc;
     * $ch->toArray();
     * $cf->toArray();
     * // [1, 2]
     * ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3],
     *     'a.b' => [4, 5, 6],
     *   ]
     * ];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->elems->elems->pop();
     * $cf->elems->elems->pop();
     * // [3, 6];
     *
     * $ch->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => [1, 2],
     * //     'a.b' => [4, 5],
     * //   ]
     * // ],
     * ```
     * @link https://www.php.net/manual/ru/function.array-pop.php php.net - array_shift
     * @param array $arr
     * @return mixed|null
     */
    public static function pop(array &$arr)
    {
        return array_pop($arr);
    }

    /**
     * Удаляет часть массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $arrFunc = [1, 2, 3, 4];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->splice(2, 1, 'item');
     * $cf->splice(2, 1, 'item');
     * Func::splice($arrFunc, 2, 1, 'item');
     * // [3]
     *
     * $arr;
     * $arrFunc;
     * $ch->toArray();
     * $cf->toArray();
     * // [1, 2, 'item', 4]
     * ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3, 4],
     *     'a.b' => [5, 6, 7, 8],
     *   ]
     * ];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->elems->elems->splice(2, 1, 'item');
     * $cf->elems->elems->splice(2, 1, 'item');
     * // [
     * //   [3],
     * //   [7],
     * // ]
     *
     * $ch->toArray();
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => [1, 2, 'item', 4],
     * //     'a.b' => [5, 6, 'item', 8],
     * //   ]
     * // ],
     * ```
     * @param array $arr
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return void
     */
    public static function splice(array &$arr, int $offset, ?int $length = null, $replacement = []): array
    {
        return array_splice($arr, $offset, $length, $replacement);
    }

    /**
     * Удаляет часть массива с начала массива и заменяет её новыми элементами.
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $arrFunc = [1, 2, 3, 4];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->splice->head(2, 'item');
     * $cf->splice->head(2, 'item');
     * Func::spliceHead($arrFunc, 2, 1, 'item');
     * // [1, 2]
     *
     * $arr;
     * $arrFunc;
     * $ch->toArray();
     * $cf->toArray();
     * // ['item', 3, 4]
     *  ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3, 4],
     *     'a.b' => [5, 6, 7, 8],
     *   ]
     * ];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->elems->elems->splice->head(2, 'item');
     * $cf->elems->elems->splice->head(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $ch->toArray();
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => ['item', 3, 4],
     * //     'a.b' => ['item', 7, 8],
     * //   ]
     * // ],
     *  ```
     * @param array $arr
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     */
    public static function spliceHead(array &$arr, ?int $length = null, $replacement = []): array
    {
        return array_splice($arr, 0, $length, $replacement);
    }

    /**
     * Удаляет часть массива с конца массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $arrFunc = [1, 2, 3, 4];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->splice->tail(2, 'item');
     * $cf->splice->tail(2, 'item');
     * Func::spliceTail($arrFunc, 2, 1, 'item');
     * // [1, 2]
     *
     * $arr;
     * $arrFunc;
     * $ch->toArray();
     * $cf->toArray();
     * // ['item', 3, 4]
     *  ```
     * Для вложенных элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [1, 2, 3, 4],
     *     'a.b' => [5, 6, 7, 8],
     *   ]
     * ];
     * $ch = Chain::from($arr);
     * $cf = ChainFunc::from($arr);
     *
     * $ch->elems->elems->splice->tail(2, 'item');
     * $cf->elems->elems->splice->tail(2, 'item');
     * // [
     * //   [1, 2],
     * //   [5, 6],
     * // ]
     *
     * $ch->toArray();
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => ['item', 3, 4],
     * //     'a.b' => ['item', 7, 8],
     * //   ]
     * // ],
     *  ```
     * @param array $arr
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     */
    public static function spliceTail(array &$arr, ?int $length = null, $replacement = []): array
    {
        $length = abs($length);

        return array_splice($arr, -$length, null, $replacement);
    }

    /**
     * Выбирает срез массива.
     *
     * Параметр `offset` обозначает положение в массиве, а не ключ.
     * Если параметр `offset` неотрицательный, последовательность начнётся на указанном расстоянии от начала array.
     * Если `offset` отрицательный, последовательность начнётся с конца array.
     *
     * Если в эту функцию передан положительный параметр `length`, последовательность будет включать количество элементов меньшее или равное `length`.
     * Если количество элементов массива меньше чем параметр `length`, то только доступные элементы массива будут присутствовать.
     * Если в эту функцию передан отрицательный параметр `length`, последовательность остановится на указанном расстоянии от конца массива.
     * Если он опущен, последовательность будет содержать все элементы с `offset` до конца массива.
     *
     * Если смещение больше длины массива, то будет возвращён пустой массив.
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Chain::from($arr)->slice(1, 2)->toArray();
     * ChainFunc::from($arr)->slice(1, 2);
     * Func::slice($arr, 1, 2);
     * // [1, 2]
     *
     * Func::slice($arr, 1, 2, true);
     * // [10 => 1, 11 => 2]
     * ```
     * @link https://www.php.net/manual/ru/function.array-slice.php php.net - array_slice
     * @param array $arr
     * @param int $offset
     * @param int|null $length
     * @param bool $isPreserveKeys
     * @return array
     */
    public static function slice(array $arr, int $offset, ?int $length = null, bool $isPreserveKeys = false): array
    {
        return array_slice($arr, $offset, $length, $isPreserveKeys);
    }

    /**
     * Выбирает срез массива - `length` элементов с начала массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Chain::from($arr)->slice->head(2)->toArray();
     * ChainFunc::from($arr)->slice->head(2);
     * Func::sliceHead($arr, 2);
     * // [1, 2]
     *
     * Func::sliceHead($arr, 2, true);
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @param array $arr
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     */
    public static function sliceHead(array $arr, int $length, bool $isPreserveKeys = false): array
    {
        $length = abs($length);

        return array_slice($arr, 0, $length, $isPreserveKeys);
    }

    /**
     * Выбирает срез массива - `length` элементов с конца массива.
     *
     * ```
     * $arr = [10 => 1, 2, 3, 4, 5];
     * Chain::from($arr)->slice->tail(2)->toArray();
     * ChainFunc::from($arr)->slice->tail(2);
     * Func::sliceTail($arr, 2);
     * // [4, 5]
     *
     * Func::sliceTail($arr, 2, true);
     * // [13 => 4, 14 => 5]
     * ```
     *
     * @param array $arr
     * @param int $length
     * @param bool $isPreserveKeys
     * @return array
     */
    public static function sliceTail(array $arr, int $length, bool $isPreserveKeys = false): array
    {
        $length = abs($length);

        return array_slice($arr, -$length, null, $isPreserveKeys);
    }


    /**
     * Заменяет элементы массива элементами других массивов.
     *
     * ```
     * $arr = [1, 2, 3, 4, 5];
     *
     * Chain::from($arr)->replace([6, 7], [4 => 8])->toArray();
     * ChainFunc::from($arr)->replace([6, 7], [4 => 8]);
     * Func::replace($arr, [6, 7], [4 => 8]);
     * // [6, 7, 3, 4, 8];
     * ```
     * @link https://www.php.net/manual/ru/function.array-replace.php php.net - array_replace
     * @param array $array
     * @param array ...$replacements
     * @return array
     */
    public static function replace(array $array, array ...$replacements): array
    {
        return array_replace($array, ...$replacements);
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
     * Chain::from($arr)->replace->recursive($arrReplace1, $arrReplace2)->toArray();
     * ChainFunc::from($arr)->replace->recursive($arrReplace1, $arrReplace2);
     * Func::replace($arr, $arrReplace1, $arrReplace2);
     * // [
     * //  [1, 2, 3],
     * //  [4, 7, 9],
     * // ]
     * ```
     * @link https://www.php.net/manual/ru/function.array-replace-recursive.php - array_replace_recursive
     * @param array $array
     * @param array ...$replacements
     * @return array
     */
    public static function replaceRecursive(array $array, array ...$replacements): array
    {
        return array_replace_recursive($array, ...$replacements);
    }

    /**
     * Уменьшить вложенность массива на величину `depth`.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Chain::from($arr)->flatten()->toArray();
     * ChainFunc::from($arr)->flatten();
     * Func::flatten($arr);
     * // [1, 2, 3, [4, [5]]];
     *
     * Func::flatten($arr, 2);
     * // [1, 2, 3, 4, [5]];
     * ```
     * @param array $arr
     * @param int $depth
     * @return array
     */
    public static function flatten(array $arr, int $depth = 1): array
    {
        $res = [];

        foreach ($arr as $item) {
            if (is_array($item) && $depth > 0) {
                $res = [...$res, ...static::flatten($item, $depth - 1)];
            } else {
                $res[] = $item;
            }
        }

        return $res;
    }

    /**
     * Убрать вложенность массива.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Chain::from($arr)->flatten->all()->toArray();
     * ChainFunc::from($arr)->flatten->all();
     * Func::flattenAll($arr);
     * // [1, 2, 3, 4, 5];
     * ```
     * @param array $arr
     * @return array
     */
    public static function flattenAll(array $arr): array
    {
        $res = [];

        foreach ($arr as $item) {
            if (is_array($item)) {
                $res = [...$res, ...static::flattenAll($item)];
            } else {
                $res[] = $item;
            }
        }

        return $res;
    }

    /**
     * Дополняет массив значением до заданной длины. Если `length` больше нуля, то добавляет в конец массива, если меньше - в начало.
     *
     * ```
     * $arr = [1, 2];
     *
     * Chain::from($arr)->pad(5, 0)->toArray();
     * ChainFunc::from($arr)->pad(5, 0);
     * Func::pad($arr, 5, 0);
     * // [1, 2, 0, 0, 0]
     *
     * Func::pad($arr, -5, 0);
     * // [0, 0, 0, 1, 2]
     * ```
     * @link https://www.php.net/manual/ru/function.array-pad.php php.net - array_pad
     * @param array $arr
     * @param int $length
     * @param mixed $value
     * @return array
     */
    public static function pad(array $arr, int $length, $value): array
    {
        return array_pad($arr, $length, $value);
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - вернётся `null`
     *
     * ```
     * $arr = [1,2,3];
     *
     * Func::get($arr, 1);
     * Chain::from($arr)->get(1);
     * ChainFunc::from($arr)->get(1);
     * // 2
     *
     * Func::get($arr, 10);
     * // null
     * ```
     * @param string|int $key
     * @return mixed|null
     */
    public static function get(array $arr, $key)
    {
        return $arr[$key] ?? null;
    }

    /**
     *  Получить элемент к ключом `$key`. Если такого элемента нет - вернётся $val
     *
     *  ```
     *  $arr = [1,2,3];
     *
     *  Func::getOrElse($arr, 1, 'else');
     *  Chain::from($arr)->get->orElse(1, 'else');
     *  ChainFunc::from($arr)->get->orElse(1, 'else');
     *  // 2
     *
     *  Func::getOrElse($arr, 10, 'else');
     *  // 'else'
     *  ```
     * @param array $arr
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
     */
    public static function getOrElse(array $arr, $key, $val)
    {
        return $arr[$key] ?? $val;
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`
     *
     * ```
     * $arr = [1,2,3];
     *
     * Func::getOrElse($arr, 1);
     * Chain::from($arr)->get->orElse(1);
     * ChainFunc::from($arr)->get->orElse(1);
     * // 2
     *
     * Func::getOrElse($arr, 10);
     * // NotFoundException
     *   ```
     * @param array $arr
     * @param string|int $key
     * @return mixed
     * @throws NotFoundException
     */
    public static function getOrException(array $arr, $key)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        } else {
            throw new NotFoundException('Элемент с ключом ' . $key . ' не найден');
        }
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `null`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->byNumber(1);
     * ChainFunc::from($arr)->get->byNumber(1);
     * Func::getByNumber($arr, 1);
     * // 2
     *
     * Func::getByNumber($arr, 10);
     * // null
     * ```
     * @param array $arr
     * @param int $number
     * @return mixed|null
     */
    public static function getByNumber(array $arr, int $number)
    {
        $arrValues = array_values($arr);

        return $arrValues[$number] ?? null;
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `$val`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->byNumberOrElse(1, 'else');
     * ChainFunc::from($arr)->get->byNumberOrElse(1, 'else');
     * Func::getByNumberOrElse($arr, 1, 'else');
     * // 2
     *
     * Func::getByNumberOrElse($arr, 10, 'else');
     * // 'else'
     * ```
     * @param array $arr
     * @param int $number
     * @param mixed $val
     * @return mixed
     */
    public static function getByNumberOrElse(array $arr, int $number, $val)
    {
        $arrValues = array_values($arr);

        return $arrValues[$number] ?? $val;
    }

    /**
     * Получить элемент с ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`
     *
     * ```
     * $arr = [1,2,3];
     *
     * Chain::from($arr)->get->byNumberOrException(1);
     * ChainFunc::from($arr)->get->byNumberOrException(1);
     * Func::getByNumberOrException($arr, 1);
     * // 2
     *
     * Func::getByNumberOrException($arr, 10);
     * // NotFoundException
     *   ```
     * @param array $arr
     * @param int $number
     * @return mixed
     * @throws NotFoundException
     */
    public static function getByNumberOrException(array $arr, int $number)
    {
        $arrValues = array_values($arr);

        if (isset($arrValues[$number])) {
            return $arrValues[$number];
        } else {
            throw new NotFoundException('Элемент с номером ' . $number . ' не найден');
        }
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->first();
     * ChainFunc::from($arr)->get->first();
     * Func::getFirst($arr);
     * // 1
     *
     * Func::getFirst([]);
     * // null
     * ```
     * @param array $arr
     * @return mixed|null
     */
    public static function getFirst(array $arr)
    {
        $key = array_key_first($arr);

        return $arr[$key] ?? null;
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->firstOrElse('else');
     * ChainFunc::from($arr)->get->firstOrElse('else');
     * Func::getFirstOrElse($arr, 'else');
     * // 1
     *
     * Func::getFirstOrElse([], 'else');
     * // 'else'
     * ```
     * @param array $arr
     * @param mixed $val
     * @return mixed|null
     */
    public static function getFirstOrElse(array $arr, $val)
    {
        $key = array_key_first($arr);

        return $arr[$key] ?? $val;
    }


    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`
     *
     * ```
     * $arr = [1,2,3];
     *
     * Chain::from($arr)->get->firstOrException();
     * ChainFunc::from($arr)->get->firstOrException();
     * Func::getFirstOrException($arr);
     * // 1
     *
     * Func::getFirstOrException([]);
     * // NotFoundException
     *   ```
     * @param array $arr
     * @return mixed
     * @throws NotFoundException
     */
    public static function getFirstOrException(array $arr)
    {
        $key = array_key_first($arr);

        if (isset($arr[$key])) {
            return $arr[$key];
        } else {
            throw new NotFoundException('Элемент с номером 0 не найден');
        }
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->last();
     * ChainFunc::from($arr)->get->last();
     * Func::getLast($arr);
     * // 3
     *
     * Func::getLast([]);
     * // null
     * ```
     * @param array $arr
     * @return mixed|null
     */
    public static function getLast(array $arr)
    {
        $key = array_key_last($arr);

        return $arr[$key] ?? null;
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`
     *
     * ```
     * $arr = ['a' => 1, 'b' => 2, 'c' => 3];
     *
     * Chain::from($arr)->get->lastOrElse('else');
     * ChainFunc::from($arr)->get->lastOrElse('else');
     * Func::getLastOrElse($arr, 'else');
     * // 3
     *
     * Func::getLastOrElse([], 'else');
     * // 'else'
     * ```
     * @param array $arr
     * @param mixed $val
     * @return mixed|null
     */
    public static function getLastOrElse(array $arr, $val)
    {
        $key = array_key_last($arr);

        return $arr[$key] ?? $val;
    }


    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`
     *
     * ```
     * $arr = [1,2,3];
     *
     * Chain::from($arr)->get->lastOrException();
     * ChainFunc::from($arr)->get->lastOrException();
     * Func::getLastOrException($arr);
     * // 3
     *
     * Func::getLastOrException([]);
     * // NotFoundException
     *   ```
     * @param array $arr
     * @return mixed
     * @throws NotFoundException
     */
    public static function getLastOrException(array $arr)
    {
        $key = array_key_last($arr);

        if (isset($arr[$key])) {
            return $arr[$key];
        } else {
            throw new NotFoundException('Элемент не найден');
        }
    }


    /**
     * @param array $arr
     * @return mixed
     */
    public static function mathMin(array $arr)
    {
        return min($arr);
    }

    /**
     * @param array $arr
     * @param callable $callback
     * @return mixed
     */
    public static function mathMinBy(array $arr, callable $callback)
    {
        return MathAction::by($arr, 'min', $callback);
    }

    /**
     * @param array $arr
     * @param string|int $field
     * @return mixed
     */
    public static function mathMinByField(array $arr, $field)
    {
        return MathAction::byField($arr, 'min', $field);
    }
}