# ilya CMS

## Table of Contents
- [File structure](#file-structure)
- [Configuring Apache for Phalcon](#configuring-apache-for-phalcon)
    - [Directory under the main Document Root](#directory-under-the-main-document-root)
- [Bootstrap](#bootstrap)
    - [Autoloaders](#autoloaders)

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