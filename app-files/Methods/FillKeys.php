<?php

namespace Ru\Progerplace\Chain\Methods;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
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