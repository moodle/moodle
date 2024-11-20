<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\core\Auth\Process\ScopeFromAttribute;

/**
 * Test for the core:ScopeFromAttribute filter.
 */
class ScopeFromAttributeTest extends TestCase
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
        $filter = new ScopeFromAttribute($config, null);
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
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['jdoe@example.com'],
            ]
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('scope', $attributes);
        $this->assertEquals($attributes['scope'], ['example.com']);
    }


    /**
     * If scope already set, module must not overwrite.
     * @return void
     */
    public function testNoOverwrite(): void
    {
        $config = [
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['jdoe@example.com'],
                'scope' => ['example.edu']
            ]
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['scope'], ['example.edu']);
    }


    /**
     * If source attribute not set, nothing happens
     * @return void
     */
    public function testNoSourceAttribute(): void
    {
        $config = [
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ];
        $request = [
            'Attributes' => [
                'mail' => ['j.doe@example.edu', 'john@example.org'],
                'scope' => ['example.edu']
            ]
        ];
        $result = self::processFilter($config, $request);
        $this->assertEquals($request['Attributes'], $result['Attributes']);
    }


    /**
     * When multiple @ signs in attribute, should use last one.
     * @return void
     */
    public function testMultiAt(): void
    {
        $config = [
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['john@doe@example.com'],
            ]
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['scope'], ['example.com']);
    }


    /**
     * When the source attribute doesn't have a scope, a warning is emitted
     * @return void
     */
    public function testNoAt(): void
    {
        $config = [
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['johndoe'],
            ]
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];

        $this->assertArrayNotHasKey('scope', $attributes);
    }
}
