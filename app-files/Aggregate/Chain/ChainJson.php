<?php

namespace Ru\Progerplace\Chain\Aggregate\Chain;

use Ru\Progerplace\Chain\Chain;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainJson
{
    protected array $array;
    protected Chain $chain;

    public function __construct(array &$array, Chain &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Кодировать в json поля с перечисленными ключами. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * Ch::from(['a'=>['f'=>1],'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeFields('a', 'b')->toArray();
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param string|int ...$keys
     * @return Chain
     *
     * @see ChainFuncJson::encodeFields()
     * @see Func::jsonEncodeFields()
     */
    public function encodeFields(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeFields'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Кодировать в json поля, для который `$callback` вернул `true`. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * Ch::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeBy(fn(string $item, string $key) => $item === ['f' => 1] || $key === 'b')->toArray();
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFuncJson::encodeBy()
     * @see Func::jsonEncodeBy()
     */
    public function encodeBy(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeBy'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Декодировать из json поля с перечисленными ключами.
     *
     * ```
     * Ch::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeFields('a', 'b')->toArray();
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param string|int ...$keys
     * @return Chain
     *
     * @see ChainFuncJson::decodeFields()
     * @see Func::jsonDecodeFields()
     */
    public function decodeFields(...$keys): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeFields'], ...$keys);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }

    /**
     * Декодировать из json поля, для которых `$callback` вернул `true`.
     *
     * ```
     * Ch::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b')->toArray();
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param callable $callback
     * @return Chain
     *
     * @see ChainFuncJson::decodeBy()
     * @see Func::jsonDecodeBy()
     */
    public function decodeBy(callable $callback): Chain
    {
        $this->array = ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeBy'], $callback);
        $this->chain->resetElemsLevel();

        return $this->chain->replaceWith($this->array);
    }
}