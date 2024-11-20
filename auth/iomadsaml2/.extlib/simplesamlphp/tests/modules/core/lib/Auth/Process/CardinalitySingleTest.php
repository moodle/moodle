<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Utils\HttpAdapter;
use SimpleSAML\Module\core\Auth\Process\CardinalitySingle;

/**
 * Test for the core:CardinalitySingle filter.
 */
class CardinalitySingleTest extends TestCase
{
    /** @var \SimpleSAML\Utils\HttpAdapter|\PHPUnit_Framework_MockObject_MockObject */
    private $http;


    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param  array $config The filter configuration.
     * @param  array $request The request state.
     * @return array  The state array after processing.
     */
    private function processFilter(array $config, array $request): array
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        /** @var \SimpleSAML\Utils\HttpAdapter $http */
        $http = $this->http;

        $filter = new CardinalitySingle($config, null, $http);
        $filter->process($request);
        return $request;
    }


    /**
     * @return void
     */
    protected function setUp()
    {
        \SimpleSAML\Configuration::loadFromArray([], '[ARRAY]', 'simplesaml');
        $this->http = $this->getMockBuilder(HttpAdapter::class)
                           ->setMethods(['redirectTrustedURL'])
                           ->getMock();
    }


    /**
     * Test singleValued
     * @return void
     */
    public function testSingleValuedUnchanged(): void
    {
        $config = [
            'singleValued' => ['eduPersonPrincipalName']
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['eduPersonPrincipalName' => ['joe@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test first value extraction
     * @return void
     */
    public function testFirstValue(): void
    {
        $config = [
            'firstValue' => ['eduPersonPrincipalName']
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com', 'bob@example.net'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['eduPersonPrincipalName' => ['joe@example.com']];
        $this->assertEquals($expectedData, $attributes, "Only first value should be returned");
    }


    /**
     * @return void
     */
    public function testFirstValueUnchanged(): void
    {
        $config = [
            'firstValue' => ['eduPersonPrincipalName']
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['eduPersonPrincipalName' => ['joe@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test flattening
     * @return void
     */
    public function testFlatten(): void
    {
        $config = [
            'flatten' => ['eduPersonPrincipalName'],
            'flattenWith' => '|',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com', 'bob@example.net'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['eduPersonPrincipalName' => ['joe@example.com|bob@example.net']];
        $this->assertEquals($expectedData, $attributes, "Flattened string should be returned");
    }


    /**
     * @return void
     */
    public function testFlattenUnchanged(): void
    {
        $config = [
            'flatten' => ['eduPersonPrincipalName'],
            'flattenWith' => '|',
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['eduPersonPrincipalName' => ['joe@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test abort
     * @return void
     */
    public function testAbort(): void
    {
        $config = [
            'singleValued' => ['eduPersonPrincipalName'],
        ];
        $request = [
            'Attributes' => [
                'eduPersonPrincipalName' => ['joe@example.com', 'bob@example.net'],
            ],
        ];

        /** @psalm-suppress UndefinedMethod */
        $this->http->expects($this->once())
                   ->method('redirectTrustedURL');

        $this->processFilter($config, $request);
    }
}
