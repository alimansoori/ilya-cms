# ilya CMS

## Table of Contents
- [File structure](#file-structure)
- [Configuring Apache for Phalcon](#configuring-apache-for-phalcon)
    - [Directory under the main Document Root](#directory-under-the-main-document-root)

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