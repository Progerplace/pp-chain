<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncKeysCase
{
    protected array     $array;
    protected ChainFunc $chain;

    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::keysCaseToCamel()}
     *
     * @return array
     */
    public function toCamel(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToCamel']);
    }

    /**
     * Подробности {@see Func::keysCaseToPaskal()}
     *
     * @return array
     */
    public function toPaskal(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToPaskal']);
    }

    /**
     * Подробности {@see Func::keysCaseToSnake()}
     *
     * @return array
     */
    public function toSnake(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToSnake']);
    }

    /**
     * Подробности {@see Func::keysCaseToKebab()}
     *
     * @return array
     */
    public function toKebab(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToKebab']);
    }

    /**
     * Подробности {@see Func::keysCaseToScreamSnake()}
     *
     * @return array
     */
    public function toScreamSnake(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamSnake']);
    }

    /**
     * Подробности {@see Func::keysCaseToScreamKebab()}
     *
     * @return array
     */
    public function toScreamKebab(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamKebab']);
    }
}