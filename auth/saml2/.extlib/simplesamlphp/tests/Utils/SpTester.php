<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use ReflectionObject;
use SAML2\AuthnRequest;
use SAML2\Binding;
use SimpleSAML\Configuration;
use SimpleSAML\Module\saml\Auth\Source\SP;

/**
 * Wrap the SSP \SimpleSAML\Module\saml\Auth\Source\SP class
 * - Use introspection to make startSSO2Test available
 * - Override sendSAML2AuthnRequest() to catch the AuthnRequest being sent
 */
class SpTester extends SP
{
    /**
     * @param array $info
     * @param array $config
     * @return void
     */
    public function __construct($info, $config)
    {
        parent::__construct($info, $config);
    }


    /**
     * @return void
     */
    public function startSSO2Test(Configuration $idpMetadata, array $state)
    {
        $reflector = new ReflectionObject($this);
        $method = $reflector->getMethod('startSSO2');
        $method->setAccessible(true);
        $method->invoke($this, $idpMetadata, $state);
    }


    /**
     * override the method that sends the request to avoid sending anything
     * @return void
     */
    public function sendSAML2AuthnRequest(array &$state, Binding $binding, AuthnRequest $ar)
    {
        // Exit test. Continuing would mean running into a assert(FALSE)
        throw new ExitTestException(
            [
                'state'   => $state,
                'binding' => $binding,
                'ar'      => $ar,
            ]
        );
    }
}
