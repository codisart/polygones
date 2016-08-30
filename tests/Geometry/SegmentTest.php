<?php

namespace GeometryTest;

use Collection\Collection;
use Geometry\Point;
use Geometry\Segment;

class SegmentTest extends \PHPUnit_Framework_TestCase
{
    public function getPointsProvider()
    {
        return [
            [[0, 0], [2, 5]],
            [[0, 1], [2, 5]],
            [[1, 0], [2, 5]],
            [[1, 1], [2, 5]],
        ];
    }

	/**
	 * @dataProvider getPointsProvider
	 */
	public function testGetPointA($pointACoordinates, $pointBCoordinates) {
		$pointA = new Point($pointACoordinates);
		$pointB = new Point($pointBCoordinates);

		$segment = new Segment($pointA, $pointB);

		$this->assertEquals($segment->getPointA(), $pointA);
	}

	/**
	 * @dataProvider getPointsProvider
	 */
	public function testGetPointB($pointACoordinates, $pointBCoordinates) {
		$pointA = new Point($pointACoordinates);
		$pointB = new Point($pointBCoordinates);

		$segment = new Segment($pointA, $pointB);

		$this->assertEquals($segment->getPointB(), $pointB);
	}

	/**
	 * @dataProvider getPointsProvider
	 */
	public function testToJSON($pointACoordinates, $pointBCoordinates) {
		$pointA = new Point($pointACoordinates);
		$pointB = new Point($pointBCoordinates);

        $segment = new Segment($pointA, $pointB);

        $json = $segment->toJSON();

		$this->assertInternalType('string', $json);
		$this->assertEquals([$pointACoordinates, $pointBCoordinates], json_decode($json));
	}

    public function isEqualProvider()
    {
        return [
            [[[0, 0], [2, 5]], [[0, 0], [2, 5]], true],
            [[[0, 1], [2, 5]], [[2, 5], [0, 1]], true],
            [[[1, 0], [2, 5]], [[1, 0], [3, 4]], false],
            [[[1, 1], [2, 5]], [[1, 1], [0, 0]], false],
        ];
    }

	/**
	 * @dataProvider isEqualProvider
	 */
	public function testIsEqual($segmentCoordinates, $otherSegmentCoordinates, $expected) {
		$segment = new Segment(
			new Point($segmentCoordinates[0]),
			new Point($segmentCoordinates[1])
		);

		$otherSegment = new Segment(
			new Point($otherSegmentCoordinates[0]),
			new Point($otherSegmentCoordinates[1])
		);

		$this->assertEquals($segment->isEqual($otherSegment), $expected);
	}

    public function providerHasForEndPoint()
    {
        return [
            [[[0, 0], [2, 5]], [2, 5], true],
            [[[0, 1], [2, 5]], [0, 1], true],
            [[[1, 0], [2, 5]], [3, 4], false],
            [[[1, 1], [2, 5]], [0, 0], false],
        ];
    }

	/**
	 * @dataProvider providerHasForEndPoint
	 */
	public function testHasForEndPoint($segmentCoordinates, $pointCoordinates, $expected) {
		$segment = new Segment(
			new Point($segmentCoordinates[0]),
			new Point($segmentCoordinates[1])
		);

		$point = new Point($pointCoordinates);

		$this->assertEquals($segment->hasForEndPoint($point), $expected);
    }

    public function getOtherPointProvider()
    {
        return [
            [[[0, 0], [2, 5]], [2, 5], [0, 0]],
            [[[0, 1], [2, 5]], [0, 1], [2, 5]],
            [[[1, 0], [2, 5]], [3, 4], null],
            [[[1, 1], [2, 5]], [0, 0], null],
        ];
    }

	/**
	 * @dataProvider getOtherPointProvider
	 */
	public function testGetOtherPoint($segmentCoordinates, $pointCoordinates, $expected) {
        $segment = new Segment(
			new Point($segmentCoordinates[0]),
			new Point($segmentCoordinates[1])
		);

		$point = new Point($pointCoordinates);

        if ($expected) {
            $expected = new Point($expected);
        }

		$this->assertEquals($segment->getOtherPoint($point), $expected);
	}


