<?php

namespace Ru\Progerplace\Chain\ChainBase\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;
use Ru\Progerplace\Chain\Utils;

class Sort extends Method
{
    public function asc(): Chain
    {
        $this->array = ChainFunc::$sort::asc($this->array);

        return $this->chain;
    }

    public function desc(): Chain
    {
        $this->array = ChainFunc::$sort::desc($this->array);

        return $this->chain;
    }

    public function natsort($isCase = false): Chain
    {
        $this->array = ChainFunc::$sort::natsort($this->array, $isCase);

        return $this->chain;
    }
}