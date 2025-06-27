<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncAppend;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncChunk;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncFilter;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncFlatten;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncGet;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncGroup;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncIs;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncJson;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncKeys;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncMath;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncPrepend;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncReject;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncReplace;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncSlice;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncSplice;
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncUnique;
use Ru\Progerplace\Chain\Utils\ArrayAction;

/**
 * @property ChainFunc $elems
 */
class ChainFunc
{
    protected int   $elemsLevel = 0;
    protected array $array      = [];

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Создание
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct(array $array)
    {
        $this->array = $array;

        $this->reject = new ChainFuncReject($this->array, $this);
        $this->json = new ChainFuncJson($this->array, $this);
        $this->filter = new ChainFuncFilter($this->array, $this);
        $this->keys = new ChainFuncKeys($this->array, $this);
        $this->is = new ChainFuncIs($this->array, $this);
        $this->group = new ChainFuncGroup($this->array, $this);
        $this->chunk = new ChainFuncChunk($this->array, $this);
        $this->slice = new ChainFuncSlice($this->array, $this);
        $this->flatten = new ChainFuncFlatten($this->array, $this);
        $this->append = new ChainFuncAppend($this->array, $this);
        $this->prepend = new ChainFuncPrepend($this->array, $this);
        $this->splice = new ChainFuncSplice($this->array, $this);
        $this->replace = new ChainFuncReplace($this->array, $this);
        $this->get = new ChainFuncGet($this->array, $this);
        $this->unique = new ChainFuncUnique($this->array, $this);
        $this->math = new ChainFuncMath($this->array, $this);
    }

    public ChainFuncJson  $json;
    public ChainFuncIs    $is;
    public ChainFuncChunk $chunk;
    public ChainFuncMath  $math;

    public static function from(?iterable $var, $default = []): ChainFunc
    {
        if (is_null($var)) {
            return new static($default);
        }

        if (is_array($var)) {
            return new static($var);
        }

        $array = [];
        foreach ($var as $item) {
            $array[] = $item;
        }

        return new static($array);
    }

    public static function fromJson(string $json): ChainFunc
    {
        $array = json_decode($json, true);

        return new static($array);
    }

    public static function fromString(string $str, string $delimiter): ChainFunc
    {
        $array = explode($delimiter, $str);

        return new static($array);
    }

    /**
     * @link https://www.php.net/manual/ru/function.range.php php.net - range
     * @param string|int|float $start
     * @param string|int|float $end
     * @param int|float $step
     * @return self
     */
    public static function fromRange($start, $end, $step = 1): self
    {
        $array = range($start, $end, $step);

        return new static($array);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Экспорт
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function toArray(): array
    {
        return $this->array;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Elems
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __get(string $name)
    {
        if ($name === 'elems') {
            $this->elemsLevel++;

            return $this;
        } elseif ($name === 'elemsLevel') {
            return $this->elemsLevel;
        }

        return null;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Операции
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Изменить элементы коллекции. Ключи сохраняются.
     *
     * Параметры callback - `$element`, `$key`
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->map(fn(int $item, string $key) => $key . $item);
     * // ['a' => 'a1', 'b' => 'b2']
     *
     * Cf::from([1, 2, 3]))->map(fn(int $item) => $item + 5);
     * // [6, 7, 8]
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-map.php Php.net - array_map
     * @see Chain::map()
     * @see Func::map()
     */
    public function map(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'map'], $callback);
    }


    public ChainFuncReject $reject;

    /**
     * Убрать элементы из коллекции, для которых функция $callback вернула `true`. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->reject(fn(int $item) => $item < 4);
     * // [3 => 4, 4 => 5]
     *
     * Cf::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject(fn(?string $item, string $key) => $key === 'a' || $item === 'foo');
     * // ['c' => '']
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see Chain::reject()
     * @see Func::reject()
     */
    public function reject(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reject'], $callback);
    }

