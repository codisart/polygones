<?php

namespace Utility;

use Utility\Collection;
use Utility\Segment;
use Utility\Point;

/**
* polygone
*/
class Polygone {

	private $segments;

	public function __construct(array $pointsListe) {
		$this->segments = new Collection();

		foreach ($pointsListe as $pointA) {
			$pointB = next($pointsListe);

			if ($pointB) {
				$pointA = new Point($pointA);
				$pointB = new Point($pointB);
				$this->segments[] = new Segment($pointA, $pointB);
			}
			unset($pointA, $pointB);
		}
	}

	public function toJSON() {
		$json = "";
		foreach ($this->segments as $segment) {
			$json .= $segment->getPointA()->toJSON();
			$json .= ",";
		}
		$json .= $this->segments[0]->getPointA()->toJSON();
		return "[".$json."]";
	}
	public function __toString() {
		return $this->toJSON();
	}

	public function getSegments() {
		return $this->segments;
	}

	public function getBoundingbox() {
		$latmin = $latmax = $lgtmin = $lgtmax = null;

		foreach ($this->segments as $i=>$segment) {
			$lgtmin = Math::min($lgtmin, $segment->getPointA()->getAbscisse());
			$lgtmax = Math::max($lgtmax, $segment->getPointA()->getAbscisse());
			$latmin = Math::min($latmin, $segment->getPointA()->getOrdonnee());
			$latmax = Math::max($latmax, $segment->getPointA()->getOrdonnee());
		}

		$boundingbox = array(
			array($latmax, $lgtmax),
			array($latmin, $lgtmin),
		);
		return $boundingbox;
	}

}
