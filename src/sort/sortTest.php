<?php

// @todo Test that original collection is not modified
// @todo Test with comparator
class sortTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($iterable, $expected)
	{
		$actual = Dash\sort($iterable);
		$this->assertEquals($expected, $actual);
	}

	public function cases()
	{
		return [

			/*
				With array
			 */

			'should return an empty array from an empty array' => [
				[],
				[]
			],
			'should return a sorted array from an indexed array' => [
				[3, 8, 2, 5],
				[2 => 2, 0 => 3, 3 => 5, 1 => 8]
			],
			'should return a sorted array from an associative array' => [
				['a' => 3, 'b' => 8, 'c' => 2, 'd' => 5],
				['c' => 2, 'a' => 3, 'd' => 5, 'b' => 8]
			],

			/*
				With stdClass
			 */

			'should return an empty array from an empty stdClass' => [
				(object) [],
				[]
			],
			'should return a sorted array from an stdClass' => [
				(object) ['a' => 3, 'b' => 8, 'c' => 2, 'd' => 5],
				['c' => 2, 'a' => 3, 'd' => 5, 'b' => 8]
			],

			/*
				With ArrayObject
			 */

			'should return an empty array from an empty ArrayObject' => [
				new ArrayObject([]),
				[]
			],
			'should return a sorted array from an ArrayObject' => [
				new ArrayObject(['a' => 3, 'b' => 8, 'c' => 2, 'd' => 5]),
				['c' => 2, 'a' => 3, 'd' => 5, 'b' => 8]
			],
		];
	}
}
