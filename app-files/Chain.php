<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\Methods\CaseKey;
use Ru\Progerplace\Chain\Methods\FillKeys;
use Ru\Progerplace\Chain\Methods\Json;

class Chain
{
    public array      $array = [];
    public ChainElems $elems;
    public Json       $json;
    public CaseKey    $caseKey;
    public FillKeys   $fillKeys;


    public function __construct(array $array)
    {
        $this->array = $array;
        $this->elems = new ChainElems($this, $this->array);
        $this->json = new Json($this, $this->array);
        $this->caseKey = new CaseKey($this, $this->array);
        $this->fillKeys = new FillKeys($this, $this->array);
    }

    /************************************************/
    /* Создание
    /************************************************/

    /**
     * @param array $array
     *
     * @return Chain
     */
    public static function fromArray(array $array): Chain
    {
        return new static($array);
    }

    /**
     * @param string $json
     *
     * @return Chain
     */
    public static function fromJson(string $json): Chain
    {
        $array = json_decode($json, true);

        return new static($array);
    }

    /**
     * @param string $delimiter
     * @param string $string
     *
     * @return Chain
     */
    public static function fromString(string $string, string $delimiter): Chain
    {
        $array = explode($delimiter, $string);

        return new static($array);
    }

    /************************************************/
    /* Экспорт
    /************************************************/

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->array);
    }

    function toString(string $delimiter = ''): string
    {
        return implode($delimiter, $this->array);
    }

    /************************************************/
    /* Модификация
    /************************************************/

    /**
     * Обёртка для array_map. Применяет callback-функцию ко всем элементам.
     *
     * @param callable $callback fn($item, $key): mixed
     *
     * @return Chain
     */
    public function map(callable $callback): Chain
    {
        $this->array = ChainFunc::map($this->array, $callback);

        return $this;
    }

    /**
     * Обёртка для array_keys(). Возвращает ключи массива.
     *
     * @return Chain
     */
    public function keys(): Chain
    {
        $this->array = array_keys($this->array);

        return $this;
    }

    /**
     * @return Chain
     */
    public function values(): Chain
    {
        $this->array = array_values($this->array);

        return $this;
    }

    /**
     * @param callable $callback fn(mixed $item, mixed $key): bool
     *
     * @return $this
     */
    public function filter(callable $callback): Chain
    {
        $this->array = ChainFunc::filter($this->array, $callback);

        return $this;
    }

    /**
     * @param callable $callback fn(mixed $item, mixed $key): bool
     *
     * @return $this
     */
    public function reject(callable $callback): Chain
    {
        $this->array = ChainFunc::reject($this->array, $callback);

        return $this;
    }

    /**
     * Обёртка для usort
     *
     * @param callable $callback fn($a, $b): int
     *
     * @return $this
     */
    public function sort(callable $callback): Chain
    {
        usort($this->array, $callback);

        return $this;
    }

    /**
     * @param callable $callback fn($res, $item, $key): mixed
     * @param mixed    $startVal
     * @param bool     $isChain
     *
     * @return Chain|mixed
     */
    public function reduce(callable $callback, $startVal = [], bool $isChain = true)
    {
        $res = ChainFunc::reduce($this->array, $callback, $startVal);

        return $isChain
            ? Chain::fromArray($res)
            : $res;
    }

    public function reverse(bool $isPreserveKeys = false): Chain
    {
        $this->array = array_reverse($this->array, $isPreserveKeys);

        return $this;
    }

    /**
     * @param callable $callback fn($item, $key): string|int
     *
     * @return $this
     */
    public function fillKeys(callable $callback): Chain
    {
        $this->array = ChainFunc::fillKeys($this->array, $callback);

        return $this;
    }

    /**
     * @param callable $callback fn(mixed $item, string|int $key): string|int
     *
     * @return $this
     */
    public function group(callable $callback): Chain
    {
        $this->array = ChainFunc::group($this->array, $callback);

        return $this;
    }

    public function column($field): Chain
    {
        $this->array = array_column($this->array, $field);

        return $this;
    }

    public function unique(): Chain
    {
        $this->array = array_unique($this->array);

        return $this;
    }

    /************************************************/
    /* Возвращающие не Chain
    /************************************************/

    /**
     * @param callable $callback
     *
     * @return mixed|null
     */
    public function find(callable $callback)
    {
        return ChainFunc::find($this->array, $callback);
    }

    public function count(): int
    {
        return count($this->array);
    }

    public function isEmpty(): bool
    {
        return empty($this->array);
    }
}