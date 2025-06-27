<?php

namespace Ru\Progerplace\Chain\Utils;

class MathAction
{
    /**
     * @param string $action
     * @param array $arr
     * @param callable $callback
     * @return mixed
     */
    public static function by(array $arr, string $action, callable $callback)
    {
        $values = [];

        foreach ($arr as $key => $item) {
            $values[] = $callback($item, $key);
        }

        return static::doAction($action, $values);
    }

    /**
     * @param string $action
     * @param array $arr
     * @param string|int $field
     * @return mixed
     */
    public static function byField(array $arr, string $action, $field)
    {
        $values = [];

        foreach ($arr as $item) {
            if (isset($item[$field])) {
                $values[] = $item[$field];
            }
        }

        return static::doAction($action, $values);
    }

    /**
     * @param string $action
     * @param array $values
     * @return mixed
     */
    protected static function doAction(string $action, array $values)
    {
        switch ($action) {
            case 'min':
                return min($values);
            case 'max':
                return max($values);
            default:
                return null;
        }
    }
}