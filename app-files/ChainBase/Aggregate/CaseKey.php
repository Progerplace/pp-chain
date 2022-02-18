<?php

namespace Ru\Progerplace\Chain\ChainBase\Aggregate;

use Ru\Progerplace\Chain\ChainBase\Chain;
use Ru\Progerplace\Chain\ChainFunc\ChainFunc;
use Ru\Progerplace\Chain\Method;

class CaseKey extends Method
{
    public function toLower(): Chain
    {
        $this->array = ChainFunc::$caseKey::toLower($this->array);

        return $this->chain;
    }

    public function toUpper(): Chain
    {
        $this->array = ChainFunc::$caseKey::toUpper($this->array);

        return $this->chain;
    }

    public function snakeToCamel(): Chain
    {
        $this->array = ChainFunc::$caseKey::snakeToCamel($this->array);

        return $this->chain;
    }

    public function camelToSnake(): Chain
    {
        $this->array = ChainFunc::$caseKey::camelToSnake($this->array);

        return $this->chain;
    }
}