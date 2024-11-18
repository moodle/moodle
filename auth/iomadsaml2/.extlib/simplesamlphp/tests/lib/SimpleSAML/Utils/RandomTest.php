<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Utils\Random;

/**
 * Tests for SimpleSAML\Utils\Random.
 */
class RandomTest extends TestCase
{
    /**
     * Test for SimpleSAML\Utils\Random::generateID().
     *
     * @covers SimpleSAML\Utils\Random::generateID
     * @return void
     */
    public function testGenerateID(): void
    {
        // check that it always starts with an underscore
        $this->assertStringStartsWith('_', Random::generateID());

        // check the length
        $this->assertEquals(Random::ID_LENGTH, strlen(Random::generateID()));
    }
}
