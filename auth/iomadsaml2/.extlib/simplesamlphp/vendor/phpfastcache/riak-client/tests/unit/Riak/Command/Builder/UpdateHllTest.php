<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class UpdateHllTest extends TestCase
{
    /**
     * Test command builder construct
     */
    public function testStoreWithKey()
    {
        // build an object
        $builder = new Command\Builder\UpdateHll(static::$riak);
        $builder->add('foo');
        $builder->add('bar');
        $builder->add('baz');
        $builder->buildLocation('some_key', 'some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Hll\Store', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertInstanceOf('Basho\Riak\Location', $command->getLocation());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());
        $this->assertEquals('some_key', $command->getLocation()->getKey());
        $this->assertEquals(['add_all' => ['foo', 'bar', 'baz']], $command->getData());
        $this->assertEquals(json_encode(['add_all' => ['foo', 'bar', 'baz']]), $command->getEncodedData());
    }

    /**
     * Test command builder construct
     */
    public function testStoreWithOutKey()
    {
        // build an object
        $builder = new Command\Builder\UpdateHll(static::$riak);
        $builder->add('foo');
        $builder->add('bar');
        $builder->add('baz');
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Hll\Store', $command);
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals(['add_all' => ['foo', 'bar', 'baz']], $command->getData());
        $this->assertEquals(json_encode(['add_all' => ['foo', 'bar', 'baz']]), $command->getEncodedData());
    }

    /**
     * Tests validate properly verifies that an intended change is not applied
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateObject()
    {
        $builder = new Command\Builder\UpdateHll(static::$riak);
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
        $builder = new Command\Builder\UpdateHll(static::$riak);
        $builder->add('some_element');
        $builder->build();
    }
}
