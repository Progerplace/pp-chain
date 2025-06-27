<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainKeysCase
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Преобразовать стиль ключей к "camelCase".
     *
     * ```
     * Ch::from(['var_first' => 1, 'var_second' => 2])->keys->case->toCamel()->toArray();
     * // ['varFirst' => 1, 'varSecond' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toCamel()
     * @see Func::keysCaseToCamel()
     */
    public function toCamel(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToCamel']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Преобразовать стиль ключей к "PaskalCase".
     *
     * ```
     * Ch::from(['var_first' => 1, 'var_second' => 2])->keys->case->toPaskal()->toArray()
     * // ['VarFirst' => 1, 'VarSecond' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toPaskal()
     * @see Func::keysCaseToPaskal()
     */
    public function toPaskal(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToPaskal']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Преобразовать стиль ключей к "snake_case".
     *
     * ```
     * Ch::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toSnake()->toArray();
     * // ['var_first' => 1, 'var_second' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toSnake()
     * @see Func::keysCaseToSnake()
     */
    public function toSnake(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToSnake']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Преобразовать стиль ключей к "kebab-case".
     *
     * ```
     * Ch::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toKebab()->toArray();
     * // ['var-first' => 1, 'var-second' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toKebab()
     * @see Func::keysCaseToKebab()
     */
    public function toKebab(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToKebab']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Преобразовать стиль ключей к "SCREAM_SNAKE_CASE".
     *
     * ```
     * Ch::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toScreamSnake()->toArray();
     * // ['VAR_FIRST' => 1, 'VAR_SECOND' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toScreamSnake()
     * @see Func::keysCaseToScreamSnake()
     */
    public function toScreamSnake(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamSnake']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Преобразовать стиль ключей к "SCREAM-KEBAB-CASE".
     *
     * ```
     * Ch::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toScreamKebab()->toArray();
     * // ['VAR-FIRST' => 1, 'VAR-SECOND' => 2]
     * ```
     *
     * @return Chain
     *
     * @see ChainFuncKeysCase::toScreamKebab()
     * @see Func::keysCaseToScreamKebab()
     */
    public function toScreamKebab(): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamKebab']);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

}