<?php

namespace Basho\Tests\Riak;

use Basho\Riak\DataType\Hll;

/**
 * Test for HyperLogLog CRDT
 *
 * @author Luke Bakken <lbakken@basho.com>
 */
class HllTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $this->assertEquals('hll', Hll::TYPE);

        $crdt = new Hll([], '');
        $this->assertEquals('hll', $crdt->getType());
    }
}
