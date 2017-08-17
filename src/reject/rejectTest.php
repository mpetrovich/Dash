<?php

/**
 * @covers Dash\reject
 */
class rejectTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider cases
	 */
	public function test($input, $predicate, $expected)
	{
		$this->assertEquals($expected, Dash\reject($input, $predicate));
	}

	public function cases()
	{
		return [
			'With null' => [
				'input' => null,
				'predicate' => 'Dash\isEven',
				'expected' => [],
			],
			'With an empty array' => [
				'input' => [],
				'predicate' => 'Dash\isEven',
				'expected' => [],
			],
			'With an array' => [
				'input' => [1, 2, 3],
				'predicate' => 'Dash\isEven',
				'expected' => [0 => 1, 2 => 3],
			],
			'With an empty stdClass' => [
				'input' => (object) [],
				'predicate' => 'Dash\isEven',
				'expected' => [],
			],
			'With a non-empty stdClass' => [
				'input' => (object) ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
				'predicate' => 'Dash\isEven',
				'expected' => ['a' => 1, 'c' => 3],
			],
			'With an empty ArrayObject' => [
				'input' => new ArrayObject([]),
				'predicate' => 'Dash\isEven',
				'expected' => [],
			],
			'With an ArrayObject' => [
				'input' => new ArrayObject(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]),
				'predicate' => 'Dash\isEven',
				'expected' => ['a' => 1, 'c' => 3],
			],
		];
	}

	/**
	 * @dataProvider casesPredicate
	 */
	public function testPredicate($input, $expectedCallCount)
	{
		$keyValueMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
		$callCount = 0;

		$predicate = function ($value, $key) use ($keyValueMap, &$callCount) {
			$callCount++;
			$this->assertEquals($keyValueMap[$key], $value);
		};

		Dash\reject($input, $predicate);

		$this->assertEquals($expectedCallCount, $callCount);
	}

	public function casesPredicate()
	{
		return [
			'With null' => [
				'input' => null,
				'expectedCallCount' => 0,
			],
			'With an empty array' => [
				'input' => [],
				'expectedCallCount' => 0,
			],
			'With an array' => [
				'input' => [],
				'expectedCallCount' => 0,
			],
			'With an empty stdClass' => [
				'input' => (object) [],
				'expectedCallCount' => 0,
			],
			'With a non-empty stdClass' => [
				'input' => (object) ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
				'expectedCallCount' => 4,
			],
			'With an empty ArrayObject' => [
				'input' => new ArrayObject([]),
				'expectedCallCount' => 0,
			],
			'With an ArrayObject' => [
				'input' => new ArrayObject(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]),
				'expectedCallCount' => 4,
			],
		];
	}
}
