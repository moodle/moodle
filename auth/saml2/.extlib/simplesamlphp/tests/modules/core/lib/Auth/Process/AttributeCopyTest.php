<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\core\Auth\Process\AttributeCopy;

/**
 * Test for the core:AttributeCopy filter.
 */
class AttributeCopyTest extends TestCase
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
        $filter = new AttributeCopy($config, null);
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
            'test' => 'testnew',
        ];
        $request = [
            'Attributes' => ['test' => ['AAP']],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test', $attributes);
        $this->assertArrayHasKey('testnew', $attributes);
        $this->assertEquals($attributes['testnew'], ['AAP']);
    }


    /**
     * Test the most basic functionality.
     * @return void
     */
    public function testArray(): void
    {
        $config = [
            'test' => ['new1', 'new2'],
        ];
        $request = [
            'Attributes' => ['test' => ['AAP']],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('test', $attributes);
        $this->assertArrayHasKey('new1', $attributes);
        $this->assertArrayHasKey('new2', $attributes);
        $this->assertEquals($attributes['new1'], ['AAP']);
        $this->assertEquals($attributes['new2'], ['AAP']);
    }


    /**
     * Test that existing attributes are left unmodified.
     * @return void
     */
    public function testExistingNotModified(): void
    {
        $config = [
            'test' => 'testnew',
        ];
        $request = [
            'Attributes' => [
                'test' => ['AAP'],
                'original1' => ['original_value1'],
                'original2' => ['original_value2'],
            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('testnew', $attributes);
        $this->assertEquals($attributes['test'], ['AAP']);
        $this->assertArrayHasKey('original1', $attributes);
        $this->assertEquals($attributes['original1'], ['original_value1']);
        $this->assertArrayHasKey('original2', $attributes);
        $this->assertEquals($attributes['original2'], ['original_value2']);
    }


    /**
     * Test copying multiple attributes
     * @return void
     */
    public function testCopyMultiple(): void
    {
        $config = [
            'test1' => 'new1',
            'test2' => 'new2',
        ];
        $request = [
            'Attributes' => ['test1' => ['val1'], 'test2' => ['val2.1', 'val2.2']],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('new1', $attributes);
        $this->assertEquals($attributes['new1'], ['val1']);
        $this->assertArrayHasKey('new2', $attributes);
        $this->assertEquals($attributes['new2'], ['val2.1', 'val2.2']);
    }


    /**
     * Test behaviour when target attribute exists (should be replaced).
     * @return void
     */
    public function testCopyClash(): void
    {
        $config = [
            'test' => 'new1',
        ];
        $request = [
            'Attributes' => [
                'test' => ['testvalue1'],
                'new1' => ['newvalue1'],
            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['new1'], ['testvalue1']);
    }


    /**
     * Test wrong attribute name
     * @return void
     */
    public function testWrongAttributeName(): void
    {
        $this->expectException(Exception::class);
        $config = [
            ['value2'],
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
            'test' => 100,
        ];
        $request = [
            'Attributes' => [
                'test' => ['value1'],
            ],
        ];
        self::processFilter($config, $request);
    }
}
