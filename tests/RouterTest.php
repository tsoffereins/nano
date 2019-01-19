<?php
declare(strict_types=1);

use Nano\Container;
use Nano\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RouterTest extends TestCase
{
	/**
	 * @test
	 */
	public function Should_ReturnControllerMethodReturnValue_When_RouteMatches_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index()
			{
				return 'bar';
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'foo' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('foo');

		// Then
		$this->assertEquals('bar', $result);
	}

	/**
	 * @test
	 */
	public function Should_ReturnNull_When_RouteDoesNotMatch_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index()
			{
				return 'bar';
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'foo' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('baz');

		// Then
		$this->assertEquals(null, $result);
	}

	/**
	 * @test
	 */
	public function Should_PassVariableToControllerMethod_When_RouteContainsVariable_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index($bar)
			{
				return ((int) $bar) * 2;
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'foo/:bar/baz' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('foo/2/baz');

		// Then
		$this->assertEquals(4, $result);
	}

	/**
	 * @test
	 */
	public function Should_PassAllVariablesToControllerMethod_When_RouteContainsVariables_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index($bar, $baz)
			{
				return ((int) $bar) * ((int) $baz);
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'foo/:bar/:baz' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('foo/2/3');

		// Then
		$this->assertEquals(6, $result);
	}

	/**
	 * @test
	 */
	public function Should_NotReturnControllerValue_When_CalledWithDifferentRequestMethod_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index()
			{
				return 'bar';
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'POST=foo' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('foo');

		// Then
		$this->assertEquals(null, $result);
	}

	/**
	 * @test
	 */
	public function Should_ReturnControllerValue_When_CalledWithRequiredRequestMethod_On_Match()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$controller = new class()
		{
			public function index()
			{
				return 'bar';
			}
		};

		$container->make(Argument::type('string'))->willReturn($controller);

		$router = new Router($container->reveal());

		$router->addRoutes(
			[
				'POST=foo' => 'FooController@index',
			]
		);

		// When
		$result = $router->match('foo', 'POST');

		// Then
		$this->assertEquals('bar', $result);
	}
}