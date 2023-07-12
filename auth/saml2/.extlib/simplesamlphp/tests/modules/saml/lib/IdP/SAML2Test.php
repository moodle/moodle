<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\saml\IdP;

use SimpleSAML\Configuration;
use SimpleSAML\IdP;
use SimpleSAML\Module\saml\IdP\SAML2;
use SimpleSAML\Test\Utils\ClearStateTestCase;

class SAML2Test extends ClearStateTestCase
{
    /**
     * Default values for the state array expected to be generated at the start of logins
     * @var array
     */
    private $defaultExpectedAuthState = [
        'Responder' => ['\SimpleSAML\Module\saml\IdP\SAML2', 'sendResponse'],
        '\SimpleSAML\Auth\State.exceptionFunc' => ['\SimpleSAML\Module\saml\IdP\SAML2', 'handleAuthError'],
        'saml:RelayState' => null,
        'saml:RequestId' => null,
        'saml:IDPList' => [],
        'saml:ProxyCount' => null,
        'saml:RequesterID' => null,
        'ForceAuthn' => false,
        'isPassive' => false,
        'saml:ConsumerURL' => 'SP-specific',
        'saml:Binding' => 'SP-specific',
        'saml:NameIDFormat' => null,
        'saml:AllowCreate' => true,
        'saml:Extensions' => null,
        'saml:RequestedAuthnContext' => null
    ];


    /**
     * Test that invoking the idp initiated endpoint with the minimum necessary parameters works.
     * @return void
     */
    public function testIdPInitiatedLoginMinimumParams(): void
    {
        $state = $this->idpInitiatedHelper(['spentityid' => 'https://some-sp-entity-id']);
        $this->assertEquals('https://some-sp-entity-id', $state['SPMetadata']['entityid']);

        $this->assertStringStartsWith(
            'http://idp.examlple.com/saml2/idp/SSOService.php?spentityid=https%3A%2F%2Fsome-sp-entity-id&cookie',
            $state['\SimpleSAML\Auth\State.restartURL']
        );
        unset($state['saml:AuthnRequestReceivedAt']); // timestamp can't be tested in equality assertion
        unset($state['SPMetadata']); // entityid asserted above
        unset($state['\SimpleSAML\Auth\State.restartURL']); // url contains a cookie time which varies by test

        $expectedState = $this->defaultExpectedAuthState;
        $expectedState['saml:ConsumerURL'] = 'https://example.com/Shibboleth.sso/SAML2/POST';
        $expectedState['saml:Binding'] = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';

        $this->assertEquals($expectedState, $state);
    }


    /**
     * Test that invoking the idp initiated endpoint with the optional parameters works.
     * @return void
     */
    public function testIdPInitiatedLoginOptionalParams(): void
    {
        $state = $this->idpInitiatedHelper([
            'spentityid' => 'https://some-sp-entity-id',
            'RelayState' => 'http://relay',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS',
            'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',

        ]);
        $this->assertEquals('https://some-sp-entity-id', $state['SPMetadata']['entityid']);

        //currently only spentityid and relay state are used in the restart url.
        $this->assertStringStartsWith(
            'http://idp.examlple.com/saml2/idp/SSOService.php?'
            . 'spentityid=https%3A%2F%2Fsome-sp-entity-id&RelayState=http%3A%2F%2Frelay&cookieTime',
            $state['\SimpleSAML\Auth\State.restartURL']
        );
        unset($state['saml:AuthnRequestReceivedAt']); // timestamp can't be tested in equality assertion
        unset($state['SPMetadata']); // entityid asserted above
        unset($state['\SimpleSAML\Auth\State.restartURL']); // url contains a cookie time which varies by test

        $expectedState = $this->defaultExpectedAuthState;
        $expectedState['saml:ConsumerURL'] = 'https://example.com/Shibboleth.sso/SAML2/ECP';
        $expectedState['saml:Binding'] = 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS';
        $expectedState['saml:NameIDFormat'] = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
        $expectedState['saml:RelayState'] = 'http://relay';

        $this->assertEquals($expectedState, $state);
    }


