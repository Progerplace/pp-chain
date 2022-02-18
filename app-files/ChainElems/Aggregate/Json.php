<?php

namespace Ru\Progerplace\Chain\ChainElems\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;
use Ru\Progerplace\Chain\Utils;

class Json extends Method
{
    /**
     * @param string|string[] ...$fieldsNames
     *
     * @return Chain
     */
    public function encodeFields(...$fieldsNames): Chain
    {
        $fieldsNames = Utils::argumentsAsArray($fieldsNames);

        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$json::encodeFields($item, $fieldsNames);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback $callback($item, $key): bool
     *
     * @return Chain
     */
    public function encodeBy(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$json::encodeBy($item, $callback);
        }

        return $this->chain;
    }

    /**
     * @param string|string[] ...$fieldsNames
     *
     * @return Chain
     */
    public function decodeFields(...$fieldsNames): Chain
    {
        $fieldsNames = Utils::argumentsAsArray($fieldsNames);

        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$json::decodeFields($item, $fieldsNames);
        }

        return $this->chain;
    }

    /**
     * @param callable $callback $callback($item, $key): bool
     *
     * @return Chain
     */
    public function decodeBy(callable $callback): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$json::decodeBy($item, $callback);
        }

        return $this->chain;
    }
}