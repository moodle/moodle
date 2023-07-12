<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\core\Auth\Process\AttributeMap;

/**
 * Test for the core:AttributeMap filter.
 */
class AttributeMapTest extends TestCase
{
    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config  The filter configuration.
     * @param array $request  The request state.
     * @return array  The state array after processing.
     */
    private static function processFilter(array $config, array $request): array
    {
        $filter = new AttributeMap($config, null);
        $filter->process($request);
        return $request;
    }


    /**
     * @return void
     */
    public function testBasic(): void
    {
        $config = [
            'attribute1' => 'attribute2',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute2' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testDuplicate(): void
    {
        $config = [
            'attribute1' => 'attribute2',
            '%duplicate',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute1' => ['value'],
            'attribute2' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testMultiple(): void
    {
        $config = [
            'attribute1' => ['attribute2', 'attribute3'],
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute2' => ['value'],
            'attribute3' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testMultipleDuplicate(): void
    {
        $config = [
            'attribute1' => ['attribute2', 'attribute3'],
            '%duplicate',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute1' => ['value'],
            'attribute2' => ['value'],
            'attribute3' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testCircular(): void
    {
        $config = [
            'attribute1' => 'attribute1',
            'attribute2' => 'attribute2',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
                'attribute2' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute1' => ['value'],
            'attribute2' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testMissingMap(): void
    {
        $config = [
            'attribute1' => 'attribute3',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
                'attribute2' => ['value'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute2' => ['value'],
            'attribute3' => ['value'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testInvalidOriginalAttributeType(): void
    {
        $config = [
            10 => 'attribute2',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $this->expectException(\Exception::class);
        self::processFilter($config, $request);
    }


    /**
     * @return void
     */
    public function testInvalidMappedAttributeType(): void
    {
        $config = [
            'attribute1' => 10,
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $this->expectException(\Exception::class);
        self::processFilter($config, $request);
    }


    /**
     * @return void
     */
    public function testMissingMapFile(): void
    {
        $config = [
            'non_existant_mapfile',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value'],
            ],
        ];

        $this->expectException(\Exception::class);
        self::processFilter($config, $request);
    }


    /**
     * @return void
     */
    public function testOverwrite(): void
    {
        $config = [
            'attribute1' => 'attribute2',
        ];
        $request = [
            'Attributes' => [
                'attribute1' => ['value1'],
                'attribute2' => ['value2'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute2' => ['value1'],
        ];

        $this->assertEquals($expected, $result);
    }


    /**
     * @return void
     */
    public function testOverwriteReversed(): void
    {
        $config = [
            'attribute1' => 'attribute2',
        ];
        $request = [
            'Attributes' => [
                'attribute2' => ['value2'],
                'attribute1' => ['value1'],
            ],
        ];

        $processed = self::processFilter($config, $request);
        $result = $processed['Attributes'];
        $expected = [
            'attribute2' => ['value1'],
        ];

        $this->assertEquals($expected, $result);
    }
}
