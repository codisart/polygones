<?php
namespace Math;

use PHPUnit\Framework\TestCase;

/**
 * Class OperationsTest
 * @package Math
 */
class OperationsTest extends TestCase
{
	public function providerMin()
	{
		return [
			[[1 , null, 3, -2], -2],
			[[1], 1],
		];
	}

    /**
     * @dataProvider providerMin
     *
     * @param $args
     * @param $expected
     */
	public function testMin($args, $expected)
	{
		self::assertEquals($expected, min(...$args));
	}

	public function providerMax()
	{
		return [
			[[1 , null, 3, -2], 3],
			[[1], 1],
		];
	}

    /**
     * @dataProvider providerMax
     *
     * @param $args
     * @param $expected
     */
	public function testMax($args, $expected)
	{
		self::assertEquals($expected, max(...$args));
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
			[35, 5,	0, false],
		];
	}

	/**
	 * @dataProvider betweenProvider
     *
     * @param $int
     * @param $first
     * @param $second
     * @param $result
	 */
	public function testIsBetween($int, $first, $second, $result) {
		self::assertEquals(isBetween($int, $first, $second), $result);
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
			[35, 5,	0, false],
		];
	}

    /**
     * @dataProvider strictBetweenProvider
     *
     * @param $int
     * @param $first
     * @param $second
     * @param $result
     */
	public function testIsStrictBetween($int, $first, $second, $result) {
		self::assertEquals(isStrictBetween($int, $first, $second), $result);
	}
}
