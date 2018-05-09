<?php
/**
 * Ilya CMS Created by PhpStorm.
 * User: projekt
 * Date: 5/8/2018
 * Time: 11:12 AM
 */

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__). '/');
define('APP_PATH', BASE_PATH. '/app/');
// ...

$loader = new \Phalcon\Loader();

$loader->registerDirs([
    APP_PATH. 'controllers/',
    APP_PATH. 'models/',
]);

$loader->register();