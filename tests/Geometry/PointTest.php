<?php

namespace GeometryTest;

use Geometry\Point;

class PointTest extends \PHPUnit_Framework_TestCase
{
    public function coordinatesProvider()
    {
        return [
            [0, 0],
            [0, 1],
            [1, 0],
            [1, 1]
        ];
    }

	/**
	 * @dataProvider coordinatesProvider
	 */
	public function testGetAbscissa($abscissa, $ordinate) {
		$point = new Point([
			$abscissa,
			$ordinate,
		]);

		$this->assertEquals($point->getAbscissa(), $abscissa);
	}

	/**
	 * @dataProvider coordinatesProvider
	 */
	public function testGetOrdinate($abscissa, $ordinate) {
		$point = new Point([
			$abscissa,
			$ordinate,
		]);

		$this->assertEquals($point->getOrdinate(), $ordinate);
	}

	public function twoPointsCoordinatesProvider()
	{
		return [
			[[0, 0], [0, 0], true],
			[[0, 1], [1, 0], false],
			[[2.554, 1], [2.555, 1], false],
			[[2.554, 1], [2.554, 1], true],
		];
	}

	/**
	 * @dataProvider twoPointsCoordinatesProvider
	 */
	public function testIsEqual($pointCoordinates, $otherPointCoordinates, $expected) {
		$point 		= new Point($pointCoordinates);
		$otherPoint = new Point($otherPointCoordinates);

		$this->assertEquals($point->isEqual($otherPoint), $expected);
		$this->assertEquals($otherPoint->isEqual($point), $expected);
	}
}
