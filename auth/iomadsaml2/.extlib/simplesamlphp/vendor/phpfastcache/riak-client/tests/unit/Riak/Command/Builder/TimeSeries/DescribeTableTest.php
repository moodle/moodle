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
class DescribeTableTest extends TestCase
{
    use TimeSeriesTrait;

    /**
     * Test command builder construct
     */
    public function testDescribeTable()
    {
        // initialize builder
        $builder = (new Command\Builder\TimeSeries\DescribeTable(static::$riak))
            ->withTable(static::$table);

        // build a command
        $command = $builder->build();

        $this->assertInstanceOf('Basho\Riak\Command\TimeSeries\Query\Fetch', $command);
        $this->assertEquals("DESCRIBE " . static::$table, $command->getData()["query"]);
    }
}
