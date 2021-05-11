<?php
declare(strict_types=1);

use Nano\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
	/**
	 * @test
	 */
	public function Should_ConstructClass_When_NotBound_On_Make()
	{
		// Given
		$container = new Container();

		// When
		$result = $container->make('stdClass');

		// Then
		$this->assertTrue($result instanceof stdClass);
	}

	/**
	 * @test
	 */
	public function Should_ReturnCallbackValue_When_CallbackBound_On_Make()
	{
		// Given
		$container = new Container();

		$container->bind('foo', function()
		{
			return new stdClass();
		});

		// When
		$result = $container->make('foo');

		// Then
		$this->assertTrue($result instanceof stdClass);
	}

	/**
	 * @test
	 */
	public function Should_ReturnInstanceOfBoundClass_When_ClassNameBound_On_Make()
	{
		// Given
		$container = new Container();

		$container->bind('foo', 'stdClass');

		// When
		$result = $container->make('foo');

		// Then
		$this->assertTrue($result instanceof stdClass);
	}

	/**
	 * @test
	 */
	public function Should_ReturnSameInstanceTwice_When_BoundAsSingleton_On_Make()
	{
		// Given
		$container = new Container();

		$container->singleton('foo', function () {
			return (object) [
			    'value' => rand(0, 10000)
            ];
		});

		// When
		$result1 = $container->make('foo');
		$result2 = $container->make('foo');

		// Then
		$this->assertEquals($result1->value, $result2->value);
	}
}
