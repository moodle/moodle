<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;

/**
 * The Artifact is part of the SAML 2.0 IdP code, and it builds an artifact object.
 * I am using strings, because I find them easier to work with.
 * I want to use this, to be consistent with the other saml2_requests
 *
 * @author Danny Bollaert, UGent AS. <danny.bollaert@ugent.be>
 * @package SimpleSAMLphp
 */
class ArtifactResolve extends Request
{
    /** @var string */
    private $artifact;


    /**
     * Constructor for SAML 2 ArtifactResolve.
     *
     * @param \DOMElement|null $xml The input assertion.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('ArtifactResolve', $xml);

        if (!is_null($xml)) {
            $results = Utils::xpQuery($xml, './saml_protocol:Artifact');
            $this->artifact = $results[0]->textContent;
        }
    }

    /**
     * Retrieve the Artifact in this response.
     *
     * @return string artifact.
     */
    public function getArtifact() : string
    {
        return $this->artifact;
    }


    /**
     * Set the artifact that should be included in this response.
     *
     * @param string $artifact
     * @return void
     */
    public function setArtifact(string $artifact) : void
    {
        $this->artifact = $artifact;
    }


    /**
     * Convert the response message to an XML element.
     *
     * @return \DOMElement This response.
     */
    public function toUnsignedXML() : DOMElement
    {
        $root = parent::toUnsignedXML();
        $artifactelement = $this->document->createElementNS(Constants::NS_SAMLP, 'Artifact', $this->artifact);
        $root->appendChild($artifactelement);

        return $root;
    }
}
