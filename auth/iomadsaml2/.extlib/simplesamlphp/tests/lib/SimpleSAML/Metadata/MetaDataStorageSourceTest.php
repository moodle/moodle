<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Metadata;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageSource;

/**
 * Class MetaDataStorageSourceTest
 */

class MetaDataStorageSourceTest extends TestCase
{
    /**
     * Test \SimpleSAML\Metadata\MetaDataStorageSourceTest::getConfig XML bad source
     * @return void
     */
    public function testBadXMLSource(): void
    {
        $this->expectException(Exception::class);
        MetaDataStorageSource::getSource(["type" => "xml", "foo" => "baa"]);
    }


    /**
     * Test \SimpleSAML\Metadata\MetaDataStorageSourceTest::getConfig invalid static XML source
     * @return void
     */
    public function testInvalidStaticXMLSource(): void
    {
        $this->expectException(Exception::class);
        $strTestXML = "
<EntityDescriptor ID=\"_12345678-90ab-cdef-1234-567890abcdef\" entityID=\"https://saml.idp/entityid\" xmlns=\"urn:oasis:names:tc:SAML:2.0:metadata\">
</EntityDescriptor>
";
        MetaDataStorageSource::getSource(["type" => "xml", "xml" => $strTestXML]);
    }


    /**
     * Test \SimpleSAML\Metadata\MetaDataStorageSourceTest::getConfig XML static XML source
     * @return void
     */
    public function testStaticXMLSource(): void
    {
        $testEntityId = "https://saml.idp/entityid";
        $strTestXML = self::generateIdpMetadataXml($testEntityId);

        // The primary test here is that - in contrast to the others above - this loads without error
        // As a secondary thing, check that the entity ID from the static source provided can be extracted
        $source = MetaDataStorageSource::getSource(["type" => "xml", "xml" => $strTestXML]);
        $idpSet = $source->getMetadataSet("saml20-idp-remote");
        $this->assertArrayHasKey(
            $testEntityId,
            $idpSet,
            "Did not extract expected IdP entity ID from static XML source"
        );

        // Finally verify that a different entity ID does not get loaded
        $this->assertCount(1, $idpSet, "Unexpectedly got metadata for an alternate entity than that defined");
    }


    /**
     * Test loading multiple entities
     * @return void
     */
    public function testLoadEntitiesStaticXMLSource(): void
    {
        $c = [
            'key' => 'value'
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $entityId1 = "https://example.com";
        $xml1 = self::generateIdpMetadataXml($entityId1);
        $entityId2 = "https://saml.idp/entity";
        $xml2 = self::generateIdpMetadataXml($entityId2);
        $strTestXML = "
        <EntitiesDescriptor xmlns=\"urn:oasis:names:tc:SAML:2.0:metadata\">
        $xml1
        $xml2
        </EntitiesDescriptor>
        ";
        $source = MetaDataStorageSource::getSource(["type" => "xml", "xml" => $strTestXML]);
        // search that is a single entity
        $entities = $source->getMetaDataForEntities([$entityId2], "saml20-idp-remote");
        $this->assertCount(1, $entities, 'Only 1 entity loaded');
        $this->assertArrayHasKey($entityId2, $entities);
        // search for multiple entities
        $entities = $source->getMetaDataForEntities([$entityId1, 'no-such-entity', $entityId2], "saml20-idp-remote");
        $this->assertCount(2, $entities, 'Only 2 of the entities are found');
        $this->assertArrayHasKey($entityId1, $entities);
        $this->assertArrayHasKey($entityId2, $entities);
        // search for non-existant entities
        $entities = $source->getMetaDataForEntities(['no-such-entity'], "saml20-idp-remote");
        $this->assertCount(0, $entities, 'no matches expected');
    }


    /**
     * @param string $entityId
     * @return string
     */
    public static function generateIdpMetadataXml(string $entityId): string
    {
        return "
<EntityDescriptor ID=\"_12345678-90ab-cdef-1234-567890abcdef\" entityID=\"$entityId\" xmlns=\"urn:oasis:names:tc:SAML:2.0:metadata\">
<RoleDescriptor xsi:type=\"fed:ApplicationServiceType\"
protocolSupportEnumeration=\"http://docs.oasis-open.org/ws-sx/ws-trust/200512 http://schemas.xmlsoap.org/ws/2005/02/trust http://docs.oasis-open.org/wsfed/federation/200706\"
ServiceDisplayName=\"SimpleSAMLphp Test\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
xmlns:fed=\"http://docs.oasis-open.org/wsfed/federation/200706\">
<NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</NameIDFormat>
<SingleSignOnService Binding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect\" Location=\"https://saml.idp/sso/\"/>
<SingleLogoutService Binding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect\" Location=\"https://saml.idp/logout/\"/>
</RoleDescriptor>
<IDPSSODescriptor protocolSupportEnumeration=\"urn:oasis:names:tc:SAML:2.0:protocol\">
<SingleSignOnService Binding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect\" Location=\"https://saml.idp/sso/\"/>
</IDPSSODescriptor>
</EntityDescriptor>
";
    }
}
