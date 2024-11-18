<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Utils\Arrays;

/**
 * Tests for SimpleSAML\Utils\Arrays.
 */
class ArraysTest extends TestCase
{
    /**
     * Test the arrayize() function.
     * @return void
     */
    public function testArrayize(): void
    {
        // check with empty array as input
        $array = [];
        $this->assertEquals($array, Arrays::arrayize($array));

        // check non-empty array as input
        $array = ['key' => 'value'];
        $this->assertEquals($array, Arrays::arrayize($array));

        // check indexes are ignored when input is an array
        $this->assertArrayNotHasKey('invalid', Arrays::arrayize($array, 'invalid'));

        // check default index
        $expected = ['string'];
        $this->assertEquals($expected, Arrays::arrayize($expected[0]));

        // check string index
        $index = 'key';
        $expected = [$index => 'string'];
        $this->assertEquals($expected, Arrays::arrayize($expected[$index], $index));
    }


    /**
     * Test the transpose() function.
     * @return void
     */
    public function testTranspose(): void
    {
        // check not array
        /** @psalm-suppress InvalidArgument   Can be removed as soon as the codebase is fully typehinted */
        $this->assertFalse(Arrays::transpose('string'));

        // check bad arrays
        $this->assertFalse(
            Arrays::transpose(['1', '2', '3']),
            'Invalid two-dimensional array was accepted'
        );
        $this->assertFalse(
            Arrays::transpose(['1' => 0, '2' => '0', '3' => [0]]),
            'Invalid elements on a two-dimensional array were accepted'
        );

        // check array with numerical keys
        $array = [
            'key1' => [
                'value1'
            ],
            'key2' => [
                'value1',
                'value2'
            ]
        ];
        $transposed = [
            [
                'key1' => 'value1',
                'key2' => 'value1'
            ],
            [
                'key2' => 'value2'
            ]
        ];
        $this->assertEquals(
            $transposed,
            Arrays::transpose($array),
            'Unexpected result of transpose()'
        );

        // check array with string keys
        $array = [
            'key1' => [
                'subkey1' => 'value1'
            ],
            'key2' => [
                'subkey1' => 'value1',
                'subkey2' => 'value2'
            ]
        ];
        $transposed = [
            'subkey1' => [
                'key1' => 'value1',
                'key2' => 'value1'
            ],
            'subkey2' => [
                'key2' => 'value2'
            ]
        ];
        $this->assertEquals(
            $transposed,
            Arrays::transpose($array),
            'Unexpected result of transpose()'
        );

        // check array with no keys in common between sub arrays
        $array = [
            'key1' => [
                'subkey1' => 'value1'
            ],
            'key2' => [
                'subkey2' => 'value1',
                'subkey3' => 'value2'
            ]
        ];
        $transposed = [
            'subkey1' => [
                'key1' => 'value1',
            ],
            'subkey2' => [
                'key2' => 'value1'
            ],
            'subkey3' => [
                'key2' => 'value2'
            ]
        ];
        $this->assertEquals(
            $transposed,
            Arrays::transpose($array),
            'Unexpected result of transpose()'
        );
    }
}
