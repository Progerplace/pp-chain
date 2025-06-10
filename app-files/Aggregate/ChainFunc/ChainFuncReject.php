<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncReject
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Подробности {@see Func::rejectEmpty}
     *
     * @return array
     */
    public function empty(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectEmpty']);
    }

    /**
     * Подробности {@see Func::rejectNull}
     *
     * @return array
     */
    public function null(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectNull']);
    }

    /**
     * Подробности {@see Func::rejectKeys}
     *
     * @param string|int ...$keys
     * @return array
     */
    public function keys(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectKeys'], ...$keys);
    }

    /**
     * Подробности {@see Func::rejectValues()}
     *
     * @param mixed ...$values
     * @return array
     */
    public function values(string ...$values): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'rejectValues'], ...$values);
    }
}