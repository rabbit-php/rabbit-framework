<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 15:21
 */

if (!function_exists('getDI')) {
    function getDI(string $name, bool $throwException = true, $default = null)
    {
        return \rabbit\core\ObjectFactory::get($name, $throwException, $default);
    }
}