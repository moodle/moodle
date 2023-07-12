<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Api\Http;
use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class StoreObjectTest extends TestCase
{
    /**
     * Test command builder construct
     */
    public function testStoreWithKey()
    {
        // build an object
        $builder = new Command\Builder\StoreObject(static::$riak);
        $builder->buildObject('some_data');
        $builder->buildLocation('some_key', 'some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\KVObject\Store', $command);
        $this->assertInstanceOf('Basho\Riak\DataObject', $command->getObject());
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertInstanceOf('Basho\Riak\Location', $command->getLocation());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());
        $this->assertEquals('some_key', $command->getLocation()->getKey());
    }

    /**
     * Test command builder construct
     */
    public function testStoreWithOutKey()
    {
        // build an object
        $builder = new Command\Builder\StoreObject(static::$riak);
        $builder->buildObject('some_data');
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\KVObject\Store', $command);
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
    }

    /**
     * Tests validate properly verifies the Object is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateObject()
    {
        $builder = new Command\Builder\StoreObject(static::$riak);
        $builder->buildBucket('some_bucket');
        $builder->build();
    }

    /**
     * Tests validate properly verifies the Bucket is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateBucket()
    {
        $builder = new Command\Builder\StoreObject(static::$riak);
        $builder->buildObject('some_data');
        $builder->build();
    }

    /**
     * Tests that attempting to store an object generates headers for any
     * 2i entries on the object.
     */
    public function testStoreObjectWithIndexGeneratesHeaders()
    {
        $inputHeaders = [Http::METADATA_PREFIX . 'My-Header' => 'cats', 'x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $builder = new Command\Builder\StoreObject(static::$riak);
        $builder->buildObject('some_data', $inputHeaders);
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\KVObject\Store', $command);

        $this->assertArrayHasKey('My-Header', $command->getObject()->getMetaData());
        $this->assertEquals($command->getObject()->getMetaData()['My-Header'], 'cats');

        $this->assertArrayHasKey('foo_bin', $command->getObject()->getIndexes());
        $this->assertCount(2, $command->getObject()->getIndex('foo_bin'));

        $this->assertArrayHasKey('foo_int', $command->getObject()->getIndexes());
        $this->assertCount(2, $command->getObject()->getIndex('foo_int'));
    }
}
