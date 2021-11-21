<?php

namespace Ru\Progerplace\Chain\ChainElems\Methods;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Method;

class CaseKey extends Method
{
    public function toLower(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::$caseKey::toLower($this->array[$keyElem]);
        }

        return $this->chain;
    }

    public function toUpper(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::$caseKey::toUpper($this->array[$keyElem]);
        }

        return $this->chain;
    }

    public function snakeToCamel(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::$caseKey::snakeToCamel($this->array[$keyElem]);
        }

        return $this->chain;
    }

    public function camelToSnake(): Chain
    {
        foreach ($this->array as $keyElem => $elem) {
            $this->array[$keyElem] = ChainFunc::$caseKey::camelToSnake($this->array[$keyElem]);
        }

        return $this->chain;
    }
}