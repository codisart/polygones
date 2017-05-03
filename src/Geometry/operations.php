<?php
namespace Geometry;

function determinant(Segment $segmentOne, Segment $segmentSecond)
{
    $abscissaVertexOne    = $segmentOne->getPointB()->getAbscissa() - $segmentOne->getPointA()->getAbscissa();
    $ordinateVertexOne    = $segmentOne->getPointB()->getOrdinate() - $segmentOne->getPointA()->getOrdinate();
    $abscissaVertexSecond = $segmentSecond->getPointB()->getAbscissa() - $segmentSecond->getPointA()->getAbscissa();
    $ordinateVertexSecond = $segmentSecond->getPointB()->getOrdinate() - $segmentSecond->getPointA()->getOrdinate();

    return ($abscissaVertexOne * $ordinateVertexSecond) - ($abscissaVertexSecond * $ordinateVertexOne);
}
