<?php

namespace Ru\Progerplace\Chain\ChainElems\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;

class Sort extends Method
{
    public function asc(): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$sort::asc($item);
        }

        return $this->chain;
    }

    public function desc(): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$sort::desc($item);
        }

        return $this->chain;
    }

    public function natsort($isCase = false): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$sort::natsort($item, $isCase);
        }

        return $this->chain;
    }
}