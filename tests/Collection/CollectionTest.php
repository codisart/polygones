<?php

namespace CollectionTest;

use Collection\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
	public function testCount() {
		$instance = new Collection();

		$this->assertInternalType('int', count($instance));
	}

	public function testAddWrongTypeToCollection() {
		$instance = new Collection();

		$instance[] = new \DateInterval('PT30S');
		$instance[] = new \DateTime;

		$this->assertInternalType('int', count($instance));
		$this->assertEquals(1, count($instance));
	}

	public function testElementWithIndexToCollection() {
		$instance = new Collection();

		$instance[] = new \DateTime;
		$instance[2] = new \DateTime;

		$this->assertInternalType('int', count($instance));
		$this->assertEquals(2, count($instance));
		$this->assertTrue(isset($instance[2]));
		$this->assertInstanceOf(\DateTime::class, $instance[2]);

		unset($instance[2]);
		$this->assertFalse(isset($instance[2]));
	}

	public function testShift(){
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';
		$instance[] = 'apple';
		$instance[] = 'raspberry';

		$element = $instance->shift();
		$this->assertInternalType('string', $element);
		$this->assertEquals('orange', $element);

		$this->assertEquals(3, count($instance));
	}

	public function testEach(){
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';
		$instance[] = 'apple';
		$instance[] = 'raspberry';

		$element = $instance->each();

		$this->assertInternalType('array', $element);
		$this->assertEquals(4, count($instance));
	}

	public function testAppend(){
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';

		$elementsToAdd = new Collection();
		$elementsToAdd[] = 'apple';
		$elementsToAdd[] = 'raspberry';

		$this->assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		$this->assertEquals(4, count($instance));
	}

	public function testWrongTypeCollectionToAppend(){
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';

		$elementsToAdd = new Collection();
		$elementsToAdd[] = new \DateTime;
		$elementsToAdd[] = new \DateTime;

		$this->assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		$this->assertEquals(2, count($instance));

		$elementsToAdd = new Collection();

		$this->assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		$this->assertEquals(2, count($instance));
	}

	public function testInsert() {
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';

		$elementsToAdd = new Collection();
		$elementsToAdd[] = 'apple';
		$elementsToAdd[] = 'raspberry';

		$this->assertInstanceOf(Collection::class, $instance->insert(0, $elementsToAdd));
		$this->assertEquals(3, count($instance));
	}

	public function testDelete() {
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';
		$instance[] = 'apple';
		$instance[] = 'raspberry';

		$this->assertInstanceOf(Collection::class, $instance->delete(2));
		$this->assertFalse(isset($instance[3]));
		$this->assertEquals(3, count($instance));
	}
}
