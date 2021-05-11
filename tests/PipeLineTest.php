<?php
declare(strict_types=1);

use Nano\Container;
use Nano\PipeLine;
use Nano\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PipeLineTest extends TestCase
{
	/**
	 * @test
	 */
	public function Should_PassPayloadToHandleAndItsReturnValue_On_Fire()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$middleware = new class()
		{
			public function handle($payload, $next)
			{
				return $payload * 2;
			}
		};

		$container->make(Argument::type('string'))->willReturn($middleware);

		$pipeLine = new PipeLine($container->reveal());

		$pipeLine->addMiddleware(
			[
				'classname',
			]
		);

		// When
		$result = $pipeLine->fire('foo', 2);

		// Then
		$this->assertEquals(4, $result);
	}

	/**
	 * @test
	 */
	public function Should_CallActionMethodAndReturnValue_When_PassedThroughStack_On_Fire()
	{
		// Given
		$container = $this->prophesize(Container::class);

		$middleware = new class()
		{
			public function handle($payload, $next)
			{
				return $next($payload);
			}
		};

		$container->make(Argument::type('string'))->willReturn($middleware);

		$pipeLine = new PipeLine($container->reveal());

		$pipeLine->addMiddleware(
			[
				'classname',
			]
		);

		// When
		$result = $pipeLine->fire(
			function($payload) {
				return $payload * 3;
			},
			2
		);

		// Then
		$this->assertEquals(6, $result);
	}
}
