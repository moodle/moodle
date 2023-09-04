<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ListObjectsTest extends TestCase
{
    /**
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testListKeysFailsWithoutAcknowledgingRisk()
    {
        $response = (new Command\Builder\ListObjects(static::$riak))
            ->buildBucket('list-keys-php')
            ->build();
    }

    /**
     * Test command builder construct
     */
    public function testList()
    {
        // build an object
        $builder = (new Command\Builder\ListObjects(static::$riak))
            ->buildBucket('some_bucket')
            ->acknowledgeRisk(true);
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\KVObject\Keys\Fetch', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('default', $command->getBucket()->getType());

        $builder->buildBucket('some_bucket', 'some_type');
        $command = $builder->build();

        $this->assertEquals('some_type', $command->getBucket()->getType());
    }
}
