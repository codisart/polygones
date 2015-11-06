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

	public function __clone()
	{
		$this->segments = clone $this->segments;
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

		return $wn != 0;
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

	public function getPointsOfIntersect($polygon) {
		$myVertexes = $this->segments;
		$hisVertexes = $polygon->getSegments();

		foreach($myVertexes as $myKey => $myVertex) {
			foreach($hisVertexes as $hisKey => $hisVertex) {
				if(!$myVertex->isEqual($hisVertex)) {
					$pointOfIntersection = $myVertex->getPointOfIntersect($hisVertex);

					if(!empty($pointOfIntersection)) {
						$myVertexesPartitionned = new Collection();
						$myVertexesPartitionned[] = new Segment($myVertex->getPointA(), $pointOfIntersection);
						$myVertexesPartitionned[] = new Segment($pointOfIntersection, $myVertex->getPointB());
						$this->insertNewPartsSegment($myKey, $myVertexesPartitionned);

						$hisVertexesPartitionned = new Collection();
						$hisVertexesPartitionned[] = new Segment($hisVertex->getPointA(), $pointOfIntersection);
						$hisVertexesPartitionned[] = new Segment($pointOfIntersection, $hisVertex->getPointB());
						$polygon->insertNewPartsSegment($hisKey, $hisVertexesPartitionned);
					}
				}
			}
		}
	}

	public function normalize($polygon) {
		$myVertexes = $this->segments;

		foreach($myVertexes as $myKey => $myVertex) {
			if($myVertex->getMidpoint()->isInsidePolygon($polygon)) {
				$this->segments->_unset($myKey);
			}
		}
	}

	public function insertNewPartsSegment ($key, Collection $vertexes) {
			$this->segments->insert($key, $vertexes);
	}
}
