# ilya CMS

## Table of Contents
- [File structure](#file-structure)
- [Configuring Apache for Phalcon](#configuring-apache-for-phalcon)
    - [Directory under the main Document Root](#directory-under-the-main-document-root)
- [Bootstrap](#bootstrap)
    - [Autoloaders](#autoloaders)
    - [Services](#services)
- [Controllers](#controllers)
- [Sending output to a view](#sending-output-to-a-view)
- [Designing a sign-up form](#designing-a-sign-up-form)
- [Creating a Model](#creating-a-model)
- [Setting a Database Connection](#setting-a-database-connection)
- [Storing data using models](#storing-data-using-models)

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

## Controllers
By default Phalcon will look for a controller named `IndexController`.

`app/controllers/IndexController.php`
```php
<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        echo '<h1>Hello!</h1>';
    }
}
```
See output!

### Sending output to a view
create Dir => `app/views/index/index.phtml` for `IndexController.php` file
```php
<?php echo "<h1>Hello!</h1>";
```

## Designing a sign-up form
Now we will change the `index.phtml` view file, to add a link to a new controller named "signup". The goal is to allow users to sign up within our application.

`app/views/index/index.phtml`
```php
<?php

echo "<h1>Hello!</h1>";

echo PHP_EOL;

echo PHP_EOL;

echo $this->tag->linkTo(
    'signup',
    'Sign Up Here!'
);
```
Here is the Signup controller `(app/controllers/SignupController.php)`:
```php
<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {

    }
}
```
The empty index action gives the clean pass to a view with the form definition `(app/views/signup/index.phtml)`:
```php
<h2>Sign up using this form</h2>

<?php echo $this->tag->form("signup/register"); ?>

    <p>
        <label for="name">Name</label>
        <?php echo $this->tag->textField("name"); ?>
    </p>

    <p>
        <label for="email">E-Mail</label>
        <?php echo $this->tag->textField("email"); ?>
    </p>

    <p>
        <?php echo $this->tag->submitButton("Register"); ?>
    </p>

</form>
```
Viewing the form in your browser.

By clicking the "Send" button, you will notice an exception thrown from the framework, indicating that we are missing the `register` action in the controller `signup`. Our `public/index.php` file throws this exception:
```php
Exception: Action "register" was not found on handler "signup"
```
Implementing that method will remove the exception:

`app/controllers/SignupController.php`
```php
<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {

    }

    public function registerAction()
    {

    }
}
```
If you click the "Send" button again, you will see a blank page. The name and email input provided by the user should be stored in a database. According to MVC guidelines, database interactions must be done through models so as to ensure clean object-oriented code.

## Creating a Model
Phalcon brings the first ORM for PHP entirely written in C-language. Instead of increasing the complexity of development, it simplifies it.

Before creating our first model, we need to create a database table outside of Phalcon to map it to. A simple table to store registered users can be created like this:

`create_users_table.sql`
```mysql
CREATE TABLE `users` (
    `id`    int(10)     unsigned NOT NULL AUTO_INCREMENT,
    `name`  varchar(70)          NOT NULL,
    `email` varchar(70)          NOT NULL,

    PRIMARY KEY (`id`)
);
```
A model should be located in the `app/models` directory `(app/models/Users.php)`. The model maps to the "users" table:

`app/models/Users.php`
```php
<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $email;
}
```
## Setting a Database Connection
In order to use a database connection and subsequently access data through our models, we need to specify it in our bootstrap process. A database connection is just another service that our application has that can be used for several components:

`public/index.php`
```php
<?php

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

// Setup the database service
$di->set(
    'db',
    function () {
        return new DbAdapter(
            [
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'dbname',
            ]
        );
    }
);
```
With the correct database parameters, our models are ready to work and interact with the rest of the application.

## Storing data using models
`app/controllers/SignupController.php`
```php
<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {

    }

    public function registerAction()
    {
        $user = new Users();

        // Store and check for errors
        $success = $user->save(
            $this->request->getPost(),
            [
                "name",
                "email",
            ]
        );

        if ($success) {
            echo "Thanks for registering!";
        } else {
            echo "Sorry, the following problems were generated: ";

            $messages = $user->getMessages();

            foreach ($messages as $message) {
                echo $message->getMessage(), "<br/>";
            }
        }

        $this->view->disable();
    }
}
```

The End Lesson 1