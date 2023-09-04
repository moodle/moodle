<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\saml\Attribute;

/**
 * Class representing SAML 2 metadata RequestedAttribute.
 *
 * @package SimpleSAMLphp
 */
class RequestedAttribute extends Attribute
{
    /**
     * Whether this attribute is required.
     *
     * @var bool|null
     */
    private $isRequired = null;


    /**
     * Initialize an RequestedAttribute.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct($xml);

        if ($xml === null) {
            return;
        }

        $this->isRequired = Utils::parseBoolean($xml, 'isRequired', null);
    }


    /**
     * Collect the value of the isRequired-property
     *
     * @return bool|null
     */
    public function getIsRequired() : ?bool
    {
        return $this->isRequired;
    }


    /**
     * Set the value of the isRequired-property
     *
     * @param bool|null $flag
     * @return void
     */
    public function setIsRequired(bool $flag = null) : void
    {
        $this->isRequired = $flag;
    }


    /**
     * Convert this RequestedAttribute to XML.
     *
     * @param \DOMElement $parent The element we should append this RequestedAttribute to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        $e = $this->toXMLInternal($parent, Constants::NS_MD, 'md:RequestedAttribute');

        if (is_bool($this->isRequired)) {
            $e->setAttribute('isRequired', $this->isRequired ? 'true' : 'false');
        }

        return $e;
    }
}
