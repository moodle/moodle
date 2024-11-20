<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils\Config;

use DOMDocument;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SAML2\Constants;
use SimpleSAML\Utils\Config\Metadata;
use TypeError;

/**
 * Tests related to SAML metadata.
 */
class MetadataTest extends TestCase
{
    /**
     * Test contact configuration parsing and sanitizing.
     * @return void
     */
    public function testGetContact(): void
    {
        // test invalid argument
        try {
            /** @psalm-suppress InvalidArgument   May be removed in 2.0 when codebase is fully typehinted */
            Metadata::getContact('string');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('Invalid input parameters', $e->getMessage());
        }

        // test missing type
        $contact = [
            'name' => 'John Doe'
        ];
        try {
            Metadata::getContact($contact);
        } catch (InvalidArgumentException $e) {
            $this->assertStringStartsWith('"contactType" is mandatory and must be one of ', $e->getMessage());
        }

        // test invalid type
        $contact = [
            'contactType' => 'invalid'
        ];
        try {
            Metadata::getContact($contact);
        } catch (InvalidArgumentException $e) {
            $this->assertStringStartsWith('"contactType" is mandatory and must be one of ', $e->getMessage());
        }

        // test all valid contact types
        foreach (Metadata::$VALID_CONTACT_TYPES as $type) {
            $contact = [
                'contactType' => $type
            ];
            $parsed = Metadata::getContact($contact);
            $this->assertArrayHasKey('contactType', $parsed);
            $this->assertArrayNotHasKey('givenName', $parsed);
            $this->assertArrayNotHasKey('surName', $parsed);
        }

        // test basic name parsing
        $contact = [
            'contactType' => 'technical',
            'name'        => 'John Doe'
        ];
        $parsed = Metadata::getContact($contact);
        $this->assertArrayNotHasKey('name', $parsed);
        $this->assertArrayHasKey('givenName', $parsed);
        $this->assertArrayHasKey('surName', $parsed);
        $this->assertEquals('John', $parsed['givenName']);
        $this->assertEquals('Doe', $parsed['surName']);

        // test comma-separated names
        $contact = [
            'contactType' => 'technical',
            'name'        => 'Doe, John'
        ];
        $parsed = Metadata::getContact($contact);
        $this->assertArrayHasKey('givenName', $parsed);
        $this->assertArrayHasKey('surName', $parsed);
        $this->assertEquals('John', $parsed['givenName']);
        $this->assertEquals('Doe', $parsed['surName']);

        // test long names
        $contact = [
            'contactType' => 'technical',
            'name'        => 'John Fitzgerald Doe Smith'
        ];
        $parsed = Metadata::getContact($contact);
        $this->assertArrayNotHasKey('name', $parsed);
        $this->assertArrayHasKey('givenName', $parsed);
        $this->assertArrayNotHasKey('surName', $parsed);
        $this->assertEquals('John Fitzgerald Doe Smith', $parsed['givenName']);

        // test comma-separated long names
        $contact = [
            'contactType' => 'technical',
            'name'        => 'Doe Smith, John Fitzgerald'
        ];
        $parsed = Metadata::getContact($contact);
        $this->assertArrayNotHasKey('name', $parsed);
        $this->assertArrayHasKey('givenName', $parsed);
        $this->assertArrayHasKey('surName', $parsed);
        $this->assertEquals('John Fitzgerald', $parsed['givenName']);
        $this->assertEquals('Doe Smith', $parsed['surName']);

        // test givenName
        $contact = [
            'contactType' => 'technical',
        ];
        $invalid_types = [0, [0], 0.1, true, false];
        foreach ($invalid_types as $type) {
            $contact['givenName'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('"givenName" must be a string and cannot be empty.', $e->getMessage());
            }
        }

        // test surName
        $contact = [
            'contactType' => 'technical',
        ];
        $invalid_types = [0, [0], 0.1, true, false];
        foreach ($invalid_types as $type) {
            $contact['surName'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('"surName" must be a string and cannot be empty.', $e->getMessage());
            }
        }

        // test company
        $contact = [
            'contactType' => 'technical',
        ];
        $invalid_types = [0, [0], 0.1, true, false];
        foreach ($invalid_types as $type) {
            $contact['company'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('"company" must be a string and cannot be empty.', $e->getMessage());
            }
        }

        // test emailAddress
        $contact = [
            'contactType' => 'technical',
        ];
        $invalid_types = [0, 0.1, true, false, []];
        foreach ($invalid_types as $type) {
            $contact['emailAddress'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals(
                    '"emailAddress" must be a string or an array and cannot be empty.',
                    $e->getMessage()
                );
            }
        }
        $invalid_types = [["string", true], ["string", 0]];
        foreach ($invalid_types as $type) {
            $contact['emailAddress'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals(
                    'Email addresses must be a string and cannot be empty.',
                    $e->getMessage()
                );
            }
        }
        $valid_types = ['email@example.com', ['email1@example.com', 'email2@example.com']];
        foreach ($valid_types as $type) {
            $contact['emailAddress'] = $type;
            $parsed = Metadata::getContact($contact);
            $this->assertEquals($type, $parsed['emailAddress']);
        }

        // test telephoneNumber
        $contact = [
            'contactType' => 'technical',
        ];
        $invalid_types = [0, 0.1, true, false, []];
        foreach ($invalid_types as $type) {
            $contact['telephoneNumber'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals(
                    '"telephoneNumber" must be a string or an array and cannot be empty.',
                    $e->getMessage()
                );
            }
        }
        $invalid_types = [["string", true], ["string", 0]];
        foreach ($invalid_types as $type) {
            $contact['telephoneNumber'] = $type;
            try {
                Metadata::getContact($contact);
            } catch (InvalidArgumentException $e) {
                $this->assertEquals('Telephone numbers must be a string and cannot be empty.', $e->getMessage());
            }
        }
        $valid_types = ['1234', ['1234', '5678']];
        foreach ($valid_types as $type) {
            $contact['telephoneNumber'] = $type;
            $parsed = Metadata::getContact($contact);
            $this->assertEquals($type, $parsed['telephoneNumber']);
        }

        // test completeness
        $contact = [];
        foreach (Metadata::$VALID_CONTACT_OPTIONS as $option) {
            $contact[$option] = 'string';
        }
        $contact['contactType'] = 'technical';
        $contact['name'] = 'to_be_removed';
        $contact['attributes'] = ['test' => 'testval'];
        $parsed = Metadata::getContact($contact);
        foreach (array_keys($parsed) as $key) {
            $this->assertEquals($parsed[$key], $contact[$key]);
        }
        $this->assertArrayNotHasKey('name', $parsed);
    }


