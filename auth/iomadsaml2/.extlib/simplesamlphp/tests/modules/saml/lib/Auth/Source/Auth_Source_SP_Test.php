<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\saml\Auth\Source;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SAML2\AuthnRequest;
use SAML2\Constants;
use SAML2\Utils;
use SimpleSAML\Configuration;
use SimpleSAML\Module\saml\Error\NoAvailableIDP;
use SimpleSAML\Module\saml\Error\NoSupportedIDP;
use SimpleSAML\Test\Metadata\MetaDataStorageSourceTest;
use SimpleSAML\Test\Utils\ClearStateTestCase;
use SimpleSAML\Test\Utils\ExitTestException;
use SimpleSAML\Test\Utils\SpTester;

/**
 * Set of test cases for \SimpleSAML\Module\saml\Auth\Source\SP.
 */
class SPTest extends ClearStateTestCase
{
    /** @var \SimpleSAML\Configuration|null $idpMetadata */
    private $idpMetadata = null;

    /** @var array $idpConfigArray */
    private $idpConfigArray;

    /** @var \SimpleSAML\Configuration */
    private $config;


    /**
     * @return \SimpleSAML\Configuration
     */
    private function getIdpMetadata(): Configuration
    {
        if (!$this->idpMetadata) {
            $this->idpMetadata = new Configuration(
                $this->idpConfigArray,
                'Auth_Source_SP_Test::getIdpMetadata()'
            );
        }

        return $this->idpMetadata;
    }


    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->idpConfigArray = [
            'metadata-set'        => 'saml20-idp-remote',
            'entityid'            => 'https://engine.surfconext.nl/authentication/idp/metadata',
            'SingleSignOnService' => [
                [
                    'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                    'Location' => 'https://engine.surfconext.nl/authentication/idp/single-sign-on',
                ],
            ],
            'keys'                => [
                [
                    'encryption'      => false,
                    'signing'         => true,
                    'type'            => 'X509Certificate',
                    'X509Certificate' =>
                        'MIID3zCCAsegAwIBAgIJAMVC9xn1ZfsuMA0GCSqGSIb3DQEBCwUAMIGFMQswCQYDVQQGEwJOTDEQMA4GA1UECAwHVXR' .
                        'yZWNodDEQMA4GA1UEBwwHVXRyZWNodDEVMBMGA1UECgwMU1VSRm5ldCBCLlYuMRMwEQYDVQQLDApTVVJGY29uZXh0MS' .
                        'YwJAYDVQQDDB1lbmdpbmUuc3VyZmNvbmV4dC5ubCAyMDE0MDUwNTAeFw0xNDA1MDUxNDIyMzVaFw0xOTA1MDUxNDIyM' .
                        'zVaMIGFMQswCQYDVQQGEwJOTDEQMA4GA1UECAwHVXRyZWNodDEQMA4GA1UEBwwHVXRyZWNodDEVMBMGA1UECgwMU1VS' .
                        'Rm5ldCBCLlYuMRMwEQYDVQQLDApTVVJGY29uZXh0MSYwJAYDVQQDDB1lbmdpbmUuc3VyZmNvbmV4dC5ubCAyMDE0MDU' .
                        'wNTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKthMDbB0jKHefPzmRu9t2h7iLP4wAXr42bHpjzTEk6gtt' .
                        'HFb4l/hFiz1YBI88TjiH6hVjnozo/YHA2c51us+Y7g0XoS7653lbUN/EHzvDMuyis4Xi2Ijf1A/OUQfH1iFUWttIgtW' .
                        'K9+fatXoGUS6tirQvrzVh6ZstEp1xbpo1SF6UoVl+fh7tM81qz+Crr/Kroan0UjpZOFTwxPoK6fdLgMAieKSCRmBGpb' .
                        'JHbQ2xxbdykBBrBbdfzIX4CDepfjE9h/40ldw5jRn3e392jrS6htk23N9BWWrpBT5QCk0kH3h/6F1Dm6TkyG9CDtt73' .
                        '/anuRkvXbeygI4wml9bL3rE8CAwEAAaNQME4wHQYDVR0OBBYEFD+Ac7akFxaMhBQAjVfvgGfY8hNKMB8GA1UdIwQYMB' .
                        'aAFD+Ac7akFxaMhBQAjVfvgGfY8hNKMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBAC8L9D67CxIhGo5aG' .
                        'Vu63WqRHBNOdo/FAGI7LURDFeRmG5nRw/VXzJLGJksh4FSkx7aPrxNWF1uFiDZ80EuYQuIv7bDLblK31ZEbdg1R9Lgi' .
                        'ZCdYSr464I7yXQY9o6FiNtSKZkQO8EsscJPPy/Zp4uHAnADWACkOUHiCbcKiUUFu66dX0Wr/v53Gekz487GgVRs8HEe' .
                        'T9MU1reBKRgdENR8PNg4rbQfLc3YQKLWK7yWnn/RenjDpuCiePj8N8/80tGgrNgK/6fzM3zI18sSywnXLswxqDb/J+j' .
                        'gVxnQ6MrsTf1urM8MnfcxG/82oHIwfMh/sXPCZpo+DTLkhQxctJ3M=',
                ],
            ],
        ];

