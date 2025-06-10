<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainKeys
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
        $this->case = new ChainKeysCase($array, $chain);
    }

    public ChainKeysCase $case;

    /**
     * Подробности {@see Func::keysGetList()}
     *
     * @return array|Chain
     */
    public function getList()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGetList($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetList']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::keysMap()}
     *
     * @param callable $callback
     * @return Chain
     */
    public function map(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysMap'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::keysFromField()}
     *
     * @param string|int $field
     * @return Chain
     */
    public function fromField($field): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysFromField'], $field);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::keysGet())}
     *
     * @param int $number
     * @return int|string|null|Chain
     */
    public function get(int $number)
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGet($this->array, $number);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGet'], $number);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::keysGetFirst())}
     *
     * @return int|string|null|Chain
     */
    public function getFirst()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGetFirst($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetFirst']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Подробности {@see Func::keysGetLast())}
     *
     * @return int|string|null|Chain
     */
    public function getLast()
    {
        if ($this->chain->elemsLevel == 0) {
            return Func::keysGetLast($this->array);
        }

        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysGetLast']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}