<?php

declare(strict_types=1);

namespace Nano;

use ReflectionException;

class PipeLine
{
	private Container $ioc;
	private array $middleware = [];
	private array $stack = [];

	public function __construct(Container $ioc)
	{
		$this->ioc = $ioc;
	}

	public function addMiddleware(array $middleware): void
	{
		$this->middleware = array_merge($this->middleware, $middleware);
	}

	/**
	 * @param  mixed $action
	 * @param  mixed $payload
	 * @return mixed
	 * @throws ReflectionException
	 */
	public function fire($action, $payload)
	{
		$this->stack = $this->middleware;

		array_push($this->stack, $action);

		return $this->next($payload);
	}

	/**
	 * @param  mixed $payload
	 * @return mixed
	 * @throws ReflectionException
	 */
	public function next($payload)
	{
		$callback = array_shift($this->stack);

		if ($callback === null) {
		    return null;
        }

		// If a the middleware callback is a string we expect i to be a classname, to
		// make it callable we will instantiate it through the app container with
		// the method 'handle' as the one that will catch and handle the payload.
		if (is_string($callback)) {
			$callback = [$this->ioc->make($callback), 'handle'];
		}

		$arguments = [$payload];

		// As long as there are still middleware callbacks left in the stack we give
		// pass the 'next' method along, so the stack can be completed. When we are
		// currently handling the last one in the stack (the action), we don't.
		if (count($this->stack) != 0) {
			$arguments[] = [$this, 'next'];
		}

		return call_user_func_array($callback, $arguments);
	}
}
