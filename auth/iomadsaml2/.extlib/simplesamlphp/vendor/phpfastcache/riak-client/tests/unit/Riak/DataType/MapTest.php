<?php

namespace Basho\Tests\Riak;

use Basho\Riak\DataType\Map;

/**
 * Test set for counter crdt
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class MapTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $this->assertEquals('map', Map::TYPE);

        $crdt = new Map([], '');
        $this->assertEquals('map', $crdt->getType());
    }
}
