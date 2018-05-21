<?php
/**
 * Created by IntelliJ IDEA.
 * Author: flashytime
 * Date: 2018/5/21 15:07
 */

/**
 * @param array ...$arrays
 * @return array
 */
if (!function_exists('array_merge_keep_keys')) {
    function array_merge_keep_keys(...$arrays)
    {
        $result = [];
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}