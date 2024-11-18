<?php

declare(strict_types=1);

/**
 * Implementation of the Shibboleth 1.3 Artifact binding.
 *
 * @package SimpleSAMLphp
 * @deprecated This class will be removed in a future release
 */

namespace SimpleSAML\Bindings\Shib13;

use SAML2\DOMDocumentFactory;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Utils;

class Artifact
{
    /**
     * Parse the query string, and extract the SAMLart parameters.
     *
     * This function is required because each query contains multiple
     * artifact with the same parameter name.
     *
     * @return array  The artifacts.
     */
    private static function getArtifacts(): array
    {
        assert(array_key_exists('QUERY_STRING', $_SERVER));

        // We need to process the query string manually, to capture all SAMLart parameters

        $artifacts = [];

        $elements = explode('&', $_SERVER['QUERY_STRING']);
        foreach ($elements as $element) {
            list($name, $value) = explode('=', $element, 2);
            $name = urldecode($name);
            $value = urldecode($value);

            if ($name === 'SAMLart') {
                $artifacts[] = $value;
            }
        }

        return $artifacts;
    }


    /**
     * Build the request we will send to the IdP.
     *
     * @param array $artifacts  The artifacts we will request.
     * @return string  The request, as an XML string.
     */
    private static function buildRequest(array $artifacts): string
    {
        $msg = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">'.
            '<SOAP-ENV:Body>'.
            '<samlp:Request xmlns:samlp="urn:oasis:names:tc:SAML:1.0:protocol"'.
            ' RequestID="'.Utils\Random::generateID().'"'.
            ' MajorVersion="1" MinorVersion="1"'.
            ' IssueInstant="'.Utils\Time::generateTimestamp().'"'.
            '>';

        foreach ($artifacts as $a) {
            $msg .= '<samlp:AssertionArtifact>'.htmlspecialchars($a).'</samlp:AssertionArtifact>';
        }

        $msg .= '</samlp:Request>'.
            '</SOAP-ENV:Body>'.
            '</SOAP-ENV:Envelope>';

        return $msg;
    }


    /**
     * Extract the response element from the SOAP response.
     *
     * @param string $soapResponse The SOAP response.
     * @return string The <saml1p:Response> element, as a string.
     * @throws Error\Exception
     */
    private static function extractResponse(string $soapResponse): string
    {
        try {
            $doc = DOMDocumentFactory::fromString($soapResponse);
        } catch (\Exception $e) {
            throw new Error\Exception('Error parsing SAML 1 artifact response.');
        }

        $soapEnvelope = $doc->firstChild;
        if (!Utils\XML::isDOMNodeOfType($soapEnvelope, 'Envelope', 'http://schemas.xmlsoap.org/soap/envelope/')) {
            throw new Error\Exception('Expected artifact response to contain a <soap:Envelope> element.');
        }

        $soapBody = Utils\XML::getDOMChildren($soapEnvelope, 'Body', 'http://schemas.xmlsoap.org/soap/envelope/');
        if (count($soapBody) === 0) {
            throw new Error\Exception('Couldn\'t find <soap:Body> in <soap:Envelope>.');
        }
        $soapBody = $soapBody[0];


        $responseElement = Utils\XML::getDOMChildren($soapBody, 'Response', 'urn:oasis:names:tc:SAML:1.0:protocol');
        if (count($responseElement) === 0) {
            throw new Error\Exception('Couldn\'t find <saml1p:Response> in <soap:Body>.');
        }
        $responseElement = $responseElement[0];

        /*
         * Save the <saml1p:Response> element. Note that we need to import it
         * into a new document, in order to preserve namespace declarations.
         */
        $newDoc = DOMDocumentFactory::create();
        $newDoc->appendChild($newDoc->importNode($responseElement, true));
        $responseXML = $newDoc->saveXML();

        return $responseXML;
    }


    /**
     * This function receives a SAML 1.1 artifact.
     *
     * @param \SimpleSAML\Configuration $spMetadata The metadata of the SP.
     * @param \SimpleSAML\Configuration $idpMetadata The metadata of the IdP.
     * @return string The <saml1p:Response> element, as an XML string.
     * @throws Error\Exception
     */
    public static function receive(Configuration $spMetadata, Configuration $idpMetadata)
    {
        $artifacts = self::getArtifacts();
        $request = self::buildRequest($artifacts);

        Utils\XML::debugSAMLMessage($request, 'out');

        /** @var array $url */
        $url = $idpMetadata->getDefaultEndpoint(
            'ArtifactResolutionService',
            ['urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding']
        );
        $url = $url['Location'];

        $peerPublicKeys = $idpMetadata->getPublicKeys('signing', true);
        $certData = '';
        foreach ($peerPublicKeys as $key) {
            if ($key['type'] !== 'X509Certificate') {
                continue;
            }
            $certData .= "-----BEGIN CERTIFICATE-----\n".
                chunk_split($key['X509Certificate'], 64).
                "-----END CERTIFICATE-----\n";
        }

        $file = Utils\System::getTempDir().DIRECTORY_SEPARATOR.sha1($certData).'.crt';
        if (!file_exists($file)) {
            Utils\System::writeFile($file, $certData);
        }

        $spKeyCertFile = Utils\Config::getCertPath($spMetadata->getString('privatekey'));

        $opts = [
            'ssl' => [
                'verify_peer' => true,
                'cafile' => $file,
                'local_cert' => $spKeyCertFile,
                'capture_peer_cert' => true,
                'capture_peer_chain' => true,
            ],
            'http' => [
                'method' => 'POST',
                'content' => $request,
                'header' => 'SOAPAction: http://www.oasis-open.org/committees/security'."\r\n".
                    'Content-Type: text/xml',
            ],
        ];

        // Fetch the artifact
        /** @var string $response */
        $response = Utils\HTTP::fetch($url, $opts);
        Utils\XML::debugSAMLMessage($response, 'in');

        // Find the response in the SOAP message
        $response = self::extractResponse($response);

        return $response;
    }
}
