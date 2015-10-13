<?php

namespace Utility;

class Collection implements \ArrayAccess, \Iterator, \Countable {
	protected $contenu = array();
	protected $type;

	public function __construct() {

	}

	public function offsetSet($offset, $value) {

		if (empty($this->contenu)) {
			$this->setType($value);
		}

		if (!empty($this->contenu) && !$this->checkType($value)) {
			return false;
		}

		if (is_null($offset)) {
			$this->contenu[] = $value;
		} else {
			$this->contenu[$offset] = $value;
		}

	}

	public function offsetExists($offset) {
		return isset($this->contenu[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->contenu[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->contenu[$offset]) ? $this->contenu[$offset] : null;
	}

	public function rewind() {
		reset($this->contenu);
	}

	public function current() {
		return current($this->contenu);
	}

	public function key() {
		return key($this->contenu);
	}

	public function next() {
		return next($this->contenu);
	}

	public function valid() {
		return $this->current() !== false;
	}

	public function count() {
		return count($this->contenu);
	}

	public function append($collection) {

		if (!(isset($collection) && is_object($collection) && get_class($collection) === get_class($this) && $collection->count() > 0)) {
			return $this;
		}

		foreach ($collection as $key => $value) {
			if (isset($this[$key])) {
				$this[] = $value;
			} else {
				$this[$key] = $value;
			}
		}

		return $this;
	}

	public function insert($index, $newValues) {
		$arrayNewValues = array();
		foreach ($newValues as $key => $value) {
			$arrayNewValues[] = $value;
		}
		array_splice($this->contenu, $index, 1, $arrayNewValues);
	}

	private function setType($value) {
		if (is_object($value)) {
			return $this->type = get_class($value);
		}

		return $this->type = gettype($value);
	}

	private function checkType($value) {
		if (is_object($value)) {
			return $this->type === get_class($value);
		}

		return $this->type === gettype($value);
	}
}
