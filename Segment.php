<?php

namespace Utility;

use Utility\Point;

/**
* segment
*/
class Segment {

	private $pointA;
	private $pointB;

	public $coefficientDirecteur;
	public $ordonneeOrigine;

	public function __construct(Point $pointA, Point $pointB) {
		$this->pointA = $pointA;
		$this->pointB = $pointB;

		if ($this->pointA->getAbscisse() - $this->pointB->getAbscisse() != 0) {
			$this->coefficientDirecteur = ($this->pointB->getOrdonnee() - $this->pointA->getOrdonnee()) / ($this->pointB->getAbscisse() - $this->pointA->getAbscisse());
			$this->ordonneeOrigine = $this->pointA->getOrdonnee() - ($this->pointA->getAbscisse() * $this->coefficientDirecteur);
		}
	}

	public function getPointA() {
		return $this->pointA;
	}

	public function getPointB() {
		return $this->pointB;
	}

	public function getOtherPoint($point) {
		if ($point->isEqual($this->pointA)) {
			return $this->pointB;
		} else if ($point->isEqual($this->pointB)) {
			return $this->pointA;
		}
		return null;
	}


	public function isEqual($segment) {
		return ($this->pointA->isEqual($segment->getPointA()) && $this->pointB->isEqual($segment->getPointB())) || ($this->pointA->isEqual($segment->getPointB()) && $this->pointB->isEqual($segment->getPointA()));
	}


	public function isOnSameLine($segment) {
		if (
			is_null($this->coefficientDirecteur)
			&& is_null($segment->coefficientDirecteur)
			&& bccomp($this->getPointA()->getAbscisse(),$segment->getPointA()->getAbscisse(), 8) === 0
		) {
			return true;
		} else if (
			!is_null($this->coefficientDirecteur)
			&& !is_null($segment->coefficientDirecteur)
		) {
			return
				bccomp($this->coefficientDirecteur, $segment->coefficientDirecteur, 8) === 0
				&& bccomp($this->ordonneeOrigine, $segment->ordonneeOrigine, 8) === 0;
		}
		return false;
	}

	public function getPointOfIntersect($segment) {
		if (Math::determinant($this, $segment) == 0) {
			return null;
		}

		if (is_null($this->coefficientDirecteur)) {
			$abscisseIntersection = $this->getPointA()->getAbscisse();
		} elseif (is_null($segment->coefficientDirecteur)) {
			$abscisseIntersection = $segment->getPointA()->getAbscisse();
		} else {
			$abscisseIntersection = ($segment->ordonneeOrigine - $this->ordonneeOrigine) / ($this->coefficientDirecteur - $segment->coefficientDirecteur);
		}

		if (
			Math::isBetween($abscisseIntersection, $this->getPointA()->getAbscisse(), $this->getPointB()->getAbscisse())
			&& Math::isBetween($abscisseIntersection, $segment->getPointA()->getAbscisse(), $segment->getPointB()->getAbscisse())
		) {
			$ordonneeIntersection = ($abscisseIntersection * $this->coefficientDirecteur) + $this->ordonneeOrigine;
			return new Point(array($abscisseIntersection, $ordonneeIntersection));
		}
		return null;
	}

	public function partionnedByPoint(Point $point) {
		if ($this->pointA->isEqual($point) || $this->pointB->isEqual($point)) {
			return $this;
		} else if ($this->contient($point)) {
			$newSegments = new SegmentCollection();
			$newSegments[] = new Segment($this->pointA, $point);
			$newSegments[] = new Segment($point, $this->pointB);
			return $newSegments;
		}
		return $this;
	}

	public function getMiddlePoint() {
		return new Point(array(
			($this->pointA->getAbscisse() + $this->pointB->getAbscisse()) / 2,
			($this->pointA->getOrdonnee() + $this->pointB->getOrdonnee()) / 2
		));
	}

	public function containsPoint(Point $point) {
		$segmentToCompare = new Segment($this->getPointA(), $point);
		if (is_null($this->coefficientDirecteur)) {
			return $this->isOnSameLine($segmentToCompare) && Math::isBetween($point->getOrdonnee(), $this->pointA->getOrdonnee(), $this->pointB->getOrdonnee());
		} else {
			return $this->isOnSameLine($segmentToCompare) && Math::isBetween($point->getAbscisse(), $this->pointA->getAbscisse(), $this->pointB->getAbscisse());
		}
	}

	public function toJSON() {
		$json = "[".$this->pointA->toJSON().",".$this->pointB->toJSON()."]";
		return $json;
	}

	public function splitByPoint(Point $point) {
		$newSegments = new Collection();
		$newSegments[] = new Segment($this->getPointA(), $point);
		$newSegments[] = new Segment($point, $this->getPointB());
		return $newSegments;
	}

	public function getOrientationRelativeToPoint(Point $point) {
		$segmentToBarycenter = new Segment($this->getPointA(), $point);
		$determinant = Math::determinant($this, $segmentToBarycenter);

		return ($determinant > 0) - ($determinant < 0);
	}

	public function isEdgeOf(Polygone $polygon) {
		$edges = $polygon->getSegments();

		foreach ($edges as $key => $edge) {
			if ($this->isEqual($edge)) {
				return true;
			}
		}
		return false;
	}
}
