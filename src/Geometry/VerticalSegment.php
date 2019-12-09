<?php
namespace Geometry;

use Collection\Collection;
use function Math\isBetween;
use function Math\isStrictBetween;

class VerticalSegment extends Segment
{
    public function __construct(Point $pointA, Point $pointB)
    {
        $this->pointA = $pointA;
        $this->pointB = $pointB;
    }

    public function containsPoint(Point $point) : bool
    {
        return self::hasSameAbscissa($this->pointA, $point)
            && isStrictBetween($point->getOrdinate(), $this->pointA->getOrdinate(), $this->pointB->getOrdinate());
    }

    public function isOnSameLine(Segment $segment) : bool
    {
        return is_null($segment->slope)
            && bccomp($this->pointA->getAbscissa(), $segment->pointA->getAbscissa(), 8) === 0
        ;
    }

    /**
     * @return Point|null
     */
    public function getPointOfIntersect(Segment $segment)
    {
        if (determinant($this, $segment) == 0) {
            return null;
        }
        return $segment->getPointOfIntersectForNullSlope($this);
    }
}