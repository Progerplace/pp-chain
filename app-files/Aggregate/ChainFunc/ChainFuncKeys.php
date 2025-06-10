<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncKeys
{
    protected array     $array;
    protected ChainFunc $chain;


    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->case = new ChainFuncKeysCase($array, $chain);
    }

    /**
     * Подробности {@see Func::keysGetList()}
     *
     * @return array
     */
    public function getList(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetList']);
    }

    public ChainFuncKeysCase $case;

    /**
     * Подробности {@see Func::map()}
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysMap'], $callback);
    }

    /**
     * Подробности {@see Func::keysFromField()}
     *
     * @param string|int $field
     * @return array
     */
    public function fromField($field): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysFromField'], $field);
    }

    /**
     * Подробности {@see Func::keysGet()}
     *
     * @param int $number
     * @return string|int|null|array
     */
    public function get(int $number)
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGet'], $number);
    }

    /**
     * Подробности {@see Func::keysGetFirst()}
     *
     * @return string|int|null|array
     */
    public function getFirst()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetFirst']);
    }

    /**
     * Подробности {@see Func::keysGetLast()}
     *
     * @return string|int|null|array
     */
    public function getLast()
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetLast']);
    }
}