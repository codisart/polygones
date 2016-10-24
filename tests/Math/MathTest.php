<?php

namespace MathTest;

use Math\Math;
use Geometry\Segment;
use Geometry\Point;

class MathTest extends \PHPUnit_Framework_TestCase
{
	public function providerMin()
	{
		return [
			[[1, null, 3, -2], -2],
			[[1], 1],
		];
	}

	/**
	 * @dataProvider providerMin
	 */
	public function testMin($args, $expected)
	{
		$this->assertEquals($expected, Math::min(...$args));
	}

	public function providerMax()
	{
		return [
			[[1, null, 3, -2], 3],
			[[1], 1],
		];
	}

	/**
	 * @dataProvider providerMax
	 */
	public function testMax($args, $expected)
	{
		$this->assertEquals($expected, Math::max(...$args));
	}

	public function betweenProvider()
	{
		return [
			[0, 0, 0, true],
			[2, 0, 5, true],
			[2, 5, 0, true],
			[0, 1, 0, true],
			[1, 1, 0, true],
			[0, 0, 1, true],
			[1, 0, 1, true],

			[1, 0, 0, false],
			[35, 0, 5, false],
			[35, 5, 0, false],
		];
	}

	/**
	 * @dataProvider betweenProvider
	 */
	public function testIsBetween($int, $first, $second, $result) {
		$this->assertEquals(Math::isBetween($int, $first, $second), $result);
	}

	public function strictBetweenProvider()
	{
		return [
			[2, 0, 5, true],
			[2, 5, 0, true],

			[0, 0, 0, false],

			[0, 1, 0, false],
			[1, 1, 0, false],
			[0, 0, 1, false],
			[1, 0, 1, false],

			[1, 0, 0, false],
			[35, 0, 5, false],
			[35, 5, 0, false],
		];
	}

	/**
	 * @dataProvider strictBetweenProvider
	 */
	public function testIsStrictBetween($int, $first, $second, $result) {
		$this->assertEquals(Math::isStrictBetween($int, $first, $second), $result);
	}
}
