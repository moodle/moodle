<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Metadata;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use SAML2\DOMDocumentFactory;
use SimpleSAML\XML\Signer;
use SimpleSAML\Metadata\SAMLParser;

/**
 * Test SAML parsing
 */
class SAMLParserTest extends \SimpleSAML\Test\SigningTestCase
{
    /**
     * Test Registration Info is parsed
     * @return void
     */
    public function testRegistrationInfo(): void
    {
        $expected = [
            'registrationAuthority' => 'https://incommon.org',
        ];

        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi">
  <EntityDescriptor entityID="theEntityID">
    <Extensions>
      <mdrpi:RegistrationInfo registrationAuthority="https://incommon.org"/>
    </Extensions>
    <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );


        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        // RegistrationInfo is accessible in the SP or IDP metadata accessors
        /** @var array $metadata */
        $metadata = $entities['theEntityID']->getMetadata20SP();
        $this->assertEquals($expected, $metadata['RegistrationInfo']);
    }


    /**
     * Test RegistrationInfo is inherited correctly from parent EntitiesDescriptor.
     * According to the spec overriding RegistrationInfo is not valid. We ignore attempts to override
     * @return void
     */
    public function testRegistrationInfoInheritance(): void
    {
        $expected = [
            'registrationAuthority' => 'https://incommon.org',
        ];

        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi">
  <Extensions>
    <mdrpi:RegistrationInfo registrationAuthority="https://incommon.org"/>
  </Extensions>
  <EntityDescriptor entityID="theEntityID">
    <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
  <EntitiesDescriptor>
    <EntityDescriptor entityID="subEntityId">
      <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
    </EntityDescriptor>
    <EntityDescriptor entityID="subEntityIdOverride">
      <Extensions>
        <mdrpi:RegistrationInfo registrationAuthority="overrides-are-ignored"/>
      </Extensions>
      <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
    </EntityDescriptor>
  </EntitiesDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        $this->assertArrayHasKey('subEntityId', $entities);
        // RegistrationInfo is accessible in the SP or IDP metadata accessors
        /** @var array $metadata */
        $metadata = $entities['theEntityID']->getMetadata20SP();
        $this->assertEquals($expected, $metadata['RegistrationInfo']);

        /** @var array $metadata */
        $metadata = $entities['subEntityId']->getMetadata20SP();
        $this->assertEquals($expected, $metadata['RegistrationInfo']);

        /** @var array $metadata */
        $metadata = $entities['subEntityIdOverride']->getMetadata20SP();
        $this->assertEquals($expected, $metadata['RegistrationInfo']);
    }


    /**
     * Test AttributeConsumingService is parsed
     * @return void
     */
    public function testAttributeConsumingServiceParsing(): void
    {
        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi">
  <EntityDescriptor entityID="theEntityID">
    <Extensions>
      <mdrpi:RegistrationInfo registrationAuthority="https://incommon.org"/>
    </Extensions>
    <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
      <AttributeConsumingService index="0">
        <ServiceName xml:lang="en">Example service</ServiceName>
        <ServiceDescription xml:lang="nl">Dit is een voorbeeld voor de unittest.</ServiceDescription>

        <RequestedAttribute FriendlyName="eduPersonPrincipalName" Name="urn:mace:dir:attribute-def:eduPersonPrincipalName" NameFormat="urn:mace:shibboleth:1.0:attributeNamespace:uri" isRequired="true"/>
        <RequestedAttribute FriendlyName="mail" Name="urn:mace:dir:attribute-def:mail" NameFormat="urn:mace:shibboleth:1.0:attributeNamespace:uri"/>
        <RequestedAttribute FriendlyName="displayName" Name="urn:mace:dir:attribute-def:displayName" NameFormat="urn:mace:shibboleth:1.0:attributeNamespace:uri"/>
      </AttributeConsumingService>
    </SPSSODescriptor>

  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);

        /** @var array $metadata */
        $metadata = $entities['theEntityID']->getMetadata20SP();

        $this->assertEquals("Example service", $metadata['name']['en']);
        $this->assertEquals("Dit is een voorbeeld voor de unittest.", $metadata['description']['nl']);

        $expected_a = [
            "urn:mace:dir:attribute-def:eduPersonPrincipalName",
            "urn:mace:dir:attribute-def:mail",
            "urn:mace:dir:attribute-def:displayName"
        ];
        $expected_r = ["urn:mace:dir:attribute-def:eduPersonPrincipalName"];

        $this->assertEquals($expected_a, $metadata['attributes']);
        $this->assertEquals($expected_r, $metadata['attributes.required']);
    }


    /**
     * @return \DOMDocument
     */
    public function makeTestDocument(): \DOMDocument
    {
        $doc = new DOMDocument();
        $doc->loadXML(
            <<<XML
<?xml version="1.0"?>
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata">
  <EntityDescriptor entityID="theEntityID">
    <SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        /** @psalm-var \DOMElement $entities_root */
        $entities_root = $doc->getElementsByTagName('EntitiesDescriptor')->item(0);
        $signer = new Signer([]);
        $signer->loadPrivateKey($this->good_private_key_file, null, true);
        $signer->loadCertificate($this->good_certificate_file, true);
        $signer->sign($entities_root, $entities_root);

        return $doc;
    }


    /**
     * @param string $algo
     * @param string $expected_fingerprint
     * @return void
     */
    private function validateFingerprint(string $algo, string $expected_fingerprint)
    {
        $doc = $this->makeTestDocument();
        $entities = \SimpleSAML\Metadata\SAMLParser::parseDescriptorsElement($doc->documentElement);
        foreach ($entities as $entity) {
            $this->assertTrue(
                $entity->validateFingerprint($expected_fingerprint, $algo)
            );
        }
    }


    /**
     * @return void
     */
    public function testValidateFingerprintSHA1()
    {
        $this->validateFingerprint(
            XMLSecurityDSig::SHA1,
            'A7:FB:75:22:57:88:A1:B0:D0:29:0A:4B:D1:EA:0C:01:F8:98:44:A0'
        );
    }


    /**
     * @return void
     */
    public function testValidateFingerprintSHA256()
    {
        $this->validateFingerprint(
            XMLSecurityDSig::SHA256,
            '3E:04:6B:2C:13:B5:02:FB:FC:93:66:EE:6C:A3:D1:BB:B8:9E:D8:38:03' .
            ':96:C5:C0:EC:95:D5:C9:F6:C1:D5:FC'
        );
    }


    /**
     * @return void
     */
    public function testValidateFingerprintSHA384()
    {
        $this->validateFingerprint(
            XMLSecurityDSig::SHA384,
            '38:87:CC:59:54:CF:ED:FC:71:B6:21:F3:8A:52:76:EF:30:C8:8C:A0:38' .
            ':48:77:87:58:14:A0:B3:55:EF:48:9C:B4:B3:44:1F:B7:BB:FC:28:65' .
            ':6E:93:83:52:C2:8E:A6'
        );
    }


    /**
     * @return void
     */
    public function testValidateFingerprintSHA512()
    {
        $this->validateFingerprint(
            XMLSecurityDSig::SHA512,
            '72:6C:51:01:A1:E9:76:D8:61:C4:B2:4F:AC:0B:64:7D:0D:4E:B7:DC:B3' .
            ':4A:92:23:51:A6:DC:A5:A1:9A:A5:DD:43:F5:05:6A:B7:7D:83:1F:B6:' .
            'CC:68:54:54:54:37:1B:EC:E1:22:5A:48:C6:BC:67:4B:A6:78:EE:E0:C6:8C:59'
        );
    }


    /**
     * @return void
     */
    public function testValidateFingerprintUnknownAlgorithmThrows()
    {
        $doc = $this->makeTestDocument();
        $entities = \SimpleSAML\Metadata\SAMLParser::parseDescriptorsElement($doc->documentElement);
        foreach ($entities as $entity) {
            try {
                $entity->validateFingerprint('unused', 'invalid_algorithm');
            } catch (\UnexpectedValueException $e) {
                $this->assertEquals(
                    'Unsupported hashing function invalid_algorithm. Known options: [' .
                    'http://www.w3.org/2000/09/xmldsig#sha1, ' .
                    'http://www.w3.org/2001/04/xmlenc#sha256, ' .
                    'http://www.w3.org/2001/04/xmldsig-more#sha384, ' .
                    'http://www.w3.org/2001/04/xmlenc#sha512]',
                    $e->getMessage()
                );
            }
        }
    }


    /**
     * Test RoleDescriptor/Extensions is parsed
     * @return void
     */
    public function testRoleDescriptorExtensions(): void
    {
        $expected = [
            'scope' => [
                'example.org',
                'example.net',
            ],
            'UIInfo' => [
                'DisplayName' => ['en' => 'DisplayName', 'af' => 'VertoonNaam'],
                'Description' => ['en' => 'Description',],
                'InformationURL' => ['en' => 'https://localhost/information',],
                'PrivacyStatementURL' => ['en' => 'https://localhost/privacypolicy',],
                'Logo' => [
                    [
                        'url' => 'https://localhost/logo',
                        'height' => 16,
                        'width' => 17,
                    ],
                    [
                        'url' => 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==',
                        'height' => 2,
                        'width' => 1,
                    ],
                ],
            ],
            'DiscoHints' => [
                'IPHint' => ['127.0.0.1', '127.0.0.2',],
                'DomainHint' => ['example.net', 'example.org',],
                'GeolocationHint' => ['geo:-29.00000,24.00000;u=830000',],
            ],
            'name' => ['en' => 'DisplayName', 'af' => 'VertoonNaam'],
        ];

        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi" xmlns:shibmd="urn:mace:shibboleth:metadata:1.0" xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui">
  <EntityDescriptor entityID="theEntityID">
    <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <Extensions>
          <shibmd:Scope regexp="false">example.org</shibmd:Scope>
          <shibmd:Scope regexp="false">example.net</shibmd:Scope>
          <mdui:UIInfo>
            <mdui:DisplayName xml:lang="en">DisplayName</mdui:DisplayName>
            <mdui:DisplayName xml:lang="af">VertoonNaam</mdui:DisplayName>
            <mdui:Description xml:lang="en">Description</mdui:Description>
            <mdui:PrivacyStatementURL xml:lang="en">https://localhost/privacypolicy</mdui:PrivacyStatementURL>
            <mdui:InformationURL xml:lang="en">https://localhost/information</mdui:InformationURL>
            <mdui:Logo width="17" height="16">https://localhost/logo</mdui:Logo>
            <mdui:Logo width="1" height="2">data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==</mdui:Logo>
          </mdui:UIInfo>
          <mdui:DiscoHints>
            <mdui:IPHint>127.0.0.1</mdui:IPHint>
            <mdui:IPHint>127.0.0.2</mdui:IPHint>
            <mdui:DomainHint>example.net</mdui:DomainHint>
            <mdui:DomainHint>example.org</mdui:DomainHint>
            <mdui:GeolocationHint>geo:-29.00000,24.00000;u=830000</mdui:GeolocationHint>
          </mdui:DiscoHints>
        </Extensions>
        <SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="https://IdentityProvider.com/SAML/SSO/Browser"/>
    </IDPSSODescriptor>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        // Various MDUI elements are accessible
        /** @var array $metadata */
        $metadata = $entities['theEntityID']->getMetadata20IdP();
        $this->assertEquals(
            $expected['scope'],
            $metadata['scope'],
            'shibmd:Scope elements not reflected in parsed metadata'
        );
        $this->assertEquals(
            $expected['UIInfo'],
            $metadata['UIInfo'],
            'mdui:UIInfo elements not reflected in parsed metadata'
        );
        $this->assertEquals(
            $expected['DiscoHints'],
            $metadata['DiscoHints'],
            'mdui:DiscoHints elements not reflected in parsed metadata'
        );
        $this->assertEquals($expected['name'], $metadata['name']);
    }

    /**
     * Test entity category hidden from discovery is parsed
     * @return void
     */
    public function testHiddenFromDiscovery(): void
    {
        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute">
  <EntityDescriptor entityID="theEntityID">
    <Extensions>
      <mdattr:EntityAttributes>
        <saml:Attribute Name="http://macedir.org/entity-category" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
          <saml:AttributeValue>https://example.org/some-category</saml:AttributeValue>
        </saml:Attribute>
        <saml:Attribute Name="http://macedir.org/entity-category" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
          <saml:AttributeValue>http://refeds.org/category/hide-from-discovery</saml:AttributeValue>
        </saml:Attribute>
      </mdattr:EntityAttributes>
    </Extensions>
    <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        $metadata = $entities['theEntityID']->getMetadata20IdP();
        $this->assertArrayHasKey('hide.from.discovery', $metadata);
        $this->assertTrue($metadata['hide.from.discovery']);
    }

    /**
     * Test entity category hidden from discovery is not returned when not present
     * @return void
     */
    public function testHiddenFromDiscoveryNotHidden(): void
    {
        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute" xmlns:mdrpi="urn:oasis:names:tc:SAML:metadata:rpi">
  <EntityDescriptor entityID="theEntityID">
    <Extensions>
      <mdrpi:RegistrationInfo registrationAuthority="https://safire.ac.za">
        <mdrpi:RegistrationPolicy xml:lang="en">https://safire.ac.za/safire/policy/mrps/v20190207.html</mdrpi:RegistrationPolicy>
      </mdrpi:RegistrationInfo>
      <mdattr:EntityAttributes>
        <saml:Attribute Name="http://macedir.org/entity-category" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
          <saml:AttributeValue>https://example.org/some-category</saml:AttributeValue>
        </saml:Attribute>
      </mdattr:EntityAttributes>
    </Extensions>
    <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        $metadata = $entities['theEntityID']->getMetadata20IdP();
        $this->assertArrayNotHasKey('hide.from.discovery', $metadata);
    }

    /**
     * Test entity category hidden from discovery is not returned when no mace dir entity categories present
     * @return void
     */
    public function testHiddenFromDiscoveryNotHiddenNoMaceDirEC(): void
    {
        $document = DOMDocumentFactory::fromString(
            <<<XML
<EntitiesDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute">
  <EntityDescriptor entityID="theEntityID">
    <Extensions>
      <mdattr:EntityAttributes>
        <saml:Attribute Name="http://macedir.org/entity-category-support" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
          <saml:AttributeValue>https://example.org/some-supported-category</saml:AttributeValue>
        </saml:Attribute>
      </mdattr:EntityAttributes>
    </Extensions>
    <IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol"/>
  </EntityDescriptor>
</EntitiesDescriptor>
XML
        );

        $entities = SAMLParser::parseDescriptorsElement($document->documentElement);
        $this->assertArrayHasKey('theEntityID', $entities);
        $metadata = $entities['theEntityID']->getMetadata20IdP();
        $this->assertArrayNotHasKey('hide.from.discovery', $metadata);
    }
}
