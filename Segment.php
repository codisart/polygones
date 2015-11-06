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


	public function isOnSameDroite($segment) {
		if (is_null($this->coefficientDirecteur) && is_null($segment->coefficientDirecteur) && $this->pointA->getAbscisse() == $segment->getPointA()->getAbscisse()) {
			return true;
		} else if (!is_null($this->coefficientDirecteur) && !is_null($segment->coefficientDirecteur)) {
			return ($this->coefficientDirecteur === $segment->coefficientDirecteur && $this->ordonneeOrigine === $segment->ordonneeOrigine);
		}
	}

	public function getPointOfIntersect($segment) {
		if (Math::determinant($this, $segment) == 0) {
			return null;
		}

		$abscisseIntersection = ($segment->ordonneeOrigine - $this->ordonneeOrigine) / ($this->coefficientDirecteur - $segment->coefficientDirecteur);

		if(
			Math::isBetween($abscisseIntersection, $this->pointA->getAbscisse(), $this->pointB->getAbscisse())
			&&
			Math::isBetween($abscisseIntersection, $segment->pointA->getAbscisse(), $segment->pointB->getAbscisse())
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

	public function getMidpoint() {
		return new Point (array(
			($this->pointA->getAbscisse() +  $this->pointB->getAbscisse()) / 2,
			($this->pointA->getOrdonnee() +  $this->pointB->getOrdonnee()) / 2
		));
	}

	public function contient(Point $point) {
		if (is_null($this->coefficientDirecteur)) {
			return Math::isBetween($point->getOrdonnee(), $this->pointA->getOrdonnee(), $this->pointB->getOrdonnee());
		} else {
			return Math::isBetween($point->getAbscisse(), $this->pointA->getAbscisse(), $this->pointB->getAbscisse());
		}
	}

	public function toJSON() {
		$json = "[".$this->pointA->toJSON().",".$this->pointB->toJSON()."]";
		return $json;
	}
}
