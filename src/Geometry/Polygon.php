<?php
namespace Geometry;

use Collection\Collection;
use Collection\SegmentCollection;
use function Math\max;
use function Math\min;

class Polygon
{
    /**
     * @var SegmentCollection
     */
    private $segments;

    public function __construct(array $pointsListe)
    {
        $lengthListe = count($pointsListe);

        if (
            $lengthListe <= 3
            || end($pointsListe) !== reset($pointsListe)
        ) {
            throw new \Exception('Not a polygon');
        }

        $this->segments = new SegmentCollection();

        for ($i = 0; $i < $lengthListe - 1; ++$i) {
            $pointA           = new Point(current($pointsListe));
            $pointB           = new Point(next($pointsListe));
            $this->segments[] = Segment::create($pointA, $pointB);
            unset($pointA, $pointB);
        }
    }

    public function getSegments()
    {
        return $this->segments;
    }

    public function getBoundingbox()
    {
        $latmin = $latmax = $lgtmin = $lgtmax = null;

        foreach ($this->segments as $segment) {
            $lgtmin = min($lgtmin, $segment->getPointA()->getAbscissa());
            $lgtmax = max($lgtmax, $segment->getPointA()->getAbscissa());
            $latmin = min($latmin, $segment->getPointA()->getOrdinate());
            $latmax = max($latmax, $segment->getPointA()->getOrdinate());
        }

        return [
            [$latmax, $lgtmax],
            [$latmin, $lgtmin],
        ];
    }

    public function containsPoint(Point $point)
    {
        $wn = 0;

        /** @var \Geometry\Segment $segment */
        foreach ($this->segments as $segment) {
            if ($segment->hasForEndPoint($point) || $segment->containsPoint($point)) {
                return false;
            }
            $wn = $this->updateWn($segment, $point, $wn);
        }

        return $wn != 0;
    }

    private function updateWn($segment, $point, $wn)
    {        
        if ($segment->getPointA()->isLower($point)) {
            if ($segment->getPointB()->isStrictlyHigher($point) && $this->isLeft($segment, $point)) {
                return ++$wn;
            }
            return $wn;
        }
        if ($segment->getPointB()->isLower($point) && $this->isRight($segment, $point)) {
            return --$wn;
        }
        return $wn;
    }

    /**
     * Calcul du determinant entre un vecteur
     * et le vecteur formé du point d'origne du premvier vecteur et un point particulier.
     */
    private function isLeft(Segment $segment, Point $point) : bool
    {
        $segmentSecond = Segment::create($segment->getPointA(), $point);
        return determinant($segment, $segmentSecond) > 0;
    }

    /**
     * Calcul du determinant entre un vecteur
     * et le vecteur formé du point d'origne du premvier vecteur et un point particulier.
     */
    private function isRight(Segment $segment, Point $point) : bool
    {
        $segmentSecond = Segment::create($segment->getPointA(), $point);
        return determinant($segment, $segmentSecond) < 0;
    }

    public function getBarycenter() : Point
    {
        $total              = 0;
        $abscissaBarycenter = 0;
        $ordinateBarycenter = 0;

        foreach ($this->segments as $segment) {
            $total++;
            $abscissaBarycenter += $segment->getPointA()->getAbscissa();
            $ordinateBarycenter += $segment->getPointA()->getOrdinate();
        }

        return new Point([$abscissaBarycenter / $total, $ordinateBarycenter / $total]);
    }

    public function getAllSegmentsIntersectionWith($polygonContender)
    {
        $mySegments  = clone $this->segments;
        $hisSegments = clone $polygonContender->segments;

        foreach ($mySegments as $myKey => $mySegment) {
            foreach ($hisSegments as $hisKey => $hisSegment) {
                $newSegments = $mySegment->getPartitionsbySegment($hisSegment);
                if ($newSegments) {
                    $mySegments->insert($myKey, $newSegments);
                    $mySegment = $newSegments[0];
                }

                $newSegments = $hisSegment->getPartitionsbySegment($mySegment);
                if ($newSegments) {
                    $hisSegments->insert($hisKey, $newSegments);
                    $hisSegment = $newSegments[0];
                }

                if ($mySegment->isEqual($hisSegment)) {
                    $hisSegments->delete($hisKey);
                    if ($mySegment->isBetweenPolygons($this, $polygonContender)) {
                        $mySegments->delete($myKey);
                        break;
                    }
                }
            }
        }

        $allSegments = (new SegmentCollection())
            ->append($mySegments)
            ->append($hisSegments)
        ;

        $resultSegments = new SegmentCollection();

        foreach ($allSegments as $segment) {
            $middlePoint = $segment->getMiddlePoint();
            if ($this->containsPoint($middlePoint) || $polygonContender->containsPoint($middlePoint)) {
                continue;
            }

            $resultSegments[] = $segment;
        }

        return $resultSegments;
    }

    public function union($polygonContender)
    {
        return PolygonFactory::buildFromSegments(
            $this->getAllSegmentsIntersectionWith($polygonContender)
        );
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->segments as $segment) {
            $array[] = $segment->getPointA()->toArray();
        }
        $array[] = $this->segments[0]->getPointA()->toArray();
        return $array;
    }

    public function toJSON()
    {
        return json_encode($this->toArray());
    }
}
