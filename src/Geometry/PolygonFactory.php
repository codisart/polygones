<?php

namespace Geometry;

use Collection\Collection;
use function Math\max;
use function Math\min;

class PolygonFactory 
{
    public static function buildFromSegments(Collection $segments) : Collection
    {
        $segments->shouldBeTypeOf(Segment::class);

        $segments->rewind();
        $point        = $segments->current()->getPointA();
        $pointOrigine = $point;

        $points       = [];
        $points[]     = $point;
        $newPolygones = new Collection;

        while ($segments->count()) {
            $segment = $segments->current();
            $key     = $segments->key();

            $segments->next();
            if ($segment->hasForEndPoint($point)) {
                $point = $segment->getOtherPoint($point);
                unset($segments[$key]);

                if ($pointOrigine->isEqual($point)) {
                    $newPolygones[] = self::finalize($pointOrigine, $points);

                    if ($segments->count()) {
                        $newPolygones->append(
                            self::buildFromSegments($segments)
                        );
                        $segments = new Collection;
                    }
                }
                $points[] = $point;
                $segments->rewind();
            }
        }
        return $newPolygones;
    }

    private static function finalize($pointOrigine, $points) : Polygon
    {
        $ptListe = '';
        foreach ($points as $pt) {
            $ptListe .= $pt->toJSON();
            $ptListe .= ',';
        }
        $ptListe .= $pointOrigine->toJSON();
        return new Polygon(json_decode('['.$ptListe.']'));
    }
}