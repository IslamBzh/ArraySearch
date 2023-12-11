<?php

namespace Islambzh\ArraySearch;

use Islambzh\ArrayMap\ArrayMap;

class ArraySearch
{
    private static mixed $NotFound = 0.15455454;

    const NOT_FOUND = 0.15455454;

    public static function returnIfNotFound(mixed $return): void
    {
        self::$NotFound = $return;
    }

    private static function callbackCheck(string|int $key, mixed &$value, callable|object $callback): bool
    {
        if (is_callable($callback)) {
            if ($callback($key, $value) === false)
                return false;

        } elseif (is_object($callback)) {
            if (method_exists($callback, 'is_type')
                && method_exists($callback, 'check')
                && $callback->is_type()
                && !$callback->check($value, true) === false
            )
                return false;

            elseif (method_exists($callback, 'onValid')
                && !$callback->onValid($key, $value) === false
            )
                return false;
        }

        return true;
    }

    public static function getArray(array|\Islambzh\ArrayMap\ArrayMap $array, ...$keys)
    {
        if (empty($keys))
            return $array;

        if (is_array($keys[0]))
            return self::getArray($array, ... $keys[0]);

        if ($array instanceof \Islambzh\ArrayMap\ArrayMap)
            return $array->get($keys);

        $res = $array;

        foreach ($keys as $key => $value) {
            if (is_int($key) && (is_string($value) || is_numeric($value)))
                $key = $value;

            if (is_array($value))
                $res = self::searchRule($res, $value);

            elseif (!isset($res[$key]))
                return self::$NotFound;

            else
                $res = $res[$key];
        }

        return $res;
    }

    public static function searchRule($array, $rules, bool $return_first = true)
    {
        $res = [];

        foreach ($array as $key => $value) {
            $success = true;

            foreach ($rules as $r_key => $rule) {
                if (!isset($value[$r_key]))
                    $success = false;

                if (!$success)
                    break;

                if (is_callable($rule) && $rule($value[$r_key]) === false)
                    $success = false;

                elseif ($value[$r_key] == $rule)
                    continue;

                $success = false;
            }

            if ($success)
                $res[] = $value;

            else
                continue;

            if ($return_first)
                return $res[0];
        }

        return $return_first
            ? self::$NotFound
            : $res;
    }


    public static function intersectArray(array|\Islambzh\ArrayMap\ArrayMap $array, array &$keys_params, bool|callable $continue = true, bool|callable $callback = false): array
    {
        $res = [];

        $params = $keys_params;
        foreach ($params as $key => $keys) {
            if ($callback === true && is_array($keys)) {
                $callback = $keys[1];
                $keys = $keys[0];
            } else if ($callback === true) {
                $callback = $keys;
                $keys = $key;
            }

            $value = self::getArray($array, $keys);

            if ($value === self::$NotFound)
                if ($continue)
                    continue;
                else
                    return [];

            if ($callback && !self::callbackCheck($key, $value, $callback))
                if ($continue)
                    continue;
                else
                    return [];

            if (!is_string($key)) {
                $new_key = is_string($keys) ? $keys : implode('.', $keys);
                $keys_params[$new_key] = $keys;
                unset($keys_params[$key]);
                $key = $new_key;
            }

            $res[$key] = $value;
        }

        return $res;
    }
}