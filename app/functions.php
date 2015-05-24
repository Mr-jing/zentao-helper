<?php

if (!function_exists('array_extract_key')) {
    function array_extract_key($arr, $key, $is_retain = true)
    {
        if (!is_array($arr)) {
            return array();
        }
        $result = array();
        foreach ($arr as $val) {
            if (isset($val[$key])) {
                $result[$val[$key]] = $val;
            }
        }
        return $result;
    }
}