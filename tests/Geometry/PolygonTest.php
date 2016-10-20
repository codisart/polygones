<?php

namespace GeometryTest;

use Collection\Collection;
use Geometry\Polygon;
use Geometry\Point;
use Geometry\Segment;

class PolygonTest extends \PHPUnit_Framework_TestCase
{
	public function instanceProvider()
	{
		return [
			[[[0,0],[0,5],[5,5],[5,0],[0,0]]],
			[[[1,1],[1,5],[5,5],[5,1],[1,1]]],
		];
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testNewInstance($pointsListe) {
		$instance = new Polygon($pointsListe);

		$this->assertInstanceOf(Polygon::class, $instance);
	}

	public function failInstanceProvider()
	{
		return [
			[[]],
			[[[1,1],[1,5]]],
			[[[0,0],[0,5],[5,5],[5,0],[0,1]]],
			[[[82.9562,98.729],[52.16232,12.5954],[95.491569,14.434342],[46.718,98.5924]]],
		];
	}

	/**
	 * @dataProvider failInstanceProvider
	 */
	public function testFailInstanceProvider($pointsListe) {
		$this->expectException(\Exception::class);

		$instance = new Polygon($pointsListe);
		$this->assertInstanceOf(Polygon::class, $instance);
	}

	/**
	 * @dataProvider instanceProvider
	 */
	public function testGetSegments($pointsListe) {
		$instance = new Polygon($pointsListe);

		$this->assertInstanceOf(Collection::class, $instance->getSegments());
	}

	public function providerGetBoundingBox()
	{
		return [
			[[[0,0], [0,5], [5,5], [5,0], [0,0]], [[5, 5], [0, 0]]],
			[[[1,1], [1,5], [5,5], [5,1], [1,1]], [[5, 5], [1, 1]]],
		];
	}

	/**
	 * @dataProvider providerGetBoundingBox
	 */
	public function testGetBoundingBox($pointsListe, $boudingBox) {
		$instance = new Polygon($pointsListe);

		$this->assertEquals($boudingBox, $instance->getBoundingBox());
	}

	public function providerContainsPoint()
	{
		return [
			[[[0, 0], [0, 5], [5, 5], [5, 0], [0, 0]], [4, 4], true],
			[[[1, 1], [1, 5], [5, 5], [5, 1], [1, 1]], [6, 6], false],
			[[[1, 1], [1, 5], [5, 5], [5, 1], [1, 1]], [5, 3], false],
		];
	}

	/**
	 * @dataProvider providerContainsPoint
	 */
	public function testContainsPoint($polygonCoordinates, $pointCoordinates, $expected)
	{
		$instance = new Polygon($polygonCoordinates);
		$point = new Point($pointCoordinates);
		$this->assertEquals($expected, $instance->containsPoint($point));
	}

	public function providerGetAllSegmentsIntersectionWith()
	{
		return [
			[
				[[0,0], [0,5], [5,5], [5,0], [0,0]],
				[[10,10], [10,15], [15,15], [15,10], [10,10]],
				[
					[[0,0],[0,5]],
					[[0,5],[5,5]],
					[[5,5],[5,0]],
					[[5,0],[0,0]],
					[[10,10],[10,15]],
					[[10,15],[15,15]],
					[[15,15],[15,10]],
					[[15,10],[10,10]],
				],
			],
			[
				[[1,1], [1,5], [5,5], [5,1], [1,1]],
				[[3,0], [3,6], [7,6], [7,0], [3,0]],
				[
					[[1,1],[1,5]],
					[[1,5],[3,5]],
					[[3,1],[1,1]],
					[[3,0],[3,1]],
					[[3,5],[3,6]],
					[[3,6],[7,6]],
					[[7,6],[7,0]],
					[[7,0],[3,0]],
				],
			],
			[
				[[0,0], [0,5], [5,5], [5,0], [0,0]],
				[[1,1], [1,5], [5,5], [5,1], [1,1]],
				[
					[[0,0],[0,5]],
					[[0,5],[1,5]],
					[[1,5],[5,5]],
					[[5,5],[5,1]],
					[[5,1],[5,0]],
					[[5,0],[0,0]],
				],
			],
			[
				[[0,0], [0,5], [5,5], [5,0], [0,0]],
				[[10,0], [10,5], [5,5], [5,0], [10,0]],
				[
					[[0,0],[0,5]],
					[[0,5],[5,5]],
					[[5,0],[0,0]],
					[[10,0],[10,5]],
					[[10,5],[5,5]],
					[[5,0],[10,0]],
				],
			],
		];
	}

	/**
	 * @dataProvider providerGetAllSegmentsIntersectionWith
	 */
	public function testGetAllSegmentsIntersectionWith($polygonACoordinates, $polygonBCoordinates, $expectedCoordinates)
	{
		$polygonA = new Polygon($polygonACoordinates);
		$polygonB = new Polygon($polygonBCoordinates);

		$segments = $polygonA->getAllSegmentsIntersectionWith($polygonB);

		$this->assertInstanceOf(Collection::class, $segments);

		$json = [];
		foreach ($segments as $key => $segment) {
			$json[] = [
				[$segment->getPointA()->getAbscissa(), $segment->getPointA()->getOrdinate()],
				[$segment->getPointB()->getAbscissa(), $segment->getPointB()->getOrdinate()],
			];
		}

		$this->assertEquals($expectedCoordinates, $json);
	}

	public function providerGetBarycenter()
	{
		return [
			[[[0, 0], [0, 5], [5, 5], [5, 0], [0, 0]], [2.5, 2.5]],
		];
	}

	/**
	 * @dataProvider providerGetBarycenter
	 */
	public function testGetBarycenter($polygonCoordinates, $expectedPointCoordinates)
	{
		$polygon = new Polygon($polygonCoordinates);
		$point   = new Point($expectedPointCoordinates);

		$barycenter = $polygon->getBarycenter();

		$this->assertInstanceOf(Point::class, $barycenter);
		$this->assertEquals($point->toJSON(), $barycenter->toJSON());
	}

	public function providerBuildFromSegments()
	{
		return [
			[
				[
					[[0,0],[0,5]],
					[[5,5],[5,0]],
					[[0,5],[5,5]],
					[[5,0],[0,0]],
				],
				[
					[[0,0], [0,5], [5,5], [5,0], [0,0]],
				]
			],
			[
				[
					[[0,0],[0,5]],
					[[0,5],[5,5]],
					[[5,5],[5,0]],
					[[5,0],[0,0]],
					[[10,10],[10,15]],
					[[10,15],[15,15]],
					[[15,15],[15,10]],
					[[15,10],[10,10]],
				],
				[
					[[0,0], [0,5], [5,5], [5,0], [0,0]],
					[[10,10], [10,15], [15,15], [15,10], [10,10]],
				]
			],
			[
				[
					[[0,0],[0,5]],
					[[1,5],[5,5]],
					[[5,1],[5,0]],
					[[0,5],[1,5]],
					[[5,5],[5,1]],
					[[5,0],[0,0]],
				],
				[
					[[0,0], [0,5], [1,5], [5,5], [5,1], [5,0], [0,0]],
				]
			]
		];
	}

	/**
	 * @dataProvider providerBuildFromSegments
	 */
	public function testBuildFromSegments($segmentsCoordinate, $expectedPolygon)
	{
		$segments = new Collection;
		foreach ($segmentsCoordinate as $key => $segmentCoordinate) {
			$segments[] = new Segment(
				new Point($segmentCoordinate[0]),
				new Point($segmentCoordinate[1])
			);
		}

		$json = [];
		foreach (Polygon::buildFromSegments($segments) as $polygon) {
			$json[] = json_decode($polygon->toJSON());
		}

		$this->assertEquals($expectedPolygon, $json);
	}

	public function testExceptionBuildFromSegments()
	{
		$collection = new Collection;

		$collection[] = new Point([1,1]);

		$this->expectException(\Exception::class);
		Polygon::buildFromSegments($collection);
	}

	public function providerUnion()
	{
		return [
			[
				[[0,0], [0,5], [5,5], [5,0], [0,0]],
				[[1,1], [1,5], [5,5], [5,1], [1,1]],
				[
					[[0,0], [0,5], [1,5], [5,5], [5,1], [5,0], [0,0]],
				],
			],
			[
				[[0,0], [0,5], [5,5], [5,0], [0,0]],
				[[1,1], [1,5], [4,5], [4,1], [1,1]],
				[
					[[0,0], [0,5], [1,5], [4,5], [5,5], [5,0], [0,0]],
				],
			],
		];
	}

	/**
	 * @dataProvider providerUnion
	 */
	public function testUnion($polygonACoordinates, $polygonBCoordinates, $expectedPolygonsCoordinates)
	{
		$polygonA 	= new Polygon($polygonACoordinates);
		$polygonB   = new Polygon($polygonBCoordinates);

		$json = [];
		foreach ($polygonA->union($polygonB) as $polygon) {
			$json[] = json_decode($polygon->toJSON());
		}

		$this->assertEquals($expectedPolygonsCoordinates, $json);
	}
}
