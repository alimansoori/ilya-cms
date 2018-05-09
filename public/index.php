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

// Create a DI
$di = new \Phalcon\Di\FactoryDefault();

// Setup the view component
$di->set('view', function () {
    $view = new \Phalcon\Mvc\View();
    $view->setViewsDir(APP_PATH. 'views/');

    return $view;
});

// Setup a base Url
$di->set('url', function () {
    $url = new \Phalcon\Mvc\Url();
    $url->setBaseUri('/ilya-cms/');

    return $url;
});

try
{
    $app = new \Phalcon\Mvc\Application($di);
    echo $app->handle()->getContent();
}
catch (Exception $e)
{
    echo "Exception => ". $e->getMessage();
}