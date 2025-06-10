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
use Ru\Progerplace\Chain\Aggregate\Chain\ChainValues;
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
        $this->values = new ChainValues($this->array, $this);
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
     *
     * Подробности {@see Func::map}
     *
     * ---
     *
     * ```
     * Chain::from(['a' => 1, 'b' => 2])->map(fn(int $item, string $key) => $key . $item)
     * // ['a' => 'a1', 'b' => 'b2']
     *
     * ChainFunc::from([1,2,3]))->map(fn(int $item) => $item + 5);
     * Chain::from([1,2,3]))->map(fn(int $item) => $item + 5)->toArray();
     * Func::map([1,2,3], fn(int $item) => $item + 5);
     * // [6,7,8]
     * ```
     * @param callable $callback
     * @return Chain
     */
    public function map(callable $callback): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'map'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    public ChainReject $reject;

    /**
     * Подробности {@see Func::reject}
     *
     * @param callable $callback
     * @return Chain
     */
    public function reject(callable $callback): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reject'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    public ChainValues $values;

    /**
     * Подробности {@see Func::values}
     *
     * @return $this
     */
    public function values(): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'values']);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Подробности {@see Func::reverse}
     *
     * @return $this
     */
    public function reverse(): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reverse']);
        $this->elemsLevel = 0;

        return $this;
    }


    public ChainKeys $keys;

    /**
     * Подробности {@see Func::keys())}
     *
     * @return $this
     */
    public function keys(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'keys']);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Подробности {@see Func::unique())}
     *
     * @return $this
     */
    public function unique(): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'unique']);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainUnique $unique;

    /**
     * Подробности {@see Func::reduce())}
     *
     * @param callable $callback
     * @param array|mixed $startVal
     * @return $this|mixed
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
     * Подробности {@see Func::each())}
     *
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback): Chain
    {
        Func::each($this->array, $callback);

        return $this;
    }

    /**
     * Подробности {@see Func::count())}
     *
     * @return int|Chain
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
     * Подробности {@see Func::append())}
     *
     * @param mixed ...$added
     * @return Chain
     */
    public function append(...$added): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'append'], ...$added);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainAppend $append;

    /**
     * Подробности {@see Func::prepend())}
     *
     * @param mixed ...$added
     * @return Chain
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
     * Подробности {@see Func::filter()}
     *
     * @param callable $callback
     * @return Chain
     */
    public function filter(callable $callback): self
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'filter'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Подробности {@see Func::find()}
     *
     * @param callable $callback
     * @return mixed|Chain
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
     * Подробности {@see Func::group()}
     *
     * @param callable $callback
     * @return Chain
     */
    public function group(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'group'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }


    /**
     * Подробности {@see Func::sort()}
     *
     * @param callable $callback
     * @return mixed
     */
    public function sort(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'sort'], $callback);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Подробности {@see Func::clear()}
     *
     * @return mixed
     */
    public function clear(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'clear']);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Подробности {@see Func::flip()}
     *
     * @return Chain
     */
    public function flip(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flip']);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Подробности {@see Func::shift()}
     *
     * @return mixed
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
     * Подробности {@see Func::pop()}
     *
     * @return mixed
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
     * Подробности {@see Func::splice()}
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return Chain|array
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
     * Подробности {@see Func::slice()}
     *
     * @param int $offset
     * @param int|null $length
     * @param bool $isPreserveKeys
     * @return mixed
     */
    public function slice(int $offset, ?int $length = null, bool $isPreserveKeys = false): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'slice'], $offset, $length, $isPreserveKeys);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainSlice $slice;


    public function replace(array ...$replacement): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'replace'], ...$replacement);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainReplace $replace;


    /**
     * Подробности {@see Func::flatten()}
     *
     * @return mixed
     */
    public function flatten(int $depth = 1): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flatten'], $depth);
        $this->elemsLevel = 0;

        return $this;
    }

    public ChainFlatten $flatten;

    /**
     * Подробности {@see Func::pad()}
     *
     * @param int $length
     * @param mixed $value
     * @return Chain
     */
    public function pad(int $length, $value): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'pad'], $length, $value);
        $this->elemsLevel = 0;

        return $this;
    }

    /**
     * Подробности {@see Func::get()}
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