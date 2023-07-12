<?php

declare(strict_types=1);

namespace SAML2\XML\mdui;

use DOMElement;

use SAML2\Utils;
use SAML2\XML\Chunk;

/**
 * Class for handling the metadata extensions for login and discovery user interface
 *
 * @link: http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-metadata-ui/v1.0/sstc-saml-metadata-ui-v1.0.pdf
 * @package SimpleSAMLphp
 */
class DiscoHints
{
    /**
     * Array with child elements.
     *
     * The elements can be any of the other \SAML2\XML\mdui\* elements.
     *
     * @var \SAML2\XML\Chunk[]
     */
    private $children = [];

    /**
     * The IPHint, as an array of strings.
     *
     * @var string[]
     */
    private $IPHint = [];

    /**
     * The DomainHint, as an array of strings.
     *
     * @var string[]
     */
    private $DomainHint = [];

    /**
     * The GeolocationHint, as an array of strings.
     *
     * @var string[]
     */
    private $GeolocationHint = [];


    /**
     * Create a DiscoHints element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        $this->IPHint = Utils::extractStrings($xml, Common::NS, 'IPHint');
        $this->DomainHint = Utils::extractStrings($xml, Common::NS, 'DomainHint');
        $this->GeolocationHint = Utils::extractStrings($xml, Common::NS, 'GeolocationHint');

        /** @var \DOMElement $node */
        foreach (Utils::xpQuery($xml, "./*[namespace-uri()!='".Common::NS."']") as $node) {
            $this->children[] = new Chunk($node);
        }
    }


    /**
     * Collect the value of the IPHint-property
     *
     * @return string[]
     */
    public function getIPHint() : array
    {
        return $this->IPHint;
    }


    /**
     * Set the value of the IPHint-property
     *
     * @param string[] $hints
     * @return void
     */
    public function setIPHint(array $hints) : void
    {
        $this->IPHint = $hints;
    }


    /**
     * Collect the value of the DomainHint-property
     *
     * @return string[]
     */
    public function getDomainHint() : array
    {
        return $this->DomainHint;
    }


    /**
     * Set the value of the DomainHint-property
     *
     * @param string[] $hints
     * @return void
     */
    public function setDomainHint(array $hints) : void
    {
        $this->DomainHint = $hints;
    }


    /**
     * Collect the value of the GeolocationHint-property
     *
     * @return string[]
     */
    public function getGeolocationHint() : array
    {
        return $this->GeolocationHint;
    }


    /**
     * Set the value of the GeolocationHint-property
     *
     * @param string[] $hints
     * @return void
     */
    public function setGeolocationHint(array $hints) : void
    {
        $this->GeolocationHint = $hints;
    }


    /**
     * Collect the value of the children-property
     *
     * @return \SAML2\XML\Chunk[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }


    /**
     * Set the value of the childen-property
     *
     * @param array $children
     * @return void
     */
    public function setChildren(array $children) : void
    {
        $this->children = $children;
    }


    /**
     * Add the value to the children-property
     *
     * @param \SAML2\XML\Chunk $child
     * @return void
     */
    public function addChildren(Chunk $child) : void
    {
        $this->children[] = $child;
    }


    /**
     * Convert this DiscoHints to XML.
     *
     * @param \DOMElement $parent The element we should append to.
     * @return \DOMElement|null
     */
    public function toXML(DOMElement $parent) : ?DOMElement
    {
        if (!empty($this->IPHint)
         || !empty($this->DomainHint)
         || !empty($this->GeolocationHint)
         || !empty($this->children)
        ) {
            $doc = $parent->ownerDocument;

            $e = $doc->createElementNS(Common::NS, 'mdui:DiscoHints');
            $parent->appendChild($e);

            foreach ($this->getChildren() as $child) {
                $child->toXML($e);
            }

            Utils::addStrings($e, Common::NS, 'mdui:IPHint', false, $this->IPHint);
            Utils::addStrings($e, Common::NS, 'mdui:DomainHint', false, $this->DomainHint);
            Utils::addStrings($e, Common::NS, 'mdui:GeolocationHint', false, $this->GeolocationHint);

            return $e;
        }

        return null;
    }
}
