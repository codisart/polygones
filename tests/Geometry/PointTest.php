<?php

namespace GeometryTest;

use Geometry\Point;

class PointTest extends \PHPUnit_Framework_TestCase
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

		$this->assertEquals($point->getAbscissa(), $abscissa);
		$this->assertEquals($point->getOrdinate(), $ordinate);
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

		$this->assertInternalType('string', $json);
		$this->assertEquals([$abscissa, $ordinate], json_decode($json));
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

		$this->assertEquals($point->isEqual($otherPoint), $expected);
		$this->assertEquals($otherPoint->isEqual($point), $expected);
	}
}
