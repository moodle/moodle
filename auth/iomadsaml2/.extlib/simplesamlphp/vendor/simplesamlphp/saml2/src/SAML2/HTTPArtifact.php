<?php

declare(strict_types=1);

namespace SAML2;

use RobRichards\XMLSecLibs\XMLSecurityKey;
use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageHandler;
use SimpleSAML\Module\saml\Message as MSG;
use SimpleSAML\Store;
use SimpleSAML\Utils\HTTP;

use SAML2\Utilities\Temporal;
use SAML2\XML\saml\Issuer;

/**
 * Class which implements the HTTP-Artifact binding.
 *
 * @author  Danny Bollaert, UGent AS. <danny.bollaert@ugent.be>
 * @package SimpleSAMLphp
 */
class HTTPArtifact extends Binding
{
    /**
     * @var mixed
     */
    private $spMetadata;


    /**
     * Create the redirect URL for a message.
     *
     * @param  \SAML2\Message $message The message.
     * @throws \Exception
     * @return string        The URL the user should be redirected to in order to send a message.
     */
    public function getRedirectURL(Message $message) : string
    {
        /** @psalm-suppress UndefinedClass */
        $store = Store::getInstance();
        if ($store === false) {
            throw new \Exception('Unable to send artifact without a datastore configured.');
        }

        $generatedId = pack('H*', bin2hex(openssl_random_pseudo_bytes(20)));
        $issuer = $message->getIssuer();
        if ($issuer === null) {
            throw new \Exception('Cannot get redirect URL, no Issuer set in the message.');
        }
        $artifact = base64_encode("\x00\x04\x00\x00".sha1($issuer->getValue(), true).$generatedId);
        $artifactData = $message->toUnsignedXML();
        $artifactDataString = $artifactData->ownerDocument->saveXML($artifactData);

        $store->set('artifact', $artifact, $artifactDataString, Temporal::getTime() + 15*60);

        $params = [
            'SAMLart' => $artifact,
        ];
        $relayState = $message->getRelayState();
        if ($relayState !== null) {
            $params['RelayState'] = $relayState;
        }

        $destination = $message->getDestination();
        if ($destination === null) {
            throw new \Exception('Cannot get redirect URL, no destination set in the message.');
        }
        $httpUtils = new HTTP();
        return $httpUtils->addURLparameters($destination, $params);
    }


    /**
     * Send a SAML 2 message using the HTTP-Redirect binding.
     *
     * Note: This function never returns.
     *
     * @param \SAML2\Message $message The message we should send.
     * @return void
     */
    public function send(Message $message) : void
    {
        $destination = $this->getRedirectURL($message);
        Utils::getContainer()->redirect($destination);
    }


    /**
     * Receive a SAML 2 message sent using the HTTP-Artifact binding.
     *
     * Throws an exception if it is unable receive the message.
     *
     * @throws \Exception
     * @return \SAML2\Message The received message.
     */
    public function receive(): Message
    {
        if (array_key_exists('SAMLart', $_REQUEST)) {
            $artifact = base64_decode($_REQUEST['SAMLart']);
            $endpointIndex = bin2hex(substr($artifact, 2, 2));
            $sourceId = bin2hex(substr($artifact, 4, 20));
        } else {
            throw new \Exception('Missing SAMLart parameter.');
        }

        /** @psalm-suppress UndefinedClass */
        $metadataHandler = MetaDataStorageHandler::getMetadataHandler();

        $idpMetadata = $metadataHandler->getMetaDataConfigForSha1($sourceId, 'saml20-idp-remote');

        if ($idpMetadata === null) {
            throw new \Exception('No metadata found for remote provider with SHA1 ID: '.var_export($sourceId, true));
        }

        $endpoint = null;
        foreach ($idpMetadata->getEndpoints('ArtifactResolutionService') as $ep) {
            if ($ep['index'] === hexdec($endpointIndex)) {
                $endpoint = $ep;
                break;
            }
        }

        if ($endpoint === null) {
            throw new \Exception('No ArtifactResolutionService with the correct index.');
        }

        Utils::getContainer()->getLogger()->debug("ArtifactResolutionService endpoint being used is := ".$endpoint['Location']);

        //Construct the ArtifactResolve Request
        $ar = new ArtifactResolve();

        /* Set the request attributes */
        $issuer = new Issuer();
        $issuer->setValue($this->spMetadata->getString('entityid'));

        $ar->setIssuer($issuer);
        $ar->setArtifact($_REQUEST['SAMLart']);
        $ar->setDestination($endpoint['Location']);

        // sign the request
        /** @psalm-suppress UndefinedClass */
        MSG::addSign($this->spMetadata, $idpMetadata, $ar); // Shoaib - moved from the SOAPClient.

        $soap = new SOAPClient();

        // Send message through SoapClient
        /** @var \SimpleSAML\SAML2\XML\samlp\ArtifactResponse $artifactResponse */
        $artifactResponse = $soap->send($ar, $this->spMetadata, $idpMetadata);

        if (!$artifactResponse->isSuccess()) {
            throw new \Exception('Received error from ArtifactResolutionService.');
        }

        $xml = $artifactResponse->getAny();
        if ($xml === null) {
            /* Empty ArtifactResponse - possibly because of Artifact replay? */

            throw new \Exception('Empty ArtifactResponse received, maybe a replay?');
        }

        $samlResponse = Message::fromXML($xml);
        $samlResponse->addValidator([get_class($this), 'validateSignature'], $artifactResponse);

        if (isset($_REQUEST['RelayState'])) {
            $samlResponse->setRelayState($_REQUEST['RelayState']);
        }

        return $samlResponse;
    }


    /**
     * @param \SimpleSAML\Configuration $sp
     *
     * @return void
     *
     * @psalm-suppress UndefinedClass
     */
    public function setSPMetadata(Configuration $sp) : void
    {
        $this->spMetadata = $sp;
    }


    /**
     * A validator which returns true if the ArtifactResponse was signed with the given key
     *
     * @param \SAML2\ArtifactResponse $message
     * @param XMLSecurityKey $key
     * @return bool
     */
    public static function validateSignature(ArtifactResponse $message, XMLSecurityKey $key) : bool
    {
        return $message->validate($key);
    }
}
