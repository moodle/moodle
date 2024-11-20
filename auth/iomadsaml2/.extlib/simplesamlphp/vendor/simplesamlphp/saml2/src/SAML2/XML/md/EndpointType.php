<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;

/**
 * Class representing SAML 2 EndpointType.
 *
 * @package SimpleSAMLphp
 */
class EndpointType
{
    /**
     * The binding for this endpoint.
     *
     * @var string|null
     */
    private $Binding = null;

    /**
     * The URI to this endpoint.
     *
     * @var string|null
     */
    private $Location = null;

    /**
     * The URI where responses can be delivered.
     *
     * @var string|null
     */
    private $ResponseLocation = null;

    /**
     * Extra (namespace qualified) attributes.
     *
     * @var array
     */
    private $attributes = [];


    /**
     * Initialize an EndpointType.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('Binding')) {
            throw new \Exception('Missing Binding on '.$xml->tagName);
        }
        $this->Binding = $xml->getAttribute('Binding');

        if (!$xml->hasAttribute('Location')) {
            throw new \Exception('Missing Location on '.$xml->tagName);
        }
        $this->Location = $xml->getAttribute('Location');

        if ($xml->hasAttribute('ResponseLocation')) {
            $this->ResponseLocation = $xml->getAttribute('ResponseLocation');
        }

        foreach ($xml->attributes as $a) {
            if ($a->namespaceURI === null) {
                continue; /* Not namespace-qualified -- skip. */
            }
            $fullName = '{'.$a->namespaceURI.'}'.$a->localName;
            $this->attributes[$fullName] = [
                'qualifiedName' => $a->nodeName,
                'namespaceURI' => $a->namespaceURI,
                'value' => $a->value,
            ];
        }
    }


    /**
     * Check if a namespace-qualified attribute exists.
     *
     * @param string $namespaceURI The namespace URI.
     * @param string $localName The local name.
     * @return bool true if the attribute exists, false if not.
     */
    public function hasAttributeNS(string $namespaceURI, string $localName) : bool
    {
        $fullName = '{'.$namespaceURI.'}'.$localName;

        return isset($this->attributes[$fullName]);
    }


    /**
     * Get a namespace-qualified attribute.
     *
     * @param string $namespaceURI The namespace URI.
     * @param string $localName The local name.
     * @return string The value of the attribute, or an empty string if the attribute does not exist.
     */
    public function getAttributeNS(string $namespaceURI, string $localName) : string
    {
        $fullName = '{'.$namespaceURI.'}'.$localName;
        if (!isset($this->attributes[$fullName])) {
            return '';
        }

        return $this->attributes[$fullName]['value'];
    }


    /**
     * Get a namespace-qualified attribute.
     *
     * @param string $namespaceURI  The namespace URI.
     * @param string $qualifiedName The local name.
     * @param string $value The attribute value.
     * @throws \Exception
     * @return void
     */
    public function setAttributeNS(string $namespaceURI, string $qualifiedName, string $value) : void
    {
        $name = explode(':', $qualifiedName, 2);
        if (count($name) < 2) {
            throw new \Exception('Not a qualified name.');
        }
        $localName = $name[1];

        $fullName = '{'.$namespaceURI.'}'.$localName;
        $this->attributes[$fullName] = [
            'qualifiedName' => $qualifiedName,
            'namespaceURI' => $namespaceURI,
            'value' => $value,
        ];
    }


    /**
     * Remove a namespace-qualified attribute.
     *
     * @param string $namespaceURI The namespace URI.
     * @param string $localName The local name.
     * @return void
     */
    public function removeAttributeNS(string $namespaceURI, string $localName) : void
    {
        $fullName = '{'.$namespaceURI.'}'.$localName;
        unset($this->attributes[$fullName]);
    }


    /**
     * Collect the value of the Binding property.
     *
     * @return string|null
     */
    public function getBinding() : ?string
    {
        return $this->Binding;
    }


    /**
     * Set the value of the Binding property.
     *
     * @param string $binding
     * @return void
     */
    public function setBinding(string $binding) : void
    {
        $this->Binding = $binding;
    }


    /**
     * Collect the value of the Location property.
     *
     * @return string|null
     */
    public function getLocation() : ?string
    {
        return $this->Location;
    }


    /**
     * Set the value of the Location-property.
     * @param string|null $location
     * @return void
     */
    public function setLocation(string $location = null) : void
    {
        $this->Location = $location;
    }


    /**
     * Collect the value of the ResponseLocation property.
     *
     * @return string|null
     */
    public function getResponseLocation() : ?string
    {
        return $this->ResponseLocation;
    }


    /**
     * Set the value of the ResponseLocation property.
     *
     * @param string|null $responseLocation
     * @return void
     */
    public function setResponseLocation(string $responseLocation = null) : void
    {
        $this->ResponseLocation = $responseLocation;
    }


    /**
     * Add this endpoint to an XML element.
     *
     * @param \DOMElement $parent The element we should append this endpoint to.
     * @param string $name The name of the element we should create.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent, string $name) : DOMElement
    {
        $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, $name);
        $parent->appendChild($e);

        if (empty($this->Binding)) {
            throw new \Exception('Cannot convert endpoint to XML without a Binding set.');
        }
        if (empty($this->Location)) {
            throw new \Exception('Cannot convert endpoint to XML without a Location set.');
        }

        $e->setAttribute('Binding', $this->Binding);
        $e->setAttribute('Location', $this->Location);

        if ($this->ResponseLocation !== null) {
            $e->setAttribute('ResponseLocation', $this->ResponseLocation);
        }

        foreach ($this->attributes as $a) {
            $e->setAttributeNS($a['namespaceURI'], $a['qualifiedName'], $a['value']);
        }

        return $e;
    }
}
