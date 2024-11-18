<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Tests\TestCase;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class QueryIndexTest extends TestCase
{
    /**
     * Test command builder construct
     */
    public function testQuery()
    {
        // build an object
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
                ->withIndexName('foo_int')
                ->withScalarValue(42);

        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\Indexes\Query', $command);
        $this->assertInstanceOf('Basho\Riak\Bucket', $command->getBucket());
        $this->assertEquals('some_bucket', $command->getBucket()->getName());
        $this->assertEquals('some_bucket_type', $command->getBucket()->getType());
        $this->assertEquals('foo_int', $command->getIndexName());
        $this->assertEquals('42', $command->getMatchValue());
    }

    /**
     * Tests validate properly verifies the index name is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateLocation()
    {
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket');

        $builder->build();
    }

    /**
     * Tests validate properly verifies the scalar match value is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateScalarValue()
    {
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket')
                ->withIndexName("foo_int")
                ->withScalarValue(null);

        $builder->build();
    }

    /**
     * Tests validate properly verifies the range lower bound value is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateRangeLowerBound()
    {
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket')
            ->withIndexName("foo_int")
            ->withRangeValue(null, 42);

        $builder->build();
    }

    /**
     * Tests validate properly verifies the range upper bound value is not there
     *
     * @expectedException \Basho\Riak\Command\Builder\Exception
     */
    public function testValidateRangeUpperBound()
    {
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket')
            ->withIndexName("foo_int")
            ->withRangeValue(42, null);

        $builder->build();
    }

    /**
     * Test command builder defaults for options
     */
    public function testOptionDefaults()
    {
        // build an object
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
            ->withIndexName('foo_int')
            ->withScalarValue(42);

        $command = $builder->build();

        $parameters = $command->getParameters();

        $this->assertFalse(isset($parameters['continuation']));
        $this->assertFalse(isset($parameters['return_terms']));
        $this->assertFalse(isset($parameters['pagination_sort']));
        $this->assertFalse(isset($parameters['term_regex']));
        $this->assertFalse(isset($parameters['timeout']));
    }

    /**
     * Test command builder settings for options
     */
    public function testOptionSettings()
    {
        // build an object
        $builder = new Command\Builder\QueryIndex(static::$riak);
        $builder->buildBucket('some_bucket', 'some_bucket_type')
            ->withIndexName('foo_int')
            ->withScalarValue(42)
            ->withContinuation('12345')
            ->withReturnTerms(true)
            ->withPaginationSort(true)
            ->withTermFilter('foobar')
            ->withTimeout(43);

        $command = $builder->build();



        $this->assertEquals('12345', $command->getParameter('continuation'));
        $this->assertEquals('true', $command->getParameter('return_terms'));
        $this->assertEquals('true', $command->getParameter('pagination_sort'));
        $this->assertEquals('foobar', $command->getParameter('term_regex'));
        $this->assertEquals(43, $command->getParameter('timeout'));
    }
}
