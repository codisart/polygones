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
}
