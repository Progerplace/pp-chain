<?php

namespace Ru\Progerplace\Chain\Methods;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
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