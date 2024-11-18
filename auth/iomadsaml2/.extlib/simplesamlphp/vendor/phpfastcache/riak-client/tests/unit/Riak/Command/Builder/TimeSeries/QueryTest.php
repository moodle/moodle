<?php

namespace Basho\Tests\Riak\Command\Builder;

use Basho\Riak\Command;
use Basho\Riak\TimeSeries\Cell;
use Basho\Tests\TestCase;
use Basho\Tests\TimeSeriesTrait;

/**
 * Tests the configuration of Riak commands via the Command Builder class
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class QueryTest extends TestCase
{
    protected static $query = "select * from GeoCheckin where time > 1234560 and time < 1234569 and myfamily = 'family1' and myseries = 'series1'";

    /**
     * Test command builder construct
     */
    public function testFetch()
    {
        // initialize builder
        $builder = (new Command\Builder\TimeSeries\Query(static::$riak))
            ->withQuery(static::$query);

        // build a command
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\TimeSeries\Query\Fetch', $command);
        $this->assertEquals(static::$query, $command->getData()["query"]);
    }
}
