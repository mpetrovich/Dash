<?php

use Dash\_;

/**
 * @covers Dash\_
 */
class _Test extends PHPUnit\Framework\TestCase
{
	public function testReadmeExamples()
	{
		/*
			Dash vs. array_*()
		 */

		$people = [
			['name' => 'John', 'gender' => 'male',   'age' => 12],
			['name' => 'Jane', 'gender' => 'female', 'age' => 34],
			['name' => 'Pete', 'gender' => 'male',   'age' => 23],
			['name' => 'Mary', 'gender' => 'female', 'age' => 42],
			['name' => 'Mark', 'gender' => 'male',   'age' => 19],
		];

		$avgMaleAge = Dash\chain($people)
			->filter(['gender', 'male'])
			->map('age')
			->average()
			->value();
		$this->assertSame(18, $avgMaleAge);

		if (function_exists('array_column')) {  // array_column() requires PHP 5.5+
			$males = array_filter($people, function ($person) {
				return $person['gender'] === 'male';
			});
			$avgMaleAge = array_sum(array_column($males, 'age')) / count($males);
			$this->assertSame(18, $avgMaleAge);
		}

		/*
			Ad-hoc operation
		 */

		$result = Dash\chain(['one' => 1, 'two' => 2, 'three' => 3])
			->filter('Dash\isOdd')
			->thru(function ($input) {
				return array_change_key_case($input, CASE_UPPER);
			})
			->keys()
			->value();
		$this->assertSame(['ONE', 'THREE'], $result);

		/*
			Data types
		 */

		$this->assertSame(
			[4, 8],
			Dash\chain([1, 2, 3, 4])
				->filter('Dash\isEven')
				->map(function ($value) {
					return $value * 2;
				})
				->value()
		);

		$this->assertSame(
			'a, c',
			Dash\chain((object) ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4])
				->filter('Dash\isOdd')
				->keys()
				->join(', ')
				->value()
		);

