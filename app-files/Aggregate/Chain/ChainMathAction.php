<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Utils\ArrayAction;
use Ru\Progerplace\Chain\Utils\MathAction;

class ChainMathAction
{
    protected array  $array;
    protected Chain  $chain;
    protected string $action;

    public function __construct(array &$array, Chain &$chain, string $action)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->action = $action;
    }

    /**
     * @param callable $callback
     * @return mixed|Chain
     */
    public function by(callable $callback)
    {
        if ($this->chain->elemsLevel == 0) {
            return MathAction::by($this->array, $this->action, $callback);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [MathAction::class, 'by'], $this->action, $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * @param $field
     * @return mixed|Chain
     */
    public function byField($field)
    {
        if ($this->chain->elemsLevel == 0) {
            return MathAction::byField($this->array, $this->action, $field);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [MathAction::class, 'byField'], $this->action, $field);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}