    /**
     * Возвращает только значения массива.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->values()
     * // [1, 2, 3]
     * ```
     *
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-values.php Php.net - array_values
     * @see Chain::values()
     * @see Func::values()
     */
    public function values(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'values']);
    }


    /**
     * Элементы коллекции в обратном порядке.
     *
     * Если `$preserveNumericKeys` установлено в `true`, то числовые ключи будут сохранены. Нечисловые ключи не подвержены этой опции и всегда сохраняются.
     *
     * ```
     * Cf::from([1, 2, 3])->reverse();
     * // [3, 2, 1]
     *
     * Cf::from([1, 2, 3], true)->reverse();
     * [2 => 3, 1 => 2, 0 => 1]
     * ```
     *
     * @param bool $isPreserveNumericKeys = false
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-reverse.php Php.net - array_reverse
     * @see ChainFunc::reverse()
     * @see Func::reverse()
     */
    public function reverse(bool $isPreserveNumericKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reverse'], $isPreserveNumericKeys);
    }


    public ChainFuncKeys $keys;

    /**
     * Возвращает массив ключей.
     *
     * ```
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->keys();
     * // ['a', 'b', 'c']
     * ```
     *
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-keys.php Php.net - array_keys
     * @see Chain::keys()
     * @see Func::keys()
     */
    public function keys(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'keys']);
    }


    /**
     * Удалить повторяющиеся значения. Ключи сохраняются.
     *
     * ```
     * Cf::from([1,1,2])->unique();
     * // [0 => 1, 2 => 2]
     * ```
     *
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-unique.php Php.net - array_unique
     * @see Chain::unique()
     * @see Func::unique()
     */
    public function unique(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'unique']);
    }

    public ChainFuncUnique $unique;


    /**
     * Параметры callback функции - `$result`, `$element`, `$key`.
     *
     * ```
     * Cf::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0);
     * // 6
     *
     * Cf::from(['a' => 1, 'b' => 2])->reduce(fn(array $res, int $item, string $key) => [...$res, $key, $item]);
     * // [ 'a', 1, 'b', 2]
     * ```
     *
     * @return array|mixed
     *
     * @see ChainFunc::reduce()
     * @see Func::reduce()
     */
    public function reduce(callable $callback, $startVal = [])
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reduce'], $callback, $startVal);
    }

    /**
     * Пройти по массиву и выполнить функцию. Не предназначен для изменения массива, только для сайд-эффектов.
     *
     * ```
     * Cf::from([1, 2, 3])->each(fn(int $item, string $key) => echo $key . $item);
     * // [1, 2, 3]
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see Chain::each()
     * @see Func::each()
     */
    public function each(callable $callback): array
    {
        Func::each($this->array, $callback);

        return $this->array;
    }

    /**
     * Получить количество элементов.
     *
     * ```
     * Cf::from([1, 2, 3])->count();
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
     * Cf::from($arr)->elems->elems->count();
     * // $arr = [
     * //  'a.a' => [
     * //    'a.a.a' => 3
     * //    'a.a.b' => 2
     * //  ]
     * // ];
     * ```
     *
     * @return int|array
     *
     * @see Chain::count()
     * @see Func::count()
     */
    public function count()
    {
        if ($this->elemsLevel == 0) {
            return Func::count($this->array);
        }

        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'count']);
    }

    /**
     * Добавить элементы в конец массива. Элементы добавляются как есть.
     *
     * ```
     * Cf::from([1,2])->append(3, 4);
     * // [1, 2, 3, 4]
     *
     * Cf::from([1,2])->append([3, 4]);
     * // [1, 2, [3, 4]]
     * ```
     * @param mixed ...$added
     * @return array
     * @see Chain::append()
     * @see Func::append()
     *
     */
    public function append(...$added): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'append'], ...$added);
    }

    public ChainFuncAppend $append;

    /**
     * Добавить элементы в начало коллекции.
     *
     * ```
     * Cf::from([3, 4])->prepend(1, 2);
     * // [1, 2, 3, 4]
     * ```
     *
     * @param mixed ...$added
     * @return array
     */
    public function prepend(...$added): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'prepend'], ...$added);
    }

    public ChainFuncPrepend $prepend;


    public ChainFuncFilter $filter;

    /**
     * Оставить элементы коллекции, для которых $callback вернёт true. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`
     *
     * ```
     * Cf::from([1, 2, 3])->filter(fn(int $item) => $item > 2);
     * // [2 => 3]
     *
     * Cf::from(['a' => 1, 'b' => 2, 'c' => 3])->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b' );
     * // ['c' => 3]
     * ````
     *
     * @param callable $callback
     * @return array
     *
     * @see Chain::filter()
     * @see Func::filter()
     */
    public function filter(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'filter'], $callback);
    }


    /**
     * Найти первое значение, для которого callback функция вернёт `true`.
     *
     * Параметры callback функции - `$element`, `$key`.
     * ```
     * Cf::from(['a' => 1, 'b' => 2])->find(fn(int $item, string $key) => $item == 1);
     * // 1
     *
     * Cf::from(['a' => 1, 'b' => 2])->find(fn(int $item, string $key) => $key == 'a');
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
     *   ],
     *   'b' => [5, 6]
     * ];
     * Cf::from($arr)->elems->elems->find(fn(int $item) => $item == 1);
     * // [
     * //   'a' => [
     * //     'a.a' => 1
     * //   ]
     * //   'b' => null
     * // ]
     * ```
     *
     * @param callable $callback
     * @return mixed
     *
     * @see Chain::find()
     * @see Func::find()
     */
    public function find(callable $callback)
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'find'], $callback);
    }


    public ChainFuncGroup $group;

    /**
     * Сгруппировать элементы на основе значений, которые вернёт callback функция.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->group(fn(int $item) => $item > 3 ? 'more' : 'less');
     * // [
     * //   'less' => [1, 2, 3],
     * //   'more' => [4,5]
     * // ]
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see Chain::group()
     * @see Func::group()
     */
    public function group(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'group'], $callback);
    }


    /**
     * Сортировка массива. Используется функция usort.
     *
     * ```
     * Cf::from([3, 1, 2])->sort(fn(int $a, int $b) => $a <=> $b);
     * // [1, 2, 3]
     * ```
     *
     * Сортировка строк:
     * ```
     * $arr = [1, 3, 11, 2];
     *
     * Cf::from($arr)->sort(fn(int $a, int $b) => strcmp($a, $b));
     * // [1, 11, 2, 3]
     *
     * Cf::from($arr)->sort(fn(int $a, int $b) => strnatcmp($a, $b));
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
     * @param callable $callback
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.usort.php Php.net - функция usort
     * @link https://www.php.net/manual/ru/ref.strings.php Php.net - методы строк (в том числе компараторы)
     * @see Chain::sort()
     * @see Func::sort()
     */
    public function sort(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'sort'], $callback);
    }

    /**
     * Очистить массив.
     *
     * ```
     * Cf::from([1, 2, 3])->clear()->toArray();
     * // []
     *
     * $arr = [
     *   'a' => [1,2],
     *   'b' => [3,4]
     * ]
     * Cf::from($arr)->elems->clear();
     * // [
     * //   'a' => [],
     * //   'b' => []
     * // ]
     * ```
     *
     * @return mixed
     *
     * @see Chain::clear()
     */
    public function clear(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'clear']);
    }

    /**
     * Меняет местами ключи с их значениями в массиве. Повторяющиеся ключи будут молча перезаписаны. Если значение не является корректным ключом (`string` или `int`), будет выдано предупреждение и данная пара ключ/значение не будет включена в результат.
     *
     * ```
     * Cf::from(['a' => 10, 'b' => 20, 'c' => 30])->flip();
     * // ['10' => 'a', '20' => 'b', '30' => 'c'];
     * ```
     *
     * @return mixed
     *
     * @see Chain::flip()
     * @see Func::flip()
     */
    public function flip(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flip']);
    }

    /**
     * Извлекает и возвращает первое значение массива array, сокращает массив array на один элемент и сдвигает остальные элементы в начало. Числовые ключи массива изменятся так, чтобы нумерация начиналась с нуля, тогда как литеральные ключи не изменятся.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $cf = Cf::from([1, 2, 3]);
     *
     * $cf->shift();
     * // 1
     *
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
     * $cf = Cf::from($arr);
     *
     * $cf->elems->elems->shift();
     * // [1, 4];
     *
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => [2, 3],
     * //     'a.b' => [5, 6],
     * //   ]
     * // ],
     * ```
     *
     * @return mixed
     *
     * @link https://www.php.net/manual/ru/function.array-shift.php Php.net - array_shift
     * @see Chain::shift()
     * @see Func::shift()
     */
    public function shift()
    {
        if ($this->elemsLevel == 0) {
            return Func::shift($this->array);
        }

        $store = [];

        return ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'shift']);
    }

    /**
     * Извлекает и возвращает последнее значение массива array, сокращает массив array на один элемент.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $cf = Cf::from([1, 2, 3]);
     *
     * $cf->pop();
     * Func::pop($arrFunc);
     * // 3
     *
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
     * $cf = Cf::from($arr);
     *
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
     *
     * @return mixed
     *
     * @link https://www.php.net/manual/ru/function.array-pop.php Php.net - array_shift
     * @see Chain::pop()
     * @see Func::pop()
     */
    public function pop()
    {
        if ($this->elemsLevel == 0) {
            return Func::pop($this->array);
        }

        $store = [];

        return ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'pop']);
    }

    /**
     * Удаляет часть массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $cf = Cf::from($arr);
     *
     * $ch->splice(2, 1, 'item');
     * // [3]
     *
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
     * $cf = Cf::from($arr);
     *
     * $cf->elems->elems->splice(2, 1, 'item');
     * // [
     * //   [3],
     * //   [7],
     * // ]
     *
     * $cf->toArray();
     * // [
     * //   'a' => [
     * //     'a.a' => [1, 2, 'item', 4],
     * //     'a.b' => [5, 6, 'item', 8],
     * //   ]
     * // ]
     * ```
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-splice.php Php.net - array_splice
     * @see Chain::splice()
     * @see Func::splice()
     */
    public function splice(int $offset, ?int $length = null, $replacement = []): array
    {
        if ($this->elemsLevel == 0) {
            return Func::splice($this->array, $offset, $length, $replacement);
        }

        $store = [];

        return ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'splice'], $offset, $length, $replacement);
    }

    public ChainFuncSplice $splice;


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
     * Cf::from($arr)->slice(1, 2);
     * // [1, 2]
     *
     * Cf::from($arr)->slice(1, 2, true);
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @return mixed
     *
     * @link https://www.php.net/manual/ru/function.array-slice.php Php.net - array_slice
     * @see Chain::slice()
     * @see Func::slice()
     */
    public function slice(int $offset, ?int $length = null, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'slice'], $offset, $length, $isPreserveKeys);
    }

    public ChainFuncSlice $slice;


    /**
     * Заменяет элементы массива элементами других массивов.
     *
     * ```
     * Cf::from([1, 2, 3, 4, 5])->replace([6, 7], [4 => 8]);
     * // [6, 7, 3, 4, 8];
     * ```
     *
     * @param array ...$replacement
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-replace.php Php.net - array_replace
     * @see Chain::replace()
     * @see Func::replace()
     */
    public function replace(array ...$replacement): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'replace'], ...$replacement);
    }

    public ChainFuncReplace $replace;


    /**
     * Уменьшить вложенность массива на величину `depth`.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Cf::from($arr)->flatten();
     * // [1, 2, 3, [4, [5]]];
     *
     * Cf::from($arr)->flatten(2);
     * // [1, 2, 3, 4, [5]];
     * ```
     *
     * @param int $depth
     * @return mixed
     *
     * @see Chain::flatten()
     * @see Func::flatten()
     */
    public function flatten(int $depth = 1): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flatten'], $depth);
    }

    public ChainFuncFlatten $flatten;


    /**
     * Дополняет массив значением до заданной длины. Если `length` больше нуля, то добавляет в конец массива, если меньше - в начало.
     *
     * ```
     * Cf::from([1, 2])->pad(5, 0);
     * // [1, 2, 0, 0, 0]
     *
     * Cf::from([1, 2])->pad(-5, 0);
     * // [0, 0, 0, 1, 2]
     * ```
     *
     * @param int $length
     * @param mixed $value
     * @return array
     *
     * @link https://www.php.net/manual/ru/function.array-pad.php Php.net - array_pad
     * @see Chain::pad()
     * @see Func::pad()
     */
    public function pad(int $length, $value): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'pad'], $length, $value);
    }

    /**
     * Получить элемент к ключом `$key`. Если такого элемента нет - вернётся `null`
     *
     * ```
     * Cf::from([1, 2, 3])->get(1);
     * // 2
     *
     * Cf::from([1, 2, 3])->get(10);
     * // null
     * ```
     *
     * @param string|int $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->elemsLevel == 0) {
            return Func::get($this->array, $key);
        }

        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'get'], $key);
    }

    public ChainFuncGet $get;
}