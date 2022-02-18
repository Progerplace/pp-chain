<?php

namespace Ru\Progerplace\Chain;

use Ru\Progerplace\Chain\ChainBase\Chain;

abstract class Method
{
    protected array $array;
    protected Chain $chain;

    public function __construct(Chain $chain, array &$array)
    {
        $this->chain = $chain;
        $this->array = &$array;

        $this->initFields();
    }

    protected function initFields()
    {
    }
}