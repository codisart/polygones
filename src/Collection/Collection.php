<?php
namespace Collection;

abstract class Collection implements \ArrayAccess, \Iterator, \Countable
{
    /** @var array */
    protected $contenu = [];

    /** @var string */
    protected $type;

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->contenu[] = $value;
            return true;
        }

        $this->contenu[$offset] = $value;
        return true;
    }

    public function offsetExists($offset)
    {
        return isset($this->contenu[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->contenu[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->contenu[$offset]) ? $this->contenu[$offset] : null;
    }

    public function rewind()
    {
        reset($this->contenu);
    }

    public function shift()
    {
        return array_shift($this->contenu);
    }

    public function current()
    {
        return current($this->contenu);
    }

    public function key()
    {
        return key($this->contenu);
    }

    public function next()
    {
        return next($this->contenu);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function count()
    {
        return count($this->contenu);
    }

    public function append($collection)
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

    public function insert($index, $newValues)
    {
        $arrayNewValues = [];
        foreach ($newValues as $value) {
            $arrayNewValues[] = $value;
        }
        array_splice($this->contenu, $index, 1, $arrayNewValues);
        return $this;
    }

    /**
     * @todo remove one of unset or delete
     * @param mixed $index
     */
    public function delete($index)
    {
        array_splice($this->contenu, $index, count($this->contenu), array_slice($this->contenu, $index + 1));
        return $this;
    }
}
