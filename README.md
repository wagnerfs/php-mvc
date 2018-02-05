# PHP MVC framework

This is a simple web application php framework that tries to meet most of the basic needs.
This framework is free and open-source, comes with a basic application sample for most of the available features.

## Requirements

- PHP 5.4+
- Mod rewrite
- Composer (to install the dependencies)
- Twig 1.* (as the example uses it)

## Configuration

Run `composer install` to install the dependencies first.

Routing, view and session configurations are stored in [config/application.php](config/application.php).

View options:
```
'view' => array(
    // Available render engines are twig and php
    'render' => 'twig',
    
    // The following optional paramenters are the default files to be used for each category (without the extension)
    'layout' => 'layout',
    '404' => '404',
    'exception' => 'error',
),
```
Session options:
```
'session' => array(
    // Whether or not cookies should be used for this application
    'use_cookies' => true,
    
    // List of session variables that should be saved as cookies
    'cookie_names' => array(
        'cookie'
    ),
    'cookie_lifetime' => 315360000
),
```

Details about route options in the Routing section.

Database, twig and other environment specific configurasions are stored in [config/autoload/local.php](config/autoload/local.php), these files should be ignored by git (There's another file with the same name in the same folder called  [config/autoload/local.production](config/autoload/local.production), this file stores configurations meant to be used in a production environment, so make sure to rename it to local.php and update it accordingly when it's needed).

All *.php files in the autoload folder are loaded into the application automatically, check [config/autoload/example.php](config/autoload/example.php) for an example of custom config files.

If you need to enforce HTTPS in your application, make sure you add `$app->forceHTTPS()` before `$app->run()` call in [public/index.php](public/index.php).
Incase of non default HTTPS port, make sure you have the follow entry in your [local.php](config/autoload/local.php) file:
```
'server' => array(
        'port' => array(
            'http' => /* Your http port */,
            'https' => /* Your https port */
        )
    )
```
## Routing

Routing configuration is stored in [application.php](config/application.php), each route entry must set controller and action informations.
```
'routes' => array(
    '/home' => array(
        'controller' => 'Test',
        'action' => 'home'
    ),
    // ...
),
```
Routes accept regular expression parameters for route matching, ex:
```
'/entry/{id:\d+}' => array(
    'controller' => 'Test',
    'action' => 'entry'
),
```
The above example will match `/entry/[number only]` and call the controller's action sending the matched number as `id` as a GET parameter.

## Directory structure

Your application files should reside in the following App directory tree.
```
App
├── Controller
│   ├── TestController.php      # Controllers should end with the Controller suffix
│   ├── ...
├── Model
│   ├── Test.php                # Entity
│   ├── TestTable.php           # Table model
│   ├── ...
├── View
│   ├── layout.twig
│   ├── test.twig               # Same name as the controller's action
│   ├── ...
```

## Controller

Controllers MUST extend `AbstractController` and be in the `App/Controller`namespace.
Controller actions must have the `Action` suffix and must either return a [View](Core/View.php) object or redirect to another route.
```
public function homeAction()
{
    return new View();
}
```
Check [App/Controller/TestController.php](App/Controller/TestController.php) sample for examples and usage.

## View

View files should have the same name as the action called without the `Action` suffix. File type depends on the render engine chosen, *.php for PHP render and *.twig for Twig render (The application's render is set to twig by default).
Additional information is sent to the view by feeding an array of data to the [View](Core/View.php) object:
```
$data = ['title' => 'A title'];
return new View($data);
```
And to access that data in a view
php:
```
The tile is <?= $this->title ?>
```
twig:
```
The tile is {{ title }}
```
For more information about the twig render, visit [https://twig.symfony.com/](https://twig.symfony.com/)

### View Layout

Layouts work differently for each render engine.

php layout and template example:
```
<html>
    <head>
        <title><?= $this->headTitle ?></title>
    </head>
    <body>
        <?= $content ?>
    <body>
</html>
```
```
<?php headTitle = 'Title' ?>
A content
```
twig layout and template example:
```
<html>
    <head>
        <title>{{ headTitle }}</title>
    </head>
    <body>
        {% block content %}{% endblock %}
    <body>
</html>
```
```
{% set headTitle = 'Title' %}
{% block content %}
    A content
{% endblock %}
```

## Model

There should be 2 files for each Model, the object representing the table structure and the object accessing the databse.
Check the samples in the [App/Model](App/Model) folder for more informations.
Table models MUST extend `AbstractModelTable` and be in the `App/Model`namespace.

## Sample application

The sample application assumes you have PDO mysql extension enabled, a database and a table both called test.
Make sure the connection information is correctly set in your [config/autoload/local.php](config/autoload/local.php) file.

## License

This project is licensed under the MIT License. This means you can use and modify it for free in private or commercial projects.