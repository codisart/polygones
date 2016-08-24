<?php

namespace Geometry;

use Collection\Collection;
use Geometry\Point;
use Geometry\Segment;
use Math\Math;

class Polygon {

	/**
	 * @var Collection
	 */
	private $segments;

	public function __construct(array $pointsListe) {
		if (
			count($pointsListe) <= 3
			|| end($pointsListe) !== reset($pointsListe)
		) {
			throw new \Exception("Not a polygon");
		}

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

	public function getSegments() {
		return $this->segments;
	}

	public function getBoundingbox() {
		$latmin = $latmax = $lgtmin = $lgtmax = null;

		foreach ($this->segments as $i=>$segment) {
			$lgtmin = Math::min($lgtmin, $segment->getPointA()->getAbscissa());
			$lgtmax = Math::max($lgtmax, $segment->getPointA()->getAbscissa());
			$latmin = Math::min($latmin, $segment->getPointA()->getOrdinate());
			$latmax = Math::max($latmax, $segment->getPointA()->getOrdinate());
		}

		$boundingbox = [
			[$latmax, $lgtmax],
			[$latmin, $lgtmin],
		];

		return $boundingbox;
	}

	public function containsPoint(Point $point) {
		$wn = 0;

		foreach ($this->segments as $i => $segment) {
			if ($segment->containsPoint($point)) {
				return false;
			}
			if ($segment->getPointA()->getOrdinate() <= $point->getOrdinate()) {
				if ($segment->getPointB()->getOrdinate() > $point->getOrdinate()) {
					if ($this->isLeft($segment, $point) > 0) {
						++$wn;
					}
				}
			} else {
				if ($segment->getPointB()->getOrdinate() <= $point->getOrdinate()) {
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
	 * @param  Segment $segment [description]
	 * @param  Point   $point  [description]
	 * @return boolean         [description]
	 */
	private function isLeft(Segment $segment, Point $point) {
		$segmentSecond = new Segment($segment->getPointA(), $point);
		return Math::determinant($segment, $segmentSecond);
	}


	public function getBarycenter() {
		$total = 0;
		$abscissaBarycentre = 0;
		$ordonneeBarycentre = 0;

		foreach ($this->segments as $key => $segment) {
			$total++;
			$abscissaBarycentre += $segment->getPointA()->getAbscisse();
			$ordonneeBarycentre += $segment->getPointA()->getOrdonnee();
		}

		return new Point([$abscissaBarycentre/$total, $ordonneeBarycentre/$total]);
	}
}
