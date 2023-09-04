<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Functional tests related to Counter CRDTs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SearchOperationsTest extends TestCase
{
    const SCHEMA = '_yz_default';
    const INDEX = 'player_index';

    protected static $default_schema = '';

    protected static $search_content = [
        'tennis'       => ['name_s' => 'T. Ennis', 'forward_i' => 1, 'position_s' => 'LW'],
        'zgirgensons'  => ['name_s' => 'Z. Girgensons', 'forward_i' => 1, 'position_s' => 'C'],
        'rristolainen' => ['name_s' => 'R. Ristolainen', 'forward_i' => 0, 'position_s' => 'RD'],
        'zbogosian'    => ['name_s' => 'Z. Bogosian', 'forward_i' => 0, 'position_s' => 'LD'],
        'alindback'    => ['name_s' => 'A. Lindback', 'forward_i' => 0, 'position_s' => 'G'],
        'bgionta'      => ['name_s' => 'B. Gionta', 'forward_i' => 1, 'position_s' => 'RW', 'captain_i' => 1],
    ];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $response = (new Command\Builder\Search\FetchIndex(static::$riak))
            ->withName(static::INDEX)
            ->build()
            ->execute();

        if ($response->getCode() == '200') {
            (new Command\Builder\Search\DissociateIndex(static::$riak))
                ->buildBucket('sabres', self::SEARCH_BUCKET_TYPE)
                ->build()
                ->execute();

            (new Command\Builder\Search\DeleteIndex(static::$riak))
                ->withName(static::INDEX)
                ->build()
                ->execute();
        }
    }

    public static function tearDownAfterClass()
    {
        foreach (static::$search_content as $key => $object) {
            (new Command\Builder\DeleteObject(static::$riak))
                ->buildLocation($key, 'sabres', self::SEARCH_BUCKET_TYPE)
                ->build()
                ->execute();
        }

        parent::tearDownAfterClass();
    }

    public function testFetchSchema()
    {
        $response = (new Command\Builder\Search\FetchSchema(static::$riak))
            ->withName('_yz_default')
            ->build()
            ->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertEquals('application/xml', $response->getContentType());
        $this->assertNotEmpty($response->getSchema());

        static::$default_schema = $response->getSchema();
    }

    /**
     * @depends      testFetchSchema
     */
    public function testStoreSchema()
    {
        $command = (new Command\Builder\Search\StoreSchema(static::$riak))
            ->withName('users')
            ->withSchemaString(static::$default_schema)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    public function testFetchIndexNotFound()
    {
        $command = (new Command\Builder\Search\FetchIndex(static::$riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        $this->assertEquals('404', $response->getCode(), $response->getMessage());
    }

    /**
     * @depends      testFetchIndexNotFound
     */
    public function testStoreIndex()
    {
        $command = (new Command\Builder\Search\StoreIndex(static::$riak))
            ->withName(static::INDEX)
            ->usingSchema('_yz_default')
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());

        $command = (new Command\Builder\Search\FetchIndex(static::$riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        // indexes take time to propagate between solr and Riak
        $attempts = 1;
        while ($response->getCode() <> '200' || $attempts <= 5) {
            sleep(1);
            $response = $command->execute();
            $attempts++;
        }

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertEquals(static::SCHEMA, $response->getIndex()->schema);
    }

    /**
     * @depends      testStoreIndex
     */
    public function testAssociateIndex()
    {
        $command = (new Command\Builder\Search\AssociateIndex(static::$riak))
            ->withName(static::INDEX)
            ->buildBucket('sabres', self::SEARCH_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    /**
     * @depends      testAssociateIndex
     */
    public function testSearch()
    {
        foreach (static::$search_content as $key => $object) {
            $command = (new Command\Builder\StoreObject(static::$riak))
                ->buildObject($object, ['Content-Type' => 'application/json'])
                ->buildLocation($key, 'sabres', self::SEARCH_BUCKET_TYPE)
                ->build();

            $command->execute();
        }

        sleep(5);

        $command = (new Command\Builder\Search\FetchObjects(static::$riak))
            ->withQuery('name_s:*Gi*')
            ->withIndexName(static::INDEX)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertEquals(2, $response->getNumFound());

        $docs = $response->getDocs();
        foreach ($docs as $d) {
            $this->assertTrue('B. Gionta' == $d->name_s || 'Z. Girgensons' == $d->name_s);
        }
    }

    /**
     * @depends      testSearch
     */
    public function testSearchWithSort()
    {
        $command = (new Command\Builder\Search\FetchObjects(static::$riak))
            ->withQuery('name_s:*Gi*')
            ->withIndexName(static::INDEX)
            ->withSortField("name_s asc")
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode(), $response->getMessage());
        $this->assertEquals(2, $response->getNumFound());
        $this->assertEquals('B. Gionta', $response->getDocs()[0]->name_s);
    }

    /**
     * Tests handling of a badly formed query
     *
     * @depends      testAssociateIndex
     */
    public function testBadSearch()
    {
        $response = (new Command\Builder\Search\FetchObjects(static::$riak))
            ->withIndexName(static::INDEX)
            ->withQuery('ffffff')
            ->build()
            ->execute();

        $this->assertEquals('400', $response->getCode(), $response->getMessage());
        $this->assertEmpty($response->getNumFound());
        $this->assertEmpty($response->getDocs());
    }

    /**
     * @depends      testSearch
     */
    public function testDissociateIndex()
    {
        $command = (new Command\Builder\Search\DissociateIndex(static::$riak))
            ->buildBucket('sabres', self::SEARCH_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());
    }

    /**
     * @depends      testDissociateIndex
     */
    public function testDeleteIndex()
    {
        $command = (new Command\Builder\Search\DeleteIndex(static::$riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode(), $response->getMessage());

        $command = (new Command\Builder\Search\FetchIndex(static::$riak))
            ->withName(static::INDEX)
            ->build();

        $response = $command->execute();

        // indexes take time to propagate between solr and Riak
        $attempts = 1;
        while ($response->getCode() <> '404' || $attempts <= 5) {
            sleep(1);
            $response = $command->execute();
            $attempts++;
        }

        $this->assertEquals('404', $response->getCode(), $response->getMessage());
    }
}
