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
* `Nano\DB` - A PDO database connection (deprecated)

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

### Database
Previously you could use the `Nano/DB` class, but since it is going to be *deprecated* in the near future, I advice you to bind the default PDO class to the container instead:
```php
$container->singleton('PDO', function()
{
    return new PDO('dsn', 'user', 'password');
});

class Controller
{
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
}
```

Using the connection:
```php
$result = $this->db->query("SELECT * FROM table");

var_dump($result); // Assoc array
```

## Support
Please file issues here at Github