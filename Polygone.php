<?php

namespace Utility;

use Utility\Collection;
use Utility\Segment;
use Utility\Point;

/**
* polygone
*/
class Polygone implements PolygonInterface {

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

	public function containsPoint(Point $point) {
		return $this->containsPointWN($point);
	}

	public function containsPointWN(Point $point) {
		$wn = 0;

		foreach ($this->segments as $i=>$segment){
			if($segment->getPointA()->getOrdonnee() <= $point->getOrdonnee()) {
				if ($segment->getPointB()->getOrdonnee() > $point->getOrdonnee()) {
					if ($this->isLeft($segment, $point) > 0) {
						++$wn;
					}
				}
			} else {
				if ($segment->getPointB()->getOrdonnee() <= $point->getOrdonnee()) {
					if ($this->isLeft($segment, $point) < 0) {
						--$wn;
					}
				}
			}
		}

		return $wn;
	}

	/**
	 * Calcul du determinant entre le vertex et le vertex formé du point d'origne du vertex et le point.
	 * @param  Segment $vertex [description]
	 * @param  Point   $point  [description]
	 * @return boolean         [description]
	 */
	public function isLeft(Segment $vertex,Point $point) {
		$vertexSecond = new Segment($vertex->getPointA(), $point);
		return Math::determinant($vertex, $vertexSecond);
	}


	public function scalarProduct ($point1, $point2, $point3) {
		$vertexSecond = new Segment($vertex->getPointA(), $point);
		return Math::scalarProduct($vertex, $vertexSecond);
		return ( ($point2->getAbscisse() - $point1->getAbscisse()) * ($point3->getAbscisse() - $point1->getAbscisse())
			+ ($point2->getOrdonnee() -  $point1->getOrdonnee()) * ($point3->getOrdonnee() - $point1->getOrdonnee()) );
	}
}
