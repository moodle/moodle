<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder classes
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class UpdateMapTest extends TestCase
{
    /**
     * Test command builder construct
     */
    public function testStoreWithKey()
    {
        // build an object
        $builder = new Command\Builder\UpdateMap(static::$riak);
        $builder->updateRegister('some_key', 'some_data');
        $builder->buildLocation('some_key', 'some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Map\Store', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertInstanceOf('Basho\Riak\Location', $command->getLocation());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());
        $this->assertEquals('some_key', $command->getLocation()->getKey());
        $this->assertEquals(['update' => ['some_key_register' => 'some_data']], $command->getData());
        $this->assertEquals(json_encode(['update' => ['some_key_register' => 'some_data']]), $command->getEncodedData());
    }

    /**
     * Test command builder construct
     */
    public function testStoreWithOutKey()
    {
        // build an object
        $builder = new Command\Builder\UpdateMap(static::$riak);
        $builder->updateRegister('some_key', 'some_data');
        $builder->buildBucket('some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Map\Store', $command);
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
    }

    /**
     * Tests validate properly verifies that intended changes are not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateUpdate()
    {
        $builder = new Command\Builder\UpdateMap(static::$riak);
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
        $builder = new Command\Builder\UpdateMap(static::$riak);
        $builder->updateRegister('some_key', 'some_data');
        $builder->build();
    }

    /**
     * Tests validate properly verifies that remove commands require the context
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateRemove()
    {
        $builder = new Command\Builder\UpdateMap(static::$riak);
        $builder->removeRegister('some_key');
        $builder->build();
    }
}
