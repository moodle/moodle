<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class FetchMapTest extends TestCase
{
    /**
     * Test command builder construct
     */
    public function testFetch()
    {
        // build an object
        $builder = new Command\Builder\FetchMap(static::$riak);
        $builder->buildLocation('some_key', 'some_bucket');
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\DataType\Map\Fetch', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertInstanceOf('Basho\Riak\Location', $command->getLocation());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());
        $this->assertEquals('some_key', $command->getLocation()->getKey());

        $builder->buildLocation('some_key', 'some_bucket', 'some_type');
        $command = $builder->build();

        $this->assertEquals('some_type', $command->getBucket()->getType());
    }

    /**
     * Tests validate properly verifies the Map is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateLocation()
    {
        $builder = new Command\Builder\FetchMap(static::$riak);
        $builder->buildBucket('some_bucket');
        $builder->build();
    }
}
