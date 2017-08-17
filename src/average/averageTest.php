<?php

/**
 * @covers Dash\average
 */
class averageTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($iterable, $expected)
	{
		$actual = Dash\average($iterable);
		$this->assertEquals($expected, $actual);
	}

	public function cases()
	{
		return [

			/*
				With array
			 */

			'should return zero for an empty array' => [
				[],
				0
			],
			'should return the average of the values of an array' => [
				[2, 3, 5, 8],
				4.5
			],

			/*
				With stdClass
			 */

			'should return zero for an empty stdClass' => [
				(object) [],
				0
			],
			'should return the average of the values of an stdClass' => [
				(object) [2, 3, 5, 8],
				4.5
			],

			/*
				With ArrayObject
			 */

			'should return zero for an empty ArrayObject' => [
				new ArrayObject([]),
				0
			],
			'should return the average of the values of an ArrayObject' => [
				new ArrayObject([2, 3, 5, 8]),
				4.5
			],
		];
	}
}
