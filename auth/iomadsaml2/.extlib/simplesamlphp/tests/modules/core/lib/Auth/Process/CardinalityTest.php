<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Error\Exception as SspException;
use SimpleSAML\Module\core\Auth\Process\Cardinality;
use SimpleSAML\Utils\HttpAdapter;

/**
 * Test for the core:Cardinality filter.
 */
class CardinalityTest extends TestCase
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

        $filter = new Cardinality($config, null, $http);
        $filter->process($request);
        return $request;
    }


    /**
     * @return void
     */
    protected function setUp()
    {
        Configuration::loadFromArray([], '[ARRAY]', 'simplesaml');
        $this->http = $this->getMockBuilder(HttpAdapter::class)
                           ->setMethods(['redirectTrustedURL'])
                           ->getMock();
    }


    /**
     * Test where a minimum is set but no maximum
     * @return void
     */
    public function testMinNoMax(): void
    {
        $config = [
            'mail' => ['min' => 1],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['mail' => ['joe@example.com', 'bob@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test where a maximum is set but no minimum
     * @return void
     */
    public function testMaxNoMin(): void
    {
        $config = [
            'mail' => ['max' => 2],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['mail' => ['joe@example.com', 'bob@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test in bounds within a maximum an minimum
     * @return void
     */
    public function testMaxMin(): void
    {
        $config = [
            'mail' => ['min' => 1, 'max' => 2],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        $attributes = $result['Attributes'];
        $expectedData = ['mail' => ['joe@example.com', 'bob@example.com']];
        $this->assertEquals($expectedData, $attributes, "Assertion values should not have changed");
    }


    /**
     * Test maximum is out of bounds results in redirect
     * @return void
     */
    public function testMaxOutOfBounds(): void
    {
        $config = [
            'mail' => ['max' => 2],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com', 'fred@example.com'],
            ],
        ];

        /** @psalm-suppress UndefinedMethod   It's a mock-object */
        $this->http->expects($this->once())
                   ->method('redirectTrustedURL');

        $this->processFilter($config, $request);
    }


    /**
     * Test minimum is out of bounds results in redirect
     * @return void
     */
    public function testMinOutOfBounds(): void
    {
        $config = [
            'mail' => ['min' => 3],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];

        /** @psalm-suppress UndefinedMethod   It's a mock-object */
        $this->http->expects($this->once())
                   ->method('redirectTrustedURL');

        $this->processFilter($config, $request);
    }


    /**
     * Test missing attribute results in redirect
     * @return void
     */
    public function testMissingAttribute(): void
    {
        $config = [
            'mail' => ['min' => 1],
        ];
        $request = [
            'Attributes' => [],
        ];

        /** @psalm-suppress UndefinedMethod   It's a mock-object */
        $this->http->expects($this->once())
                   ->method('redirectTrustedURL');

        $this->processFilter($config, $request);
    }


    /*
     * Configuration errors
     */


    /**
     * Test invalid minimum values
     * @return void
     */
    public function testMinInvalid(): void
    {
        $this->expectException(SspException::class);
        $this->expectExceptionMessageRegExp('/Minimum/');
        $config = [
            'mail' => ['min' => false],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $this->processFilter($config, $request);
    }


    /**
     * Test invalid minimum values
     * @return void
     */
    public function testMinNegative(): void
    {
        $this->expectException(SspException::class);
        $this->expectExceptionMessageRegExp('/Minimum/');
        $config = [
            'mail' => ['min' => -1],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $this->processFilter($config, $request);
    }


    /**
     * Test invalid maximum values
     * @return void
     */
    public function testMaxInvalid(): void
    {
        $this->expectException(SspException::class);
        $this->expectExceptionMessageRegExp('/Maximum/');
        $config = [
            'mail' => ['max' => false],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $this->processFilter($config, $request);
    }


    /**
     * Test maximum < minimum
     * @return void
     */
    public function testMinGreaterThanMax(): void
    {
        $this->expectException(SspException::class);
        $this->expectExceptionMessageRegExp('/less than/');
        $config = [
            'mail' => ['min' => 2, 'max' => 1],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $this->processFilter($config, $request);
    }


    /**
     * Test invalid attribute name
     * @return void
     */
    public function testInvalidAttributeName(): void
    {
        $this->expectException(SspException::class);
        $this->expectExceptionMessageRegExp('/Invalid attribute/');
        $config = [
            ['min' => 2, 'max' => 1],
        ];
        $request = [
            'Attributes' => [
                'mail' => ['joe@example.com', 'bob@example.com'],
            ],
        ];
        $this->processFilter($config, $request);
    }
}
