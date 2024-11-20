<?php

namespace Basho\Tests;

use Basho\Riak;
use Basho\Riak\Command;

/**
 * Scenario tests for when an internal server error occurs
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class InternalServerErrorTest extends TestCase
{
    /**
     * Currently not executable on any Riak instance configured for multi-backend
     * @expectedException \Basho\Riak\Exception
     */
    public function testQueryInvalidIndex()
    {
        $command = (new Command\Builder\QueryIndex(static::$riak))
            ->buildBucket('Students', static::BITCASK_BUCKET_TYPE)
            ->withIndexName('index_not_found_int')
            ->withScalarValue(5)
            ->build();

        $command->execute();
    }
}
