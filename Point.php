<?php

namespace Utility;

/**
* point
*/
class Point {

	// axe X
	private $abscisse;

	// axe Y
	private $ordonnee;

	public function __construct(array $coordonnees) {
		$this->abscisse = $coordonnees[0];
		$this->ordonnee = $coordonnees[1];
	}

	public function getAbscisse() {
		return $this->abscisse;
	}
	/**
	 * retourne l'ordonnee du point.
	 * @return int l'ordonnee du point
	 */
	public function getOrdonnee() {
		return $this->ordonnee;
	}

	public function toJSON() {
		$json = "[".$this->abscisse.",".$this->ordonnee."]";
		return $json;
	}

	public function isEqual($point) {
		return bccomp($this->abscisse, $point->getAbscisse(), 10) == 0 && bccomp($this->ordonnee, $point->getOrdonnee(), 10) == 0;
	}

	public function isExtremite($segment) {
		if ($this->isEqual($segment->getPointA())) {
			return true;
		} elseif ($this->isEqual($segment->getPointB())) {
			return true;
		}
		return false;
	}

	public function isInsidePolygon($polygon) {
		return $polygon->containsPoint($this);
	}
}
