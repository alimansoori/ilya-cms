# ilya CMS

## Table of Contents
- [File structure](#file-structure)
- [Configuring Apache for Phalcon](#configuring-apache-for-phalcon)
    - [Directory under the main Document Root](#directory-under-the-main-document-root)
- [Bootstrap](#bootstrap)
    - [Autoloaders](#autoloaders)
    - [Services](#services)

## File structure
A key feature of Phalcon is it's loosly coupled, you can build a Phalcon project with a directory structure that is convenient for
your specific application.

```
project
├── app
│   ├── controllers
│   │   ├── IndexController.php
│   │   └── SignupController.php
│   ├── models
│   │   └── Users.php
│   └── views
└── public
    ├── css
    ├── img
    ├── index.php
    └── js
```

**Practice**: Build this structure as a project.

## Configuring Apache for Phalcon
The following are potential configurations you can use to setup Apache with Phalcon. These notes are primarily focused on the configuration of the mod_rewrite module allowing to use friendly URLs and the router component.

### Directory under the main Document Root
```apacheconfig
# project/.htaccess

<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule  ^$ public/    [L]
    RewriteRule  ((?s).*) public/$1 [L]
</IfModule>
```
Now a second .htaccess file is located in the public/ directory, this re-writes all the URIs to the public/index.php file:
```apacheconfig
# project/public/.htaccess

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^((?s).*)$ index.php?_url=/$1 [QSA,L]
</IfModule>
```

## Bootstrap
The first file you need to create is the bootstrap file. This file acts as the entry-point and configuration for your application. In this file, you can implement initialization of components as well as application behavior.

This file handles 3 things:

- Registration of component autoloaders
- Configuring Services and registering them with the Dependency Injection context
- Resolving the application's HTTP requests

### Autoloaders
Common things that should be added to the autoloader are your controllers and models. You can register directories which will search for files within the application's namespace.

To start, lets register our app's `controllers` and `models` directories. Don't forget to include the loader from **Phalcon\Loader**.

```php
# public/index.php
<?php

use Phalcon\Loader;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
// ...

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();
```

### Services

**Dependency Management**

Phalcon's IoC container consists of the following concepts:
- Service Container: a "bag" where we globally store the services that our application needs to function.
- Service or Component: Data processing object which will be injected into components

Each time the framework requires a component or service, it will ask the container using an agreed upon name for the service.

**Instante DI**
```php
public/index.php

<?php

use Phalcon\Di\FactoryDefault;

// ...

// Create a DI
$di = new FactoryDefault();
```

**register the "view" service**

In the next part, we register the "view" service indicating the directory where the framework will find the views files. As the views do not correspond to classes, they cannot be charged with an autoloader.
```php
public/index.php

<?php

use Phalcon\Mvc\View;

// ...

// Setup the view component
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
```
**Register a base URI**
```php
public/index.php

<?php

use Phalcon\Mvc\Url as UrlProvider;

// ...

// Setup a base URI
$di->set(
    'url',
    function () {
        $url = new UrlProvider();
        $url->setBaseUri('/');
        return $url;
    }
);
```
**Handling the application request**

In the last part of this file, we find Phalcon\Mvc\Application. Its purpose is to initialize the request environment, route the incoming request, and then dispatch any discovered actions; it aggregates any responses and returns them when the process is complete.

```php
public/index.php

<?php

use Phalcon\Mvc\Application;

// ...

$application = new Application($di);
$response = $application->handle();
$response->send();
```
The MVC Application was completed in less than 30 lines of code.