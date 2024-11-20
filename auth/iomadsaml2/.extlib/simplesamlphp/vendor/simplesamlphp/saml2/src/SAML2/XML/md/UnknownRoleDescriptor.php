<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\XML\Chunk;

/**
 * Class representing unknown RoleDescriptors.
 *
 * @package SimpleSAMLphp
 */
class UnknownRoleDescriptor extends RoleDescriptor
{
    /**
     * This RoleDescriptor as XML
     *
     * @var \SAML2\XML\Chunk
     */
    private $xml;


    /**
     * Initialize an unknown RoleDescriptor.
     *
     * @param \DOMElement $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml)
    {
        parent::__construct('md:RoleDescriptor', $xml);

        $this->xml = new Chunk($xml);
    }


    /**
     * Add this RoleDescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this RoleDescriptor to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        return $this->xml->toXML($parent);
    }
}
