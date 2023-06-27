<?php

namespace Basho\Tests;

use Basho\Riak\Command;

/**
 * Functional test to ping Riak
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class PingTest extends TestCase
{
    public function testPing()
    {
        // build an object
        $command = (new Command\Builder\Ping(static::$riak))
            ->build();

        $response = $command->execute();

        // expects 201 - Created
        $this->assertEquals('200', $response->getCode());
    }
}
