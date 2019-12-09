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
        if (is_null($offset)) {
            $this->contenu[] = $value;
            return true;
        }

        $this->contenu[$offset] = $value;
        return true;
    }
    
    public function append($collection)
    {
        return $this->appendPolygon($collection);
    }

    private function appendPolygon(PolygonCollection $collection)
    {
        foreach ($collection as $key => $value) {
            $newKey = $key;
            if (isset($this[$key])) {
                $newKey = $this->count();
            }
            $this[$newKey] = $value;
        }

        return $this;
    }
}
