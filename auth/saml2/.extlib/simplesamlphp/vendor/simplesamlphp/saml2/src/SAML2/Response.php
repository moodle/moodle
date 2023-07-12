<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;

/**
 * Class for SAML 2 Response messages.
 *
 * @package SimpleSAMLphp
 */
class Response extends StatusResponse
{
    /**
     * The assertions in this response.
     *
     * @var (Assertion|EncryptedAssertion)[]
     */
    private $assertions;


    /**
     * Constructor for SAML 2 response messages.
     *
     * @param \DOMElement|null $xml The input message.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('Response', $xml);

        $this->assertions = [];

        if ($xml === null) {
            return;
        }

        foreach ($xml->childNodes as $node) {
            if ($node->namespaceURI !== Constants::NS_SAML) {
                continue;
            } else if (!($node instanceof DOMElement)) {
                continue;
            }

            if ($node->localName === 'Assertion') {
                $this->assertions[] = new Assertion($node);
            } elseif ($node->localName === 'EncryptedAssertion') {
                $this->assertions[] = new EncryptedAssertion($node);
            }
        }
    }


    /**
     * Retrieve the assertions in this response.
     *
     * @return \SAML2\Assertion[]|\SAML2\EncryptedAssertion[]
     */
    public function getAssertions() : array
    {
        return $this->assertions;
    }


    /**
     * Set the assertions that should be included in this response.
     *
     * @param \SAML2\Assertion[]|\SAML2\EncryptedAssertion[] $assertions The assertions.
     * @return void
     */
    public function setAssertions(array $assertions) : void
    {
        $this->assertions = $assertions;
    }


    /**
     * Convert the response message to an XML element.
     *
     * @return \DOMElement This response.
     */
    public function toUnsignedXML() : DOMElement
    {
        $root = parent::toUnsignedXML();

        /** @var \SAML2\Assertion|\SAML2\EncryptedAssertion $assertion */
        foreach ($this->assertions as $assertion) {
            $assertion->toXML($root);
        }

        return $root;
    }
}
