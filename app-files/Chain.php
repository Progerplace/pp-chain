<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\Aggregate\Chain\ChainAppend;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainChunk;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainFilter;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainFlatten;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainGet;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainGroup;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainIs;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainJson;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainKeys;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainMath;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainOuter;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainPrepend;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainReject;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainReplace;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainSlice;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainSplice;
use Ru\Progerplace\Chain\Aggregate\Chain\ChainUnique;
use Ru\Progerplace\Chain\Utils\ArrayAction;

/**
 * @property Chain elems
 */
class Chain
{
    protected int   $elemsLevel = 0;
    protected array $array      = [];

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Создание
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct(array $array)
    {
        $this->array = $array;
        $this->reject = new ChainReject($this->array, $this);
        $this->json = new ChainJson($this->array, $this);
        $this->filter = new ChainFilter($this->array, $this);
        $this->keys = new ChainKeys($this->array, $this);
        $this->is = new ChainIs($this->array, $this);
        $this->group = new ChainGroup($this->array, $this);
        $this->chunk = new ChainChunk($this->array, $this);
        $this->outer = new ChainOuter($this->array, $this);
        $this->slice = new ChainSlice($this->array, $this);
        $this->flatten = new ChainFlatten($this->array, $this);
        $this->append = new ChainAppend($this->array, $this);
        $this->prepend = new ChainPrepend($this->array, $this);
        $this->splice = new ChainSplice($this->array, $this);
        $this->replace = new ChainReplace($this->array, $this);
        $this->get = new ChainGet($this->array, $this);
        $this->unique = new ChainUnique($this->array, $this);
        $this->math = new ChainMath($this->array, $this);
    }

    public ChainJson  $json;
    public ChainIs    $is;
    public ChainChunk $chunk;
    public ChainOuter $outer;
    public ChainMath  $math;


    public static function from(?iterable $var, $default = []): self
    {
        if (is_null($var)) {
            return new static($default);
        }

        if (is_array($var)) {
            return new static($var);
        }

        $array = iterator_to_array($var);

        return new static($array);
    }

    public static function fromJson(string $json): self
    {
        $array = json_decode($json, true);

        return new static($array);
    }

    public static function fromString(string $str, string $delimiter): self
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

    public function toJson(): string
    {
        return json_encode($this->array);
    }

