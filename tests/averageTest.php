<?php

/**
 * @covers Dash\average
 * @covers Dash\Curry\average
 * @covers Dash\mean
 * @covers Dash\Curry\mean
 */
class averageTest extends PHPUnit\Framework\TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($iterable, $expected)
	{
		$this->assertSame($expected, Dash\average($iterable));
		$this->assertSame($expected, Dash\mean($iterable));
	}

	/**
	 * @dataProvider cases
	 */
	public function testCurried($iterable, $expected)
	{
		$average = Dash\Curry\average();
		$this->assertSame($expected, $average($iterable));

		$mean = Dash\Curry\mean();
		$this->assertSame($expected, $mean($iterable));
	}

	public function cases()
	{
		return [
			'With null' => [
				'iterable' => null,
				'expected' => null,
			],
			'With an empty array' => [
				'iterable' => [],
				'expected' => null,
			],

			/*
				With indexed array
			 */

			'With an indexed array with one element' => [
				'iterable' => [3],
				'expected' => 3,
			],
			'With an indexed array' => [
				'iterable' => [2, 3, 5, 8],
				'expected' => 4.5,
			],

			/*
				With associative array
			 */

			'With an associative array with one element' => [
				'iterable' => ['a' => 3],
				'expected' => 3,
			],
			'With an associative array' => [
				'iterable' => ['a' => 2, 'b' => 3, 'c' => 5, 'd' => 8],
				'expected' => 4.5,
			],

			/*
				With stdClass
			 */

			'With an empty stdClass' => [
				'iterable' => (object) [],
				'expected' => null,
			],
			'With an stdClass with one element' => [
				'iterable' => (object) ['a' => 3],
				'expected' => 3,
			],
			'With an stdClass' => [
				'iterable' => (object) ['a' => 2, 'b' => 3, 'c' => 5, 'd' => 8],
				'expected' => 4.5,
			],

			/*
				With ArrayObject
			 */

			'With an empty ArrayObject' => [
				'iterable' => new ArrayObject([]),
				'expected' => null,
			],
			'With an ArrayObject with one element' => [
				'iterable' => new ArrayObject(['a' => 3]),
				'expected' => 3,
			],
			'With an ArrayObject' => [
				'iterable' => new ArrayObject(['a' => 2, 'b' => 3, 'c' => 5, 'd' => 8]),
				'expected' => 4.5,
			],
		];
	}

	/**
	 * @dataProvider casesTypeAssertions
	 */
	public function testTypeAssertions($iterable, $type)
	{
		$this->expectException(InvalidArgumentException::class);

		try {
			Dash\average($iterable);
		} catch (Exception $e) {
			$this->assertSame(
				"Dash\\average expects iterable or stdClass or null but was given $type",
				$e->getMessage()
			);
			throw $e;
		}

		try {
			Dash\mean($iterable);
		} catch (Exception $e) {
			$this->assertSame(
				"Dash\\average expects iterable or stdClass or null but was given $type",
				$e->getMessage()
			);
			throw $e;
		}
	}

	public function casesTypeAssertions()
	{
		return [
			'With an empty string' => [
				'iterable' => '',
				'type' => 'string',
			],
			'With a string' => [
				'iterable' => 'hello',
				'type' => 'string',
			],
			'With a zero number' => [
				'iterable' => 0,
				'type' => 'integer',
			],
			'With a number' => [
				'iterable' => 3.14,
				'type' => 'double',
			],
			'With a DateTime' => [
				'iterable' => new DateTime(),
				'type' => 'DateTime',
			],
		];
	}

	public function testExamples()
	{
		$this->assertSame(4.5, Dash\average([2, 3, 5, 8]));
	}
}
