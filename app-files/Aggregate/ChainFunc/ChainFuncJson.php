<?php

namespace Ru\Progerplace\Chain\Aggregate\ChainFunc;

use Ru\Progerplace\Chain\ChainFunc;
use Ru\Progerplace\Chain\Func;
use Ru\Progerplace\Chain\Utils\ArrayAction;

class ChainFuncJson
{
    protected array     $array;
    protected ChainFunc $chain;

    public function __construct(array &$array, ChainFunc &$chain)
    {
        $this->array = &$array;
        $this->chain = &$chain;
    }

    /**
     * Кодировать в json поля с перечисленными ключами. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * Cf::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeFields('a', 'b');
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainJson::encodeFields()
     * @see Func::jsonEncodeFields()
     */
    public function encodeFields(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeFields'], ...$keys);
    }

    /**
     * Кодировать в json поля, для который `$callback` вернул `true`. Для json задан флаг `JSON_UNESCAPED_UNICODE`.
     *
     * ```
     * Cf::from(['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>['f'=>3]])->json->encodeBy(fn(string $item, string $key) => $item === ['f' => 1] || $key === 'b');
     * // ['a'=>'{"f":1}','b'=>'{"f":2}', 'c'=>['f'=>3]]
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see ChainJson::encodeBy()
     * @see Func::jsonEncodeBy()
     */
    public function encodeBy(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonEncodeBy'], $callback);
    }

    /**
     * Декодировать из json поля с перечисленными ключами.
     *
     * ```
     * Cf::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeFields('a', 'b');
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param string|int ...$keys
     * @return array
     *
     * @see ChainJson::decodeFields()
     * @see Func::jsonDecodeFields()
     */
    public function decodeFields(...$keys): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeFields'], ...$keys);
    }

    /**
     * Декодировать из json поля, для которых `$callback` вернул `true`.
     *
     * ```
     * Cf::from(['a'=>'{"f":1}', 'b'=>'{"f":2}', 'c'=>'{"f":3}'])->json->decodeBy(fn(string $item, string $key) => $item === '{"f":1}' || $key === 'b');
     * // ['a'=>['f'=>1], 'b'=>['f'=>2], 'c'=>'{"f":3}']
     * ```
     *
     * @param callable $callback
     * @return array
     *
     * @see ChainJson::decodeBy()
     * @see Func::jsonDecodeBy()
     */
    public function decodeBy(callable $callback): array
    {
        return ArrayAction::doAction($this->array, $this->chain->elemsLevel, [Func::class, 'jsonDecodeBy'], $callback);
    }
}