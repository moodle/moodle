<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Metadata;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Metadata\SAMLBuilder;

/**
 * Class SAMLBuilderTest
 */

class SAMLBuilderTest extends TestCase
{
    /**
     * Test the requested attributes are valued correctly.
     * @return void
     */
    public function testAttributes(): void
    {
        $entityId = 'https://entity.example.com/id';

        //  test SP20 array parsing, no friendly name
        $set = 'saml20-sp-remote';
        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                'urn:oid:0.9.2342.19200300.100.1.3',
                'urn:oid:2.5.4.3',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        /** @psalm-var \DOMNodeList $acs */
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");
        $this->assertEquals(1, $acs->length);

        /** @psalm-var \DOMElement $first */
        $first = $acs->item(0);
        $attributes = $first->getElementsByTagName("RequestedAttribute");
        $this->assertEquals(4, $attributes->length);
        for ($c = 0; $c < $attributes->length; $c++) {
            /** @psalm-var \DOMElement $curAttribute */
            $curAttribute = $attributes->item($c);
            $this->assertTrue($curAttribute->hasAttribute("Name"));
            $this->assertFalse($curAttribute->hasAttribute("FriendlyName"));
            $this->assertEquals($metadata['attributes'][$c], $curAttribute->getAttribute("Name"));
        }

        // test SP20 array parsing, no friendly name
        $set = 'saml20-sp-remote';
        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'eduPersonTargetedID'    => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'eduPersonPrincipalName' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                'eduPersonOrgDN'         => 'urn:oid:0.9.2342.19200300.100.1.3',
                'cn'                     => 'urn:oid:2.5.4.3',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        /** @var \DOMNodeList $acs */
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");
        $this->assertEquals(1, $acs->length);

        /** @psalm-var \DOMElement $first */
        $first = $acs->item(0);
        $attributes = $first->getElementsByTagName("RequestedAttribute");
        $this->assertEquals(4, $attributes->length);
        $keys = array_keys($metadata['attributes']);
        for ($c = 0; $c < $attributes->length; $c++) {
            /** @psalm-var \DOMElement $curAttribute */
            $curAttribute = $attributes->item($c);
            $this->assertTrue($curAttribute->hasAttribute("Name"));
            $this->assertTrue($curAttribute->hasAttribute("FriendlyName"));
            $this->assertEquals($metadata['attributes'][$keys[$c]], $curAttribute->getAttribute("Name"));
            $this->assertEquals($keys[$c], $curAttribute->getAttribute("FriendlyName"));
        }

        //  test SP13 array parsing, no friendly name
        $set = 'shib13-sp-remote';
        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                'urn:oid:0.9.2342.19200300.100.1.3',
                'urn:oid:2.5.4.3',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        /** @var \DOMNodeList $acs */
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");
        $this->assertEquals(1, $acs->length);

        /** @psalm-var \DOMElement $first */
        $first = $acs->item(0);
        $attributes = $first->getElementsByTagName("RequestedAttribute");
        $this->assertEquals(4, $attributes->length);
        for ($c = 0; $c < $attributes->length; $c++) {
            /** @psalm-var \DOMElement $curAttribute */
            $curAttribute = $attributes->item($c);
            $this->assertTrue($curAttribute->hasAttribute("Name"));
            $this->assertFalse($curAttribute->hasAttribute("FriendlyName"));
            $this->assertEquals($metadata['attributes'][$c], $curAttribute->getAttribute("Name"));
        }

        // test SP20 array parsing, no friendly name
        $set = 'shib13-sp-remote';
        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'eduPersonTargetedID'    => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'eduPersonPrincipalName' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
                'eduPersonOrgDN'         => 'urn:oid:0.9.2342.19200300.100.1.3',
                'cn'                     => 'urn:oid:2.5.4.3',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        /** @var \DOMNodeList $acs */
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");
        $this->assertEquals(1, $acs->length);

        /** @psalm-var \DOMElement $first */
        $first = $acs->item(0);
        $attributes = $first->getElementsByTagName("RequestedAttribute");
        $this->assertEquals(4, $attributes->length);
        $keys = array_keys($metadata['attributes']);
        for ($c = 0; $c < $attributes->length; $c++) {
            /** @psalm-var \DOMElement $curAttribute */
            $curAttribute = $attributes->item($c);
            $this->assertTrue($curAttribute->hasAttribute("Name"));
            $this->assertTrue($curAttribute->hasAttribute("FriendlyName"));
            $this->assertEquals($metadata['attributes'][$keys[$c]], $curAttribute->getAttribute("Name"));
            $this->assertEquals($keys[$c], $curAttribute->getAttribute("FriendlyName"));
        }
    }