    function toString(string $delimiter = ''): string
    {
        return implode($delimiter, $this->array);
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

    public function __call(string $name, array $args)
    {
        if ($name === 'resetElemsLevel') {
            $this->elemsLevel = 0;
        }

        return null;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Операции над всей коллекцией
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function replaceWith(array $arr): self
    {
        $this->array = $arr;

        return $this;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Изменение элементов
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Изменить элементы коллекции. Ключи сохраняются.
     *
     * Параметры callback - `$element`, `$key`
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->map(fn(int $item, string $key) => $key . $item)->toArray();
     * // ['a' => 'a1', 'b' => 'b2']
     *
     * Ch::from([1, 2, 3]))->map(fn(int $item) => $item + 5)->toArray();
     * // [6, 7, 8]
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-map.php Php.net - array_map
     * @see Chain::map()
     * @see Func::map()
     */
    public function map(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'map'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    public ChainReject $reject;

    /**
     * Убрать элементы из коллекции, для которых функция $callback вернула `true`. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * Ch::from([1, 2, 3, 4, 5])->reject(fn(int $item) => $item < 4)->toArray();
     * // [3 => 4, 4 => 5]
     *
     * Ch::from(['a' => null, 'b' => 'foo', 'c' => ''])->reject(fn(?string $item, string $key) => $key === 'a' || $item === 'foo')->toArray();
     * // ['c' => '']
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFunc::reject()
     * @see Func::reject()
     */
    public function reject(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reject'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Оставить только значения массива.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->values()->toArray()
     * // [1, 2, 3]
     * ```
     *
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-values.php Php.net - array_values
     * @see ChainFunc::values()
     * @see Func::values()
     */
    public function values(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'values']);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Элементы коллекции в обратном порядке.
     *
     * Если `$preserveNumericKeys` установлено в `true`, то числовые ключи будут сохранены. Нечисловые ключи не подвержены этой опции и всегда сохраняются.
     *
     * ```
     * Ch::from([1, 2, 3])->reverse()->toArray();
     * // [3, 2, 1]
     *
     * Ch::from([1, 2, 3], true)->reverse()->toArray();
     * [2 => 3, 1 => 2, 0 => 1]
     * ```
     *
     * @param bool $isPreserveNumericKeys = false
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-reverse.php Php.net - array_reverse
     * @see ChainFunc::reverse()
     * @see Func::reverse()
     */
    public function reverse(bool $isPreserveNumericKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reverse'], $isPreserveNumericKeys);
        $this->elemsLevel = 0;

        return $this;
    }


    public ChainKeys $keys;

    /**
     * Возвращает массив ключей.
     *
     * ```
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->keys()->toArray();
     * // ['a', 'b', 'c']
     * ```
     *
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-keys.php Php.net - array_keys
     * @see ChainFunc::keys()
     * @see Func::keys()
     */
    public function keys(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'keys']);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Удалить повторяющиеся значения. Ключи сохраняются.
     *
     * ```
     * Ch::from([1,1,2])->unique()->toArray();
     * // [0 => 1, 2 => 2]
     * ```
     *
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-unique.php Php.net - array_unique
     * @see ChainFunc::unique()
     * @see Func::unique()
     */
    public function unique(): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'unique']);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainUnique $unique;

    /**
     * Параметры callback функции - `$result`, `$element`, `$key`.
     *
     * ```
     * Ch::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0);
     * // 6
     *
     * Ch::from(['a' => 1, 'b' => 2])->reduce(fn(array $res, int $item, string $key) => [...$res, $key, $item])->toArray();
     * // [ 'a', 1, 'b', 2]
     * ```
     *
     * Если аргумент `$startVal` имеет тип `array`, то `Chain->reduce` вернёт `Chain` и цепочку можно продолжить. В ином случае вернётся само значение.
     * ```
     * Ch::from([1, 2, 3])->reduce(fn(int $res, int $item) => $res + $item, 0)
     * // 0
     * Ch::from([1, 2, 3])->reduce(fn(int $res, int $item) => [...$res, $item])->toArray()
     * // [1, 2, 3]
     * ```
     *
     * @param callable $callback
     * @param array|mixed $startVal
     * @return Chain|mixed
     *
     * @see ChainFunc::reduce()
     * @see Func::reduce()
     */
    public function reduce(callable $callback, $startVal = [])
    {
        if (!is_array($startVal)) {
            return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reduce'], $callback, $startVal);
        }

        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reduce'], $callback, $startVal);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Пройти по массиву и выполнить функцию. Не предназначен для изменения массива, только для сайд-эффектов.
     *
     * ```
     * Ch::from([1, 2, 3])->each(fn(int $item, string $key) => echo $key . $item)->toArray();
     * // [1, 2, 3]
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFunc::each()
     * @see Func::each()
     */
    public function each(callable $callback): Chain
    {
        Func::each($this->array, $callback);

        return $this;
    }

    /**
     * Получить количество элементов.
     *
     * ```
     * Ch::from([1, 2, 3])->count();
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
     * Ch::from($arr)->elems->elems->count()->toArray();
     * // $arr = [
     * //  'a.a' => [
     * //    'a.a.a' => 3
     * //    'a.a.b' => 2
     * //  ]
     * // ];
     * ```
     *
     * @return int|Chain
     *
     * @see ChainFunc::count()
     * @see Func::count()
     */
    public function count()
    {
        if ($this->elemsLevel == 0) {
            return Func::count($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'count']);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Добавить элементы в конец массива. Элементы добавляются как есть.
     *
     * ```
     * Ch::from([1,2])->append(3, 4)->toArray();
     * // [1, 2, 3, 4]
     *
     * Ch::from([1,2])->append([3, 4])->toArray();
     * // [1, 2, [3, 4]]
     * ```
     * @param mixed ...$added
     * @return Chain
     * @see ChainFunc::append()
     * @see Func::append()
     *
     */
    public function append(...$added): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'append'], ...$added);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainAppend $append;

    /**
     * Добавить элементы в начало коллекции.
     *
     * ```
     * Ch::from([3, 4])->prepend(1, 2)->toArray();
     * // [1, 2, 3, 4]
     * ```
     *
     * @param mixed ...$added
     * @return Chain
     *
     * @see ChainFunc::prepend()
     * @see Func::prepend()
     */
    public function prepend(...$added): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'prepend'], ...$added);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainPrepend $prepend;


    public ChainFilter $filter;

