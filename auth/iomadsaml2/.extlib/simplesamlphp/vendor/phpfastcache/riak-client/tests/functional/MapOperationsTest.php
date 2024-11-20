<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class MapOperationsTest extends TestCase
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
        // build a map update command
        $command = (new Command\Builder\UpdateMap(static::$riak))
            ->updateRegister('favorite', 'Buffalo Sabres')
            ->buildBucket('default', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('201', $response->getCode());
        $this->assertNotEmpty($response->getLocation());
    }

    public function testFetchNotFound()
    {
        $command = (new Command\Builder\FetchMap(static::$riak))
            ->buildLocation(static::$key, 'default', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode());
    }

    /**
     * @depends      testFetchNotFound
     */
    public function testAddNewWithKey()
    {
        $updateSetBuilder = (new Command\Builder\UpdateSet(static::$riak))
            ->add('Sabres');

        $updateCounterBuilder = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(1);

        $command = (new Command\Builder\UpdateMap(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->updateCounter('teams', $updateCounterBuilder)
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->build();

        $response = $command->execute();

        // expects 204 - No Content
        // this is wonky, its not 201 because the key may have been generated on another node
        $this->assertEquals('204', $response->getCode());
        $this->assertEmpty($response->getLocation());

        $command = (new Command\Builder\FetchMap(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(1, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(1, $map->getCounter('teams')->getData());
        $this->assertNotEmpty($map->getContext());

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testAddNewWithKey
     */
    public function testAddExisting()
    {
        $updateSetBuilder = (new Command\Builder\UpdateSet(static::$riak))
            ->add('Bruins')
            ->add('Thrashers');

        $updateCounterBuilder = (new Command\Builder\IncrementCounter(static::$riak))
            ->withIncrement(2);

        // build a map update command
        $command = (new Command\Builder\UpdateMap(static::$riak))
            ->updateFlag('expansion_year', TRUE)
            ->updateCounter('teams', $updateCounterBuilder)
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchMap(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(3, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(3, $map->getCounter('teams')->getData());

        $this->assertTrue($map->getFlag('expansion_year'));

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testAddExisting
     *
     * @expectedException \Basho\Riak\DataType\Exception
     */
    public function testRemoveExisting()
    {
        $updateSetBuilder = (new Command\Builder\UpdateSet(static::$riak))
            ->remove('Thrashers')
            ->add('Lightning');

        // build a map update command with stale context
        $command = (new Command\Builder\UpdateMap(static::$riak))
            ->removeFlag('expansion_year')
            ->updateSet('ATLANTIC_DIVISION', $updateSetBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->withContext(static::$context[0])
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchMap(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $map = $response->getMap();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Set', $map->getSet('ATLANTIC_DIVISION'));
        $this->assertEquals(3, count($map->getSet('ATLANTIC_DIVISION')->getData()));

        $this->assertInstanceOf('Basho\Riak\DataType\Counter', $map->getCounter('teams'));
        $this->assertEquals(3, $map->getCounter('teams')->getData());

        $this->assertTrue($map->getFlag('expansion_year'));

        static::$context[] = $response->getMap()->getContext();
    }

    /**
     * @depends      testRemoveExisting
     */
    public function testAddMapExisting()
    {
        $updateMapBuilder = (new Command\Builder\UpdateMap(static::$riak))
            ->updateFlag('notifications', FALSE)
            ->updateRegister('label', 'Email Alerts');

        // build a map update command
        $command = (new Command\Builder\UpdateMap(static::$riak))
            ->updateMap('preferences', $updateMapBuilder)
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        // 204 - No Content
        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchMap(static::$riak))
            ->buildLocation(static::$key, 'Teams', static::MAP_BUCKET_TYPE)
            ->build();

        $response = $command->execute();
        $map = $response->getMap();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataType\Map', $response->getMap());

        $this->assertInstanceOf('Basho\Riak\DataType\Map', $map->getMap('preferences'));
        $this->assertEquals('Email Alerts', $map->getMap('preferences')->getRegister('label'));
        $this->assertFalse($map->getMap('preferences')->getFlag('notifications'));

        static::$context[] = $response->getMap()->getContext();
    }
}
