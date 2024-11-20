<?php

namespace Basho\Tests\Riak;

use Basho\Riak\Node;
use Basho\Tests\TestCase;

/**
 * Main class for testing Riak clustering
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class NodeTest extends TestCase
{
    public function testConfig()
    {
        $node = static::getLocalNode();

        $this->assertEquals(static::getTestHost(), $node->getHost());
        $this->assertEquals(static::getTestPort(), $node->getPort());
        $this->assertNotEmpty($node->getSignature());
    }
}
