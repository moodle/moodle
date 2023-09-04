<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;
use Webmozart\Assert\Assert;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\Chunk;

/**
 * Class representing SAML 2 Organization element.
 *
 * @package SimpleSAMLphp
 */
class Organization
{
    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var array
     */
    private $Extensions = [];

    /**
     * The OrganizationName, as an array of language => translation.
     *
     * @var array
     */
    private $OrganizationName = [];

    /**
     * The OrganizationDisplayName, as an array of language => translation.
     *
     * @var array
     */
    private $OrganizationDisplayName = [];

    /**
     * The OrganizationURL, as an array of language => translation.
     *
     * @var array
     */
    private $OrganizationURL = [];


    /**
     * Initialize an Organization element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        $this->Extensions = Extensions::getList($xml);

        $this->OrganizationName = Utils::extractLocalizedStrings($xml, Constants::NS_MD, 'OrganizationName');
        if (empty($this->OrganizationName)) {
            $this->OrganizationName = ['invalid' => ''];
        }

        $this->OrganizationDisplayName = Utils::extractLocalizedStrings(
            $xml,
            Constants::NS_MD,
            'OrganizationDisplayName'
        );
        if (empty($this->OrganizationDisplayName)) {
            $this->OrganizationDisplayName = ['invalid' => ''];
        }

        $this->OrganizationURL = Utils::extractLocalizedStrings($xml, Constants::NS_MD, 'OrganizationURL');
        if (empty($this->OrganizationURL)) {
            $this->OrganizationURL = ['invalid' => ''];
        }
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
     * Collect the value of the OrganizationName property.
     *
     * @return string[]
     */
    public function getOrganizationName() : array
    {
        return $this->OrganizationName;
    }


    /**
     * Set the value of the OrganizationName property.
     *
     * @param array $organizationName
     * @return void
     */
    public function setOrganizationName(array $organizationName) : void
    {
        $this->OrganizationName = $organizationName;
    }


    /**
     * Collect the value of the OrganizationDisplayName property.
     *
     * @return string[]
     */
    public function getOrganizationDisplayName() : array
    {
        return $this->OrganizationDisplayName;
    }


    /**
     * Set the value of the OrganizationDisplayName property.
     *
     * @param array $organizationDisplayName
     * @return void
     */
    public function setOrganizationDisplayName(array $organizationDisplayName) : void
    {
        $this->OrganizationDisplayName = $organizationDisplayName;
    }


    /**
     * Collect the value of the OrganizationURL property.
     *
     * @return string[]
     */
    public function getOrganizationURL() : array
    {
        return $this->OrganizationURL;
    }


    /**
     * Set the value of the OrganizationURL property.
     *
     * @param array $organizationURL
     * @return void
     */
    public function setOrganizationURL(array $organizationURL) : void
    {
        $this->OrganizationURL = $organizationURL;
    }


    /**
     * Convert this Organization to XML.
     *
     * @param  \DOMElement $parent The element we should add this organization to.
     * @return \DOMElement This Organization-element.
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->OrganizationName);
        Assert::notEmpty($this->OrganizationDisplayName);
        Assert::notEmpty($this->OrganizationURL);

        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(Constants::NS_MD, 'md:Organization');
        $parent->appendChild($e);

        Extensions::addList($e, $this->Extensions);

        Utils::addStrings($e, Constants::NS_MD, 'md:OrganizationName', true, $this->OrganizationName);
        Utils::addStrings($e, Constants::NS_MD, 'md:OrganizationDisplayName', true, $this->OrganizationDisplayName);
        Utils::addStrings($e, Constants::NS_MD, 'md:OrganizationURL', true, $this->OrganizationURL);

        return $e;
    }
}
