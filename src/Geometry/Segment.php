<?php

namespace Geometry;

use Math\Math;
use Collection\Collection;

class Segment {

	private $pointA;
	private $pointB;

	private $slope;
	private $ordinateIntercept;

	public function __construct(Point $pointA, Point $pointB) {
		$this->pointA = $pointA;
		$this->pointB = $pointB;

		if ($this->pointA->getAbscissa() - $this->pointB->getAbscissa() != 0) {
			$this->slope = ($this->pointB->getOrdinate() - $this->pointA->getOrdinate()) / ($this->pointB->getAbscissa() - $this->pointA->getAbscissa());
			$this->ordinateIntercept = $this->pointA->getOrdinate() - ($this->pointA->getAbscissa() * $this->slope);
		}
	}

	public function getPointA() {
		return $this->pointA;
	}

	public function getPointB() {
		return $this->pointB;
	}

	public function getSlope() {
		return $this->slope;
	}

	public function getOrdinateIntercept() {
		return $this->ordinateIntercept;
	}

	public function isEqual($segment) {
		return (
			$this->pointA->isEqual($segment->getPointA()) && $this->pointB->isEqual($segment->getPointB()))
			|| ($this->pointA->isEqual($segment->getPointB()) && $this->pointB->isEqual($segment->getPointA())
		);
	}

	public function hasForEndPoint($point) {
		return $point->isEqual($this->pointA) || $point->isEqual($this->pointB);
	}

	public function getOtherPoint($point) {
		if ($point->isEqual($this->pointA)) {
			return $this->pointB;
		} else if ($point->isEqual($this->pointB)) {
			return $this->pointA;
		}
		return null;
	}

	public function getMiddlePoint() {
		return new Point(array(
			($this->pointA->getAbscissa() + $this->pointB->getAbscissa()) / 2,
			($this->pointA->getOrdinate() + $this->pointB->getOrdinate()) / 2
		));
	}

	public function isOnSameLine($segment) {
		if (
			is_null($this->slope)
			&& is_null($segment->slope)
			&& bccomp($this->getPointA()->getAbscissa(),$segment->getPointA()->getAbscissa(), 8) === 0
		) {
			return true;
		} else if (
			!is_null($this->slope)
			&& !is_null($segment->slope)
		) {
			return
				bccomp($this->slope, $segment->slope, 8) === 0
				&& bccomp($this->ordinateIntercept, $segment->ordinateIntercept, 8) === 0;
		}
		return false;
	}

	public function getPointOfIntersect($segment) {
		if (Math::determinant($this, $segment) == 0) {
			return null;
		}

		if (is_null($this->slope)) {
			$intersectAbscissa = $this->getPointA()->getAbscissa();

			if (Math::isBetween($intersectAbscissa, $segment->getPointA()->getAbscissa(), $segment->getPointB()->getAbscissa()))
			{
				$intersectOrdinate = ($intersectAbscissa * $segment->slope) + $segment->ordinateIntercept;
				$intersectPoint = new Point([$intersectAbscissa, $intersectOrdinate]);
				if (
					Math::isBetween($intersectOrdinate, $this->getPointA()->getOrdinate(), $this->getPointB()->getOrdinate())
					&& !($segment->hasForEndPoint($intersectPoint)
					&& $this->hasForEndPoint($intersectPoint))
				)
				{
					return $intersectPoint;
				}
			}
			return null;
		} elseif (is_null($segment->slope)) {
			$intersectAbscissa = $segment->getPointA()->getAbscissa();

			if (Math::isBetween($intersectAbscissa, $this->getPointA()->getAbscissa(), $this->getPointB()->getAbscissa()))
			{
				$intersectOrdinate = ($intersectAbscissa * $this->slope) + $this->ordinateIntercept;
				$intersectPoint = new Point([$intersectAbscissa, $intersectOrdinate]);
				if (
					Math::isBetween($intersectOrdinate, $segment->getPointA()->getOrdinate(), $segment->getPointB()->getOrdinate())
					&& !($segment->hasForEndPoint($intersectPoint)
					&& $this->hasForEndPoint($intersectPoint))
				)
				{
					return $intersectPoint;
				}
			}
			return null;
		}

		$intersectAbscissa = ($segment->ordinateIntercept - $this->ordinateIntercept) / ($this->slope - $segment->slope);

		if (
			Math::isBetween($intersectAbscissa, $this->getPointA()->getAbscissa(), $this->getPointB()->getAbscissa())
			&& Math::isBetween($intersectAbscissa, $segment->getPointA()->getAbscissa(), $segment->getPointB()->getAbscissa())
			&& !$this->hasCommonEndPoint($segment)
		) {
			$intersectOrdinate = ($intersectAbscissa * $this->slope) + $this->ordinateIntercept;
			return new Point(array($intersectAbscissa, $intersectOrdinate));
		}
		return null;
	}

	public function containsPoint(Point $point) {
		if ($this->hasForEndPoint($point)) {
			return true;
		}

		$segmentToCompare = new Segment($this->getPointA(), $point);
		if (is_null($this->slope)) {
			return
				is_null($segmentToCompare->getSlope())
				&& Math::isStrictBetween($point->getOrdinate(), $this->pointA->getOrdinate(), $this->pointB->getOrdinate());
		} else {
			return
				$this->isOnSameLine($segmentToCompare)
				&& Math::isStrictBetween($point->getAbscissa(), $this->pointA->getAbscissa(), $this->pointB->getAbscissa());
		}
	}

	public function getOrientationRelativeToPoint(Point $point) {
		$determinant = Math::determinant($this, new Segment($this->getPointA(), $point));
		return ($determinant > 0) - ($determinant < 0);
	}

	public function isBetweenPolygons($polygonChampion, $polygonContender) {
		return bccomp(
			$this->getOrientationRelativeToPoint($polygonChampion->getBarycenter()),
			-$this->getOrientationRelativeToPoint($polygonContender->getBarycenter()),
			8
		) === 0;
	}

	public function isStrictContainedBySegment($segment) {
		return $segment->containsPoint($this->getPointA()) && $segment->containsPoint($this->getPointB());
	}

	public function getPartitionsbyVertex($vertex) {
		$pointA = $vertex->getPointA();
		$pointB = $vertex->getPointB();

		if ($vertex->isStrictContainedByVertex($this)) {
			$newVertexes = $this->splitByPoint($pointA);

			if($newVertexes[0]->containsPoint($pointB)) {
				$newVertexes->insert(0, $newVertexes[0]->splitByPoint($pointB));
			} else {
				$newVertexes->insert(1, $newVertexes[1]->splitByPoint($pointB));
			}
			return $newVertexes;
		}

		if ($this->containsPoint($pointA)) {
			$splittingPoint = $pointA;
		} else if ($this->containsPoint($pointB)) {
			$splittingPoint = $pointB;
		}

		if(!empty($splittingPoint)) {
			return $this->splitByPoint($splittingPoint);
		}

		return null;
	}

	public function getPartitionsbyEndPointOfVertex($vertex) {
		return null;
	}

	public function hasCommonEndPoint($segment)
	{
		return $segment->hasForEndPoint($this->pointA) || $segment->hasForEndPoint($this->pointB);
	}

	public function toJSON() {
		$json = "[".$this->pointA->toJSON().",".$this->pointB->toJSON()."]";
		return $json;
	}


	private function splitByPoint(Point $point) {
		$newSegments = new Collection();
		$newSegments[] = new Segment($this->getPointA(), $point);
		$newSegments[] = new Segment($point, $this->getPointB());
		return $newSegments;
	}
}
