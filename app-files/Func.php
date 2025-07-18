<?php

namespace Ru\Progerplace\Chain;

use Error;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainAppend;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainJson;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainKeys;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainKeysCase;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainPrepend;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainReject;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainUnique;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncReject;
use Ru\Progerplace\Chain\Exception\NotFoundException;
use Ru\Progerplace\Chain\Utils\CaseKey;
use Ru\Progerplace\Chain\Utils\MathAction;

class Func
{
    /**
     * Изменить элементы коллекции. Ключи сохраняются.
     *
     * Параметры $callback - `$element`, `$key`
     *
     * ```
     * F::map(['a' => 1, 'b' => 2], fn(int $item, string $key) => $key . $item);
     * // ['a' => 'a1', 'b' => 'b2']
     *
     * F::map([1, 2, 3], fn(int $item) => $item + 5);
     * // [6, 7, 8]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-map.php Php.net - array_map
     * @see Cf::map()
     * @see Ch::map()
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
     * Убрать элементы из коллекции, для которых функция $callback вернула `true`. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->reject(fn(int $item) => $item < 4);
     * Ch::from([1, 2, 3, 4, 5])->reject(fn(int $item) => $item < 4)->toArray();
     * Func::reject([1, 2, 3, 4, 5], fn(int $item) => $item < 4);
     * // [3 => 4, 4 => 5]
     *
     * Func::reject(['a' => null, 'b' => 'foo', 'c' => ''], fn(?string $item, string $key) => $key === 'a' || $item === 'foo');
     * // ['c' => '']
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see Chain::reject()
     * @see ChainFunc::reject()
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
     * Cf::from([null, 'foo', ''])->reject->null();
     * Ch::from([null, 'foo', ''])->reject->null()->toArray();
     * Func::rejectNull([null, 'foo', '']);
     * // [1 => 'foo', 2 => '']
     *
     * Func::rejectNull(['a' => null, 'b' => 'foo', 'c' => '']);
     * // ['b' => 'foo', 'c' => '']
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainReject::null()
     * @see ChainFuncReject::null()
     */
    public static function rejectNull(array $arr): array
    {
        return array_filter($arr, fn($item) => !is_null($item));
    }

    /**
     * Убрать пустые элементы из коллекции. Проверка осуществляется методом `empty`. Ключи сохраняются.
     *
     * ```
     * Cf::from([null, 'foo', ''])->reject->empty();
     * Ch::from([null, 'foo', ''])->reject->empty()->toArray();
     * Func::rejectEmpty([null, 'foo', '']);
     * // [1 => 'foo']
     *
     * Func::rejectEmpty(['a' => null, 'b' => 'foo', 'c' => '']);
     * // ['b' => 'foo']
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainReject::empty()
     * @see ChainFuncReject::empty()
     */
    public static function rejectEmpty(array $arr): array
    {
        return array_filter($arr, fn($item) => !empty($item));
    }

