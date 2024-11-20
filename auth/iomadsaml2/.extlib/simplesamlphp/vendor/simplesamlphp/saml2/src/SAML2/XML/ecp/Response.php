<?php

declare(strict_types=1);

namespace SAML2\XML\ecp;

use DOMElement;
use InvalidArgumentException;

use SAML2\Constants;

/**
 * Class representing the ECP Response element.
 */
class Response
{
    /**
     * The AssertionConsumerServiceURL.
     *
     * @var string
     */
    private $AssertionConsumerServiceURL;


    /**
     * Create a ECP Response element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttributeNS(Constants::NS_SOAP, 'mustUnderstand')) {
            throw new \Exception('Missing SOAP-ENV:mustUnderstand attribute in <ecp:Response>.');
        }
        if ($xml->getAttributeNS(Constants::NS_SOAP, 'mustUnderstand') !== '1') {
            throw new \Exception('Invalid value of SOAP-ENV:mustUnderstand attribute in <ecp:Response>.');
        }

        if (!$xml->hasAttributeNS(Constants::NS_SOAP, 'actor')) {
            throw new \Exception('Missing SOAP-ENV:actor attribute in <ecp:Response>.');
        }
        if ($xml->getAttributeNS(Constants::NS_SOAP, 'actor') !== 'http://schemas.xmlsoap.org/soap/actor/next') {
            throw new \Exception('Invalid value of SOAP-ENV:actor attribute in <ecp:Response>.');
        }

        if (!$xml->hasAttribute('AssertionConsumerServiceURL')) {
            throw new \Exception('Missing AssertionConsumerServiceURL attribute in <ecp:Response>.');
        }

        $this->setAssertionConsumerServiceURL($xml->getAttribute('AssertionConsumerServiceURL'));
    }


    /**
     * Collect the value of the AssertionConsumerServiceURL-property
     *
     * @return string
     */
    public function getAssertionConsumerServiceURL() : string
    {
        return $this->AssertionConsumerServiceURL;
    }


    /**
     * Set the value of the AssertionConsumerServiceURL-property
     *
     * @param string $assertionConsumerServiceURL
     * @throws InvalidArgumentException
     * @return void
-     */
    public function setAssertionConsumerServiceURL(string $assertionConsumerServiceURL) : void
    {
        if (!filter_var($assertionConsumerServiceURL, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('AssertionConsumerServiceURL is not a valid URL.');
        }
        $this->AssertionConsumerServiceURL = $assertionConsumerServiceURL;
    }


    /**
     * Convert this ECP Response to XML.
     *
     * @param \DOMElement $parent The element we should append this element to.
     * @throws InvalidArgumentException
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        $acs = $this->getAssertionConsumerServiceURL();
        if (!isset($acs)) {
            throw new InvalidArgumentException("AssertionConsumerServiceURL must be a string");
        }

        $doc = $parent->ownerDocument;
        $response = $doc->createElementNS(Constants::NS_ECP, 'ecp:Response');

        $parent->appendChild($response);

        $response->setAttributeNS(Constants::NS_SOAP, 'SOAP-ENV:mustUnderstand', '1');
        $response->setAttributeNS(Constants::NS_SOAP, 'SOAP-ENV:actor', 'http://schemas.xmlsoap.org/soap/actor/next');
        $response->setAttribute('AssertionConsumerServiceURL', $acs);

        return $response;
    }
}
