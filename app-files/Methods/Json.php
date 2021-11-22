<?php

namespace Ru\Progerplace\Chain\Methods;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Method;
use Ru\Progerplace\Chain\Utils;

class Json extends Method
{
    /**
     * @param string|string[]|int|int[] ...$fields
     *
     * @return Chain
     */
    public function encodeFields(...$fields): Chain
    {
        $fields = Utils::argumentsAsArray($fields);
        $this->array = ChainFunc::$json::encodeFields($this->array, $fields);

        return $this->chain;
    }

    /**
     * @param callable $callback $callback($item, $key): bool
     *
     * @return Chain
     */
    public function encodeBy(callable $callback): Chain
    {
        $this->array = ChainFunc::$json::encodeBy($this->array, $callback);

        return $this->chain;
    }

    public function decodeFields(...$fields): Chain
    {
        $fields = Utils::argumentsAsArray($fields);
        $this->array = ChainFunc::$json::decodeFields($this->array, $fields);

        return $this->chain;
    }

    /**
     * @param callable $callback $callback($item, $key): bool
     *
     * @return Chain
     */
    public function decodeBy(callable $callback): Chain
    {
        $this->array = ChainFunc::$json::decodeBy($this->array, $callback);

        return $this->chain;
    }
}