    public function isOnSameLineProvider()
    {
        return [
            [[[0, 0], [2, 5]], [[0, 0], [2, 5]], true],
            [[[1, 0], [2, 5]], [[2, 5], [1, 0]], true],
            [[[1, 1], [2, 2]], [[3, 3], [4, 4]], true],
            [[[1, 1], [1, 2]], [[1, 3], [1, 4]], true],

            [[[1, 1], [1, 2]], [[2, 3], [2, 4]], false],
            [[[0, 1], [2, 5]], [[0, 0], [2, 5]], false],
        ];
    }

    /**
     * @dataProvider isOnSameLineProvider
     */
    public function testIsOnSameLine($segmentCoordinates, $otherSegmentCoordinates, $expected) {
        $segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $otherSegment = new Segment(
            new Point($otherSegmentCoordinates[0]),
            new Point($otherSegmentCoordinates[1])
        );

        $this->assertEquals($segment->isOnSameLine($otherSegment), $expected);
    }

    public function getPointOfIntersectProvider()
    {
        return [
            [[[0, 0], [2, 2]], [[0, 2], [2, 0]], [1, 1]],
            [[[0, 4], [2, 2]], [[0, 0], [4, 4]], [2, 2]],
            [[[1, 1], [1, 5]], [[0, 0], [5, 5]], [1, 1]],
            [[[1, 4], [1, 5]], [[0, 0], [1, 1]], null],
            [[[0, 0], [2, 2]], [[0, 2], [2, 4]], null],
            [[[4, 5], [4, 4]], [[5, 0], [1, 4]], null],
            [[[82.9562,98.729],[52.16232,12.5954]], [[91.12968,35.716],[38.349,67.74]], [65.90079400141, 51.023302695207]],

            [[[0, 2], [2, 0]], [[0, 0], [2, 2]], [1, 1]],
            [[[0, 0], [4, 4]], [[0, 4], [2, 2]], [2, 2]],
            [[[0, 0], [5, 5]], [[1, 1], [1, 5]], [1, 1]],
            [[[0, 0], [1, 1]], [[1, 4], [1, 5]], null],
            [[[0, 2], [2, 4]], [[0, 0], [2, 2]], null],
            [[[5, 0], [1, 4]], [[4, 5], [4, 4]], null],

            [[[5, 0], [1, 4]], [[14, 5], [13, 4]], null],
        ];
    }

    /**
     * @dataProvider getPointOfIntersectProvider
     */
    public function testGetPointOfIntersect($segmentCoordinates, $otherSegmentCoordinates, $expected) {
        $segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $otherSegment = new Segment(
            new Point($otherSegmentCoordinates[0]),
            new Point($otherSegmentCoordinates[1])
        );

        if ($expected) {
            $expected = new Point($expected);
        }

        $this->assertEquals($expected, $segment->getPointOfIntersect($otherSegment));
    }

    public function providerGetMiddlePoint()
    {
        return [
            [[[0, 0], [2, 2]], [1, 1]],
            [[[0, 5], [5, 5]], [2.5, 5]],
        ];
    }

    /**
     * @dataProvider providerGetMiddlePoint
     */
    public function testGetMiddlePoint($segmentCoordinates, $middlePointCoordinates) {
        $segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $middlePoint = new Point($middlePointCoordinates);

        $this->assertEquals($segment->getMiddlePoint(), $middlePoint);
    }

    public function providerContainsPoint()
    {
        return [
            [[[0, 0], [2, 2]], [1, 1], true],
            [[[0, 5], [5, 5]], [2.5, 5], true],
            [[[0, 0], [0, 5]], [0, 2], true],

            [[[0, 5], [5, 5]], [3, 4], false],
            [[[0, 0], [0, 5]], [1, 2], false],

            // endpoints are considered inside
            [[[0, 0], [2, 5]], [2, 5], true],
            [[[0, 1], [2, 5]], [0, 1], true],
        ];
    }

