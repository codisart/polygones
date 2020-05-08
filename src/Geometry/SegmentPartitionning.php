<?php
namespace Geometry;

use Collection\SegmentCollection;
use function Math\isBetween;
use function Math\isStrictBetween;

class SegmentPartitionning
{
    public function process(Segment $thisSegment, Segment $thatSegment)
    {
        if (!$thisSegment->isOnSameLine($thatSegment) && !$thisSegment->hasCommonEndPoint($thatSegment)) {
            return $this->splitByIntersection($thisSegment, $thatSegment);
        }
        if ($thisSegment->containsSegment($thatSegment)) {
            return $this->splitInThree($thisSegment, $thatSegment);
        }
        if ($thisSegment->containsPoint($thatSegment->getPointA())) {
            return $this->splitByPoint($thisSegment, $thatSegment->getPointA());
        }
        if ($thisSegment->containsPoint($thatSegment->getPointB())) {
            return $this->splitByPoint($thisSegment, $thatSegment->getPointB());
        }
        return null;
    }

    private function splitInThree(Segment $thisSegment, Segment $thatSegment)
    {
        $newSegments = $this->splitByPoint($thisSegment, $thatSegment->getPointA());

        $index = 1;
        if ($newSegments[0]->containsPoint($thatSegment->getPointB())) {
            $index = 0;
        }

        $newSegment = $newSegments[$index];
        return $newSegments->insert($index, $this->splitByPoint($newSegment, $thatSegment->getPointB()));
    }

    private function splitByIntersection(Segment $thisSegment, Segment $thatSegment)
    {
        $pointOfIntersection = $thisSegment->getPointOfIntersect($thatSegment);

        if (!empty($pointOfIntersection) && !$thisSegment->hasForEndPoint($pointOfIntersection)) {
            return $this->splitByPoint($thisSegment, $pointOfIntersection);
        }
        return null;
    }

    private function splitByPoint(Segment $thisSegment, Point $point) : SegmentCollection
    {
        $newSegments   = new SegmentCollection();
        $newSegments[] = Segment::create($thisSegment->getPointA(), $point);
        $newSegments[] = Segment::create($point, $thisSegment->getPointB());
        return $newSegments;
    }
}