<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Scenario tests for when Kv Object changes result in a conflict
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class ObjectConflictTest extends TestCase
{
    private static $key = 'conflicted';
    private static $vclock = '';

    public function testStoreTwiceWithKey()
    {
        $command = (new Command\Builder\StoreObject(static::$riak))
            ->buildObject('some_data')
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->buildObject('some_other_data')
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testStoreTwiceWithKey
     */
    public function testFetchConflicted()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('300', $response->getCode());
        $this->assertTrue($response->hasSiblings());
        $this->assertNotEmpty($response->getSiblings());
        $this->assertNotEmpty($response->getObject()->getVclock());

        static::$vclock = $response->getObject()->getVclock();
    }

    /**
     * @depends      testFetchConflicted
     */
    public function testResolveConflict()
    {
        $object = new Riak\DataObject('some_resolved_data');
        $object->setVclock(static::$vclock);

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testResolveConflict
     */
    public function testFetchResolved()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'test', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertEquals('some_resolved_data', $response->getObject()->getData());
    }
}