    /**
     * Оставить элементы коллекции, для которых $callback вернёт true. Ключи сохраняются.
     *
     * Параметры callback функции - `$element`, `$key`
     *
     * ```
     * Ch::from([1, 2, 3])->filter(fn(int $item) => $item > 2)->toArray();
     * // [2 => 3]
     *
     * Ch::from(['a' => 1, 'b' => 2, 'c' => 3])->filter(fn(int $item, string $key) => $item > 1 && $key !== 'b' )->toArray();
     * // ['c' => 3]
     * ````
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFunc::filter()
     * @see Func::filter()
     */
    public function filter(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'filter'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Найти первое значение, для которого callback функция вернёт `true`.
     *
     * Параметры callback функции - `$element`, `$key`.
     * ```
     * Ch::from(['a' => 1, 'b' => 2])->find(fn(int $item, string $key) => $item == 1);
     * // 1
     *
     * Ch::from(['a' => 1, 'b' => 2])->find(fn(int $item, string $key) => $key == 'a');
     * // 1
     * ```
     * Для дочерних элементов:
     * ```
     * $arr = [
     *   'a' => [
     *     'a.a' => [
     *       'a.a.a' => 1,
     *       'a.a.b' => 2
     *     ]
     *   ],
     *   'b' => [5, 6]
     * ];
     * Ch::from($arr)->elems->elems->find(fn(int $item) => $item == 1);
     * // [
     * //   'a' => [
     * //     'a.a' => 1
     * //   ]
     * //   'b' => null
     * // ]
     * ```
     *
     * @param callable $callback
     * @return mixed|Chain
     *
     * @see ChainFunc::find()
     * @see Func::find()
     */
    public function find(callable $callback)
    {
        if ($this->elemsLevel == 0) {
            return Func::find($this->array, $callback);
        }

        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'find'], $callback);
    }


    public ChainGroup $group;

    /**
     * Сгруппировать элементы на основе значений, которые вернёт callback функция.
     *
     * Параметры callback функции - `$element`, `$key`.
     *
     * ```
     * Ch::from([1, 2, 3, 4, 5])->group(fn(int $item) => $item > 3 ? 'more' : 'less')->toArray();
     * // [
     * //   'less' => [1, 2, 3],
     * //   'more' => [4,5]
     * // ]
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFunc::group()
     * @see Func::group()
     */
    public function group(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'group'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Сортировка массива. Используется функция usort.
     *
     * ```
     * Ch::from([3, 1, 2])->sort(fn(int $a, int $b) => $a <=> $b)->toArray();
     * // [1, 2, 3]
     * ```
     *
     * Сортировка строк:
     * ```
     * $arr = [1, 3, 11, 2];
     *
     * Ch::from($arr)->sort(fn(int $a, int $b) => strcmp($a, $b));
     * // [1, 11, 2, 3]
     *
     * Ch::from($arr)->sort(fn(int $a, int $b) => strnatcmp($a, $b));
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
     * @return mixed
     *
     * @link https://www.php.net/manual/ru/function.usort.php Php.net - функция usort
     * @link https://www.php.net/manual/ru/ref.strings.php Php.net - методы строк (в том числе компараторы)
     * @see ChainFunc::sort()
     * @see Func::sort()
     */
    public function sort(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'sort'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Очистить массив.
     *
     * ```
     * Ch::from([1, 2, 3])->clear()->toArray();
     * // []
     *
     * $arr = [
     *   'a' => [1,2],
     *   'b' => [3,4]
     * ]
     * Ch::from($arr)->elems->clear();
     * // [
     * //   'a' => [],
     * //   'b' => []
     * // ]
     * ```
     *
     * @return Chain
     *
     * @see ChainFunc::clear()
     */
    public function clear(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'clear']);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Меняет местами ключи с их значениями в массиве. Повторяющиеся ключи будут молча перезаписаны. Если значение не является корректным ключом (`string` или `int`), будет выдано предупреждение и данная пара ключ/значение не будет включена в результат.
     *
     * ```
     * Ch::from(['a' => 10, 'b' => 20, 'c' => 30])->flip()->toArray();
     * // ['10' => 'a', '20' => 'b', '30' => 'c'];
     * ```
     *
     * @return Chain
     *
     * @see ChainFunc::flip()
     * @see Func::flip()
     */
    public function flip(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flip']);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Извлекает и возвращает первое значение массива array, сокращает массив array на один элемент и сдвигает остальные элементы в начало. Числовые ключи массива изменятся так, чтобы нумерация начиналась с нуля, тогда как литеральные ключи не изменятся.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $ch = Ch::from([1, 2, 3]);
     *
     * $ch->shift();
     * // 1
     *
     * $ch->toArray();
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
     * $ch = Ch::from($arr);
     *
     * $ch->elems->elems->shift();
     * // [1, 4];
     *
     * $ch->toArray();
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
     * @see ChainFunc::shift()
     * @see Func::shift()
     */
    public function shift()
    {
        if ($this->elemsLevel == 0) {
            return Func::shift($this->array);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'shift']);
        $this->elemsLevel = 0;

        return $res;
    }

    /**
     * Извлекает и возвращает последнее значение массива array, сокращает массив array на один элемент.
     *
     * Функция возвращает извлечённое значение или null, если массив array оказался пустым.
     *
     * ```
     * $ch = Ch::from([1, 2, 3]);
     *
     * $ch->pop();
     * // 3
     *
     * $ch->toArray();
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
     * $ch = Ch::from($arr);
     *
     * $ch->elems->elems->pop();
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
     * @see ChainFunc::pop()
     * @see Func::pop()
     */
    public function pop()
    {
        if ($this->elemsLevel == 0) {
            return Func::pop($this->array);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'pop']);
        $this->elemsLevel = 0;

        return $res;
    }


    /**
     * Удаляет часть массива и заменяет её новыми элементами
     *
     * ```
     * $arr = [1, 2, 3, 4];
     * $ch = Ch::from($arr);
     *
     * $ch->splice(2, 1, 'item');
     * // [3]
     *
     * $ch->toArray();
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
     * $ch = Ch::from($arr);
     *
     * $ch->elems->elems->splice(2, 1, 'item');
     * // [
     * //   [3],
     * //   [7],
     * // ]
     *
     * $ch->toArray();
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
     * @return Chain|array
     *
     * @link https://www.php.net/manual/ru/function.array-splice.php Php.net - array_splice
     * @see ChainFunc::splice()
     * @see Func::splice()
     */
    public function splice(int $offset, ?int $length = null, $replacement = [])
    {
        if ($this->elemsLevel == 0) {
            return Func::splice($this->array, $offset, $length, $replacement);
        }

        $store = [];
        $res = ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'splice'], $offset, $length, $replacement);
        $this->elemsLevel = 0;

        return $res;
    }

    public ChainSplice $splice;


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
     * Ch::from($arr)->slice(1, 2)->toArray();
     * Func::slice($arr, 1, 2);
     * // [1, 2]
     *
     * Ch::from($arr)->slice(1, 2, true)->toArray();
     * // [10 => 1, 11 => 2]
     * ```
     *
     * @param int $offset
     * @param int|null $length
     * @param bool $isPreserveKeys
     * @return mixed
     *
     * @link https://www.php.net/manual/ru/function.array-slice.php Php.net - array_slice
     * @see ChainFunc::slice()
     * @see Func::slice()
     */
    public function slice(int $offset, ?int $length = null, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'slice'], $offset, $length, $isPreserveKeys);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainSlice $slice;


    /**
     * Заменяет элементы массива элементами других массивов.
     *
     * ```
     * Ch::from([1, 2, 3, 4, 5])->replace([6, 7], [4 => 8])->toArray();
     * // [6, 7, 3, 4, 8];
     * ```
     *
     * @param array ...$replacement
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-replace.php Php.net - array_replace
     * @see ChainFunc::replace()
     * @see Func::replace()
     */
    public function replace(array ...$replacement): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'replace'], ...$replacement);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainReplace $replace;


    /**
     * Уменьшить вложенность массива на величину `depth`.
     *
     * ```
     * $arr = [1, [2], [3, [4, [5]]]];
     *
     * Ch::from($arr)->flatten()->toArray();
     * // [1, 2, 3, [4, [5]]];
     *
     * Ch::from($arr)->flatten(2)->toArray();
     * // [1, 2, 3, 4, [5]];
     * ```
     *
     * @param int $depth
     * @return Chain
     *
     * @see ChainFunc::flatten()
     * @see Func::flatten()
     */
    public function flatten(int $depth = 1): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flatten'], $depth);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainFlatten $flatten;

    /**
     * Дополняет массив значением до заданной длины. Если `length` больше нуля, то добавляет в конец массива, если меньше - в начало.
     *
     * ```
     * Ch::from([1, 2])->pad(5, 0)->toArray();
     * // [1, 2, 0, 0, 0]
     *
     * Ch::from([1, 2])->pad(-5, 0)->toArray();
     * // [0, 0, 0, 1, 2]
     * ```
     *
     * @param int $length
     * @param mixed $value
     * @return Chain
     *
     * @link https://www.php.net/manual/ru/function.array-pad.php Php.net - array_pad
     * @see ChainFunc::pad()
     * @see Func::pad()
     */
    public function pad(int $length, $value): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'pad'], $length, $value);
        $this->elemsLevel = 0;

        return $this;
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

        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'get'], $key);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainGet $get;
}