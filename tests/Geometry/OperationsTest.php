<?php
namespace Geometry;

use PHPUnit\Framework\TestCase;

/**
 * Class OperationsTest
 * @package Geometry
 */
class OperationsTest extends TestCase
{
    public function providerDeterminant()
    {
        return [
            [
                Segment::create(new Point([0, 0]), new Point([2, 2])),
                Segment::create(new Point([0, 2]), new Point([2, 0])),
                -8
            ],
        ];
    }

    /**
     * @dataProvider providerDeterminant
     */
    public function testDeterminant($segment, $otherSegment, $expected) {
        $determinant = determinant($segment, $otherSegment);

        self::assertIsInt($determinant);
        self::assertSame($expected, $determinant);
    }
}
