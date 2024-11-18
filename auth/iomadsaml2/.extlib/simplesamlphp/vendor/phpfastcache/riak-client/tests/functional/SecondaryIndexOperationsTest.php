<?php

namespace Basho\Tests;

use Basho\Riak\Command;
use Basho\Riak\DataObject as RObject;

/**
 * Functional tests related to secondary indexes
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class SecondaryIndexOperationsTest extends TestCase
{
    private static $key = '';
    private static $bucket = '';

    /**
     * @var \Basho\Riak\DataObject|null
     */
    private static $object = NULL;

    /**
     * @var array|null
     */
    private static $vclock = NULL;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // make completely random key/bucket based on time
        static::$key = md5(rand(0, 99) . time());
        static::$bucket = md5(rand(0, 99) . time());
    }

    public function testStoreObjectWithIndexes()
    {
        $object = new RObject('person');
        $object->addValueToIndex('lucky_numbers_int', 42);
        $object->addValueToIndex('lucky_numbers_int', 64);
        $object->addValueToIndex('lastname_bin', 'Knuth');

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());
    }

    /**
     * @depends      testStoreObjectWithIndexes
     */
    public function testFetchObjectWithIndexes()
    {
        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataObject', $response->getObject());
        $this->assertEquals('person', $response->getObject()->getData());
        $this->assertNotEmpty($response->getObject()->getVClock());
        $indexes = $response->getObject()->getIndexes();
        $this->assertEquals($indexes['lucky_numbers_int'], [42, 64]);
        $this->assertEquals($indexes['lastname_bin'], ['Knuth']);

        static::$object = $response->getObject();
        static::$vclock = $response->getObject()->getVClock();
    }

    /**
     * @depends      testFetchObjectWithIndexes
     */
    public function testRemoveIndexes()
    {
        $object = static::$object;
        $object->removeValueFromIndex('lucky_numbers_int', 64);
        $object->removeValueFromIndex('lastname_bin', 'Knuth');
        $object->setVclock(static::$vclock);

        $command = (new Command\Builder\StoreObject(static::$riak))
            ->withObject($object)
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        // TODO: internalize Vclock to Riak\Object.

        $response = $command->execute();

        $this->assertEquals('204', $response->getCode());

        $command = (new Command\Builder\FetchObject(static::$riak))
            ->buildLocation(static::$key, 'Users', static::LEVELDB_BUCKET_TYPE)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertInstanceOf('Basho\Riak\DataObject', $response->getObject());
        $this->assertEquals('person', $response->getObject()->getData());
        $indexes = $response->getObject()->getIndexes();
        $this->assertEquals($indexes['lucky_numbers_int'], [42]);
    }

    public function testSetupIndexObjects()
    {
        for($x = 0; $x <= 1000; $x++) {
            $object = (new RObject('student'.$x))
                        ->addValueToIndex('lucky_numbers_int', $x) // 0,1,2...
                        ->addValueToIndex('group_int', $x % 2)     // 0,0,1,1,2,2,3,3,...
                        ->addValueToIndex('grade_bin', chr(65 + ($x % 6))) // A,B,C,D,E,F,A...
                        ->addValueToIndex('lessThan500_bin', $x < 500 ? 'less' : 'more');

            $command = (new Command\Builder\StoreObject(static::$riak))
                ->withObject($object)
                ->buildLocation('student'.$x, 'Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
                ->build();

            $response = $command->execute();
            $this->assertEquals('204', $response->getCode());
        }
    }

    /**
     * @depends      testSetupIndexObjects
     */
    public function testScalarQuery()
    {
        $command = (new Command\Builder\QueryIndex(static::$riak))
                        ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
                        ->withIndexName('lucky_numbers_int')
                        ->withScalarValue(5)
                        ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertEquals(1, count($response->getResults()));
        $this->assertEquals('student5', $response->getResults()[0]);
    }

    /**
     * @depends      testSetupIndexObjects
     */
    public function testRangeQuery()
    {
        $command = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('grade_bin')
            ->withRangeValue('A', 'B')
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $matches = $response->getResults();
        sort($matches, SORT_NATURAL | SORT_FLAG_CASE);
        $this->assertEquals(334, count($matches));
        $this->assertEquals(['student0','student1','student6','student7'], array_slice($matches, 0, 4));
    }

    /**
     * @depends      testSetupIndexObjects
     */
    public function testReturnTerms()
    {
        $keysAndTerms = [['A' => 'student0'], ['B' => 'student1'], ['A' => 'student6'], ['B' => 'student7']];

        $command = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('grade_bin')
            ->withRangeValue('A', 'B')
            ->withReturnTerms(true)
            ->build();

        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $matches = $response->getResults();
        usort($matches, function($a, $b) { return strnatcmp(current($a), current($b)); });

        $this->assertEquals(334, count($matches));
        $this->assertTrue($response->hasReturnTerms());
        $this->assertEquals($keysAndTerms, array_slice($matches, 0, 4));
    }



    /**
     * @depends      testSetupIndexObjects
     */
    public function testGettingKeysWithContinuationWorks()
    {
        $builder = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0,3)
            ->withMaxResults(3);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertEquals(3, count($response->getResults()));
        $this->assertNotNull($response->getContinuation());
        $this->assertFalse($response->isDone());

        // Get second page
        $builder = $builder->withContinuation($response->getContinuation());
        $command2 = $builder->build();

        $response2 = $command2->execute();

        $this->assertEquals('200', $response2->getCode());
        $this->assertEquals(1, count($response2->getResults()));
        $this->assertNull($response2->getContinuation());
        $this->assertTrue($response2->isDone());
    }

    /**
     * @depends      testSetupIndexObjects
     */
    public function testTimeoutWorks()
    {
        $this->markTestSkipped('It is a weak test since it depends on local processing speed.');

        $builder = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students' . static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0, 1000)
            ->withTimeout(1);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();
        $this->assertFalse($response->isSuccess());
        $this->assertContains($response->getCode(), ['500', '503']);
        $this->assertEquals(0, count($response->getResults()));
        $this->assertNull($response->getContinuation());
        $this->assertTrue($response->isDone());
    }

    /**
     * @depends      testSetupIndexObjects
     */
    public function testUsingPaginationSortWillSortResultsWhilePaging()
    {
        $builder = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students'.static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lucky_numbers_int')
            ->withRangeValue(0,500)
            ->withMaxResults(10)
            ->withReturnTerms(true);

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertEquals(['0' => 'student0'], $response->getResults()[0]);

        // Get second page
        $builder = $builder->withContinuation($response->getContinuation());
        $command2 = $builder->build();

        $response2 = $command2->execute();

        $this->assertEquals('200', $response2->getCode());
        $this->assertEquals(10, count($response2->getResults()));
        $this->assertEquals(['10' => 'student10'], $response2->getResults()[0]);
    }


    /**
     * @depends      testSetupIndexObjects
     */
    public function testUsingTermRegexOnARangeFiltersTheResults()
    {
        $builder = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students' . static::$bucket, static::LEVELDB_BUCKET_TYPE)
            ->withIndexName('lessThan500_bin')
            ->withRangeValue('a', 'z')
            ->withTermFilter('^less');

        // Get first page
        $command = $builder->build();
        $response = $command->execute();

        $this->assertEquals('200', $response->getCode());
        $this->assertEquals(500, count($response->getResults()));
    }
}
