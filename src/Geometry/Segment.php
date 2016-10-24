<?php

namespace Geometry;

use Collection\Collection;
use function Math\isBetween;
use function Math\isStrictBetween;
use function Math\max;
use function Math\min;

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
			is_null($this->getSlope())
			&& is_null($segment->getSlope())
			&& bccomp($this->getPointA()->getAbscissa(), $segment->getPointA()->getAbscissa(), 8) === 0
		) {
			return true;
		} else if (
			!is_null($this->getSlope())
			&& !is_null($segment->getSlope())
		) {
			return
				bccomp($this->getSlope(), $segment->getSlope(), 8) === 0
				&& bccomp($this->getOrdinateIntercept(), $segment->getOrdinateIntercept(), 8) === 0;
		}
		return false;
	}

	public function getPointOfIntersect($segment) {
		if (determinant($this, $segment) == 0) {
			return null;
		}

		if (is_null($this->getSlope())) {
			return $segment->getPointOfIntersectForNullSlope($this);
		} elseif (is_null($segment->getSlope())) {
			return $this->getPointOfIntersectForNullSlope($segment);
		}

		$intersectAbscissa = ($segment->getOrdinateIntercept() - $this->getOrdinateIntercept()) / ($this->getSlope() - $segment->getSlope());

		if (
			isBetween($intersectAbscissa, $this->getPointA()->getAbscissa(), $this->getPointB()->getAbscissa())
			&& isBetween($intersectAbscissa, $segment->getPointA()->getAbscissa(), $segment->getPointB()->getAbscissa())
			&& !$this->hasCommonEndPoint($segment)
		) {
			$intersectOrdinate = ($intersectAbscissa * $this->getSlope()) + $this->getOrdinateIntercept();
			return new Point(array($intersectAbscissa, $intersectOrdinate));
		}
		return null;
	}

	public function getPointOfIntersectForNullSlope($segment) {
		$intersectAbscissa = $segment->getPointA()->getAbscissa();

		if (isBetween($intersectAbscissa, $this->getPointA()->getAbscissa(), $this->getPointB()->getAbscissa()))
		{
			$intersectOrdinate = ($intersectAbscissa * $this->getSlope()) + $this->getOrdinateIntercept();
			$intersectPoint = new Point([$intersectAbscissa, $intersectOrdinate]);
			if (
				isBetween($intersectOrdinate, $segment->getPointA()->getOrdinate(), $segment->getPointB()->getOrdinate())
				&& !($segment->hasForEndPoint($intersectPoint)
				&& $this->hasForEndPoint($intersectPoint))
			)
			{
				return $intersectPoint;
			}
		}
		return null;
	}

	public function containsPoint(Point $point) {
		return $this->hasForEndPoint($point) || $this->strictContainsPoint($point);
	}

	public function strictContainsPoint(Point $point) {
		$segmentToCompare = new Segment($this->getPointA(), $point);

		if (is_null($this->getSlope())) {
			return
				is_null($segmentToCompare->getSlope())
				&& isStrictBetween($point->getOrdinate(), $this->pointA->getOrdinate(), $this->pointB->getOrdinate());
		}

		return
			$this->isOnSameLine($segmentToCompare)
			&& isStrictBetween($point->getAbscissa(), $this->pointA->getAbscissa(), $this->pointB->getAbscissa());
	}

	public function getOrientationRelativeToPoint(Point $point) {
		$determinant = determinant($this, new Segment($this->getPointA(), $point));
		return ($determinant > 0) - ($determinant < 0);
	}

	public function isBetweenPolygons($polygonChampion, $polygonContender) {
		return bccomp(
			$this->getOrientationRelativeToPoint($polygonChampion->getBarycenter()),
			-$this->getOrientationRelativeToPoint($polygonContender->getBarycenter()),
			8
		) === 0;
	}

	public function strictContainsSegment($segment) {
		return $this->strictContainsPoint($segment->getPointA()) && $this->strictContainsPoint($segment->getPointB());
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

	public function getPartitionsbySegment($segment)
	{
		if (!$this->isOnSameLine($segment) && !$this->hasCommonEndPoint($segment)) {
			$pointOfIntersection = $this->getPointOfIntersect($segment);

			if (!empty($pointOfIntersection) && !$this->hasForEndPoint($pointOfIntersection)) {
				return $this->splitByPoint($pointOfIntersection);
			}
			return null;
		}

		$pointA = $segment->getPointA();
		$pointB = $segment->getPointB();

		if ($this->strictContainsSegment($segment)) {
			$newSegments = $this->splitByPoint($pointA);

			$index = 1;
			if ($newSegments[0]->strictContainsPoint($pointB)) {
				$index = 0;
			}

			return $newSegments->insert($index, $newSegments[$index]->splitByPoint($pointB));
		}

		if ($this->strictContainsPoint($pointA)) {
			return $this->splitByPoint($pointA);
		} else if ($this->strictContainsPoint($pointB)) {
			return $this->splitByPoint($pointB);
		}

		return null;
	}
}
