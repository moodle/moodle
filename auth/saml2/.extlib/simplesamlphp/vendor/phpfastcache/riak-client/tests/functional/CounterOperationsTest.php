<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Class CounterTest
 *
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class CounterOperationsTest extends TestCase
{
    private static $key = '';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());
    }

    public function testIncrementNewWithoutKey()
    {
        // build an object
        $command = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(1)
            ->buildBucket('visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode());
        $this->assertNotEmpty($response->getLocation());
    }

    public function testFetchNotFound()
    {
        $command = (new Command\Builder\FetchCounter(static::$riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());
    }

    /**
     * @depends      testFetchNotFound
     */
    public function testIncrementNewWithKey()
    {
        $command = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getCode());
        $this->assertEmpty($response->getLocation());
    }

    /**
     * @depends      testIncrementNewWithKey
     */
    public function testFetchOk()
    {
        $command = (new Command\Builder\FetchCounter(static::$riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(1, $response->getCounter()->getData());
    }

    /**
     * @depends      testFetchOk
     */
    public function testIncrementExisting()
    {
        $command = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testIncrementExisting
     */
    public function testFetchOk2()
    {
        $command = (new Command\Builder\FetchCounter(static::$riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(2, $response->getCounter()->getData());
    }

    /**
     * @depends      testFetchOk
     */
    public function testDecrementExisting()
    {
        $command = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(-1)
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testDecrementExisting
     */
    public function testFetchOk3()
    {
        $command = (new Command\Builder\FetchCounter(static::$riak))
            ->buildLocation(static::$key, 'visits', static::COUNTER_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $response->getCounter());
        $this->assertNotEmpty($response->getCounter()->getData());
        $this->assertTrue(is_integer($response->getCounter()->getData()));
        $this->assertEquals(1, $response->getCounter()->getData());
    }
}
