<?php

/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/18 0018
 * Time: 17:24
 */


$loader = require __DIR__ . '/../../Phpoy/autoload.php';

define('PROJECT_PATH', strtr(dirname(__DIR__), '\\', '/'));

$loader->add('Test', dirname(PROJECT_PATH));


use Phpoy\Lib\App;
use Test\Config\ConfigDev;


//Phpoy\Helper\ClassCache::run(
//    PROJECT_PATH.'/Cache/ClassesCached',
//    function(){
        App::createApp(new ConfigDev())->run();
//    }
//);
