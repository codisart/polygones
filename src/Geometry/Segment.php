<?php
namespace Geometry;

use Collection\SegmentCollection;
use function Math\isBetween;
use function Math\isStrictBetween;

class Segment
{
    /** @var Point */
    protected $pointA;
    
    /** @var Point */
    protected $pointB;

    /** @var int */
    protected $slope;

    /** @var int */
    protected $ordinateIntercept;

    private function __construct(Point $pointA, Point $pointB)
    {
        $this->pointA = $pointA;
        $this->pointB = $pointB;

        $this->slope = ($pointB->getOrdinate() - $pointA->getOrdinate()) / ($pointB->getAbscissa() - $pointA->getAbscissa());
        
        $this->ordinateIntercept = $pointA->getOrdinate() - ($pointA->getAbscissa() * $this->slope);
    }

    public function getPointA() : Point
    {
        return $this->pointA;
    }

    public function getPointB() : Point
    {
        return $this->pointB;
    }

    public function getMiddlePoint() : Point
    {
        return new Point([
            ($this->pointA->getAbscissa() + $this->pointB->getAbscissa()) / 2,
            ($this->pointA->getOrdinate() + $this->pointB->getOrdinate()) / 2,
        ]);
    }

    public function isEqual(Segment $segment) : bool
    {
        return ($this->pointA->isEqual($segment->pointA) && $this->pointB->isEqual($segment->pointB))
            || ($this->pointA->isEqual($segment->pointB) && $this->pointB->isEqual($segment->pointA))
        ;
    }

    public function hasForEndPoint(Point $point) : bool
    {
        return $point->isEqual($this->pointA) || $point->isEqual($this->pointB);
    }

    /**
     * @return Point|null
     */
    public function getOtherPoint(Point $point)
    {
        if ($point->isEqual($this->pointA)) {
            return $this->pointB;
        }
        if ($point->isEqual($this->pointB)) {
            return $this->pointA;
        }
        return null;
    }

    public function isOnSameLine(Segment $segment) : bool
    {
        return bccomp($this->slope, $segment->slope, 8) === 0
            && bccomp($this->ordinateIntercept, $segment->ordinateIntercept, 8) === 0;
    }

    public function containsPoint(Point $point) : bool
    {
        $segmentToCompare = self::create($this->pointA, $point);

        return $this->isOnSameLine($segmentToCompare)
            && isStrictBetween($point->getAbscissa(), $this->pointA->getAbscissa(), $this->pointB->getAbscissa());
    }

    public function getOrientationRelativeToPoint(Point $point) : int
    {
        $determinant = determinant($this, self::create($this->pointA, $point));
        return ($determinant > 0) - ($determinant < 0);
    }

    public function isBetweenPolygons(Polygon $polygonChampion, Polygon $polygonContender) : bool
    {
        return $this->getOrientationRelativeToPoint($polygonChampion->getBarycenter())
            === -$this->getOrientationRelativeToPoint($polygonContender->getBarycenter())
        ;
    }

    public function containsSegment(Segment $segment) : bool
    {
        return $this->containsPoint($segment->pointA) && $this->containsPoint($segment->pointB);
    }

    public function hasCommonEndPoint(Segment $segment) : bool
    {
        return $segment->hasForEndPoint($this->pointA) || $segment->hasForEndPoint($this->pointB);
    }

    private function splitByPoint(Point $point) : SegmentCollection
    {
        $newSegments   = new SegmentCollection();
        $newSegments[] = self::create($this->pointA, $point);
        $newSegments[] = self::create($point, $this->pointB);
        return $newSegments;
    }

    /**
     * @return SegmentCollection|null
     */
    public function partitionedBy(Segment $segment)
    {
        if (!$this->isOnSameLine($segment) && !$this->hasCommonEndPoint($segment)) {
            $pointOfIntersection = $this->getPointOfIntersect($segment);

            if (!empty($pointOfIntersection) && !$this->hasForEndPoint($pointOfIntersection)) {
                return $this->splitByPoint($pointOfIntersection);
            }
            return null;
        }

        $pointA = $segment->pointA;
        $pointB = $segment->pointB;

        if ($this->containsSegment($segment)) {
            $newSegments = $this->splitByPoint($pointA);

            $index = 1;
            if ($newSegments[0]->containsPoint($pointB)) {
                $index = 0;
            }

            return $newSegments->insert($index, $newSegments[$index]->splitByPoint($pointB));
        }

        if ($this->containsPoint($pointA)) {
            return $this->splitByPoint($pointA);
        } elseif ($this->containsPoint($pointB)) {
            return $this->splitByPoint($pointB);
        }

        return null;
    }

    /**
     * @return Point|null
     */
    public function getPointOfIntersect(Segment $segment)
    {
        if (determinant($this, $segment) == 0) {
            return null;
        }

        if (is_null($segment->slope)) {
            return $this->getPointOfIntersectForNullSlope($segment);
        }

        if ($this->hasCommonEndPoint($segment)) {
            return null;
        }

        $intersectAbscissa = ($segment->ordinateIntercept - $this->ordinateIntercept) / ($this->slope - $segment->slope);
        if (isBetween($intersectAbscissa, $this->pointA->getAbscissa(), $this->pointB->getAbscissa())
         && isBetween($intersectAbscissa, $segment->pointA->getAbscissa(), $segment->pointB->getAbscissa())
        ) {
            $intersectOrdinate = ($intersectAbscissa * $this->slope) + $this->ordinateIntercept;
            return new Point([$intersectAbscissa, $intersectOrdinate]);
        }
        return null;
    }

    /**
     * @return Point|null
     */
    public function getPointOfIntersectForNullSlope(Segment $segment)
    {
        $intersectAbscissa = $segment->getPointA()->getAbscissa();

        if (!isBetween($intersectAbscissa, $this->pointA->getAbscissa(), $this->pointB->getAbscissa())) {
            return null;
        }

        $intersectOrdinate = ($intersectAbscissa * $this->slope) + $this->ordinateIntercept;
        $intersectPoint    = new Point([$intersectAbscissa, $intersectOrdinate]);

        if (isBetween($intersectOrdinate, $segment->pointA->getOrdinate(), $segment->pointB->getOrdinate())
         && !($segment->hasForEndPoint($intersectPoint) && $this->hasForEndPoint($intersectPoint))
        ) {
            return $intersectPoint;
        }
        return null;
    }

    public function toArray() : array
    {
        return [
            $this->pointA->toArray(),
            $this->pointB->toArray()
        ];
    }

    public function toJSON() : string
    {
        return json_encode($this->toArray());
    }

    protected static function hasSameAbscissa(Point $pointA, Point $pointB)
    {
        return $pointA->getAbscissa() - $pointB->getAbscissa() === 0;
    }

    public static function create(Point $pointA, Point $pointB)
    {
        if (self::hasSameAbscissa($pointA, $pointB)) {
            return new VerticalSegment($pointA, $pointB);
        }
        return new self($pointA, $pointB);
    }
}
