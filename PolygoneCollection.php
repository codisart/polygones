<?php
namespace Anah\AnahOpah\Utility;

use Anah\AnahOpah\Utility\Polygone;
use Anah\AnahOpah\Utility\Collection;
use Anah\AnahOpah\Utility\Math;




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

	private function getIntersections() {
		$segmentsA = clone $this->segments;
		$segmentsB = clone $this->segments;
		$pointsIntersections = new Collection();

		foreach ($segmentsA as $segmentA) {
			foreach ($segmentsB as $segmentB) {
				if ($segmentA->intersect($segmentB)) {
					echo $segmentA->toJSON();
					echo $segmentB->toJSON();
				}
			}
		}
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

	private function normalize() {
		$segments1 = clone $this->segments;
		$segments2 = clone $this->segments;

		while ($segment1 = $segments1->next()) {
			$key1 = $segments1->key();

			$segmentsInput = new Collection();
			foreach ($segments2 as $key2 => $segment2) {

				if (!$segment1->isEqual($segment2) && $segment1->isOnSameDroite($segment2)) {
					if ($segment1->contient($segment2->getPointA()) && $segment1->contient($segment2->getPointB())) {
						if (is_null($segment1->coefficientDirecteur)) {
							$signe = ($segment1->getPointB()->getOrdonnee() - $segment1->getPointA()->getOrdonnee()) * ($segment2->getPointB()->getOrdonnee() - $segment2->getPointA()->getOrdonnee());
						}
						else {							
							$signe = ($segment1->getPointB()->getAbcisse() - $segment1->getPointA()->getAbcisse()) * ($segment2->getPointB()->getAbcisse() - $segment2->getPointA()->getAbcisse());
						}

						if ($signe > 0) {
							$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointA());
							$segmentsInput[] = new Segment($segment2->getPointA(), $segment2->getPointB());
							$segmentsInput[] = new Segment($segment2->getPointB(), $segment1->getPointB());
						}
						else {
							$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointB());
							$segmentsInput[] = new Segment($segment2->getPointB(), $segment2->getPointA());
							$segmentsInput[] = new Segment($segment2->getPointA(), $segment1->getPointB());								
						}
					}
					else if ($segment1->contient($segment2->getPointA())) {
						$segmentsInput[] = new Segment($segment1->getPointA(), $segment2->getPointA());
						$segmentsInput[] = new Segment($segment2->getPointA(), $segment1->getPointB());
					}
					else if ($segment1->contient($segment2->getPointB())) {
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