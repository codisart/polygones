<?php
namespace Collection;

use Geometry\Polygon;

class PolygonCollection extends Collection
{
    public function offsetSet($offset, $value)
    {
        return $this->offsetSetPolygon($offset, $value);
    }

    private function offsetSetPolygon($offset, Polygon $value)
    {
        return parent::offsetSet($offset, $value);
    }
    
    public function append($collection)
    {
        return $this->appendPolygon($collection);
    }

    private function appendPolygon(PolygonCollection $collection)
    {
        return parent::append($collection);
    }
}
