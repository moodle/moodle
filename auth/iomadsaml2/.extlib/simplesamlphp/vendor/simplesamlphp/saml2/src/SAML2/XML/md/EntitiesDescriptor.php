<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;
use Webmozart\Assert\Assert;

use SAML2\Constants;
use SAML2\DOMDocumentFactory;
use SAML2\SignedElementHelper;
use SAML2\Utils;
use SAML2\XML\Chunk;

/**
 * Class representing SAML 2 EntitiesDescriptor element.
 *
 * @package SimpleSAMLphp
 */
class EntitiesDescriptor extends SignedElementHelper
{
    /**
     * The ID of this element.
     *
     * @var string|null
     */
    private $ID = null;

    /**
     * The name of this entity collection.
     *
     * @var string|null
     */
    private $Name = null;

    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var array
     */
    private $Extensions = [];

    /**
     * Child EntityDescriptor and EntitiesDescriptor elements.
     *
     * @var (\SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor)[]
     */
    private $children = [];


    /**
     * Initialize an EntitiesDescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if ($xml->hasAttribute('ID')) {
            $this->ID = $xml->getAttribute('ID');
        }
        if ($xml->hasAttribute('validUntil')) {
            $this->validUntil = Utils::xsDateTimeToTimestamp($xml->getAttribute('validUntil'));
        }
        if ($xml->hasAttribute('cacheDuration')) {
            $this->cacheDuration = $xml->getAttribute('cacheDuration');
        }
        if ($xml->hasAttribute('Name')) {
            $this->Name = $xml->getAttribute('Name');
        }

        $this->Extensions = Extensions::getList($xml);

        /** @var \DOMElement $node */
        foreach (Utils::xpQuery($xml, './saml_metadata:EntityDescriptor|./saml_metadata:EntitiesDescriptor') as $node) {
            if ($node->localName === 'EntityDescriptor') {
                $this->children[] = new EntityDescriptor($node);
            } else {
                $this->children[] = new EntitiesDescriptor($node);
            }
        }
    }


    /**
     * Collect the value of the Name property.
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->Name;
    }


    /**
     * Set the value of the Name property.
     *
     * @param string|null $name
     * @return void
     */
    public function setName(string $name = null) : void
    {
        $this->Name = $name;
    }


    /**
     * Collect the value of the ID property.
     *
     * @return string|null
     */
    public function getID() : ?string
    {
        return $this->ID;
    }


    /**
     * Set the value of the ID property.
     *
     * @param string|null $Id
     * @return void
     */
    public function setID(string $Id = null) : void
    {
        $this->ID = $Id;
    }


    /**
     * Collect the value of the validUntil-property
     * @return int|null
     */
    public function getValidUntil() : ?int
    {
        return $this->validUntil;
    }


    /**
     * Set the value of the validUntil-property
     * @param int|null $validUntil
     * @return void
     */
    public function setValidUntil(int $validUntil = null) : void
    {
        $this->validUntil = $validUntil;
    }


    /**
     * Collect the value of the cacheDuration-property
     * @return string|null
     */
    public function getCacheDuration() : ?string
    {
        return $this->cacheDuration;
    }


    /**
     * Set the value of the cacheDuration-property
     * @param string|null $cacheDuration
     * @return void
     */
    public function setCacheDuration(string $cacheDuration = null) : void
    {
        $this->cacheDuration = $cacheDuration;
    }


    /**
     * Collect the value of the Extensions property.
     *
     * @return \SAML2\XML\Chunk[]
     */
    public function getExtensions() : array
    {
        return $this->Extensions;
    }


    /**
     * Set the value of the Extensions property.
     *
     * @param array $extensions
     * @return void
     */
    public function setExtensions(array $extensions) : void
    {
        $this->Extensions = $extensions;
    }


    /**
     * Add an Extension.
     *
     * @param \SAML2\XML\Chunk $extensions The Extensions
     * @return void
     */
    public function addExtension(Extensions $extension) : void
    {
        $this->Extensions[] = $extension;
    }


    /**
     * Collect the value of the children property.
     *
     * @return (\SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor)[]
     */
    public function getChildren() : array
    {
        return $this->children;
    }


    /**
     * Set the value of the childen property.
     *
     * @param array $children
     * @return void
     */
    public function setChildren(array $children) : void
    {
        $this->children = $children;
    }


    /**
     * Add the value to the children property.
     *
     * @param \SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor $child
     * @return void
     */
    public function addChildren($child) : void
    {
        Assert::isInstanceOfAny($child, [EntityDescriptor::class, EntitiesDescriptor::class]);
        $this->children[] = $child;
    }


    /**
     * Convert this EntitiesDescriptor to XML.
     *
     * @param \DOMElement|null $parent The EntitiesDescriptor we should append this EntitiesDescriptor to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent = null) : DOMElement
    {
        if ($parent === null) {
            $doc = DOMDocumentFactory::create();
            $e = $doc->createElementNS(Constants::NS_MD, 'md:EntitiesDescriptor');
            $doc->appendChild($e);
        } else {
            $e = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:EntitiesDescriptor');
            $parent->appendChild($e);
        }

        if ($this->ID !== null) {
            $e->setAttribute('ID', $this->ID);
        }

        if ($this->validUntil !== null) {
            $e->setAttribute('validUntil', gmdate('Y-m-d\TH:i:s\Z', $this->validUntil));
        }

        if ($this->cacheDuration !== null) {
            $e->setAttribute('cacheDuration', $this->cacheDuration);
        }

        if ($this->Name !== null) {
            $e->setAttribute('Name', $this->Name);
        }

        Extensions::addList($e, $this->Extensions);

        /** @var \SAML2\XML\md\EntityDescriptor|\SAML2\XML\md\EntitiesDescriptor $node */
        foreach ($this->children as $node) {
            $node->toXML($e);
        }

        $this->signElement($e, $e->firstChild);

        return $e;
    }
}
