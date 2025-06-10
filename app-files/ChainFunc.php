<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\Aggregate\Chain\ChainPrepend;
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
use Ru\Progerplace\Chain\Aggregate\ChainFunc\ChainFuncValues;
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
        $this->values = new ChainFuncValues($this->array, $this);
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
     * Подробности {@see Func::map}
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'map'], $callback);
    }


    public ChainFuncReject $reject;

    /**
     * Подробности {@see Func::reject}
     *
     * @param callable $callback
     * @return array
     */
    public function reject(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reject'], $callback);
    }


    public ChainFuncValues $values;

    /**
     * Подробности {@see Func::values}
     *
     * @return array
     */
    public function values(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'values']);
    }


    /**
     * Подробности {@see Func::reverse}
     *
     * @return array
     */
    public function reverse(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reverse']);
    }


    public ChainFuncKeys $keys;

    /**
     * Подробности {@see Func::keys()}
     *
     * @return array
     */
    public function keys(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'keys']);
    }


    /**
     * Подробности {@see Func::unique()}
     *
     * @return array
     */
    public function unique(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'unique']);
    }

    public ChainFuncUnique $unique;


    /**
     * Подробности {@see Func::reduce()}
     *
     * @return array|mixed
     */
    public function reduce(callable $callback, $startVal = [])
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'reduce'], $callback, $startVal);
    }

    /**
     * Подробности {@see Func::each())}
     *
     * @param callable $callback
     * @return array
     */
    public function each(callable $callback): array
    {
        Func::each($this->array, $callback);

        return $this->array;
    }

    /**
     * Подробности {@see Func::count())}
     *
     * @return int|array
     */
    public function count()
    {
        if ($this->elemsLevel == 0) {
            return Func::count($this->array);
        }

        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'count']);
    }

    /**
     * Подробности {@see Func::append())}
     *
     * @param mixed ...$added
     * @return array
     */
    public function append(...$added): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'append'], ...$added);
    }

    public ChainFuncAppend $append;

    /**
     * Подробности {@see Func::prepend())}
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
     * Подробности {@see Func::filter()}
     *
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'filter'], $callback);
    }


    /**
     * Подробности {@see Func::find()}
     *
     * @param callable $callback
     * @return mixed
     */
    public function find(callable $callback)
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'find'], $callback);
    }


    public ChainFuncGroup $group;

    /**
     * Подробности {@see Func::group()}
     *
     * @param callable $callback
     * @return array
     */
    public function group(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'group'], $callback);
    }


    /**
     * Подробности {@see Func::sort()}
     *
     * @param callable $callback
     * @return array
     */
    public function sort(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'sort'], $callback);
    }

    /**
     * Подробности {@see Func::clear()}
     *
     * @return mixed
     */
    public function clear(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'clear']);
    }

    /**
     * Подробности {@see Func::flip()}
     *
     * @return mixed
     */
    public function flip(): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flip']);
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

        return ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'shift']);
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

        return ArrayAction::doActionMutableReturn($this->array, $this->elemsLevel, $store, [Func::class, 'pop']);
    }

    /**
     * Подробности {@see Func::splice()}
     *
     * @param int $offset
     * @param int|null $length
     * @param mixed $replacement
     * @return array
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
     * Подробности {@see Func::slice()}
     *
     * @return mixed
     */
    public function slice(int $offset, ?int $length = null, bool $isPreserveKeys = false): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'slice'], $offset, $length, $isPreserveKeys);
    }

    public ChainFuncSlice $slice;


    public function replace(array ...$replacement): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'replace'], ...$replacement);
    }

    public ChainFuncReplace $replace;


    /**
     * Подробности {@see Func::flatten()}
     *
     * @return mixed
     */
    public function flatten(int $depth = 1): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'flatten'], $depth);
    }

    public ChainFuncFlatten $flatten;


    /**
     * Подробности {@see Func::pad()}
     *
     * @param int $length
     * @param mixed $value
     * @return array
     */
    public function pad(int $length, $value): array
    {
        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'pad'], $length, $value);
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

        return ArrayAction::doAction($this->array, $this->elemsLevel, [Func::class, 'get'], $key);
    }

    public ChainFuncGet $get;
}