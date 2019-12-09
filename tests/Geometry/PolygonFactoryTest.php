<?php

namespace Geometry;

use Collection\PolygonCollection;
use Collection\SegmentCollection;
use PHPUnit\Framework\TestCase;

class PolygonFactoryTest extends TestCase
{    
    public function providerBuildFromSegments()
    {
        return [
            [
                [
                    [[0, 0], [0, 5]],
                    [[5, 5], [5, 0]],
                    [[0, 5], [5, 5]],
                    [[5, 0], [0, 0]],
                ],
                [
                    [[0, 0], [0, 5], [5, 5], [5, 0], [0, 0]],
                ]
            ],
            [
                [
                    [[0, 0], [0, 5]],
                    [[0, 5], [5, 5]],
                    [[5, 5], [5, 0]],
                    [[5, 0], [0, 0]],
                    [[10, 10], [10, 15]],
                    [[10, 15], [15, 15]],
                    [[15, 15], [15, 10]],
                    [[15, 10], [10, 10]],
                ],
                [
                    [[0, 0], [0, 5], [5, 5], [5, 0], [0, 0]],
                    [[10, 10], [10, 15], [15, 15], [15, 10], [10, 10]],
                ]
            ],
            [
                [
                    [[0, 0], [0, 5]],
                    [[1, 5], [5, 5]],
                    [[5, 1], [5, 0]],
                    [[0, 5], [1, 5]],
                    [[5, 5], [5, 1]],
                    [[5, 0], [0, 0]],
                ],
                [
                    [[0, 0], [0, 5], [1, 5], [5, 5], [5, 1], [5, 0], [0, 0]],
                ]
            ]
        ];
    }

    public function testExceptionBuildFromSegments()
    {
        $collection = new PolygonCollection;

        $this->expectException('TypeError');
        PolygonFactory::buildFromSegments($collection);
    }

    /**
     * @dataProvider providerBuildFromSegments
     */
    public function testBuildFromSegments($segmentsCoordinate, $expectedPolygon)
    {
        $segments = new SegmentCollection;
        foreach ($segmentsCoordinate as $key => $segmentCoordinate) {
            $segments[] = Segment::create(
                new Point($segmentCoordinate[0]),
                new Point($segmentCoordinate[1])
            );
        }

        $json = [];
        foreach (PolygonFactory::buildFromSegments($segments) as $polygon) {
            $json[] = json_decode($polygon->toJSON());
        }

        self::assertEquals($expectedPolygon, $json);
    }
}