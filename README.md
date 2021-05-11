# Nano framework
Nano is a teeny tiny framework that offers the bare minimum for you to create, for example, a very slim and super quick API.

## Installation
Installation can be done using [Composer](https://getcomposer.org/) to download and install Nano framework as well as its dependencies.
```$xslt
composer require nano/framework
```

## Building blocks
The Nano framework contains the following classes:
* `Nano\Container` - An IoC container for dependency injection
* `Nano\PipeLine` - A middleware bus
* `Nano\Router` - A router for defining endpoints

### Container
Using the container:
```php
$container = new Container();

$container->bind('abstract', 'concrete');

$instance = $container->make('abstract');
```

Binding a callback:
```php
$container->bind('abstract', function()
{
    return new Concrete('config');
});
```

Binding as a singleton:
```php
$container->singleton('abstract', function()
{
    return new Concrete('config');
});
````

### PipeLine
Using the PipeLine:
```php
$pipe = $container->make('Nano\PipeLine');

$pipe->addMiddleare(['Middleware']);

$pipe->fire(function()
{
    return 'Hello world!';
}, $request);
```

Defining middleware:
```php
class Middleware
{
    public function handle($request, $next)
    {
        // Do something with request
        
        $response = $next($request);
        
        // Do something with response
        
        return $response;
    }
}
```

### Router
Using the Router:
```php
$router = $container->make('Nano\Router');

$router->addRoutes(['/home', 'Controller@index']);

echo $router->match($_SERVER['REQUEST_URI']);
```

Defining a controller:
```php
class Controller
{
    public function index()
    {
        return 'Hello world!';
    }
}
```

Route parameters:
```php
$router->addRoutes(['/user/:id', 'Controller@user']);
```

Request methods:
```php
$router->addRoutes([
    '/home', 'Controller@index', // defaults to GET
    'POST=/user/',
    'GET=/user/:id',
    'PUT=/user/:id',
    'DELETE=/user/:id'
]);
```
## Support
Please file issues here at Github
