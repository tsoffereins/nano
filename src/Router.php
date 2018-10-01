<?php namespace Nano;

class Router
{
	/**
	 * The IoC container.
	 *
	 * @var Container
	 */
	private $ioc;

	/**
	 * The routes to match.
	 *
	 * @var array
	 */
	private $routes = [];

	/**
	 * Setup the router.
	 *
	 * @param  Container $ioc
	 * @return void
	 */
	public function __construct(Container $ioc)
	{
		$this->ioc = $ioc;
	}

	/**
	 * Add routes.
	 *
	 * @param  array $routes
	 * @return void
	 */
	public function addRoutes(array $routes)
	{
		foreach ($routes as $route => $target) {
			$route = str_replace('/', '\/', $route);

			$pattern = preg_replace('/:[^\/]+/', '([^\/]+)', $route);

			$this->routes["/^$pattern$/"] = $target;
		}
	}

	/**
	 * Match a uri against the routes.
	 *
	 * @param  string $uri
	 * @return mixed
	 * @throws \ReflectionException
	 */
	public function match(string $uri)
	{
		foreach ($this->routes as $pattern => $target) {
			if (preg_match_all($pattern, $uri, $matches)) {
				$matches = array_map(
					function($match)
					{
						return $match[0];
					},
					array_slice($matches, 1)
				);

				return $this->dispatch($target, $matches);
			}
		}
	}

	/**
	 * Dispatch a target with arguments.
	 *
	 * @param  string $target
	 * @param  array  $arguments
	 * @return mixed
	 * @throws \ReflectionException
	 */
	protected function dispatch(string $target, array $arguments)
	{
		$segments = explode('@', $target);

		$callback = [$this->ioc->make($segments[0]), $segments[1]];

		return call_user_func_array($callback, $arguments);
	}
}