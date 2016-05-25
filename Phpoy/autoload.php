<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/23 0023
 * Time: 14:40
 */



$loader = require __DIR__.'/../vendor/autoload.php';

define('BASE_PATH', strtr(dirname(__DIR__), '\\', '/'));

$loader->add('Phpoy', BASE_PATH);

return $loader;