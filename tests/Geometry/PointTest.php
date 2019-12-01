<?php
namespace Geometry;

use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
	public function providerCoordinates()
	{
		return [
			[0, 0],
			[0, 1],
			[1, 0],
			[1, 1]
		];
	}

	/**
	 * @dataProvider providerCoordinates
	 */
	public function testGetCoordinates($abscissa, $ordinate) {
		$point = new Point([
			$abscissa,
			$ordinate,
		]);

		self::assertEquals($point->getAbscissa(), $abscissa);
		self::assertEquals($point->getOrdinate(), $ordinate);
	}

	/**
	 * @dataProvider providerCoordinates
	 */
	public function testToJSON($abscissa, $ordinate) {
		$point = new Point([
			$abscissa,
			$ordinate,
		]);

		$json = $point->toJSON();

		self::assertIsString($json);
		self::assertEquals([$abscissa, $ordinate], json_decode($json));
	}

	public function providerTwoPointsCoordinates()
	{
		return [
			[[0, 0], [0, 0], true],
			[[0, 1], [1, 0], false],
			[[2.554, 1], [2.555, 1], false],
			[[2.554, 1], [2.554, 1], true],
		];
	}

	/**
	 * @dataProvider providerTwoPointsCoordinates
	 */
	public function testIsEqual($pointCoordinates, $otherPointCoordinates, $expected) {
		$point = new Point($pointCoordinates);
		$otherPoint = new Point($otherPointCoordinates);

		self::assertSame($expected, $point->isEqual($otherPoint));
		self::assertSame($expected, $otherPoint->isEqual($point));
	}

	public function providerTwoPointsCoordinatesStrictlyHigher()
	{
		return [
			[[0, 0], [0, 0], false],
			[[0, 1], [1, 0], true],
			[[2.554, 1], [2.554, 1], false],
			[[2.554, 1], [2.554, 4], false],
			[[2.554, 9], [2.554, 4], true],
		];
	}

	/**
	 * @dataProvider providerTwoPointsCoordinatesStrictlyHigher
	 */
	public function testIsStrictlyHigher($pointCoordinates, $otherPointCoordinates, $expected) {
		$point = new Point($pointCoordinates);
		$otherPoint = new Point($otherPointCoordinates);

		self::assertSame($expected, $point->isStrictlyHigher($otherPoint));
	}

	public function providerTwoPointsCoordinatesLower()
	{
		return [
			[[0, 0], [0, 0], true],
			[[0, 1], [1, 0], false],
			[[2.554, 1], [2.554, 1], true],
			[[2.554, 1], [2.554, 4], true],
			[[2.554, 9], [2.554, 4], false],
		];
	}

	/**
	 * @dataProvider providerTwoPointsCoordinatesLower
	 */
	public function testIsLower($pointCoordinates, $otherPointCoordinates, $expected) {
		$point = new Point($pointCoordinates);
		$otherPoint = new Point($otherPointCoordinates);

		self::assertSame($expected, $point->isLower($otherPoint));
	}
}
