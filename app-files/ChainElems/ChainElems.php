<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\ChainElems\Methods\CaseKey;
use Ru\Progerplace\Chain\ChainElems\Methods\Json;

class ChainElems extends Method
{
    protected array $array;
    protected Chain $chain;

    public Json    $json;
    public CaseKey $caseKey;

    protected function initFields()
    {
        $this->json = new Json($this->chain, $this->array);
        $this->caseKey = new CaseKey($this->chain, $this->array);
    }

    /**
     * @param callable $callback fn($item, $key): mixed
     *
     * @return Chain
     */
    public function map(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::map($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn(mixed $item, mixed $key): bool
     *
     * @return Chain
     */
    public function filter(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::filter($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn(mixed $item, mixed $key): bool
     *
     * @return Chain
     */
    public function reject(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::reject($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn($res, $item, $key): mixed
     *
     * @return Chain
     */
    public function find(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::find($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn($res, $item, $key): Chain|mixed
     * @param mixed    $startVal
     *
     * @return Chain
     */
    public function reduce(callable $callback, $startVal = []): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::reduce($this->array[$keyElem], $callback, $startVal);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn($item, $key): string|int
     *
     * @return Chain
     */
    public function fillKeys(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::fillKeys($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    /************************************************/
    /* Обёртки для стандартных функций
    /************************************************/

    /**
     * @return Chain
     */
    public function keys(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = array_keys($this->array[$keyElem]);
        }

        return $this->chain;
    }

    /**
     * @return Chain
     */
    public function values(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = array_values($this->array[$keyElem]);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback fn($a, $b): int
     *
     * @return Chain
     */
    public function sort(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            usort($this->array[$keyElem], $callback);
        }

        return $this->chain;
    }

    public function reverse(bool $isPreserveKeys = false): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = array_reverse($this->array[$keyElem], $isPreserveKeys);
        }

        return $this->chain;
    }

    public function column($field): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = array_column($this->array[$keyElem], $field);
        }

        return $this->chain;
    }

    public function unique(): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = array_unique($this->array[$keyElem]);
        }

        return $this->chain;
    }

    /************************************************/
    /* Возвращающие не массив для элементов
    /************************************************/

    public function count(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = count($this->array[$keyElem]);
        }

        return $this->chain;
    }

    public function isEmpty(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = empty($this->array[$keyElem]);
        }

        return $this->chain;
    }
}