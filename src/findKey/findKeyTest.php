<?php

/**
 * @covers Dash\findKey
 */
class findKeyTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($iterable, $predicate, $expected)
	{
		$actual = Dash\findKey($iterable, $predicate);
		$this->assertEquals($expected, $actual);
	}

	public function cases()
	{
		return [
			'With an empty array' => [
				[],
				function () { return false; },
				null
			],
			'With a non-matching search of an array' => [
				[
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
					'd' => 'second',
					'e' => 'fifth',
				],
				function () { return false; },
				null
			],
			'With a matching value search of an array' => [
				[
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
					'd' => 'second',
					'e' => 'fifth',
				],
				function ($value) {
					return $value == 'second';
				},
				'b'
			],
			'With a matching key search of an array' => [
				[
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
					'd' => 'second',
					'e' => 'fifth',
				],
				function ($value, $key) {
					return $key == 'd';
				},
				'd'
			],
		];
	}
}