    /**
     * Test \SimpleSAML\Utils\Config\Metadata::isHiddenFromDiscovery().
     * @return void
     */
    public function testIsHiddenFromDiscovery(): void
    {
        // test for success
        $metadata = [
            'EntityAttributes' => [
                Metadata::$ENTITY_CATEGORY => [
                    Metadata::$HIDE_FROM_DISCOVERY,
                ],
            ],
        ];
        $this->assertTrue(Metadata::isHiddenFromDiscovery($metadata));

        // test for failure
        $this->assertFalse(Metadata::isHiddenFromDiscovery([
            'EntityAttributes' => [
                Metadata::$ENTITY_CATEGORY => [],
            ],
        ]));

        // test for failures
        $this->expectException(TypeError::class);
        Metadata::isHiddenFromDiscovery(['foo']);

        $this->assertFalse(Metadata::isHiddenFromDiscovery([
            'EntityAttributes' => 'bar',
        ]));
        $this->assertFalse(Metadata::isHiddenFromDiscovery([
            'EntityAttributes' => [],
        ]));
        $this->assertFalse(Metadata::isHiddenFromDiscovery([
            'EntityAttributes' => [
                Metadata::$ENTITY_CATEGORY => '',
            ],
        ]));
    }


    /**
     * Test \SimpleSAML\Utils\Config\Metadata::parseNameIdPolicy().
     * @return void
     */
    public function testParseNameIdPolicy(): void
    {
        // Test null or unset
        $nameIdPolicy = null;
        $this->assertEquals(
            ['Format' => Constants::NAMEID_TRANSIENT, 'AllowCreate' => true],
            Metadata::parseNameIdPolicy($nameIdPolicy)
        );

        // Test false
        $nameIdPolicy = false;
        $this->assertEquals(null, Metadata::parseNameIdPolicy($nameIdPolicy));

        // Test string
        $nameIdPolicy = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
        $this->assertEquals(
            ['Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress', 'AllowCreate' => true],
            Metadata::parseNameIdPolicy($nameIdPolicy)
        );

        // Test array
        $nameIdPolicy = [
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:persistent',
            'AllowCreate' => false
        ];
        $this->assertEquals([
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:persistent',
            'AllowCreate' => false
        ], Metadata::parseNameIdPolicy($nameIdPolicy));

        $nameIdPolicy = [
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:persistent',
            'AllowCreate' => false,
            'SPNameQualifier' => 'TEST'
        ];
        $this->assertEquals([
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:persistent',
            'AllowCreate' => false,
            'SPNameQualifier' => 'TEST'
        ], Metadata::parseNameIdPolicy($nameIdPolicy));
    }
}
