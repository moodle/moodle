<?php

declare(strict_types=1);

/**
 * Implementation of the Shibboleth 1.3 HTTP-POST binding.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 * @deprecated This class will be removed in a future release
 */

namespace SimpleSAML\Bindings\Shib13;

use SAML2\DOMDocumentFactory;
use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Utils;
use SimpleSAML\XML\Shib13\AuthnResponse;
use SimpleSAML\XML\Signer;

class HTTPPost
{
    /**
     * @var \SimpleSAML\Configuration
     */
    private $configuration;

    /**
     * @var \SimpleSAML\Metadata\MetaDataStorageHandler
     */
    private $metadata;


    /**
     * Constructor for the \SimpleSAML\Bindings\Shib13\HTTPPost class.
     *
     * @param \SimpleSAML\Configuration                   $configuration The configuration to use.
     * @param \SimpleSAML\Metadata\MetaDataStorageHandler $metadatastore A store where to find metadata.
     */
    public function __construct(
        Configuration $configuration,
        MetaDataStorageHandler $metadatastore
    ) {
        $this->configuration = $configuration;
        $this->metadata = $metadatastore;
    }


    /**
     * Send an authenticationResponse using HTTP-POST.
     *
     * @param string                    $response The response which should be sent.
     * @param \SimpleSAML\Configuration $idpmd The metadata of the IdP which is sending the response.
     * @param \SimpleSAML\Configuration $spmd The metadata of the SP which is receiving the response.
     * @param string|null               $relayState The relaystate for the SP.
     * @param string                    $shire The shire which should receive the response.
     * @return void
     */
    public function sendResponse(
        $response,
        Configuration $idpmd,
        Configuration $spmd,
        $relayState,
        $shire
    ) {
        Utils\XML::checkSAMLMessage($response, 'saml11');

        $privatekey = Utils\Crypto::loadPrivateKey($idpmd, true);
        $publickey = Utils\Crypto::loadPublicKey($idpmd, true);

        $responsedom = DOMDocumentFactory::fromString(str_replace("\r", "", $response));

        $responseroot = $responsedom->getElementsByTagName('Response')->item(0);
        $firstassertionroot = $responsedom->getElementsByTagName('Assertion')->item(0);

        /* Determine what we should sign - either the Response element or the Assertion. The default is to sign the
         * Assertion, but that can be overridden by the 'signresponse' option in the SP metadata or
         * 'saml20.signresponse' in the global configuration.
         *
         * TODO: neither 'signresponse' nor 'shib13.signresponse' are valid options any longer. Remove!
         */
        if ($spmd->hasValue('signresponse')) {
            $signResponse = $spmd->getBoolean('signresponse');
        } else {
            $signResponse = $this->configuration->getBoolean('shib13.signresponse', true);
        }

        // check if we have an assertion to sign. Force to sign the response if not
        if ($firstassertionroot === null) {
            $signResponse = true;
        }

        $signer = new Signer([
            'privatekey_array' => $privatekey,
            'publickey_array'  => $publickey,
            'id'               => ($signResponse ? 'ResponseID' : 'AssertionID'),
        ]);

        if ($idpmd->hasValue('certificatechain')) {
            $signer->addCertificate($idpmd->getString('certificatechain'));
        }

        if ($signResponse) {
            // sign the response - this must be done after encrypting the assertion
            // we insert the signature before the saml2p:Status element
            $statusElements = Utils\XML::getDOMChildren($responseroot, 'Status', '@saml1p');
            assert(count($statusElements) === 1);
            $signer->sign($responseroot, $responseroot, $statusElements[0]);
        } else {
            // Sign the assertion
            $signer->sign($firstassertionroot, $firstassertionroot);
        }

        $response = $responsedom->saveXML();

        Utils\XML::debugSAMLMessage($response, 'out');

        Utils\HTTP::submitPOSTData($shire, [
            'TARGET'       => $relayState,
            'SAMLResponse' => base64_encode($response),
        ]);
    }


    /**
     * Decode a received response.
     *
     * @param array $post POST data received.
     * @return \SimpleSAML\XML\Shib13\AuthnResponse The response decoded into an object.
     * @throws \Exception If there is no SAMLResponse parameter.
     */
    public function decodeResponse($post)
    {
        assert(is_array($post));

        if (!array_key_exists('SAMLResponse', $post)) {
            throw new \Exception('Missing required SAMLResponse parameter.');
        }
        $rawResponse = $post['SAMLResponse'];
        $samlResponseXML = base64_decode($rawResponse);

        Utils\XML::debugSAMLMessage($samlResponseXML, 'in');

        Utils\XML::checkSAMLMessage($samlResponseXML, 'saml11');

        $samlResponse = new AuthnResponse();
        $samlResponse->setXML($samlResponseXML);

        if (array_key_exists('TARGET', $post)) {
            $samlResponse->setRelayState($post['TARGET']);
        }

        return $samlResponse;
    }
}
