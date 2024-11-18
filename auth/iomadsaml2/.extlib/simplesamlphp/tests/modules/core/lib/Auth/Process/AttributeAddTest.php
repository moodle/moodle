<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\core\Auth\Process\AttributeAdd;

/**
 * Test for the core:AttributeAdd filter.
 */
class AttributeAddTest extends TestCase
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
        $filter = new AttributeAdd($config, null);
        $filter->process($request);
        return $request;
    }


    /**
     * Test the most basic functionality.
     * @return void
     */
    public function testBasic(): void
    {
        $config = [
            'test' => ['value1', 'value2'],
        ];
        $request = [
            'Attributes' => [],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test', $attributes);
        $this->assertEquals($attributes['test'], ['value1', 'value2']);
    }


    /**
     * Test that existing attributes are left unmodified.
     * @return void
     */
    public function testExistingNotModified(): void
    {
        $config = [
            'test' => ['value1', 'value2'],
        ];
        $request = [
            'Attributes' => [
                'original1' => ['original_value1'],
                'original2' => ['original_value2'],
            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test', $attributes);
        $this->assertEquals($attributes['test'], ['value1', 'value2']);
        $this->assertArrayHasKey('original1', $attributes);
        $this->assertEquals($attributes['original1'], ['original_value1']);
        $this->assertArrayHasKey('original2', $attributes);
        $this->assertEquals($attributes['original2'], ['original_value2']);
    }


    /**
     * Test single string as attribute value.
     * @return void
     */
    public function testStringValue(): void
    {
        $config = [
            'test' => 'value',
        ];
        $request = [
            'Attributes' => [],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test', $attributes);
        $this->assertEquals($attributes['test'], ['value']);
    }


    /**
     * Test adding multiple attributes in one config.
     * @return void
     */
    public function testAddMultiple(): void
    {
        $config = [
            'test1' => ['value1'],
            'test2' => ['value2'],
        ];
        $request = [
            'Attributes' => [],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test1', $attributes);
        $this->assertEquals($attributes['test1'], ['value1']);
        $this->assertArrayHasKey('test2', $attributes);
        $this->assertEquals($attributes['test2'], ['value2']);
    }


    /**
     * Test behavior when appending attribute values.
     * @return void
     */
    public function testAppend(): void
    {
        $config = [
            'test' => ['value2'],
        ];
        $request = [
            'Attributes' => [
                'test' => ['value1'],
            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['test'], ['value1', 'value2']);
    }


    /**
     * Test replacing attribute values.
     * @return void
     */
    public function testReplace(): void
    {
        $config = [
            '%replace',
            'test' => ['value2'],
        ];
        $request = [
            'Attributes' => [
                'test' => ['value1'],
            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['test'], ['value2']);
    }


    /**
     * Test wrong usage generates exceptions
     * @return void
     */
    public function testWrongFlag(): void
    {
        $this->expectException(Exception::class);
        $config = [
            '%nonsense',
            'test' => ['value2'],
        ];
        $request = [
            'Attributes' => [
                'test' => ['value1'],
            ],
        ];
        self::processFilter($config, $request);
    }


    /**
     * Test wrong attribute value
     * @return void
     */
    public function testWrongAttributeValue(): void
    {
        $this->expectException(Exception::class);
        $config = [
            '%replace',
            'test' => [true],
        ];
        $request = [
            'Attributes' => [
                'test' => ['value1'],
            ],
        ];
        self::processFilter($config, $request);
    }
}