    /**
     * Test that invoking the idp initiated endpoint using minimum shib params works
     * @return void
     */
    public function testIdPInitShibCompatyMinimumParams(): void
    {
        //https://wiki.shibboleth.net/confluence/display/IDP30/UnsolicitedSSOConfiguration
        // Shib uses the param providerId instead of spentityid
        $state = $this->idpInitiatedHelper(['providerId' => 'https://some-sp-entity-id']);
        $this->assertEquals('https://some-sp-entity-id', $state['SPMetadata']['entityid']);

        $this->assertStringStartsWith(
            'http://idp.examlple.com/saml2/idp/SSOService.php?spentityid=https%3A%2F%2Fsome-sp-entity-id&cookie',
            $state['\SimpleSAML\Auth\State.restartURL']
        );
        unset($state['saml:AuthnRequestReceivedAt']); // timestamp can't be tested in equality assertion
        unset($state['SPMetadata']); // entityid asserted above
        unset($state['\SimpleSAML\Auth\State.restartURL']); // url contains a cookie time which varies by test

        $expectedState = $this->defaultExpectedAuthState;
        $expectedState['saml:ConsumerURL'] = 'https://example.com/Shibboleth.sso/SAML2/POST';
        $expectedState['saml:Binding'] = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';

        $this->assertEquals($expectedState, $state);
    }


    /**
     * Test that invoking the idp initiated endpoint using minimum shib params works
     * @return void
     */
    public function testIdPInitShibCompatOptionalParams(): void
    {
        $state = $this->idpInitiatedHelper([
            'providerId' => 'https://some-sp-entity-id',
            'target' => 'http://relay',
            'shire' => 'https://example.com/Shibboleth.sso/SAML2/ECP',
        ]);
        $this->assertEquals('https://some-sp-entity-id', $state['SPMetadata']['entityid']);

        //currently only spentityid and relay state are used in the restart url.
        $this->assertStringStartsWith(
            'http://idp.examlple.com/saml2/idp/SSOService.php?'
            . 'spentityid=https%3A%2F%2Fsome-sp-entity-id&RelayState=http%3A%2F%2Frelay&cookieTime',
            $state['\SimpleSAML\Auth\State.restartURL']
        );
        unset($state['saml:AuthnRequestReceivedAt']); // timestamp can't be tested in equality assertion
        unset($state['SPMetadata']); // entityid asserted above
        unset($state['\SimpleSAML\Auth\State.restartURL']); // url contains a cookie time which varies by test

        $expectedState = $this->defaultExpectedAuthState;
        $expectedState['saml:ConsumerURL'] = 'https://example.com/Shibboleth.sso/SAML2/ECP';
        $expectedState['saml:Binding'] = 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS';
        $expectedState['saml:RelayState'] = 'http://relay';

        $this->assertEquals($expectedState, $state);
    }


    /**
     * Invoke IDP initiated login with the given query parameters.
     * Callers should validate the return state array or confirm appropriate exceptions are returned.
     *
     * @param array $queryParams
     * @return array The state array used for handling the authentication request.
     */
    private function idpInitiatedHelper(array $queryParams): array
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject $idpStub */
        $idpStub = $this->getMockBuilder(IdP::class)
            ->disableOriginalConstructor()
            ->getMock();
        $idpMetadata = Configuration::loadFromArray([
            'entityid' => 'https://idp-entity.id',
            'saml20.ecp' => true, //enable additional bindings so we can test selection logic
        ]);

        /** @psalm-suppress UndefinedMethod   Remove when Psalm 3.x is in place */
        $idpStub->method("getConfig")
            ->willReturn($idpMetadata);

        // phpcs:disable
        $spMetadataXml = <<< 'EOT'
<EntityDescriptor xmlns="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="https://some-sp-entity-id">
   <md:SPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol urn:oasis:names:tc:SAML:1.1:protocol">
      <md:AssertionConsumerService index="1" Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://example.com/Shibboleth.sso/SAML2/POST" />
      <md:AssertionConsumerService index="2" Binding="urn:oasis:names:tc:SAML:2.0:bindings:PAOS" Location="https://example.com/Shibboleth.sso/SAML2/ECP" />
   </md:SPSSODescriptor>
</EntityDescriptor>
EOT;
        // phpcs:enable

        Configuration::loadFromArray([
            'baseurlpath' => 'https://idp.example.com/',
            'metadata.sources' => [
                ["type" => "xml", 'xml' => $spMetadataXml],
            ],
        ], '', 'simplesaml');

        // Since we aren't really running on a webserver some of the url calculations done, such as for restart url
        // won't line up perfectly
        $_REQUEST = $_REQUEST + $queryParams;
        $_SERVER['HTTP_HOST'] = 'idp.examlple.com';
        $_SERVER['REQUEST_URI'] = '/saml2/idp/SSOService.php?' . http_build_query($queryParams);


        $state = [];

        /** @psalm-suppress InvalidArgument   Remove when PHPunit 8 is in place */
        $idpStub->expects($this->once())
            ->method('handleAuthenticationRequest')
            ->with($this->callback(
                /**
                 * @param array $arg
                 * @return bool
                 */
                function ($arg) use (&$state) {
                    $state = $arg;
                    return true;
                }
            ));

        /** @psalm-suppress InvalidArgument */
        SAML2::receiveAuthnRequest($idpStub);

        return $state;
    }
}
