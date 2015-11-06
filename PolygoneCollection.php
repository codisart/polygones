<?php

namespace Utility;

use Utility\Polygone;
use Utility\Collection;
use Utility\Math;

/**
* collection de polygone.
*/
class PolygoneCollection extends Collection {

	private $segments;

	public function __construct() {
		$this->segments = new Collection();
	}

	public function offsetSet($offset, $value) {
		if (parent::offsetSet($offset, $value) !== false) {
			$segments = $value->getSegments();
			$this->segments->append($segments);
		}
	}

	function __clone()
	{
		$contenuTmp = array();
		foreach ($this->contenu as $key => $value) {
			$contenuTmp[$key] = clone $value;
		}
		$this->contenu = $contenuTmp;
		$this->segments = clone $this->segments;
	}


	public function union() {
		if (count($this) === 0) {
			return $this;
		}

		$listeSegments = new Collection();
		$inArraySegments = new Collection();

		$normalizedSegments = $this->normalize();

		foreach ($normalizedSegments as $segmentA) {
			$inArray = false;
			foreach ($listeSegments as $keyB => $segmentB) {
				if ($segmentA->isEqual($segmentB)) {
					$inArray = true;
					$inArraySegments[] = $segmentB;
					unset($listeSegments[$keyB]);
				}
			}
			foreach ($inArraySegments as $keyB => $segmentB) {
				if ($segmentA->isEqual($segmentB)) {
					$inArray = true;
				}
			}
			if (!$inArray) {
				$listeSegments[] = $segmentA;
			}
		}

		$newPolygones = $this->buildPolygone($listeSegments);

		return $newPolygones;
	}

	public function getBoundingbox() {
		if (count($this) === 0) {
			return null;
		}

		if (count($this) === 1) {
			return $this->contenu[0]->getBoundingbox();
		}

		$points = array();
		foreach ($this as $poly) {
			$boundingBox = $poly->getBoundingbox();

			$points[] = $boundingBox[0];
			$points[] = $boundingBox[1];
		}

		$latmin = $latmax = $lgtmin = $lgtmax = null;
		foreach ($points as $coord) {
			$lgtmin = Math::min($lgtmin, $coord[0]);
			$lgtmax = Math::max($lgtmax, $coord[0]);
			$latmin = Math::min($latmin, $coord[1]);
			$latmax = Math::max($latmax, $coord[1]);
		}

		$boundingbox = array(
			array($lgtmax, $latmax),
			array($lgtmin, $latmin),
		);
		return $boundingbox;


	}


	/**
	 * @param Collection $listeSegments
	 */
	private function buildPolygone($listeSegments) {
		$points = array();
		$listeSegments->rewind();
		$point = $listeSegments->current()->getPointA();
		$pointOrigine = $point;
		$points[] = $point;

		$newPolygones = new PolygoneCollection();

		while ($listeSegments->count()) {
			foreach ($listeSegments as $key => $segment) {
				if ($point->isExtremite($segment)) {
					$point = $segment->getOtherPoint($point);
					unset($listeSegments[$key]);

					if ($pointOrigine->isEqual($point)) {
						$ptListe = "";
						foreach ($points as $pt) {
							$ptListe .= $pt->toJSON();
							$ptListe .= ",";
						}
						$ptListe .= $pointOrigine->toJSON();
						$newPolygones[] = new Polygone(json_decode("[".$ptListe."]"));

						if ($listeSegments->count()) {
							$nPolygones = $this->buildPolygone($listeSegments);
							$newPolygones->append($nPolygones);
							$listeSegments = new Collection();
						}
					}
					$points[] = $point;
					unset($listeSegments[$key]);

					break;
				}
			}
		}

		return $newPolygones;
	}


