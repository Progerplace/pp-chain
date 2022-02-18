<?php

namespace Ru\Progerplace\Chain\ChainElems\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;

class FillKeys extends Method
{
    public function fromField($field): Chain
    {
        foreach ($this->array as $keyElem => $item) {
            $this->array[$keyElem] = ChainFunc::$fillKeys::fromField($this->array, $field);
        }

        return $this->chain;
    }
}