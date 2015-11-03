<?php

namespace Utility;

use Utility\Collection;
use Utility\Polygone;
use Utility\Segment;
use Utility\Point;

/**
* polygone
*/
class MultiPolygon implements PolygonInterface
{
	private $polygons;

	public function toJSON() {
		$json = "";
		foreach ($this->polygons as $polygon) {
			$json[] = $polygon->toJSON();
		}
		return '['.implode(', ', $json).']';
	}

	public function __toString() {
		return $this->toJSON();
	}
}
