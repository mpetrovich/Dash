<?php

/**
 * @covers Dash\identical
 * @covers Dash\Curry\identical
 */
class identicalTest extends PHPUnit\Framework\TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($a, $b, $expected)
	{
		$this->assertSame($expected, Dash\identical($a, $b));
	}

	/**
	 * @dataProvider cases
	 */
	public function testCurried($a, $b, $expected)
	{
		$identical = Dash\Curry\identical($b);
		$this->assertSame($expected, $identical($a));
	}

	public function cases()
	{
		return [
			'With identical numbers' => [
				'a' => 3,
				'b' => 3,
				'expected' => true,
			],
			'With two equal but non-identical numbers' => [
				'a' => 123,
				'b' => 123.0,
				'expected' => false,
			],
			'With equal but not identical string/integer values' => [
				'a' => '3',
				'b' => 3,
				'expected' => false,
			],
			'With equal but not identical boolean/integer values' => [
				'a' => true,
				'b' => 1,
				'expected' => false,
			],
			'With equal but not identical null/integer values' => [
				'a' => null,
				'b' => 0,
				'expected' => false,
			],
			'With identical values of the same type' => [
				'a' => 4,
				'b' => 3,
				'expected' => false,
			],
			'With identical values of different types' => [
				'a' => '4',
				'b' => 3,
				'expected' => false,
			],
			'With two identical arrays' => [
				'a' => [1, 2, 3],
				'b' => [1, 2, 3],
				'expected' => true,
			],
			'With two arrays containing loosely equal values' => [
				'a' => [null, 1, 2, 3],
				'b' => [0, 1, '2', 3],
				'expected' => false,
			],
			'With two arrays containing identical values in different orders' => [
				'a' => [1, 2, 3],
				'b' => [3, 2, 1],
				'expected' => false,
			],
			'With two arrays containing identical values but different keys' => [
				'a' => ['a' => 1, 'b' => 2, 'c' => 3],
				'b' => ['a' => 1, 'd' => 2, 'c' => 3],
				'expected' => false,
			],
			'With two arrays containing equal but not identical nested arrays' => [
				'a' => ['a' => [1, 2], 'b' => [3, 4, 5]],
				'b' => ['a' => [1, '2'], 'b' => [3.0, 4, 5]],
				'expected' => false,
			],
		];
	}

	public function testExamples()
	{
		$this->assertSame(false, Dash\identical(3, '3'));
		$this->assertSame(true, Dash\identical(3, 3));
		$this->assertSame(false, Dash\identical([1, 2, 3], [1, '2', 3]));
		$this->assertSame(true, Dash\identical([1, 2, 3], [1, 2, 3]));
	}
}
