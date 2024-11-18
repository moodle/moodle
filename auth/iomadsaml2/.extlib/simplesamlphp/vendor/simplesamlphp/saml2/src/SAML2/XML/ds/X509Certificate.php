<?php

declare(strict_types=1);

namespace SAML2\XML\ds;

use DOMElement;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

use SAML2\Utils;

/**
 * Class representing a ds:X509Certificate element.
 *
 * @package SimpleSAMLphp
 */
class X509Certificate
{
    /**
     * The base64-encoded certificate.
     *
     * @var string
     */
    private $certificate;


    /**
     * Initialize an X509Certificate element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        $this->setCertificate($xml->textContent);
    }


    /**
     * Collect the value of the certificate-property
     *
     * @return string
     */
    public function getCertificate() : string
    {
        return str_replace(["\r\n", "\r", "\n", "\t", ' '], '', $this->certificate);
    }


    /**
     * Set the value of the certificate-property
     *
     * @param string $certificate
     * @return void
     */
    public function setCertificate(string $certificate) : void
    {
        $this->certificate = $certificate;
    }


    /**
     * Convert this X509Certificate element to XML.
     *
     * @param \DOMElement $parent The element we should append this X509Certificate element to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        return Utils::addString($parent, XMLSecurityDSig::XMLDSIGNS, 'ds:X509Certificate', $this->getCertificate());
    }
}
