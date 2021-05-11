<?php

declare(strict_types=1);

namespace Nano;

use ReflectionException;

class Router
{
	private Container $ioc;
	private array $routes = [];

	public function __construct(Container $ioc)
	{
		$this->ioc = $ioc;
	}

	public function addRoutes(array $routes): void
	{
		foreach ($routes as $route => $target) {
            $pattern = str_replace('/', '\/', $route);

			$pattern = preg_replace('/:[^\/]+/', '([^\/]+)', $pattern);

			$pattern = str_replace('+)/', '+)\/', $pattern);

			if (preg_match('/^(GET|POST|PUT|PATCH|DELETE)=/', $pattern) !== 1) {
			    $pattern = 'GET=' . $pattern;
            }

			$this->routes["/^$pattern$/"] = $target;
		}
	}

    /**
     * @param  string $uri
     * @param  string $method
     * @return mixed
     * @throws ReflectionException
     */
	public function match(string $uri, string $method = 'GET')
	{
	    $uri = trim($uri, '/');

		foreach ($this->routes as $pattern => $target) {
			if (preg_match_all($pattern, "$method=$uri", $matches)) {
				$matches = array_map(
					function(array $match): string {
						return $match[0];
					},
					array_slice($matches, 1)
				);

				return $this->dispatch($target, $matches);
			}
		}

		return null;
	}

	/**
	 * @param  string $target
	 * @param  array  $arguments
	 * @return mixed
	 * @throws ReflectionException
	 */
	protected function dispatch(string $target, array $arguments)
	{
		$segments = explode('@', $target);

		$callback = [$this->ioc->make($segments[0]), $segments[1]];

		return call_user_func_array($callback, $arguments);
	}
}