		$this->assertSame(
			5,
			Dash\chain(new ArrayObject(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]))
				->pick(['b', 'c'])
				->values()
				->sum()
				->value()
		);

		$iterator = new \FilesystemIterator(__DIR__, \FilesystemIterator::SKIP_DOTS);
		$filenames = Dash\chain($iterator)
			->reject(function ($fileinfo) {
				return $fileinfo->isDir();
			})
			->map(function ($fileinfo) {
				return pathinfo($fileinfo)['filename'];
			})
			->value();

		$this->assertGreaterThan(10, count($filenames));

		/*
			Custom operation
		 */

		_::setCustom('keyCase', function ($input, $case = CASE_LOWER) {
			return array_change_key_case($input, $case);
		});

		$result = Dash\chain(['one' => 1, 'two' => 2, 'three' => 3])
			->filter('Dash\isOdd')
			->keyCase(CASE_UPPER)
			->keys()
			->value();

		_::unsetCustom('keyCase');
		$this->assertSame(['ONE', 'THREE'], $result);
	}

	public function testExamples()
	{
		$this->assertSame(
			[2, 4, 6],
			_::map([1, 2, 3], function ($n) {
				return $n * 2;
			})
		);

		$result = _::chain([1, 2, 3])
			->filter(function ($n) {
				return $n < 3;
			})
			->map(function ($n) {
				return $n * 2;
			})
			->value();
		$this->assertSame([2, 4], $result);
	}

	public function testCurryExamples()
	{
		// @codingStandardsIgnoreLine
		function _Test_listThree($a, $b, $c)
		{
			return "$a, $b, and $c";
		}

		$listThree = Dash\curry('_Test_listThree');
		$listTwo = $listThree('first');
		$this->assertSame('first, second, and third', $listTwo('second', 'third'));

		$filtered = _::chain(['a' => 3, 'b' => '3', 'c' => 3, 'd' => 3.0])
			->filter(Dash\Curry\identical(3))
			->value();

		$this->assertSame(['a' => 3, 'c' => 3], $filtered);

		$containsTruthy = Dash\Curry\contains(true, 'Dash\equal');
		$this->assertTrue($containsTruthy([0, 1, 0]));

		$containsTrue = Dash\Curry\contains(true, 'Dash\identical');
		$this->assertFalse($containsTrue([0, 1, 0]));
	}

	/*
		addGlobalAlias()
		------------------------------------------------------------
	 */

	public function testAddGlobalAliasWithCustom()
	{
		_::addGlobalAlias('_Test_dash');
		$chain = _Test_dash([1, 2, 3]);

		$this->assertInstanceOf('Dash\Dash', $chain);
		$this->assertSame([1, 2, 3], $chain->value());

		$chain->map(function ($n) {
			return $n * 2;
		});
		$this->assertSame([2, 4, 6], $chain->value());
	}

	public function testAddGlobalAliasWithExisting()
	{
		$this->expectException(RuntimeException::class);

		$name = '_Test_dash' . intval(microtime(true));
		eval("function $name() {}");

		_::addGlobalAlias($name);
	}

	/*
		chain()
		------------------------------------------------------------
	 */

	public function testChain()
	{
		$chain = _::chain([1, 2, 3]);
		$this->assertSame([1, 2, 3], $chain->value());
	}

	public function testChainWithArray()
	{
		$chain = _::chain([1, 2, 3])
			->filter(function ($n) {
				return $n < 3;
			})
			->map(function ($n) {
				return $n * 2;
			});

		$this->assertSame([2, 4], $chain->value());
	}

	public function testChainWithObject()
	{
		$chain = _::chain((object) ['a' => 1, 'b' => 2, 'c' => 3])
			->pick(['b', 'c'])
			->toObject();

		$this->assertEquals((object) ['b' => 2, 'c' => 3], $chain->value());
	}

	public function testChainWithDefault()
	{
		$chain = _::chain()
			->filter(function ($n) {
				return $n < 3;
			})
			->map(function ($n) {
				return $n * 2;
			});

		try {
			$chain->value();
			$this->assertTrue(false, 'This should never be called');
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		$chain->with([1, 2, 3]);
		$this->assertSame([2, 4], $chain->value());
	}

	public function testChainReuse()
	{
		$chain = _::chain([1, 2, 3])->map(function ($n) {
			return $n * 2;
		});
		$this->assertSame([2, 4, 6], $chain->value());

		$chain->with([4, 5, 6]);
		$this->assertSame([8, 10, 12], $chain->value());
	}

	/*
		setCustom(), unsetCustom()
		------------------------------------------------------------
	 */

	public function testCustomOperation()
	{
		/*
			setCustom()
		 */

		_::setCustom('triple', function ($value) {
			return $value * 3;
		});

		$this->assertSame(12, _::triple(4));

		/*
			unsetCustom()
		 */

		_::unsetCustom('triple');

		try {
			_::triple(2);
			$this->assertTrue(false, 'This should never be called');
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	public function testCustomOperationWithExisting()
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("Cannot create a custom method named 'map'; Dash\map() already exists and cannot be overridden");

		_::setCustom('map', function ($n) {
		});
	}

	public function testCustomOperationForNumbersStandalone()
	{
		_::setCustom('triple', function ($value) {
			return $value * 3;
		});

		$this->assertSame(12, _::triple(4));

		_::unsetCustom('triple');
	}

	public function testCustomOperationForNumbersChained()
	{
		_::setCustom('double', function ($value) {
			return $value * 2;
		});

		$this->assertSame(8, _::chain(4)->double()->value());

		_::unsetCustom('double');
	}

	public function testCustomOperationForIterablesStandalone()
	{
		_::setCustom('addEach', function ($iterable, $add) {
			return _::map($iterable, function ($n) use ($add) {
				return $n + $add;
			});
		});

		$this->assertSame(
			[4, 5, 6],
			_::addEach([1, 2, 3], 3)
		);

		_::unsetCustom('addEach');
	}

	public function testCustomOperationForIterablesChained()
	{
		_::setCustom('addEach', function ($iterable, $add) {
			return _::map($iterable, function ($n) use ($add) {
				return $n + $add;
			});
		});

		$this->assertSame(
			[4, 5, 6],
			_::chain([1, 2, 3])->addEach(3)->value()
		);

		_::unsetCustom('addEach');
	}

	public function testCustomOperationLookup()
	{
		_::setCustom('double', function ($value) {
			return $value * 2;
		});

		$this->assertSame(
			[2, 4, 6],
			_::chain([1, 2, 3])->map('Dash\_::double')->value()
		);

		_::unsetCustom('double');
	}

	/*
		Standalone operations
		------------------------------------------------------------
	 */

	public function testStandalone()
	{
		$this->assertSame(
			[2, 4, 6],
			Dash\_::map([1, 2, 3], function ($n) {
				return $n * 2;
			})
		);
	}

	public function testStandaloneInvalid()
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("No operation named 'foobar' found");

		_::foobar([1, 2, 3]);
	}

	/*
		with()
		------------------------------------------------------------
	 */

	/**
	 * @dataProvider casesWith
	 */
	public function testWith($value)
	{
		$chain = _::chain();
		$return = $chain->with($value);

		$this->assertSame($value, $chain->value());
		$this->assertSame($chain, $return);
	}

	public function casesWith()
	{
		return [
			'With null' => [
				'value' => null,
			],
			'With a number' => [
				'value' => 3.14,
			],
			'With an empty string' => [
				'value' => '',
			],
			'With a string' => [
				'value' => 'hello',
			],
			'With an empty array' => [
				'value' => [],
			],
			'With an indexed array' => [
				'value' => [1, 2, 3],
			],
			'With an associative array' => [
				'value' => ['a' => 1, 'b' => 2, 'c' => 3],
			],
			'With an empty stdClass' => [
				'value' => (object) [],
			],
			'With an stdClass' => [
				'value' => (object) ['a' => 1, 'b' => 2, 'c' => 3],
			],
			'With an empty ArrayObject' => [
				'value' => new ArrayObject(),
			],
			'With an ArrayObject' => [
				'value' => new ArrayObject(['a' => 1, 'b' => 2, 'c' => 3]),
			],
		];
	}

	/*
		value(), arrayValue(), objectValue(), run(), copy()
		------------------------------------------------------------
	 */

	public function testValue()
	{
		$chain = _::chain((object) [1, 2, 3]);

		$mapCallCount = 0;
		$chain->map(function ($n) use (&$mapCallCount) {
			$mapCallCount++;
			return $n * 2;
		});
		$this->assertSame(0, $mapCallCount);

		$this->assertSame([2, 4, 6], $chain->value());
		$this->assertSame(3, $mapCallCount);
		$this->assertSame([2, 4, 6], $chain->value());
		$this->assertSame(3, $mapCallCount);

		$chain->with((object) [4, 5, 6]);
		$this->assertSame([8, 10, 12], $chain->value());
		$this->assertSame(6, $mapCallCount);
		$this->assertSame([8, 10, 12], $chain->value());
		$this->assertSame(6, $mapCallCount);

		$chain->map(function ($n) {
			return $n + 1;
		});
		$this->assertSame([9, 11, 13], $chain->value());
		$this->assertSame(9, $mapCallCount);
		$this->assertSame([9, 11, 13], $chain->value());
		$this->assertSame(9, $mapCallCount);
	}

	public function testArrayValue()
	{
		$value = _::chain([1, 2, 3])
			->map(function ($n) {
				return $n * 2;
			})
			->arrayValue();

		$this->assertSame([2, 4, 6], $value);
		$this->assertIsArray($value);
	}

	public function testObjectValue()
	{
		$value = _::chain([1, 2, 3])
			->map(function ($n) {
				return $n * 2;
			})
			->objectValue();

		$this->assertEquals((object) [2, 4, 6], $value);
		$this->assertInstanceOf('stdClass', $value);
	}

	public function testRun()
	{
		$obj = (object) ['a' => 1];

		$chain = _::chain($obj)->tap(function ($obj) {
			$obj->a = 2;
		});
		$this->assertEquals((object) ['a' => 1], $obj);

		$chain->run();
		$this->assertEquals((object) ['a' => 2], $obj);
	}

	public function testRunExamples()
	{
		ob_start();
		Dash\_::chain([1, 2, 3])
			->each(function ($n) {
				echo $n;
			})
			->run();
		$output = ob_get_clean();

		$this->assertSame('123', $output);
	}

	public function testCopy()
	{
		$original = _::chain()->map(function ($n) {
			return $n * 2;
		});

		$original->with([1, 2, 3]);
		$this->assertSame([2, 4, 6], $original->value());

		$copy = $original->copy();
		$copy->map(function ($n) {
			return $n + 1;
		});

		$copy->with([4, 5, 6]);
		$this->assertSame([9, 11, 13], $copy->value());

		$original->with([1, 2, 3]);
		$this->assertSame([2, 4, 6], $original->value());
	}

	public function testClone()
	{
		$original = _::chain()->map(function ($n) {
			return $n * 2;
		});

		$original->with([1, 2, 3]);
		$this->assertSame([2, 4, 6], $original->value());

		$copy = clone $original;
		$copy->map(function ($n) {
			return $n + 1;
		});

		$copy->with([4, 5, 6]);
		$this->assertSame([9, 11, 13], $copy->value());

		$original->with([1, 2, 3]);
		$this->assertSame([2, 4, 6], $original->value());
	}

	/*
		Invalid chaining
		------------------------------------------------------------
	 */

	public function testChainInvalidMethod()
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("No operation named 'foo' found");

		_::chain([1, 2, 3])
			->foo()
			->value();
	}
}
