<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use DOMNode;

/**
 * The \SAML2\ArtifactResponse, is the response to the \SAML2\ArtifactResolve.
 *
 * @author Danny Bollaert, UGent AS. <danny.bollaert@ugent.be>
 * @package SimpleSAMLphp
 */
class ArtifactResponse extends StatusResponse
{
    /**
     * The \DOMElement with the message the artifact refers
     * to, or null if we don't refer to any artifact.
     *
     * @var \DOMElement|null
     */
    private $any = null;


    /**
     * Constructor for SAML 2 ArtifactResponse.
     *
     * @param \DOMElement|null $xml The input assertion.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('ArtifactResponse', $xml);

        if (!is_null($xml)) {
            $status = Utils::xpQuery($xml, './saml_protocol:Status');
            $status = $status[0];

            /** @psalm-suppress RedundantCondition */
            for ($any = $status->nextSibling; $any instanceof DOMNode; $any = $any->nextSibling) {
                if ($any instanceof DOMElement) {
                    $this->any = $any;
                    break;
                }
                /* Ignore comments and text nodes. */
            }
        }
    }


    /**
     * @param \DOMElement|null $any
     * @return void
     */
    public function setAny(DOMElement $any = null) : void
    {
        $this->any = $any;
    }


    /**
     * @return \DOMElement|null
     */
    public function getAny() : ?DOMElement
    {
        return $this->any;
    }


    /**
     * Convert the response message to an XML element.
     *
     * @return \DOMElement This response.
     */
    public function toUnsignedXML() : DOMElement
    {
        $root = parent::toUnsignedXML();
        if (isset($this->any)) {
            $node = $root->ownerDocument->importNode($this->any, true);
            $root->appendChild($node);
        }

        return $root;
    }
}
