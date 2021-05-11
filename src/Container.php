<?php

declare(strict_types=1);

namespace Nano;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class Container
{
	private array $bindings = [];
	private array $cache = [];

	public function __construct()
	{
		$this->cache[self::class] = $this;
	}

    /**
     * @param string $class
     * @param array $payload
     * @return mixed|object
     * @throws ReflectionException
     */
	public function make(string $class, array $payload = [])
	{
		$resolved = $this->resolve($class);

		if (is_object($resolved)) {
			return $resolved;
		} else if (is_string($resolved)) {
			$class = $resolved;
		}

		if ( ! class_exists($class)) {
		    throw new ReflectionException("Class $class does not exist");
        }

		$reflection = new ReflectionClass($class);

		// If the class we try to make does not have a constructor we do not have to do 
		// any dependency injection. Instead we simply instanciate the class and 
		// return it. In the other case we'll try to find the dependencies.
		if (is_null($constructor = $reflection->getConstructor())) {
			return $reflection->newInstance();
		}

		$arguments = [];

		// Loop over each parameter for the constructor and check if the the parameter is 
		// a class and therefore a dependency. If so, get this dependency also through 
		// the container. If not, fill it with the arguments given in the payload.
		foreach ($constructor->getParameters() as $dependency) {
            $type = $dependency->getType();

			if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
				$instance = $this->make($type->getName());

				$arguments[] = $instance ?: array_shift($payload);
			} else {
				$arguments[] = array_shift($payload);
			}
		}

		return $reflection->newInstanceArgs($arguments);
	}

    /**
     * @param string $class
     * @return false|mixed|string|null
     */
	private function resolve(string $class)
	{
		// Bindings can be cached (singletons). The class is instantiated once and
		// returned every next time it is requested. If it is not cached a we 
		// check the bindings array if it can be resolved from there.
		if (isset($this->cache[$class])) {
			return $this->cache[$class];
		}

		if (isset($this->bindings[$class])) {
			$binding = $this->bindings[$class];

			// If the binding is targeted to a callback function we expect that this 
			// callable object will return the instance of the class that is bound 
			// to the class name. If the cache option is set we store it in there.
			if (is_callable($binding[0])) {
				$instance = call_user_func($binding[0], $this);

				if ($binding[1]) {
					$this->cache[$class] = $instance;
				}

				return $instance;
			}

			if (is_string($binding[0])) {
				return $binding[0];
			}
		}

		return null;
	}

    /**
     * @param string $class
     * @param mixed $target
     */
	public function bind(string $class, $target): void
	{
		$this->bindings[$class] = [$target, false];
	}

	/**
	 * @param  string $class
	 * @param  mixed  $target
	 * @return void
	 */
	public function singleton(string $class, $target): void
	{
		$this->bindings[$class] = [$target, true];
	}
}
