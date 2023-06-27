<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;
use SAML2\Utils;

/**
 * Class representing SAML 2 SSODescriptorType.
 *
 * @package SimpleSAMLphp
 */
abstract class SSODescriptorType extends RoleDescriptor
{
    /**
     * List of ArtifactResolutionService endpoints.
     *
     * Array with IndexedEndpointType objects.
     *
     * @var \SAML2\XML\md\IndexedEndpointType[]
     */
    private $ArtifactResolutionService = [];

    /**
     * List of SingleLogoutService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $SingleLogoutService = [];

    /**
     * List of ManageNameIDService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $ManageNameIDService = [];

    /**
     * List of supported NameID formats.
     *
     * Array of strings.
     *
     * @var string[]
     */
    private $NameIDFormat = [];


    /**
     * Initialize a SSODescriptor.
     *
     * @param string $elementName The name of this element.
     * @param \DOMElement|null $xml The XML element we should load.
     */
    protected function __construct(string $elementName, DOMElement $xml = null)
    {
        parent::__construct($elementName, $xml);

        if ($xml === null) {
            return;
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:ArtifactResolutionService') as $ep) {
            $this->addArtifactResolutionService(new IndexedEndpointType($ep));
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:SingleLogoutService') as $ep) {
            $this->addSingleLogoutService(new EndpointType($ep));
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:ManageNameIDService') as $ep) {
            $this->addManageNameIDService(new EndpointType($ep));
        }

        $this->setNameIDFormat(Utils::extractStrings($xml, Constants::NS_MD, 'NameIDFormat'));
    }


    /**
     * Collect the value of the ArtifactResolutionService-property
     *
     * @return \SAML2\XML\md\IndexedEndpointType[]
     */
    public function getArtifactResolutionService() : array
    {
        return $this->ArtifactResolutionService;
    }


    /**
     * Set the value of the ArtifactResolutionService-property
     *
     * @param \SAML2\XML\md\IndexedEndpointType[] $artifactResolutionService
     * @return void
     */
    public function setArtifactResolutionService(array $artifactResolutionService) : void
    {
        $this->ArtifactResolutionService = $artifactResolutionService;
    }


    /**
     * Add the value to the ArtifactResolutionService-property
     *
     * @param \SAML2\XML\md\IndexedEndpointType $artifactResolutionService
     * @return void
     */
    public function addArtifactResolutionService(IndexedEndpointType $artifactResolutionService) : void
    {
        $this->ArtifactResolutionService[] = $artifactResolutionService;
    }


    /**
     * Collect the value of the SingleLogoutService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getSingleLogoutService() : array
    {
        return $this->SingleLogoutService;
    }


    /**
     * Set the value of the SingleLogoutService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $singleLogoutService
     * @return void
     */
    public function setSingleLogoutService(array $singleLogoutService) : void
    {
        $this->SingleLogoutService = $singleLogoutService;
    }


    /**
     * Add the value to the SingleLogoutService-property
     *
     * @param \SAML2\XML\md\EndpointType $singleLogoutService
     * @return void
     */
    public function addSingleLogoutService(EndpointType $singleLogoutService) : void
    {
        $this->SingleLogoutService[] = $singleLogoutService;
    }


    /**
     * Collect the value of the ManageNameIDService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getManageNameIDService() : array
    {
        return $this->ManageNameIDService;
    }


    /**
     * Set the value of the ManageNameIDService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $manageNameIDService
     * @return void
     */
    public function setManageNameIDService(array $manageNameIDService) : void
    {
        $this->ManageNameIDService = $manageNameIDService;
    }


    /**
     * Add the value to the ManageNameIDService-property
     *
     * @param \SAML2\XML\md\EndpointType $manageNameIDService
     * @return void
     */
    public function addManageNameIDService(EndpointType $manageNameIDService) : void
    {
        $this->ManageNameIDService[] = $manageNameIDService;
    }


    /**
     * Collect the value of the NameIDFormat-property
     *
     * @return string[]
     */
    public function getNameIDFormat() : array
    {
        return $this->NameIDFormat;
    }


    /**
     * Set the value of the NameIDFormat-property
     *
     * @param string[] $nameIDFormat
     * @return void
     */
    public function setNameIDFormat(array $nameIDFormat) : void
    {
        $this->NameIDFormat = $nameIDFormat;
    }


    /**
     * Add this SSODescriptorType to an EntityDescriptor.
     *
     * @param  \DOMElement $parent The EntityDescriptor we should append this SSODescriptorType to.
     * @return \DOMElement The generated SSODescriptor DOMElement.
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        $e = parent::toXML($parent);

        foreach ($this->ArtifactResolutionService as $ep) {
            $ep->toXML($e, 'md:ArtifactResolutionService');
        }

        foreach ($this->SingleLogoutService as $ep) {
            $ep->toXML($e, 'md:SingleLogoutService');
        }

        foreach ($this->ManageNameIDService as $ep) {
            $ep->toXML($e, 'md:ManageNameIDService');
        }

        Utils::addStrings($e, Constants::NS_MD, 'md:NameIDFormat', false, $this->NameIDFormat);

        return $e;
    }
}
