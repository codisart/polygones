<?php
namespace Collection;

use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private function setupCollection()
    {
        return new class() extends Collection {
            public function offsetSet($offset, $value)
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
                foreach ($collection as $key => $value) {
                    $newKey = $key;
                    if (isset($this[$key])) {
                        $newKey = $this->count();
                    }
                    $this[$newKey] = $value;
                }
        
                return $this;
            }
        };
    }

    public function testCount() {
        $instance = $this->setupCollection();

        self::assertIsInt(count($instance));
    }

    public function testElementWithIndexToCollection()
    {
        $instance = $this->setupCollection();

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
        $instance = $this->setupCollection();

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
        self::assertIsString($element);
        self::assertEquals('orange', $element);

        self::assertCount(3, $instance);
    }

    /**
     * @dataProvider providerArrayAccess
     */
    public function testAppend(Collection $instance)
    {
        $elementsToAdd = $this->setupCollection();
        $elementsToAdd[] = 'apple';
        $elementsToAdd[] = 'raspberry';

        self::assertInstanceOf(Collection::class, $instance->append($elementsToAdd));
        self::assertCount(4, $instance);
    }

    /**
     * @dataProvider providerArrayAccess
     */
    public function testInsert(Collection $instance) {
        $elementsToAdd = $this->setupCollection();
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
