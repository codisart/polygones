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

		self::assertInternalType('string', $json);
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

		self::assertEquals($point->isEqual($otherPoint), $expected);
		self::assertEquals($otherPoint->isEqual($point), $expected);
	}
}