    /**
     * @dataProvider providerContainsPoint
     */
    public function testContainsPoint($segmentCoordinates, $pointCoordinates, $expected) {
        $segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $point = new Point($pointCoordinates);

        $this->assertEquals($expected, $segment->containsPoint($point));
    }

    public function providerGetOrientationRelativeToPoint()
    {
        return [
            [[[0, 0], [2, 2]], [1, 1], 0],
            [[[0, 5], [5, 5]], [2.5, 5], 0],
            [[[0, 0], [0, 5]], [0, 2], 0],

            [[[0, 5], [5, 5]], [3, 4], -1],
            [[[0, 0], [0, 5]], [1, 2], -1],
            [[[0, 5], [0, 0]], [1, 2], 1],

            // endpoints are considered inside
            [[[0, 0], [2, 5]], [2, 5], 0],
            [[[0, 1], [2, 5]], [0, 1], 0],
        ];
    }

    /**
     * @dataProvider providerGetOrientationRelativeToPoint
     */
    public function testGetOrientationRelativeToPoint($segmentCoordinates, $pointCoordinates, $expected)
    {
        $segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $point = new Point($pointCoordinates);

        $this->assertEquals($expected, $segment->getOrientationRelativeToPoint($point));
    }

    public function providerGetPartitionsbySegment()
    {
        return [
            [
                [[0, 0], [2, 2]],
                [[1, 1], [4, 5]],
                [[[0, 0], [1, 1]], [[1, 1],[2, 2]]]
            ],
            [
                [[1, 1], [4, 5]],
                [[0, 0], [2, 2]],
                null
            ],
            [
                [[0, 0], [2, 2]],
                [[0, 2], [2, 0]],
                [[[0, 0], [1, 1]], [[1, 1],[2, 2]]]
            ],
            [
                [[0, 0], [2, 2]],
                [[1, 0],[2, 2]],
                null
            ],

            // same line
            [
                [[0, 0], [2, 2]],
                [[1, 1],[2, 2]],
                [[[0, 0], [1, 1]], [[1, 1],[2, 2]]]
            ],
            [
                [[0, 0], [3, 3]],
                [[1, 1],[2, 2]],
                [[[0, 0], [1, 1]], [[1, 1],[2, 2]], [[2, 2],[3, 3]]]
            ],
            [
                [[0, 0], [3, 3]],
                [[2, 2],[1, 1]],
                [[[0, 0], [1, 1]], [[1, 1],[2, 2]], [[2, 2],[3, 3]]]
            ],
            [
                [[0, 0], [3, 3]],
                [[2, 2],[5, 5]],
                [[[0, 0], [2, 2]], [[2, 2],[3, 3]]]
            ],
            [
                [[2, 2],[5, 5]],
                [[0, 0], [3, 3]],
                [[[2, 2],[3, 3]], [[3, 3], [5, 5]]]
            ],
            [
                [[2, 2],[5, 5]],
                [[6, 6], [8, 8]],
                null
            ],
        ];
    }

    /**
     * @dataProvider providerGetPartitionsbySegment
     */
    public function testGetPartitionsbySegment($segmentACoordinates, $segmentBCoordinates, $expected)
    {
        $segmentA = new Segment(
            new Point($segmentACoordinates[0]),
            new Point($segmentACoordinates[1])
        );

        $segmentB = new Segment(
            new Point($segmentBCoordinates[0]),
            new Point($segmentBCoordinates[1])
        );

        $segments = $segmentA->getPartitionsbySegment($segmentB);

        $json = null;
        if ($segments) {
            $json = [];
            foreach ($segments as $key => $segment) {
                $json[] = json_decode($segment->toJSON());
            }
        }

        $this->assertEquals($expected, $json);
    }
}
