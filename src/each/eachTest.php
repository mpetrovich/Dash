<?php

/**
 * @covers Dash\each
 */
class eachTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($iterable, $expected)
	{
		$self = $this;
		$iterated = [];
		$iteratee = function ($value, $key, $iterable2) use ($self, $iterable, &$iterated) {
			$self->assertSame($iterable, $iterable2);
			$iterated[] = $key . ' is ' . $value;
		};

		Dash\each($iterable, $iteratee);
		$this->assertEquals($expected, $iterated);
	}

	public function cases()
	{
		return array(
			'With an empty array' => [
				[],
				[]
			],
			'With an indexed array' => array(
				[
					'first',
					'second',
					'third',
				],
				[
					'0 is first',
					'1 is second',
					'2 is third',
				],
			),
			'With an associative array' => array(
				[
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
				],
				[
					'a is first',
					'b is second',
					'c is third',
				],
			),
			'With an empty object' => array(
				(object) [],
				[]
			),
			'With an object' => array(
				(object) [
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
				],
				[
					'a is first',
					'b is second',
					'c is third',
				],
			),
			'With an empty ArrayObject' => array(
				new ArrayObject([]),
				[],
			),
			'With an ArrayObject' => array(
				new ArrayObject(array(
					'a' => 'first',
					'b' => 'second',
					'c' => 'third',
				)),
				[
					'a is first',
					'b is second',
					'c is third',
				],
			),
		);
	}
}
