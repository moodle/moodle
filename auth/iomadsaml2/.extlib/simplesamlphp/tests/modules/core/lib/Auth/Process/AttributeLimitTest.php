<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\core\Auth\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Module\core\Auth\Process\AttributeLimit;

/**
 * Test for the core:AttributeLimit filter.
 */
class AttributeLimitTest extends TestCase
{
    /**
     * setUpBeforeClass a request that will be used for the following tests.
     * note the above tests don't use self::$request for processFilter input.
     */
    protected static $request;


    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config  The filter configuration.
     * @param array $request  The request state.
     * @return array  The state array after processing.
     */
    private static function processFilter(array $config, array $request): array
    {
        $filter = new AttributeLimit($config, null);
        $filter->process($request);
        return $request;
    }


    /**
     * Test reading IdP Attributes.
     * @return void
     */
    public function testIdPAttrs(): void
    {
        $config = [
            'cn', 'mail'
        ];

        $request = [
            'Attributes' => [
                 'eduPersonTargetedID' => ['eptid@example.org'],
                 'eduPersonAffiliation' => ['member'],
                 'cn' => ['user name'],
                 'mail' => ['user@example.org'],
            ],
            'Destination' => [
            ],
            'Source' => [
                'attributes' => ['cn', 'mail'],
            ],
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);

        $config = [
            'cn',
            'default' => true,
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);
    }