    /**
     * Test the working of the isDefault config option
     * @return void
     */
    public function testAttributeConsumingServiceDefault(): void
    {
        $entityId = 'https://entity.example.com/id';
        $set = 'saml20-sp-remote';

        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'eduPersonTargetedID'    => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'eduPersonPrincipalName' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        /** @var \DOMNodeList $acs */
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");

        /** @psalm-var \DOMElement $acs1 */
        $acs1 = $acs->item(0);
        $this->assertFalse($acs1->hasAttribute("isDefault"));

        $metadata['attributes.isDefault'] = true;

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);
        $spDesc = $samlBuilder->getEntityDescriptor();
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");

        /** @var \DOMElement $acs1 */
        $acs1 = $acs->item(0);
        $this->assertTrue($acs1->hasAttribute("isDefault"));
        $this->assertEquals("true", $acs1->getAttribute("isDefault"));

        $metadata['attributes.isDefault'] = false;

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);
        $spDesc = $samlBuilder->getEntityDescriptor();
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");

        /** @var \DOMElement $acs1 */
        $acs1 = $acs->item(0);
        $this->assertTrue($acs1->hasAttribute("isDefault"));
        $this->assertEquals("false", $acs1->getAttribute("isDefault"));
    }


    /**
     * Test the index option is used correctly.
     * @return void
     */
    public function testAttributeConsumingServiceIndex(): void
    {
        $entityId = 'https://entity.example.com/id';
        $set = 'saml20-sp-remote';

        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test SP'],
            'metadata-set' => $set,
            'attributes'   => [
                'eduPersonTargetedID'    => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
                'eduPersonPrincipalName' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
            ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");

        /** @var \DOMElement $acs1 */
        $acs1 = $acs->item(0);
        $this->assertTrue($acs1->hasAttribute("index"));
        $this->assertEquals("0", $acs1->getAttribute("index"));

        $metadata['attributes.index'] = 15;

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);

        $spDesc = $samlBuilder->getEntityDescriptor();
        $acs = $spDesc->getElementsByTagName("AttributeConsumingService");

        /** @var \DOMElement $acs1 */
        $acs1 = $acs->item(0);
        $this->assertTrue($acs1->hasAttribute("index"));
        $this->assertEquals("15", $acs1->getAttribute("index"));
    }


    /**
     * Test the required protocolSupportEnumeration in AttributeAuthorityDescriptor
     * @return void
     */
    public function testProtocolSupportEnumeration(): void
    {
        $entityId = 'https://entity.example.com/id';
        $set = 'attributeauthority-remote';

        // without protocolSupportEnumeration fallback to default: urn:oasis:names:tc:SAML:2.0:protocol
        $metadata = [
            'entityid'     => $entityId,
            'name'         => ['en' => 'Test AA'],
            'metadata-set' => $set,
            'AttributeService' =>
                [
                    0 =>
                        [
                            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
                            'Location' => 'https://entity.example.com:8443/idp/profile/SAML2/SOAP/AttributeQuery',
                        ],
                ],
        ];

        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);
        $entityDescriptorXml = $samlBuilder->getEntityDescriptorText();

        $this->assertRegExp(
            '/<md:AttributeAuthorityDescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">/',
            $entityDescriptorXml
        );

        // explicit protocols
        $metadata['protocols'] =
            [
                0 => 'urn:oasis:names:tc:SAML:1.1:protocol',
                1 => 'urn:oasis:names:tc:SAML:2.0:protocol',
            ];
        $samlBuilder = new SAMLBuilder($entityId);
        $samlBuilder->addMetadata($set, $metadata);
        $entityDescriptorXml = $samlBuilder->getEntityDescriptorText();

        $protocols = implode(' ', $metadata['protocols']);
        $this->assertRegExp(
            '/<md:AttributeAuthorityDescriptor protocolSupportEnumeration="' . $protocols . '">/',
            $entityDescriptorXml
        );
    }
}
