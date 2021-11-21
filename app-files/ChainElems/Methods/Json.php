<?php

namespace Ru\Progerplace\Chain\ChainElems\Methods;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Method;
use Ru\Progerplace\Chain\Methods\Json as JsonChain;
use Ru\Progerplace\Chain\Utils;

class Json extends Method
{
    protected JsonChain $jsonChain;

    protected function initFields()
    {
        $this->jsonChain = new JsonChain($this->chain, $this->array);
    }

    /**
     * @param string|string[] ...$fieldsNames
     *
     * @return Chain
     */
    public function encodeFields(...$fieldsNames): Chain
    {
        $fieldsNames = Utils::argumentsAsArray($fieldsNames);

        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = Chain::fromArray($this->array[$keyElem])->json->encodeFields($fieldsNames)->array;
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
            $this->array[$keyElem] = Chain::fromArray($this->array[$keyElem])->json->encodeBy($callback)->array;
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
            $this->array[$keyElem] = Chain::fromArray($this->array[$keyElem])->json->decodeFields($fieldsNames)->array;
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
            $this->array[$keyElem] = Chain::fromArray($this->array[$keyElem])->json->decodeBy($callback)->array;
        }

        return $this->chain;
    }
}