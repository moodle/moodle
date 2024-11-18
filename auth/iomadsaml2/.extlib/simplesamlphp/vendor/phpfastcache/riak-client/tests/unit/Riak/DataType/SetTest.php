<?php

namespace Basho\Tests\Riak;

use Basho\Riak\DataType\Set;

/**
 * Test set for set crdt
 *
 * @author Christopher Mancini <cmancini at basho d0t com>
 */
class SetTest extends \PHPUnit_Framework_TestCase
{
    public function testType()
    {
        $this->assertEquals('set', Set::TYPE);

        $crdt = new Set([], '');
        $this->assertEquals('set', $crdt->getType());

    }
}
