<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional tests related to Set CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SetTest extends TestCase
{
    /**
     * Key to be used for tests
     *
     * @var string
     */
    private static $key = '';

    /**
     * Array of context generated from working with the same Set
     *
     * @var array
     */
    private static $context = [];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make completely random key based on time
        static::$key = md5(rand(0, 99) . time());
    }

    public function testAddWithoutKey()
    {
        // build an object
        $command = (new Command\Builder\UpdateSet(static::$riak))
            ->add('gosabres poked you.')
            ->add('phprocks viewed your profile.')
            ->add('phprocks started following you.')
            ->buildBucket('default', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode());
        $this->assertNotEmpty($response->getLocation());
    }

    public function testFetchNotFound()
    {
        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'default', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());
    }

    /**
     * @depends      testFetchNotFound
     */
    public function testAddNewWithKey()
    {
        $command = (new Command\Builder\UpdateSet(static::$riak))
            ->add('Sabres')
            ->add('Canadiens')
            ->add('Bruins')
            ->add('Maple Leafs')
            ->add('Senators')
            ->add('Red Wings')
            ->add('Thrashers')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getCode());
        $this->assertEmpty($response->getLocation());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
        $this->assertNotEmpty($response->getSet()->getContext());

        static::$context[] = $response->getSet()->getContext();
    }

    /**
     * @depends      testAddNewWithKey
     */
    public function testAddAnotherNew()
    {
        // add without context
        $command = (new Command\Builder\UpdateSet(static::$riak))
            ->add('Lightning')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(8, count($response->getSet()->getData()));

        static::$context[] = $response->getSet()->getContext();
    }

    /**
     * @depends      testAddNewWithKey
     */
    public function testRemoveExisting()
    {
        // using stale context
        $command = (new Command\Builder\UpdateSet(static::$riak))
            ->remove('Thrashers')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->withContext(static::$context[0])
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));

        static::$context[] = $response->getSet()->getContext();
    }

    /**
     * @depends      testRemoveExisting
     */
    public function testAddRemoveExisting()
    {
        // using latest context
        $command = (new Command\Builder\UpdateSet(static::$riak))
            ->add('Penguins')
            ->add('Ducks')
            ->remove('Lightning')
            ->remove('Red Wings')
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->withContext(end(static::$context))
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchSet(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::SET_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Set', $response->getSet());
        $this->assertNotEmpty($response->getSet()->getData());
        $this->assertTrue(is_array($response->getSet()->getData()));
        $this->assertEquals(7, count($response->getSet()->getData()));
        $this->assertTrue(in_array('Ducks', $response->getSet()->getData()));
        $this->assertFalse(in_array('Lightning', $response->getSet()->getData()));

        static::$context[] = $response->getSet()->getContext();
    }
}