    /**
     * Tests when no attributes are in metadata.
     * @return void
     */
    public function testNULLMetadataAttrs(): void
    {
        $config = [
            'cn', 'mail'
        ];

        $request = [
            'Attributes' => [
                 'eduPersonTargetedID' => ['eptid@example.org'],
                 'eduPersonAffiliation' => ['member'],
                 'cn' => ['user name'],
                 'mail' => ['user@example.org'],
            ],
            'Destination' => [
            ],
            'Source' => [
            ],
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);

        $config = [
            'cn',
            'default' => true,
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayNotHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(1, $attributes);

        $config = [
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertCount(4, $attributes);
        $this->assertArrayHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
    }


    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::$request = [
            'Attributes' => [
                 'eduPersonTargetedID' => ['eptid@example.org'],
                 'eduPersonAffiliation' => ['member'],
                 'cn' => ['common name'],
                 'mail' => ['user@example.org'],
            ],
            'Destination' => [
                'attributes' => ['cn', 'mail'],
            ],
            'Source' => [
            ],
        ];
    }


    /**
     * Test the most basic functionality.
     * @return void
     */
    public function testBasic(): void
    {
        $config = [
            'cn', 'mail'
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertCount(2, $attributes);
    }


    /**
     * Test defaults with metadata available.
     * @return void
     */
    public function testDefaultWithMetadata(): void
    {
        $config = [
            'default' => true,
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertCount(2, $attributes);
    }


    /**
     * Test defaults with attributes and metadata
     * @return void
     */
    public function testDefaultWithAttrs(): void
    {
        $config = [
            'default' => true,
            'eduPersonTargetedID', 'eduPersonAffiliation',
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(2, $attributes);
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
    }


    /**
     * Test for exception with illegal config.
     * @return void
     */
    public function testInvalidConfig(): void
    {
        $this->expectException(Exception::class);
        $config = [
            'invalidArg' => true,
        ];

        self::processFilter($config, self::$request);
    }


    /**
     * Test for invalid attribute name
     * @return void
     */
    public function testInvalidAttributeName(): void
    {
        $this->expectException(Exception::class);
        $config = [
            null
        ];

        self::processFilter($config, self::$request);
    }


    /**
     * Test for attribute value matching
     * @return void
     */
    public function testMatchAttributeValues(): void
    {
        $config = [
            'eduPersonAffiliation' => ['member']
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], ['member']);

        $config = [
            'eduPersonAffiliation' => ['member', 'staff']
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], ['member']);

        $config = [
            'eduPersonAffiliation' => ['student']
        ];
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);

        $config = [
            'eduPersonAffiliation' => ['student', 'staff']
        ];
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
    }


    /**
     * @return void
     */
    public function testBadOptionsNotTreatedAsValidValues(): void
    {
        // Ensure really misconfigured ignoreCase and regex options are not interpretted as valid valus
        $config = [
            'eduPersonAffiliation' => ['ignoreCase' => 'member', 'nomatch'],
            'mail' => ['regex' => 'user@example.org', 'nomatch']
        ];
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
    }


    /**
     * Verify that the true value for ignoreCase doesn't get converted into a string ('1') by
     * php and matched against an attribute value of '1'
     * @return void
     */
    public function testThatIgnoreCaseOptionNotMatchBooleanAsStringValue(): void
    {
        $config = [
            'someAttribute' => ['ignoreCase' => true, 'someValue']
        ];

        $request = [
            'Attributes' => [
                'someAttribute' => ['1'], //boolean true as a string

            ],
        ];
        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
    }


    /**
     * Test for attribute value matching ignore case
     * @return void
     */
    public function testMatchAttributeValuesIgnoreCase(): void
    {
        $config = [
            'eduPersonAffiliation' => ['ignoreCase' => true, 'meMber']
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], ['member']);

        $config = [
            'eduPersonAffiliation' => ['ignoreCase' => true, 'membeR', 'sTaff']
        ];

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], ['member']);

        $config = [
            'eduPersonAffiliation' => ['ignoreCase' => true, 'Student']
        ];
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);

        $config = [
            'eduPersonAffiliation' => ['ignoreCase' => true, 'studeNt', 'sTaff']
        ];
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
    }


    /**
     * Test for attribute value matching
     * @return void
     */
    public function testMatchAttributeValuesRegex(): void
    {
        // SSP Logger requires a configuration to be set.
        Configuration::loadFromArray([], '[ARRAY]', 'simplesaml');
        $state = self::$request;
        $state['Attributes']['eduPersonEntitlement'] = [
            'urn:mace:example.terena.org:tcs:personal-user',
            'urn:x-surfnet:surfdomeinen.nl:role:dnsadmin',
            'urn:x-surfnet:surf.nl:surfdrive:quota:100',
            '1' //boolean true as a string
        ];

        $config = [
            'eduPersonEntitlement' => [
                'regex' => true,
                '/^urn:x-surfnet:surf/'
            ]
        ];

        $result = self::processFilter($config, $state);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonEntitlement', $attributes);
        $this->assertEquals(
            ['urn:x-surfnet:surfdomeinen.nl:role:dnsadmin', 'urn:x-surfnet:surf.nl:surfdrive:quota:100'],
            $attributes['eduPersonEntitlement']
        );

        // Matching multiple lines shouldn't duplicate the attribute
        $config = [
            'eduPersonEntitlement' => [
                'regex' => true,
                '/urn:x-surfnet:surf/',
                '/urn:x-surfnet/'

            ]
        ];

        $result = self::processFilter($config, $state);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonEntitlement', $attributes);
        $this->assertEquals(
            ['urn:x-surfnet:surfdomeinen.nl:role:dnsadmin', 'urn:x-surfnet:surf.nl:surfdrive:quota:100'],
            $attributes['eduPersonEntitlement']
        );

        // Invalid and no-match regex expressions should not stop a valid regex from matching
        $config = [
            'eduPersonEntitlement' => [
                'regex' => true,
                '/urn:mace:example.terena.org:tcs:no-match/',
                '$invalidRegex[',
                '/^URN:x-surf.*SURF.*n$/i'
            ]
        ];

        $result = self::processFilter($config, $state);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonEntitlement', $attributes);
        $this->assertEquals(
            ['urn:x-surfnet:surfdomeinen.nl:role:dnsadmin'],
            $attributes['eduPersonEntitlement']
        );

        // No matches should remove attribute
        $config = [
            'eduPersonEntitlement' => [
                'regex' => true,
                '/urn:x-no-match/'
            ]
        ];
        $result = self::processFilter($config, $state);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);

        // A regex that matches an input value multiple times should work.
        $config = [
            'eduPersonEntitlement' => [
                'regex' => true,
                '/surf/'
            ]
        ];
        $result = self::processFilter($config, $state);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonEntitlement', $attributes);
        $this->assertEquals(
            ['urn:x-surfnet:surfdomeinen.nl:role:dnsadmin', 'urn:x-surfnet:surf.nl:surfdrive:quota:100'],
            $attributes['eduPersonEntitlement']
        );
    }


    /**
     * Test for allowed attributes not an array.
     *
     * This test is very unlikely and would require malformed metadata processing.
     * Cannot be generated via config options.
     * @return void
     */
    public function testMatchAttributeValuesNotArray(): void
    {
        $this->expectException(Exception::class);
        $config = [
        ];

        $request = [
            'Attributes' => [
                 'eduPersonTargetedID' => ['eptid@example.org'],
                 'eduPersonAffiliation' => ['member'],
                 'cn' => ['user name'],
                 'mail' => ['user@example.org'],
                 'discardme' => ['somethingiswrong'],
            ],
            'Destination' => [
                'attributes' => ['eduPersonAffiliation' => 'student'],
            ],
            'Source' => [
            ],
        ];


        self::processFilter($config, $request);
    }


    /**
     * Test attributes not intersecting
     * @return void
     */
    public function testNoIntersection(): void
    {
        $config = [
            'default' => true,
        ];

        $request = [
            'Attributes' => [
                 'eduPersonTargetedID' => ['eptid@example.org'],
                 'eduPersonAffiliation' => ['member'],
                 'cn' => ['user name'],
                 'mail' => ['user@example.org'],
                 'discardme' => ['somethingiswrong'],
            ],
            'Destination' => [
                'attributes' => ['urn:oid:1.2.840.113549.1.9.1'],
            ],
            'Source' => [
            ],
        ];

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
        $this->assertEmpty($attributes);
    }
}
