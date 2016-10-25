<?php
namespace Geometry;

class OperationsTest extends \PHPUnit_Framework_TestCase
{
    public function providerDeterminant()
    {
        return [
            [[[0, 0], [2, 2]], [[0, 2], [2, 0]], -8],
        ];
    }

    /**
     * @dataProvider providerDeterminant
     */
	public function testDeterminant($segmentCoordinates, $otherSegmentCoordinates, $expected) {
		$segment = new Segment(
            new Point($segmentCoordinates[0]),
            new Point($segmentCoordinates[1])
        );

        $otherSegment = new Segment(
            new Point($otherSegmentCoordinates[0]),
            new Point($otherSegmentCoordinates[1])
        );

		$this->assertEquals(determinant($segment, $otherSegment), $expected);
	}
}
