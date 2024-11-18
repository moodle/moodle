<?php

namespace Basho\Tests\Riak;

use Basho\Riak\DataObject as RObject;
use Basho\Tests\TestCase;

/**
 * Test set for key value objects
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectTest extends TestCase
{
    public function testConstruct()
    {
        // simple new object
        $object = new RObject();
        $this->assertEmpty($object->getData());

        // more complex object
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new RObject($data, ['Content-Type' => 'text/plain']);
        $this->assertEquals('sauce', $object->getData()->woot);
        $this->assertEquals('text/plain', $object->getContentType());
    }

    public function testExtractIndexes()
    {
        // no headers means no indexes, empty array.
        $data = new \StdClass();
        $data->woot = 'sauce';

        $object = new RObject($data);
        $this->assertEmpty($object->getIndexes());
        $this->assertEquals(NULL, $object->getIndex("foo_bin"));

        // 2i headers will result in indexes
        $headers = ['My-Header' => 'cats', 'x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $object = new RObject($data, $headers);

        $indexes = $object->getIndexes();
        $this->assertNotEmpty($indexes);
        $this->assertEquals(2, count($indexes));
        $this->assertEquals(['bar', 'baz'], $indexes["foo_bin"]);
        $this->assertEquals([42, 50], $indexes["foo_int"]);
    }

    public function testAddIndexes()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';

        $headers = ['x-riak-index-foo_bin' => 'bar', 'x-riak-index-foo_int' => '42'];
        $object = new RObject($data, $headers);

        $object->addValueToIndex("foo_int", 50);
        $object->addValueToIndex("foo_bin", 'baz');

        $indexes = $object->getIndexes();
        $this->assertNotEmpty($indexes);
        $this->assertEquals(2, count($indexes));
        $this->assertEquals(['bar', 'baz'], $indexes["foo_bin"]);
        $this->assertEquals([42, 50], $indexes["foo_int"]);
    }

    public function testRemoveIndexes()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';

        $headers = ['x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $object = new RObject($data, $headers);

        $object->removeValueFromIndex("foo_int", 50);
        $object->removeValueFromIndex("foo_bin", 'baz');
        $object->removeValueFromIndex("foo_bin", 'bar');

        $indexes = $object->getIndexes();
        $this->assertNotEmpty($indexes);
        $this->assertEquals(1, count($indexes));
        $this->assertEquals([42], $indexes["foo_int"]);
    }

    public function testGetIndex()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';

        $headers = ['x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $object = new RObject($data, $headers);

        $index = $object->getIndex('foo_bin');
        $this->assertNotNull($index);
        $this->assertEquals(['bar', 'baz'], $index);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidIndexName()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new RObject($data);

        $object->addValueToIndex('foo_bar', 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDataTypeForBinIndex()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new RObject($data);

        $object->addValueToIndex('foo_bin', 42);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDataTypeForIntIndex()
    {
        $data = new \StdClass();
        $data->woot = 'sauce';
        $object = new RObject($data);

        $object->addValueToIndex('foo_int', 'bar');
    }
}