	public function getPointsOfIntersect() {
		$polygonChampions = clone $this;
		$polygonFinals = new PolygoneCollection();

		$vertexes = new Collection();

		$polygonChampion = $polygonChampions->current();

		do {
			while ($polygonContender = $polygonChampions->next()) {
				$polygonChampion->getPointsOfIntersect($polygonContender);
			}
			$polygonFinals[] = $polygonChampion;
			$polygonChampions->shift();
		} while ($polygonChampion = $polygonChampions->current());

				var_dump($polygonFinals[1]->toJSON());
		$polygonFinals[1]->normalize($polygonFinals[0]);
				var_dump($polygonFinals[1]->toJSON());

		// $polygonFinals[1]->normalize($polygonFinals[0]);
	}


	private function normalize() {
		$segments1 = clone $this->segments;
		$segments2 = clone $this->segments;

		/*
		$polygons = clone $this->contenu;

		foreach($polygons as $polygon) {
			foreach($segments1 as $key => $segment) {
				// if($polygon->containsSegment($segment)) {
				// 	break; continue; ?
				// }

				$segmentsOfPolygon = $polygon->getSegments();
				foreach($segmentsOfPolygon as $segmentOfPolygon) {
					$pointOfIntersection = $segment->getPointofIntersect($segmentOfPolygon);

					if(!empty($pointOfIntersection)) {
						$segmentsInput = new Collection();
						$segmentsInput[] = new Segment($segment->getPointA(), $pointOfIntersection);
						$segmentsInput[] = new Segment($pointOfIntersection, $segment->getPointB());

						$segments->insert($key, $segmentsInput);
					}
				}
			}
		}
		*/

		/*
			Pour tous les segments, vérifier si un des points est à l'intérieur d'un Polygone.
				Si c'est le cas, pour tous les segments du polygone
					chercher toutes les intersections avec le segment et créer deux segments à partir du premier
					supprimer le segment contenu dans le polygone.
		*/





		while ($segment1 = $segments1->next()) {
			$key1 = $segments1->key();

			$segmentsInput = new Collection();
			foreach ($segments2 as $key2 => $segment2) {

				// Crée trois segements à partir de deux segments qui sont sur la meme droite mais qui ne sont pas confondus.
				if (!$segment1->isEqual($segment2) && $segment1->isOnSameDroite($segment2)) {
					if ($segment1->contient($segment2->getPointA()) && $segment1->contient($segment2->getPointB())) {
						if (is_null($segment1->coefficientDirecteur)) {
							$signe = ($segment1->getPointB()->getOrdonnee() - $segment1->getPointA()->getOrdonnee()) * ($segment2->getPointB()->getOrdonnee() - $segment2->getPointA()->getOrdonnee());
						} else {
							$signe = ($segment1->getPointB()->getAbcisse() - $segment1->getPointA()->getAbcisse()) * ($segment2->getPointB()->getAbcisse() - $segment2->getPointA()->getAbcisse());
						}

						if ($signe > 0) {
							$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointA());
							$segmentsInput[] = new Segment($segment2->getPointA(), $segment2->getPointB());
							$segmentsInput[] = new Segment($segment2->getPointB(), $segment1->getPointB());
						} else {
							$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointB());
							$segmentsInput[] = new Segment($segment2->getPointB(), $segment2->getPointA());
							$segmentsInput[] = new Segment($segment2->getPointA(), $segment1->getPointB());
						}
					} else if ($segment1->contient($segment2->getPointA())) {
						$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointA());
						$segmentsInput[] = new Segment($segment2->getPointA(), $segment1->getPointB());
					} else if ($segment1->contient($segment2->getPointB())) {
						$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointB());
						$segmentsInput[] = new Segment($segment2->getPointB(), $segment1->getPointB());
					}
				}
				if ($segmentsInput->count() > 0) {
					$segments1->insert($key1, $segmentsInput);
					$segments1->rewind();
					break;
				}
			}
		}

		return $segments1;
	}

	public function toJSON() {
		$json = array();
		foreach ($this->contenu as $polygone) {
			$json[] = $polygone->toJSON();
		}
		return '['.implode(', ', $json).']';
	}

	public function __toString() {
		return $this->toJSON();
	}
}