        $this->config = Configuration::loadFromArray([], '[ARRAY]', 'simplesaml');
    }


    /**
     * Create a SAML AuthnRequest using \SimpleSAML\Module\saml\Auth\Source\SP
     *
     * @param array $state The state array to use in the test. This is an array of the parameters described in section
     * 2 of https://simplesamlphp.org/docs/development/saml:sp
     *
     * @return \SAML2\AuthnRequest The AuthnRequest generated.
     */
    private function createAuthnRequest(array $state = []): AuthnRequest
    {
        $info = ['AuthId' => 'default-sp'];
        $config = [];
        $as = new SpTester($info, $config);

        /** @var \SAML2\AuthnRequest $ar */
        $ar = null;
        try {
            $as->startSSO2Test($this->getIdpMetadata(), $state);
            $this->assertTrue(false, 'Expected ExitTestException');
        } catch (ExitTestException $e) {
            $r = $e->getTestResult();
            $ar = $r['ar'];
        }
        return $ar;
    }


    /**
     * Test generating an AuthnRequest
     * @test
     * @return void
     */
    public function testAuthnRequest(): void
    {
        /** @var \SAML2\AuthnRequest $ar */
        $ar = $this->createAuthnRequest();

        // Assert values in the generated AuthnRequest
        /** @var \DOMElement $xml */
        $xml = $ar->toSignedXML();

        /** @var \DOMAttr[] $q */
        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/@Destination');
        $this->assertEquals(
            $this->idpConfigArray['SingleSignOnService'][0]['Location'],
            $q[0]->value
        );

        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/saml:Issuer');
        $this->assertEquals(
            'http://localhost/simplesaml/module.php/saml/sp/metadata.php/default-sp',
            $q[0]->textContent
        );
    }


    /**
     * Test setting a Subject
     * @test
     * @return void
     */
    public function testNameID(): void
    {
        $state = [
            'saml:NameID' => ['Value' => 'user@example.org', 'Format' => Constants::NAMEID_UNSPECIFIED]
        ];

        /** @var \SAML2\AuthnRequest $ar */
        $ar = $this->createAuthnRequest($state);

        /** @var \SAML2\XML\saml\NameID $nameID */
        $nameID = $ar->getNameId();
        $this->assertEquals($state['saml:NameID']['Value'], $nameID->getValue());
        $this->assertEquals($state['saml:NameID']['Format'], $nameID->getFormat());

        /** @var \DOMElement $xml */
        $xml = $ar->toSignedXML();

        /** @var \DOMAttr[] $q */
        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/saml:Subject/saml:NameID/@Format');
        $this->assertEquals(
            $state['saml:NameID']['Format'],
            $q[0]->value
        );

        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/saml:Subject/saml:NameID');
        $this->assertEquals(
            $state['saml:NameID']['Value'],
            $q[0]->textContent
        );
    }


    /**
     * Test setting an AuthnConextClassRef
     * @test
     * @return void
     */
    public function testAuthnContextClassRef(): void
    {
        $state = [
            'saml:AuthnContextClassRef' => 'http://example.com/myAuthnContextClassRef'
        ];

        /** @var \SAML2\AuthnRequest $ar */
        $ar = $this->createAuthnRequest($state);

        /** @var array $a */
        $a = $ar->getRequestedAuthnContext();
        $this->assertEquals(
            $state['saml:AuthnContextClassRef'],
            $a['AuthnContextClassRef'][0]
        );

        /** @var \DOMElement $xml */
        $xml = $ar->toSignedXML();

        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/samlp:RequestedAuthnContext/saml:AuthnContextClassRef');
        $this->assertEquals(
            $state['saml:AuthnContextClassRef'],
            $q[0]->textContent
        );
    }


    /**
     * Test setting ForcedAuthn
     * @test
     * @return void
     */
    public function testForcedAuthn(): void
    {
        /** @var bool $state['ForceAuthn'] */
        $state = [
            'ForceAuthn' => true
        ];

        /** @var \SAML2\AuthnRequest $ar */
        $ar = $this->createAuthnRequest($state);

        $this->assertEquals(
            $state['ForceAuthn'],
            $ar->getForceAuthn()
        );

        /** @var \DOMElement $xml */
        $xml = $ar->toSignedXML();

        /** @var \DOMAttr[] $q */
        $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/@ForceAuthn');
        $this->assertEquals(
            $state['ForceAuthn'] ? 'true' : 'false',
            $q[0]->value
        );
    }


    /**
     * Test specifying an IDPList where no metadata found for those idps is an error
     * @return void
     */
    public function testIdpListWithNoMatchingMetadata(): void
    {
        $this->expectException(NoSupportedIDP::class);
        $state = [
            'saml:IDPList' => ['noSuchIdp']
        ];

        $info = ['AuthId' => 'default-sp'];
        $config = [];
        $as = new SpTester($info, $config);
        $as->authenticate($state);
    }


    /**
     * Test specifying an IDPList where the list does not overlap with the Idp specified in SP config is an error
     * @return void
     */
    public function testIdpListWithExplicitIdpNotMatch(): void
    {
        $this->expectException(NoAvailableIDP::class);
        $entityId = "https://example.com";
        $xml = MetaDataStorageSourceTest::generateIdpMetadataXml($entityId);
        $c = [
            'metadata.sources' => [
                ["type" => "xml", "xml" => $xml],
            ],
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $state = [
            'saml:IDPList' => ['noSuchIdp', $entityId]
        ];

        $info = ['AuthId' => 'default-sp'];
        $config = [
            'idp' => 'https://engine.surfconext.nl/authentication/idp/metadata'
        ];
        $as = new SpTester($info, $config);
        $as->authenticate($state);
    }


    /**
     * Test that IDPList overlaps with the IDP specified in SP config results in AuthnRequest
     * @return void
     */
    public function testIdpListWithExplicitIdpMatch(): void
    {
        $entityId = "https://example.com";
        $xml = MetaDataStorageSourceTest::generateIdpMetadataXml($entityId);
        $c = [
            'metadata.sources' => [
                ["type" => "xml", "xml" => $xml],
            ],
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $state = [
            'saml:IDPList' => ['noSuchIdp', $entityId]
        ];

        $info = ['AuthId' => 'default-sp'];
        $config = [
            'idp' => $entityId
        ];
        $as = new SpTester($info, $config);
        try {
            $as->authenticate($state);
            $this->fail('Expected ExitTestException');
        } catch (ExitTestException $e) {
            $r = $e->getTestResult();
            /** @var AuthnRequest $ar */
            $ar = $r['ar'];
            $xml = $ar->toSignedXML();

            /** @var \DOMAttr[] $q */
            $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/@Destination');
            $this->assertEquals(
                'https://saml.idp/sso/',
                $q[0]->value
            );
        }
    }


    /**
     * Test that IDPList with a single valid idp and no SP config idp results in AuthnRequest to that idp
     * @return void
     */
    public function testIdpListWithSingleMatch(): void
    {
        $entityId = "https://example.com";
        $xml = MetaDataStorageSourceTest::generateIdpMetadataXml($entityId);
        $c = [
            'metadata.sources' => [
                ["type" => "xml", "xml" => $xml],
            ],
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $state = [
            'saml:IDPList' => ['noSuchIdp', $entityId]
        ];

        $info = ['AuthId' => 'default-sp'];
        $config = [];
        $as = new SpTester($info, $config);
        try {
            $as->authenticate($state);
            $this->fail('Expected ExitTestException');
        } catch (ExitTestException $e) {
            $r = $e->getTestResult();
            /** @var AuthnRequest $ar */
            $ar = $r['ar'];
            $xml = $ar->toSignedXML();

            /** @var \DOMAttr[] $q */
            $q = Utils::xpQuery($xml, '/samlp:AuthnRequest/@Destination');
            $this->assertEquals(
                'https://saml.idp/sso/',
                $q[0]->value
            );
        }
    }


    /**
     * Test that IDPList with multiple valid idp and no SP config idp results in discovery redirect
     * @return void
     */
    public function testIdpListWithMultipleMatch(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL: smtp://invalidurl');
        $entityId = "https://example.com";
        $xml = MetaDataStorageSourceTest::generateIdpMetadataXml($entityId);
        $entityId1 = "https://example1.com";
        $xml1 = MetaDataStorageSourceTest::generateIdpMetadataXml($entityId1);
        $c = [
            'metadata.sources' => [
                ["type" => "xml", "xml" => $xml],
                ["type" => "xml", "xml" => $xml1],
            ],
        ];
        Configuration::loadFromArray($c, '', 'simplesaml');
        $state = [
            'saml:IDPList' => ['noSuchIdp', $entityId, $entityId1]
        ];

        $info = ['AuthId' => 'default-sp'];
        $config = [
            // Use a url that is invalid for http redirects so redirect code throws an error
            // otherwise it will call exit
            'discoURL' => 'smtp://invalidurl'
        ];
        // Http redirect util library requires a request_uri to be set.
        $_SERVER['REQUEST_URI'] = 'https://l.example.com/';
        $as = new SpTester($info, $config);
        $as->authenticate($state);
    }
}
