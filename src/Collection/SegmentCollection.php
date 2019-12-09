<?php
namespace Collection;

use Geometry\Segment;

class SegmentCollection extends Collection
{
    public function offsetSet($offset, $value)
    {
        return $this->offsetSetSegment($offset, $value);
    }

    private function offsetSetSegment($offset, Segment $value)
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
        return $this->appendSegment($collection);
    }

    private function appendSegment(SegmentCollection $collection)
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
