<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Utils\ArrayAction;
use Ru\Progerplace\Chain\Utils\MathAction;

class ChainFuncMathAction
{
    protected array  $array;
    protected ChainFunc  $chain;
    protected string $action;

    public function __construct(array &$array, ChainFunc &$chain, string $action)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->action = $action;
    }

    /**
     * @param callable $callback
     * @return mixed|array
     */
    public function by(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return MathAction::by($this->array, $this->action, $callback);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [MathAction::class, 'by'], $this->action, $callback);
    }

    /**
     * @param $field
     * @return mixed|array
     */
    public function byField($field)
    {
        if ($this->chain->elemsLevel == 0) {
            return MathAction::byField($this->array, $this->action, $field);
        }

        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [MathAction::class, 'byField'], $this->action, $field);
    }
}