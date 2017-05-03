<?php
namespace Collection;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
	public function testCount() {
		$instance = new Collection();

		self::assertInternalType('int', count($instance));
	}

	public function testAddWrongTypeToCollection() {
		$instance = new Collection();

		$instance[] = new \DateInterval('PT30S');
		$instance[] = new \DateTime;

		self::assertInternalType('int', count($instance));
		self::assertCount(1, $instance);
	}

	public function testElementWithIndexToCollection()
	{
		$instance = new Collection();

		$instance[] = new \DateTime;
		$instance[2] = new \DateTime;

		self::assertCount(2, $instance);
		self::assertTrue(isset($instance[2]));
		self::assertInstanceOf(\DateTime::class, $instance[2]);

		unset($instance[2]);
		self::assertFalse(isset($instance[2]));
	}

	public function providerArrayAccess()
	{
		$instance = new Collection();

		$instance[] = 'orange';
		$instance[] = 'banana';

		return [
			[$instance]
		];
	}

	/**
	 * @dataProvider providerArrayAccess
	 */
	public function testShift(Collection $instance)
	{
		$instance[] = 'apple';
		$instance[] = 'raspberry';

		$element = $instance->shift();
		self::assertInternalType('string', $element);
		self::assertEquals('orange', $element);

		self::assertCount(3, $instance);
	}

	/**
	 * @dataProvider providerArrayAccess
	 */
	public function testAppend(Collection $instance)
	{
		$elementsToAdd = new Collection();
		$elementsToAdd[] = 'apple';
		$elementsToAdd[] = 'raspberry';

		self::assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		self::assertCount(4, $instance);
	}

	/**
	 * @dataProvider providerArrayAccess
	 */
	public function testWrongTypeCollectionToAppend(Collection $instance) {
		$elementsToAdd = new Collection();
		$elementsToAdd[] = new \DateTime;
		$elementsToAdd[] = new \DateTime;

		self::assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		self::assertCount(2, $instance);

		$elementsToAdd = new Collection();

		self::assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
		self::assertCount(2, $instance);
	}

	/**
	 * @dataProvider providerArrayAccess
	 */
	public function testInsert(Collection $instance) {
		$elementsToAdd = new Collection();
		$elementsToAdd[] = 'apple';
		$elementsToAdd[] = 'raspberry';

		self::assertInstanceOf(Collection::class, $instance->insert(0, $elementsToAdd));
		self::assertCount(3, $instance);
	}

	/**
	 * @dataProvider providerArrayAccess
	 */
	public function testDelete(Collection $instance) {
		$instance[] = 'apple';
		$instance[] = 'raspberry';

		self::assertInstanceOf(Collection::class, $instance->delete(2));
		self::assertFalse(isset($instance[3]));
		self::assertCount(3, $instance);
	}
}
