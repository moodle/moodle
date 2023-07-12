<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Utils;

/**
 * Class representing SAML 2 IndexedEndpointType.
 *
 * @package SimpleSAMLphp
 */
class IndexedEndpointType extends EndpointType
{
    /**
     * The index for this endpoint.
     *
     * @var int
     */
    private $index = 0;

    /**
     * Whether this endpoint is the default.
     *
     * @var bool|null
     */
    private $isDefault = null;


    /**
     * Initialize an IndexedEndpointType.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('index')) {
            throw new \Exception('Missing index on '.$xml->tagName);
        }
        $this->index = intval($xml->getAttribute('index'));

        $this->isDefault = Utils::parseBoolean($xml, 'isDefault', null);
    }


    /**
     * Collect the value of the index property.
     *
     * @return int
     */
    public function getIndex() : int
    {
        return $this->index;
    }


    /**
     * Set the value of the index property.
     *
     * @param int $index
     * @return void
     */
    public function setIndex(int $index) : void
    {
        $this->index = $index;
    }


    /**
     * Collect the value of the isDefault property.
     *
     * @return bool|null
     */
    public function getIsDefault() : ?bool
    {
        return $this->isDefault;
    }


    /**
     * Set the value of the isDefault property.
     *
     * @param bool|null $flag
     * @return void
     */
    public function setIsDefault(bool $flag = null) : void
    {
        $this->isDefault = $flag;
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
        $e = parent::toXML($parent, $name);
        $e->setAttribute('index', strval($this->index));

        if (is_bool($this->isDefault)) {
            $e->setAttribute('isDefault', $this->isDefault ? 'true' : 'false');
        }

        return $e;
    }
}
