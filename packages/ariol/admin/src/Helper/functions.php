<?php

use Ariol\Classes\Localization;

/**
 * Дамп информации
 *
 * @param $args
 */
function ddd(...$args)
{
    http_response_code(500);
    call_user_func_array('dd', $args);
}

/**
 * Поиск ключа в многомерном массиве.
 *
 * @param array $arr
 * @param $key
 * @param bool $isArray
 * @return bool
 */
function multi_array_key_exists(array $arr, $key, $isArray = false)
{
    if (array_key_exists($key, $arr) && (! $isArray || ($isArray && is_array($arr[$key])))) {
        return true;
    }

    foreach ($arr as $item) {
        if (is_array($item) && multi_array_key_exists($item, $key, true)) {
            return true;
        }
    }

    return false;
}

/**
 * Поиск значения в многомерном массиве.
 *
 * @param $needle
 * @param $haystack
 * @param string $currentKey
 * @return bool|string
 */
function multi_array_search($needle, $haystack, $currentKey = '')
{
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $nextKey = multi_array_search($needle, $value, $currentKey . '[' . $key . ']');

            if ($nextKey) {
                return $nextKey;
            }
        } elseif ($value == $needle) {
            return is_numeric($key) ? $currentKey . '[' . $key . ']' : $currentKey . '["' . $key . '"]';
        }
    }

    return false;
}

/**
 * Перевод выражений.
 *
 * @param null $key
 * @param array $replace
 * @param null $locale
 * @return array|\Illuminate\Contracts\Translation\Translator|null|string
 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
 */
function translate($key = null, $replace = [], $locale = null)
{
    $defaultLanguage = Localization::getDefaultLanguage();

    $lang = empty($locale) ? Localization::getLocale() : $locale;
    $lang = empty(trans($key, $replace, $lang)) ? $defaultLanguage : Localization::getLocale();

    return trans($key, $replace, $lang);
}
