<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;
use Webmozart\Assert\Assert;

use SAML2\Constants;
use SAML2\Utils;

/**
 * Class representing SAML 2 metadata AuthnAuthorityDescriptor.
 *
 * @package SimpleSAMLphp
 */
class AuthnAuthorityDescriptor extends RoleDescriptor
{
    /**
     * List of AuthnQueryService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $AuthnQueryService = [];

    /**
     * List of AssertionIDRequestService endpoints.
     *
     * Array with EndpointType objects.
     *
     * @var \SAML2\XML\md\EndpointType[]
     */
    private $AssertionIDRequestService = [];

    /**
     * List of supported NameID formats.
     *
     * Array of strings.
     *
     * @var string[]
     */
    private $NameIDFormat = [];


    /**
     * Initialize an IDPSSODescriptor.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('md:AuthnAuthorityDescriptor', $xml);

        if ($xml === null) {
            return;
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:AuthnQueryService') as $ep) {
            $this->addAuthnQueryService(new EndpointType($ep));
        }
        if ($this->getAuthnQueryService() === []) {
            throw new \Exception('Must have at least one AuthnQueryService in AuthnAuthorityDescriptor.');
        }

        /** @var \DOMElement $ep */
        foreach (Utils::xpQuery($xml, './saml_metadata:AssertionIDRequestService') as $ep) {
            $this->addAssertionIDRequestService(new EndpointType($ep));
        }

        $this->setNameIDFormat(Utils::extractStrings($xml, Constants::NS_MD, 'NameIDFormat'));
    }


    /**
     * Collect the value of the AuthnQueryService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getAuthnQueryService() : array
    {
        return $this->AuthnQueryService;
    }


    /**
     * Set the value of the AuthnQueryService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $authnQueryService
     * @return void
     */
    public function setAuthnQueryService(array $authnQueryService) : void
    {
        $this->AuthnQueryService = $authnQueryService;
    }


    /**
     * Add the value to the AuthnQueryService-property
     *
     * @param \SAML2\XML\md\EndpointType $authnQueryService
     * @return void
     */
    public function addAuthnQueryService(EndpointType $authnQueryService) : void
    {
        $this->AuthnQueryService[] = $authnQueryService;
    }


    /**
     * Collect the value of the AssertionIDRequestService-property
     *
     * @return \SAML2\XML\md\EndpointType[]
     */
    public function getAssertionIDRequestService() : array
    {
        return $this->AssertionIDRequestService;
    }


    /**
     * Set the value of the AssertionIDRequestService-property
     *
     * @param \SAML2\XML\md\EndpointType[] $assertionIDRequestService
     * @return void
     */
    public function setAssertionIDRequestService(array $assertionIDRequestService) : void
    {
        $this->AssertionIDRequestService = $assertionIDRequestService;
    }


    /**
     * Add the value to the AssertionIDRequestService-property
     *
     * @param \SAML2\XML\md\EndpointType $assertionIDRequestService
     * @return void
     */
    public function addAssertionIDRequestService(EndpointType $assertionIDRequestService) : void
    {
        $this->AssertionIDRequestService[] = $assertionIDRequestService;
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
     * Add this IDPSSODescriptor to an EntityDescriptor.
     *
     * @param \DOMElement $parent The EntityDescriptor we should append this AuthnAuthorityDescriptor to.
     * @return \DOMElement
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        Assert::notEmpty($this->AuthnQueryService);

        $e = parent::toXML($parent);

        foreach ($this->AuthnQueryService as $ep) {
            $ep->toXML($e, 'md:AuthnQueryService');
        }

        foreach ($this->AssertionIDRequestService as $ep) {
            $ep->toXML($e, 'md:AssertionIDRequestService');
        }

        Utils::addStrings($e, Constants::NS_MD, 'md:NameIDFormat', false, $this->NameIDFormat);

        return $e;
    }
}