    /**
     * Убрать элементы из коллекции с указанными ключами. Используется строгое сравнение `===`.
     *
     * ```
     * Func::rejectKeys(['a' => 1, 'b' => 2, 'c' => 3], 'b', 'c');
     * // ['a' => 1]
     * ```
     *
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainReject::keys()
     * @see ChainFuncReject::keys()
     */
    public static function rejectKeys(array $arr, ...$keys): array
    {
        return array_filter($arr, fn($key) => !in_array($key, $keys, true), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Убрать элементы из коллекции с указанными значениями. Используется строгое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Func::rejectValues(['a' => 1, 'b' => 2, 'c' => 3], 1, '2')
     * // ['c' => 3]
     * ```
     *
     * @param array $arr
     * @param mixed ...$values
     * @return array
     *
     * @see ChainReject::values()
     * @see ChainFuncReject::values()
     */
    public static function rejectValues(array $arr, ...$values): array
    {
        return array_filter($arr, fn($value) => !in_array($value, $values, true));
    }

    /**
     * Возвращает значения массива.
     *
     * ```
     * Func::values(['a' => 1, 'b' => 2, 'c' => 3])
     * // [1, 2, 3]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-values.php Php.net - array_values
     * @see Chain::values()
     * @see ChainFunc::values()
     */
    public static function values(array $arr): array
    {
        return array_values($arr);
    }

    /**
     * Элементы коллекции в обратном порядке.
     *
     * Если `$preserveNumericKeys` установлено в `true`, то числовые ключи будут сохранены. Нечисловые ключи не подвержены этой опции и всегда сохраняются.
     *
     * ```
     * Func::reverse([1, 2, 3])
     * // [3, 2, 1]
     *
     * Func::reverse([1, 2, 3], true)
     * [2 => 3, 1 => 2, 0 => 1]
     * ```
     *
     * @param array $arr
     * @param bool $isPreserveNumericKeys = false
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-reverse.php Php.net - array_reverse
     * @see Ch::reverse()
     * @see Cf::reverse()
     */
    public static function reverse(array $arr, bool $isPreserveNumericKeys = false): array
    {
        return array_reverse($arr, $isPreserveNumericKeys);
    }


    /**
     * Проверка на пустой массив.
     *
     * ```
     * Func::isEmpty([])
     * // true
     * ```
     *
     * @param array $arr
     * @return bool
     *
     * @see ChainIs::empty()
     * @see ChainFuncIs::empty()
     */
    public static function isEmpty(array $arr): bool
    {
        return empty($arr);
    }

    /**
     * Проверка на непустой массив.
     *
     * ```
     * Func::isNotEmpty([])
     * // true
     * ```
     *
     * @param array $arr
     * @return bool
     *
     * @see ChainIs::notEmpty()
     * @see ChainFuncIs::notEmpty()
     */
    public static function isNotEmpty(array $arr): bool
    {
        return !empty($arr);
    }

    /**
     * Проверка "все элементы удовлетворяют условию". Вернёт `true`, если для каждого элемента функция callback вернёт `true`.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Func::isEvery($arr, fn(int $item) => $item > 0);
     * // true
     *
     * Func::isEvery($arr, fn(int $item) => $item > 1);
     * // false
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return bool
     *
     * @see ChainIs::every()
     * @see ChainFuncIs::every()
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
     * Проверка "все элементы не удовлетворяют условию". Вернёт `true`, если для каждого элемента функция `callback` вернёт `false`.
     *
     * ```
     * Func::isNone([1, 2, 3], fn(int $item) => $item < 0);
     * // true
     *
     * Func::isNone($arr, fn(int $item) => $item > 2);
     * // false
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return bool
     *
     * @see ChainIs::none()
     * @see ChainFuncIs::none()
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
     * Проверка "хотя бы один элемент удовлетворяют условию". Вернёт `true`, если хотя бы для одного элемента функция `callback` вернёт `true`.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Func::isAny([1, 2, 3], fn(int $item) => $item >= 3);
     * // true
     *
     * Func::isAny($arr, fn(int $item) => $item > 10);
     * // false
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return bool
     *
     * @see ChainIs::any()
     * @see ChainFuncIs::any()
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
     * Func::isList([0 => 1, 1 => 2, 2 => 3]);
     * // true
     *
     * Func::isList([10 => 1, 11 => 2, 12 => 3]);
     * // false
     * ```
     *
     * @param array $arr
     * @return bool
     *
     * @link https://www.php.net/manual/ru/function.array-is-list.php Php.net - array_is_list
     * @see ChainIs::list()
     * @see ChainFuncIs::list()
     */
    public static function isList(array $arr): bool
    {
        return array_is_list($arr);
    }

    /**
     * Проверки, есть ли хотя бы одно из переданных значений в массиве. Используется строгое сравнение `===`.
     *
     * ```
     * Func::isHasValue([1, 2, 3], 3, 4)
     * // true
     * ```
     *
     * @param array $arr
     * @param mixed ...$values
     * @return bool
     *
     * @see ChainIs::hasValue()
     * @see ChainFuncIs::hasValue()
     */
    public static function isHasValue(array $arr, ...$values): bool
    {
        foreach ($arr as $item) {
            if (in_array($item, $values, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Проверка, что значение поля `field` равно хотя бы одному из переданных значений `values`. Используется строгое сравнение `===`.
     *
     * ```
     * Func::isFieldHasValue(['a' => 1, 'b' => 2], 'a', 1, 10);
     * // true
     * ```
     *
     * @param array $arr
     * @param int|string $field
     * @param mixed ...$values
     * @return bool
     *
     * @see ChainIs::fieldHasValue()
     * @see ChainFuncIs::fieldHasValue()
     */
    public static function isFieldHasValue(array $arr, $field, ...$values): bool
    {
        if (in_array($arr[$field], $values, true)) {
            return true;
        }

        return false;
    }

    /**
     * Проверка, присутствует ли в массиве хотя бы один ключ из `keys`.
     *
     * ```
     * Func::isHasKey(['a' => 1, 'b' => 2], 'a', 'd');
     * // true
     * ```
     *
     * @param array $arr
     * @param int|string ...$keys
     * @return bool
     *
     * @see ChainIs::hasKey()
     * @see ChainFuncIs::hasKey()
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
     * Func::unique([1,1,2])
     * // [0 => 1, 2 => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-unique.php Php.net - array_unique
     * @see Chain::unique()
     * @see ChainFunc::unique()
     */
    public static function unique(array $arr): array
    {
        return array_unique($arr);
    }

    /**
     * Удалить повторяющиеся элементы на основе возвращаемых функцией `$callback` значений. Ключи сохраняются.
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
     * Func::uniqueBy($arr, fn(stdClass $item, string $key) => $item->value);
     * // ['a' => $first, 'b' => $second, 'd' => $fourth],
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see ChainUnique::by()
     * @see ChainFuncUnique::by()
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
     * Cf::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * Ch::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * Func::reduce([1, 2, 3], fn(int $res, int $item) => $res + $item, 0)
     * // 6
     *
     * Func::reduce(['a' => 1, 'b' => 2], fn(array $res, int $item, string $key) => [...$res, $key, $item])
     * // [ 'a', 1, 'b', 2]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @param mixed $startVal
     * @return array|mixed
     *
     * @see Chain::reduce()
     * @see ChainFunc::reduce()
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
     * Func::each([1, 2, 3], fn(int $item, string $key) => echo $key . $item);
     * // [1, 2, 3]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see Chain::each()
     * @see ChainFunc::each()
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
     * Func::count([1, 2, 3]);
     * // 3
     * ```
     *
     * @param array $arr
     * @return int
     *
     * @see Chain::count()
     * @see ChainFunc::count()
     */
    public static function count(array $arr): int
    {
        return count($arr);
    }

    /**
     * Кодировать в json поля с перечисленными ключами. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * Func::jsonEncodeFields(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]], 'a', 'b');
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainJson::encodeFields()
     * @see ChainFuncJson::encodeFields()
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
     * Func::jsonEncodeBy(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]], fn(array $item, string $key) => $item === ['f' => 1] || $key === 'b');
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see ChainJson::encodeBy()
     * @see ChainFuncJson::encodeBy()
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
     * Func::jsonDecodeFields(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'], 'a', 'b')
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param array $arr
     * @param ...$keys
     * @return array
     *
     * @see ChainJson::decodeFields()
     * @see ChainFuncJson::decodeFields()
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
     *  Func::jsonDecodeBy(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'], fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')
     *  // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see ChainJson::decodeBy()
     * @see ChainFuncJson::decodeBy()
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
     * Добавить элементы в конец массива. Элементы добавляются как есть.
     *
     * ```
     * Func::append([1, 2], 3, 4);
     * // [1, 2, 3, 4]
     *
     * Func::append([1, 2], [3, 4]);
     * // [1, 2, [3, 4]]
     * ```
     * @param array $arr
     * @param mixed ...$items
     * @return array
     * @see Cf::append()
     *
     * @see Ch::append()
     */
    public static function append(array $arr, ...$items): array
    {
        return [...$arr, ...$items];
    }

    /**
     * Добавить элементы в конец коллекции. Если элемент итерируемый - то будет выполнено слияние. Неитерируемые элементы будут добавлены как есть.
     *
     * ```
     * $arr = [1, 2];
     *
     * Func::appendMerge($arr, 3, [4, 5]);
     * // [1, 2, 3, 4, 5]
     *
     * Func::appendMerge($arr, 3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param mixed ...$items
     * @return array
     *
     * @see ChainAppend::merge()
     * @see ChainFuncAppend::merge()
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
     * Func::appendMergeFromJson($arr, '[3, 4, 5, [6, 7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     * @param array $arr
     * @param string $json
     * @return array
     *
     * @see ChainAppend::mergeFromJson()
     * @see ChainFuncAppend::mergeFromJson()
     */
    public static function appendMergeFromJson(array $arr, string $json): array
    {
        $array = json_decode($json, true);

        return static::appendMerge($arr, $array);
    }

    /**
     * Конвертировать строку в массив и добавить в конец массива (с распаковкой итерируемых элементов).
     *
     * ```
     * $arr = [1, 2];
     *
     * Func::appendMergeFromString($arr, '3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param array $arr
     * @param string $str
     * @param string $delimiter
     * @return array
     *
     * @see ChainAppend::mergeFromString()
     * @see ChainFuncAppend::mergeFromString()
     */
    public static function appendMergeFromString(array $arr, string $str, string $delimiter): array
    {
        $array = explode($delimiter, $str);

        return static::appendMerge($arr, $array);
    }


    /**
     * Добавить элементы в начало коллекции.
     *
     * ```
     * Func::prepend([3, 4], 1, 2);
     * // [1, 2, 3, 4]
     * ```
     *
     * @param array $arr
     * @param mixed ...$items
     * @return array
     *
     * @see Chain::prepend()
     * @see ChainFunc::prepend()
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
     * Ch::from($arr)->prepend->merge(3, [4, 5, [6, 7]])->toArray();
     * Cf::from($arr)->prepend->merge(3, [4, 5, [6, 7]]);
     * Func::prependMerge($arr, 3, [4, 5, [6, 7]]);
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param array $arr
     * @param ...$items
     * @return array
     *
     * @see ChainPrepend::merge()
     * @see ChainFuncPrepend::merge()
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
     * Ch::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]')->toArray();
     * Cf::from($arr)->prepend->mergeFromJson('[3,4,5,[6,7]]');
     * Func::prependMergeFromJson($arr, '[3,4,5,[6,7]]');
     * // [1, 2, 3, 4, 5, [6, 7]]
     * ```
     *
     * @param array $arr
     * @param string $json
     * @return array
     *
     * @see ChainPrepend::mergeFromJson()
     * @see ChainFuncPrepend::mergeFromJson()
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
     * Ch::from($arr)->prepend->mergeFromString('3,4,5', ',')->toArray();
     * Cf::from($arr)->prepend->mergeFromString('3,4,5', ',');
     * Func::prependMergeFromJson($arr, '3,4,5', ',');
     * // [1, 2, 3, 4, 5]
     * ```
     *
     * @param array $arr
     * @param string $str
     * @param string $delimiter
     * @return array
     *
     * @see ChainPrepend::mergeFromString()
     * @see ChainFuncPrepend::mergeFromString()
     */
    public static function prependMergeFromString(array $arr, string $str, string $delimiter): array
    {
        $array = explode($delimiter, $str);

        return static::prependMerge($arr, $array);
    }


    /**
     * Оставить элементы коллекции, для которых $callback вернёт true. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`
     *
     * ```
     * Func::filter([1, 2, 3], fn(int $item) => $item > 2)
     * // [2 => 3]
     *
     * Func::filter(['a' => 1, 'b' => 2, 'c' => 3], fn(int $item, string $key) => $item > 1 && $key !== 'b' )
     * // ['c' => 3]
     * ````
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see Chain::filter()
     * @see ChainFunc::filter()
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
     * Оставить только элементы коллекции с указанными ключами. Используется строгое сравнение `===`.
     *
     * ```
     * Func::filterKeys(['a' => 1, 'b' => 2, 'c' => 3], 'a', 'b');
     * // ['a' => 1, 'b' => 2]
     * ```
     * @param array $arr
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainFilter::keys()
     * @see ChainFuncFilter::keys()
     */
    public static function filterKeys(array $arr, ...$keys): array
    {
        return array_filter($arr, fn($key) => in_array($key, $keys, true), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Оставить только элементы из коллекции с указанными значениями. Используется строгое сравнение `===`. Ключи сохраняются.
     *
     * ```
     * Func::filterValues(['a' => 1, 'b' => 2, 'c' => 3], 1, '2');
     * // ['a' => 1, 'b' => 2]
     * ```
     *
     * @param array $arr
     * @param mixed ...$values
     * @return array
     *
     * @see ChainFilter::values()
     * @see ChainFuncFilter::values()
     */
    public static function filterValues(array $arr, ...$values): array
    {
        return array_filter($arr, fn($value) => in_array($value, $values, true));
    }

    /**
     * Преобразовать стиль ключей к "camelCase".
     *
     * ```
     * Func::keysCaseToCamel(['var_first' => 1, 'var_second' => 2]);
     * // ['varFirst' => 1, 'varSecond' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toCamel()
     * @see ChainFuncKeysCase::toCamel()
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
     * Func::keysCaseToPaskal(['var_first' => 1, 'var_second' => 2])
     * // ['VarFirst' => 1, 'VarSecond' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toPaskal()
     * @see ChainFuncKeysCase::toPaskal()
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
     * Func::keysCaseToSnake(['varFirst' => 1, 'varSecond' => 2]);
     * // ['var_first' => 1, 'var_second' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toSnake()
     * @see ChainFuncKeysCase::toSnake()
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
     * Func::keysCaseToKebab(['varFirst' => 1, 'varSecond' => 2]);
     * // ['var-first' => 1, 'var-second' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toKebab()
     * @see ChainFuncKeysCase::toKebab()
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
     * Func::keysCaseToScreamSnake(['varFirst' => 1, 'varSecond' => 2]);
     * // ['VAR_FIRST' => 1, 'VAR_SECOND' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toScreamSnake()
     * @see ChainFuncKeysCase::toScreamSnake()
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
     * Func::keysCaseToScreamKebab(['varFirst' => 1, 'varSecond' => 2]);
     * // ['VAR-FIRST' => 1, 'VAR-SECOND' => 2]
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainKeysCase::toScreamKebab()
     * @see ChainFuncKeysCase::toScreamKebab()
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
     * Func::find(['a' => 1, 'b' => 2], fn(int $item, string $key) => $item == 1);
     * // 1
     *
     * Func::find(['a' => 1, 'b' => 2], fn(int $item, string $key) => $key == 'a');
     * // 1
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return mixed
     *
     * @see Chain::find()
     * @see ChainFunc::find()
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
     * Func::group([1, 2, 3, 4, 5], fn(int $item) => $item > 3 ? 'more' : 'less')
     * // [
     * //   'less' => [1, 2, 3],
     * //   'more' => [4,5]
     * // ]
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see Chain::group()
     * @see ChainFunc::group()
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
     * Сгруппировать элементы на основе значений поля `$field`. Если указанного поля в элементе нет, то он попадёт в группу с пустым ключом `''`.
     *
     * ```
     * $arr = [
     *   ['a' => 1],
     *   ['a' => 1],
     *   ['a' => 3],
     * ];
     *
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
     *
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
     *
     * @see ChainGroup::byField()
     * @see ChainFuncGroup::byField()
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
     * Сгруппировать элементы на основе значений, которые вернёт callback функция, и привести к структуре вида
     *
     * `['key' => ..., 'items' => [...]]`.
     *
     * Актуально, если возвращаемое значение не является валидным ключом массива (например, объекты). Сравнение ключей производится через сериализацию (функция `serialize`).
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
     * Func::groupToStruct($arr);
     * // [
     * //   ['key' => $first, 'items' => [$first, $third]],
     * //   ['key' => $second, 'items' => [$second]],
     * //   ['key' => $fourth, 'items' => [$fourth]],
     * // ],
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see ChainGroup::toStruct()
     * @see ChainFuncGroup::toStruct()
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
     * Возвращает массив ключей.
     *
     * ```
     * Func::keys(['a' => 1, 'b' => 2, 'c' => 3]);
     * // ['a', 'b', 'c']
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-keys.php Php.net - array_keys
     * @see Chain::keys()
     * @see ChainFunc::keys()
     */
    public static function keys(array $arr): array
    {
        return array_keys($arr);
    }

    /**
     * Изменить значения ключей. Повторяющиеся значения будут молча перезаписаны.
     *
     * Параметры callback функции - `$key`, `$element`
     *
     * ```
     * Func::keysMap(['a' =>1, 'b' => 2, 'c' => 3], fn(string $key, int $item) => $key . $item);
     * // ['a1' =>1, 'b2' => 2, 'c3' => 3];
     * ```
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @see ChainKeys::map()
     * @see ChainFuncKeys::map()
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
     *
     * @see ChainKeys::fromField()
     * @see ChainFuncKeys::fromField()
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
     * Func::keysGet(['a' => 1, 'b' => 2], 1);
     * // 'b'
     * ```
     *
     * @param array $arr
     * @param int $number
     * @return int|string|null
     *
     * @see ChainKeys::get()
     * @see ChainFuncKeys::get()
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
     * Func::keysGetFirst(['a' => 1, 'b' => 2]);
     * // 'a'
     * ```
     *
     * @param array $arr
     * @return int|string|null
     *
     * @link https://www.php.net/manual/ru/function.array-key-first.php php.net - Php.net - array_key_first
     * @see ChainKeys::getFirst()
     * @see ChainFuncKeys::getFirst()
     */
    public static function keysGetFirst(array $arr)
    {
        return array_key_first($arr);
    }

    /**
     * Получить последний ключ массива.
     *
     * ```
     * Func::keysGetLast(['a' => 1, 'b' => 2]);
     * // 'b'
     * ```
     *
     * @param array $arr
     * @return int|string|null
     *
     * @link https://www.php.net/manual/ru/function.array-key-last.php php.net - Php.net - array_key_last
     * @see ChainKeys::getLast()
     * @see ChainFuncKeys::getLast()
     */
    public static function keysGetLast(array $arr)
    {
        return array_key_last($arr);
    }


    /**
     * Сортировка массива. Используется функция usort.
     *
     * ```
     * Func::sort([3, 1, 2], fn(int $a, int $b) => $a <=> $b));
     * // [1, 2, 3]
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
     * ##### Компараторы для строк:
     * - `strcasecmp` — сравнивает строки без учёта регистра в бинарно безопасном режиме, [подробнее](https://www.php.net/manual/ru/function.strcasecmp.php)
     * - `strcmp` — сравнивает строки в бинарно-безопасном режиме: как последовательности байтов [подробнее](https://www.php.net/manual/ru/function.strcmp.php)
     * - `strnatcasecmp` — сравнивает строки без учёта регистра по алгоритму natural order [подробнее](https://www.php.net/manual/ru/function.strnatcasecmp.php)
     * - `strnatcmp` — сравнивает строк алгоритмом natural order [подробнее](https://www.php.net/manual/ru/function.strnatcmp.php)
     * - `strncasecmp` — сравнивает первые n символов строк без учёта регистра в бинарно-безопасном режиме [подробнее](https://www.php.net/manual/ru/function.strncasecmp.php)
     * - `strncmp` — сравнивает первые n символов строк в бинарно безопасном режиме [подробнее](https://www.php.net/manual/ru/function.strncmp.php)
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.usort.php Php.net - функция usort
     * @link https://www.php.net/manual/ru/ref.strings.php Php.net - методы строк (в том числе компараторы)
     * @see Chain::sort()
     * @see ChainFunc::sort()
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
     * Ch::from([1, 2, 3])->clear()->toArray()
     * Cf::from([1, 2, 3])->clear()->toArray()
     * Func::clear([1, 2, 3])
     * // []
     *
     * $arr = [
     *  'a' => [1,2],
     *  'b' => [3,4]
     * ]
     * Cf::from($arr)->elems->clear()
     * // [
     * //   'a' => [],
     * //   'b' => []
     * // ]
     * ```
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
     * Func::chunkBySize([1, 2, 3, 4, 5], 2);
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
     * @link https://www.php.net/manual/ru/function.array-chunk.php Php.net - array_chunk
     * @see ChainChunk::bySize()
     * @see ChainFuncChunk::bySize()
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
     * Func::chunkByCount([1, 2, 3, 4, 5], 3);
     * // [
     * //  [1, 2],
     * //  [3, 4],
     * //  [5]
     * // ]
     *
     * Func::chunkByCount([1, 2, 3, 4, 5], 3, true);
     * // [
     * //   [0 => 1, 1 => 2],
     * //   [2 => 3, 3 => 4],
     * //   [4 => 5],
     * // ],
     * ```
     *
     * @param array $arr
     * @param int $count
     * @param bool $isPreserveKeys
     * @return array
     *
     * @see ChainChunk::byCount()
     * @see ChainFuncChunk::byCount()
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
     * Func::flip(['a' => 10, 'b' => 20, 'c' => 30]);
     * // ['10' => 'a', '20' => 'b', '30' => 'c'];
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-flip.php Php.net - array_flip
     * @see Chain::flip()
     * @see ChainFunc::flip()
     */
    public static function flip(array $arr): array
    {
        return array_flip($arr);
    }

    /**
     * Извлекает и возвращает первое значение массива array, сокращает массив array на один элемент и сдвигает остальные элементы в начало. Числовые ключи массива изменятся так, чтобы нумерация начиналась с нуля, тогда как литеральные ключи не изменятся.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $arr = [1, 2, 3];
     *
     * Func::shift($arr);
     * // 1
     *
     * $arr;
     * // [2, 3]
     * ```
     *
     * @param array $arr
     * @return mixed|null
     *
     * @link https://www.php.net/manual/ru/function.array-shift.php Php.net - array_shift
     * @see Chain::shift()
     * @see ChainFunc::shift()
     */
    public static function shift(array &$arr)
    {
        return array_shift($arr);
    }

    /**
     * Извлекает и возвращает последнее значение массива array, сокращает массив array на один элемент.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $arr = [1, 2, 3];
     * Func::pop($arr);
     * // 3
     *
     * $arr;
     * // [1, 2]
     * ```
     *
     * @link https://www.php.net/manual/ru/function.array-pop.php Php.net - array_shift
     * @param array $arr
     * @return mixed|null
     *
     * @see Chain::pop()
     * @see ChainFunc::pop()
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
     * Func::splice($arr, 2, 1, 'item');
     * // [3]
     *
     * $arr;
     * // [1, 2, 'item', 4]
     * ```
     *
     * @param array $arr
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-splice.php Php.net - array_splice
     * @see Chain::splice
     * @see ChainFunc::splice
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
     * Func::spliceHead($arr, 2, 1, 'item');
     * // [1, 2]
     *
     * $arr;
     * // ['item', 3, 4]
     *  ```
     *
     * @param array $arr
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @see ChainSplice::head
     * @see ChainFuncSplice::head
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
     * Func::spliceTail($arr, 2, 1, 'item');
     * // [1, 2]
     *
     * $arr;
     * // ['item', 3, 4]
     *  ```
     *
     * @param array $arr
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @see ChainSplice::tail
     * @see ChainFuncSplice::tail
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
     * Func::slice($arr, 1, 2);
     * // [1, 2]
     *
     * Func::slice($arr, 1, 2, true);
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @param array $arr
     * @param int $offset
     * @param int|null $length
     * @param bool $isPreserveKeys
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-slice.php Php.net - array_slice
     * @see Chain::slice()
     * @see ChainFunc::slice()
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
     *
     * @see ChainSlice::head()
     * @see ChainFuncSlice::head()
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
     *
     * @see ChainSlice::tail()
     * @see ChainFuncSlice::tail()
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
     * Func::replace($arr, [6, 7], [4 => 8]);
     * // [6, 7, 3, 4, 8];
     * ```
     *
     * @param array $array
     * @param array ...$replacements
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-replace.php Php.net - array_replace
     * @see Chain::replace()
     * @see ChainFunc::replace()
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
     * Func::replace($arr, $arrReplace1, $arrReplace2);
     * // [
     * //   [1, 2, 3],
     * //   [4, 7, 9],
     * // ]
     * ```
     *
     * @param array $array
     * @param array ...$replacements
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-replace-recursive.php Php.net - array_replace_recursive
     * @see ChainReplace::recursive()
     * @see ChainFuncReplace::recursive()
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
     * Func::flatten($arr);
     * // [1, 2, 3, [4, [5]]];
     *
     * Func::flatten($arr, 2);
     * // [1, 2, 3, 4, [5]];
     * ```
     *
     * @param array $arr
     * @param int $depth
     * @return array
     *
     * @see Chain::flatten()
     * @see ChainFunc::flatten()
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
     * Полностью убрать вложенность массива.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Func::flattenAll($arr);
     * // [1, 2, 3, 4, 5];
     * ```
     *
     * @param array $arr
     * @return array
     *
     * @see ChainFlatten::all()
     * @see ChainFuncFlatten::all()
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
     * Func::pad([1, 2], 5, 0);
     * // [1, 2, 0, 0, 0]
     *
     * Func::pad([1, 2], -5, 0);
     * // [0, 0, 0, 1, 2]
     * ```
     *
     * @link https://www.php.net/manual/ru/function.array-pad.php Php.net - array_pad
     * @param array $arr
     * @param int $length
     * @param mixed $value
     * @return array
     *
     * @see Chain::pad()
     * @see ChainFunc::pad()
     */
    public static function pad(array $arr, int $length, $value): array
    {
        return array_pad($arr, $length, $value);
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - вернётся `null`
     *
     * ```
     * Func::get([1, 2, 3], 1);
     * // 2
     *
     * Func::get([1, 2, 3], 10);
     * // null
     * ```
     *
     * @param string|int $key
     * @return mixed|null
     *
     * @see Chain::get()
     * @see ChainFunc::get()
     */
    public static function get(array $arr, $key)
    {
        return $arr[$key] ?? null;
    }

    /**
     *  Получить элемент к ключом `$key`. Если такого элемента нет - вернётся $val.
     *
     *  ```
     *  Func::getOrElse([1, 2, 3], 1, 'else');
     *  // 2
     *
     *  Func::getOrElse([1, 2, 3], 10, 'else');
     *  // 'else'
     *  ```
     *
     * @param array $arr
     * @param string|int $key
     * @param mixed $val
     * @return mixed|null
     *
     * @see Chain::getOrElse()
     * @see ChainFunc::getOrElse()
     */
    public static function getOrElse(array $arr, $key, $val)
    {
        return $arr[$key] ?? $val;
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`
     *
     * ```
     * Func::getOrElse([1, 2, 3], 1);
     * // 2
     *
     * Func::getOrElse([1, 2, 3], 10);
     * // NotFoundException
     * ```
     *
     * @param array $arr
     * @param string|int $key
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainGet::orException()
     * @see ChainFuncGet::orException()
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
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `null`.
     *
     * ```
     * Func::getByNumber(['a' => 1, 'b' => 2, 'c' => 3], 1);
     * // 2
     *
     * Func::getByNumber($arr, 10);
     * // null
     * ```
     *
     * @param array $arr
     * @param int $number
     * @return mixed|null
     *
     * @see ChainGet::byNumber()
     * @see ChainFuncGet::byNumber()
     */
    public static function getByNumber(array $arr, int $number)
    {
        $arrValues = array_values($arr);

        return $arrValues[$number] ?? null;
    }

    /**
     * Получить элемент по номеру в массиве. Если такого элемента нет - вернётся `$val`.
     *
     * ```
     * Func::getByNumberOrElse(['a' => 1, 'b' => 2, 'c' => 3], 1, 'else');
     * // 2
     *
     * Func::getByNumberOrElse(['a' => 1, 'b' => 2, 'c' => 3], 10, 'else');
     * // 'else'
     * ```
     *
     * @param array $arr
     * @param int $number
     * @param mixed $val
     * @return mixed
     *
     * @see ChainGet::byNumberOrElse()
     * @see ChainFuncGet::byNumberOrElse()
     */
    public static function getByNumberOrElse(array $arr, int $number, $val)
    {
        $arrValues = array_values($arr);

        return $arrValues[$number] ?? $val;
    }

    /**
     * Получить элемент с ключом `$key`. Если такого элемента нет - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Func::getByNumberOrException([1, 2, 3], 1);
     * // 2
     *
     * Func::getByNumberOrException([1, 2, 3], 10);
     * // NotFoundException
     * ```
     *
     * @param array $arr
     * @param int $number
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainGet::byNumberOrException()
     * @see ChainFuncGet::byNumberOrException()
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
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->get->first();
     * // 1
     *
     * Func::getFirst([]);
     * // null
     * ```
     *
     * @param array $arr
     * @return mixed|null
     *
     * @see ChainGet::first()
     * @see ChainFuncGet::first()
     */
    public static function getFirst(array $arr)
    {
        $key = array_key_first($arr);

        return $arr[$key] ?? null;
    }

    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Func::getFirstOrElse(['a' => 1, 'b' => 2, 'c' => 3], 'else');
     * // 1
     *
     * Func::getFirstOrElse([], 'else');
     * // 'else'
     * ```
     *
     * @param array $arr
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::firstOrElse()
     * @see ChainFuncGet::firstOrElse()
     */
    public static function getFirstOrElse(array $arr, $val)
    {
        $key = array_key_first($arr);

        return $arr[$key] ?? $val;
    }


    /**
     * Получить первый элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Func::getFirstOrException([1, 2, 3]);
     * // 1
     *
     * Func::getFirstOrException([]);
     * // NotFoundException
     * ```
     *
     * @param array $arr
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainGet::firstOrException()
     * @see ChainFuncGet::firstOrException()
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
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `null`.
     *
     * ```
     * Func::getLast(['a' => 1, 'b' => 2, 'c' => 3]);
     * // 3
     *
     * Func::getLast([]);
     * // null
     * ```
     *
     * @param array $arr
     * @return mixed|null
     *
     * @see ChainGet::last()
     * @see ChainFuncGet::last()
     */
    public static function getLast(array $arr)
    {
        $key = array_key_last($arr);

        return $arr[$key] ?? null;
    }

    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - вернётся `$val`.
     *
     * ```
     * Func::getLastOrElse(['a' => 1, 'b' => 2, 'c' => 3], 'else');
     * // 3
     *
     * Func::getLastOrElse([], 'else');
     * // 'else'
     * ```
     *
     * @param array $arr
     * @param mixed $val
     * @return mixed|null
     *
     * @see ChainGet::lastOrElse()
     * @see ChainFuncGet::lastOrElse()
     */
    public static function getLastOrElse(array $arr, $val)
    {
        $key = array_key_last($arr);

        return $arr[$key] ?? $val;
    }


    /**
     * Получить последний элемент массива. Если такого элемента нет (массив пустой) - будет брошено исключение `NotFoundException`.
     *
     * ```
     * Func::getLastOrException([1, 2, 3]);
     * // 3
     *
     * Func::getLastOrException([]);
     * // NotFoundException
     * ```
     *
     * @param array $arr
     * @return mixed
     * @throws NotFoundException
     *
     * @see ChainGet::lastOrException()
     * @see ChainFuncGet::lastOrException()
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