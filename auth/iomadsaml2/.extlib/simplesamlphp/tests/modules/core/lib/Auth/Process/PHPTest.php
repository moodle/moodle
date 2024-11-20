<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Error;
use SimpleSAML\Module\core\Auth\Process\PHP;

/**
 * Test for the core:PHP filter.
 */
class PHPTest extends TestCase
{
    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config The filter configuration.
     * @param array $request The request state.
     *
     * @return array The state array after processing.
     */
    private static function processFilter(array $config, array $request): array
    {
        $filter = new PHP($config, null);
        @$filter->process($request);
        return $request;
    }


    /**
     * Test the configuration of the filter.
     * @return void
     */
    public function testInvalidConfiguration(): void
    {
        $config = [];
        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage(
            "core:PHP: missing mandatory configuration option 'code'."
        );
        new PHP($config, null);
    }


    /**
     * Check that defining the code works as expected.
     * @return void
     */
    public function testCodeDefined(): void
    {
        $config = [
            'code' => '
                $attributes["key"] = array("value");
            ',
        ];
        $request = ['Attributes' => []];
        $expected = [
            'Attributes' => [
                'key' => ['value'],
            ],
        ];

        $this->assertEquals($expected, $this->processFilter($config, $request));
    }


    /**
     * Check that the incoming attributes are also available after processing
     * @return void
     */
    public function testPreserveIncomingAttributes(): void
    {
        $config = [
            'code' => '
                $attributes["orig2"] = array("value0");
            ',
        ];
        $request = [
            'Attributes' => [
                'orig1' => ['value1', 'value2'],
                'orig2' => ['value3'],
                'orig3' => ['value4']
            ]
        ];
        $expected = [
            'Attributes' => [
                'orig1' => ['value1', 'value2'],
                'orig2' => ['value0'],
                'orig3' => ['value4']
            ],
        ];

        $this->assertEquals($expected, $this->processFilter($config, $request));
    }


    /**
     * Check that throwing an Exception inside the PHP code of the
     * filter (a documented use case) works.
     * @return void
     */
    public function testThrowExceptionFromFilter(): void
    {
        $config = [
            'code' => '
                 if (empty($attributes["uid"])) {
                     throw new Exception("Missing uid attribute.");
                 }
                 $attributes["uid"][0] = strtoupper($attributes["uid"][0]);
            ',
        ];
        $request = [
            'Attributes' => [
                'orig1' => ['value1', 'value2'],
            ]
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Missing uid attribute.");
        $this->processFilter($config, $request);
    }


    /**
     * Check that the entire state can be adjusted.
     * @return void
     */
    public function testStateCanBeModified(): void
    {

        $config = [
            'code' => '
                $attributes["orig2"] = array("value0");
                $state["newKey"] = ["newValue"];
                $state["Destination"]["attributes"][] = "givenName";
            ',
        ];
        $request = [
            'Attributes' => [
                'orig1' => ['value1', 'value2'],
                'orig2' => ['value3'],
                'orig3' => ['value4']
            ],
            'Destination' => [
                'attributes' => ['eduPersonPrincipalName']
            ],
        ];
        $expected = [
            'Attributes' => [
                'orig1' => ['value1', 'value2'],
                'orig2' => ['value0'],
                'orig3' => ['value4']
            ],
            'Destination' => [
                'attributes' => ['eduPersonPrincipalName', 'givenName']
            ],
            'newKey' => ['newValue']
        ];

        $this->assertEquals($expected, $this->processFilter($config, $request));
    }
}
