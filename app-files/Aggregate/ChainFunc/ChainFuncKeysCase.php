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
     * Преобразовать стиль ключей к "camelCase".
     *
     * ```
     * Cf::from(['var_first' => 1, 'var_second' => 2])->keys->case->toCamel();
     * // ['varFirst' => 1, 'varSecond' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toCamel()
     * @see Func::keysCaseToCamel()
     */
    public function toCamel(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToCamel']);
    }

    /**
     * Преобразовать стиль ключей к "PaskalCase".
     *
     * ```
     * Cf::from(['var_first' => 1, 'var_second' => 2])->keys->case->toPaskal()
     * // ['VarFirst' => 1, 'VarSecond' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toPaskal()
     * @see Func::keysCaseToPaskal()
     */
    public function toPaskal(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToPaskal']);
    }

    /**
     * Преобразовать стиль ключей к "snake_case".
     *
     * ```
     * Cf::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toSnake();
     * // ['var_first' => 1, 'var_second' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toSnake()
     * @see Func::keysCaseToSnake()
     */
    public function toSnake(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToSnake']);
    }

    /**
     * Преобразовать стиль ключей к "kebab-case".
     *
     * ```
     * Cf::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toKebab();
     * // ['var-first' => 1, 'var-second' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toKebab()
     * @see Func::keysCaseToKebab()
     */
    public function toKebab(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToKebab']);
    }

    /**
     * Преобразовать стиль ключей к "SCREAM_SNAKE_CASE".
     *
     * ```
     * Cf::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toScreamSnake();
     * // ['VAR_FIRST' => 1, 'VAR_SECOND' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toScreamSnake()
     * @see Func::keysCaseToScreamSnake()
     */
    public function toScreamSnake(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamSnake']);
    }

    /**
     * Преобразовать стиль ключей к "SCREAM-KEBAB-CASE".
     *
     * ```
     * Cf::from(['varFirst' => 1, 'varSecond' => 2])->keys->case->toScreamKebab();
     * // ['VAR-FIRST' => 1, 'VAR-SECOND' => 2]
     * ```
     *
     * @return array
     *
     * @see ChainKeysCase::toScreamKebab()
     * @see Func::keysCaseToScreamKebab()
     */
    public function toScreamKebab(): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'keysCaseToScreamKebab']);
    }
}