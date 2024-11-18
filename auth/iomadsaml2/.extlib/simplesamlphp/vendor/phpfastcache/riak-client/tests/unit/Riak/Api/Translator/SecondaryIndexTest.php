<?php

namespace Basho\Tests\Riak;

use Basho\Riak\Api;
use Basho\Tests\TestCase;

/**
 * Test set for the HTTP Header <-> Secondary Index translator
 *
 * @author Alex Moore <amoore at basho d0t com>
 */
class SecondaryIndexTest extends TestCase
{
    public function testExtractIndexes()
    {
        $headers = ['My-Header' => 'cats', 'x-riak-index-foo_bin' => 'bar, baz', 'x-riak-index-foo_int' => '42, 50'];
        $translator = new Api\Http\Translator\SecondaryIndex();

        $indexes = $translator->extractIndexesFromHeaders($headers);

        // Check that array was modified, and only the non-index header is left.
        $this->assertEquals(['My-Header' => 'cats'], $headers);

        // Check that we have 2 indexes, with the appropriate values.
        $this->assertNotEmpty($indexes);
        $this->assertEquals(2, count($indexes));
        $this->assertEquals(['bar', 'baz'], $indexes["foo_bin"]);
        $this->assertEquals([42, 50], $indexes["foo_int"]);
    }

    public function testExtractIndexesNoHeaders()
    {
        $headers = [];
        $translator = new Api\Http\Translator\SecondaryIndex();
        $indexes = $translator->extractIndexesFromHeaders($headers);

        // Check that we get an empty array back.
        $this->assertNotNull($indexes);
        $this->assertEmpty($indexes);
    }

    public function testCreateHeaders()
    {
        $indexes = ['foo_bin' => ['bar', 'baz'], 'foo_int' => [42, 50]];
        $translator = new Api\Http\Translator\SecondaryIndex();

        $headers = $translator->createHeadersFromIndexes($indexes);

        // Check that 4 different header key/value pairs are created, with the correct values.
        $this->assertEquals(4, count($headers));
        $this->assertEquals(['x-riak-index-foo_bin', 'bar'], $headers[0]);
        $this->assertEquals(['x-riak-index-foo_bin', 'baz'], $headers[1]);
        $this->assertEquals(['x-riak-index-foo_int', '42'], $headers[2]);
        $this->assertEquals(['x-riak-index-foo_int', '50'], $headers[3]);
    }
}
