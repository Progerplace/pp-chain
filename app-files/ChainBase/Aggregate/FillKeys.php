<?php

namespace Ru\Progerplace\Chain\ChainBase\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;

class FillKeys extends Method
{
    /**
     * @param string|int $field
     *
     * @return Chain
     */
    public function fromField($field): Chain
    {
        $this->array = ChainFunc::$fillKeys::fromField($this->array, $field);

        return $this->chain;
    }
}