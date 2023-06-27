<?php

declare(strict_types=1);

namespace SAML2\XML;

use DOMElement;

use SAML2\DOMDocumentFactory;
use SAML2\Utils;
use Serializable;

/**
 * Serializable class used to hold an XML element.
 *
 * @package SimpleSAMLphp
 */
class Chunk implements Serializable
{
    /**
     * The localName of the element.
     *
     * @var string
     */
    private $localName;

    /**
     * The namespaceURI of this element.
     *
     * @var string|null
     */
    private $namespaceURI;

    /**
     * The \DOMElement we contain.
     *
     * @var \DOMElement
     */
    private $xml;


    /**
     * Create a XMLChunk from a copy of the given \DOMElement.
     *
     * @param \DOMElement $xml The element we should copy.
     */
    public function __construct(DOMElement $xml)
    {
        $this->localName = $xml->localName;
        $this->namespaceURI = $xml->namespaceURI;

        $this->xml = Utils::copyElement($xml);
    }


    /**
     * Get this \DOMElement.
     *
     * @return \DOMElement This element.
     */
    public function getXML() : DOMElement
    {
        return $this->xml;
    }


    /**
     * Append this XML element to a different XML element.
     *
     * @param  \DOMElement $parent The element we should append this element to.
     * @return \DOMElement The new element.
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        return Utils::copyElement($this->xml, $parent);
    }


    /**
     * Collect the value of the localName-property
     *
     * @return string
     */
    public function getLocalName() : string
    {
        return $this->localName;
    }


    /**
     * Set the value of the localName-property
     *
     * @param string $localName
     * @return void
     */
    public function setLocalName(string $localName) : void
    {
        $this->localName = $localName;
    }


    /**
     * Collect the value of the namespaceURI-property
     *
     * @return string|null
     */
    public function getNamespaceURI() : ?string
    {
        return $this->namespaceURI;
    }


    /**
     * Set the value of the namespaceURI-property
     *
     * @param string|null $namespaceURI
     * @return void
     */
    public function setNamespaceURI(string $namespaceURI = null) : void
    {
        $this->namespaceURI = $namespaceURI;
    }


    /**
     * Serialize this XML chunk.
     *
     * @return string The serialized chunk.
     */
    public function serialize() : string
    {
        return serialize($this->xml->ownerDocument->saveXML($this->xml));
    }


    /**
     * Un-serialize this XML chunk.
     *
     * @param string $serialized The serialized chunk.
     * @return void
     *
     * Type hint not possible due to upstream method signature
     */
    public function unserialize($serialized) : void
    {
        $doc = DOMDocumentFactory::fromString(unserialize($serialized));
        $this->xml = $doc->documentElement;
        $this->setLocalName($this->xml->localName);
        $this->setNamespaceURI($this->xml->namespaceURI);
    }



    /**
     * Serialize this XML chunk.
     *
     * This method will be invoked by any calls to serialize().
     *
     * @return array The serialized representation of this XML object.
     */
    public function __serialize(): array
    {
        $xml = $this->getXML();
        /** @psalm-var \DOMDocument $xml->ownerDocument */
        return [$xml->ownerDocument->saveXML($xml)];
    }


    /**
     * Unserialize an XML object and load it..
     *
     * This method will be invoked by any calls to unserialize(), allowing us to restore any data that might not
     * be serializable in its original form (e.g.: DOM objects).
     *
     * @param array $vars The XML object that we want to restore.
     */
    public function __unserialize(array $serialized): void
    {
        $xml = new self(
            DOMDocumentFactory::fromString(array_pop($serialized))->documentElement
        );

        $vars = get_object_vars($xml);
        foreach ($vars as $k => $v) {
            $this->$k = $v;
        }
    }
}
