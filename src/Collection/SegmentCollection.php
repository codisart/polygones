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
        return parent::offsetSet($offset, $value);
    }
    
    public function append($collection)
    {
        return $this->appendSegment($collection);
    }

    private function appendSegment(SegmentCollection $collection)
    {
        return parent::append($collection);
    }
}
