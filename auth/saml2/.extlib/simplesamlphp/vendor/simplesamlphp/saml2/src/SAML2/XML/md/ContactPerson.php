<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\Chunk;

/**
 * Class representing SAML 2 ContactPerson.
 *
 * @package SimpleSAMLphp
 */
class ContactPerson
{
    /**
     * The several different contact types as defined per specification
     */
    public const CONTACT_TYPES = [
        'technical',
        'support',
        'administrative',
        'billing',
        'other',
    ];

    /**
     * The contact type.
     *
     * @var string
     */
    private $contactType;

    /**
     * Extensions on this element.
     *
     * Array of extension elements.
     *
     * @var array
     */
    private $Extensions = [];

    /**
     * The Company of this contact.
     *
     * @var string|null
     */
    private $Company = null;

    /**
     * The GivenName of this contact.
     *
     * @var string|null
     */
    private $GivenName = null;

    /**
     * The SurName of this contact.
     *
     * @var string|null
     */
    private $SurName = null;

    /**
     * The EmailAddresses of this contact.
     *
     * @var array
     */
    private $EmailAddress = [];

    /**
     * The TelephoneNumbers of this contact.
     *
     * @var array
     */
    private $TelephoneNumber = [];

    /**
     * Extra attributes on the contact element.
     *
     * @var array
     */
    private $ContactPersonAttributes = [];


    /**
     * Initialize a ContactPerson element.
     *
     * @param \DOMElement|null $xml The XML element we should load.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('contactType')) {
            throw new \Exception('Missing contactType on ContactPerson.');
        }
        $this->setContactType($xml->getAttribute('contactType'));

        $this->setExtensions(Extensions::getList($xml));

        $this->setCompany(self::getStringElement($xml, 'Company'));
        $this->setGivenName(self::getStringElement($xml, 'GivenName'));
        $this->setSurName(self::getStringElement($xml, 'SurName'));
        $this->setEmailAddress(self::getStringElements($xml, 'EmailAddress'));
        $this->setTelephoneNumber(self::getStringElements($xml, 'TelephoneNumber'));

        foreach ($xml->attributes as $attr) {
            if ($attr->nodeName == "contactType") {
                continue;
            }

            $this->addContactPersonAttributes($attr->nodeName, $attr->nodeValue);
        }
    }


    /**
     * Retrieve the value of a child \DOMElements as an array of strings.
     *
     * @param  \DOMElement $parent The parent element.
     * @param  string     $name   The name of the child elements.
     * @return array      The value of the child elements.
     */
    private static function getStringElements(\DOMElement $parent, string $name) : array
    {
        $e = Utils::xpQuery($parent, './saml_metadata:'.$name);

        $ret = [];
        foreach ($e as $i) {
            $ret[] = $i->textContent;
        }

        return $ret;
    }


    /**
     * Retrieve the value of a child \DOMElement as a string.
     *
     * @param  \DOMElement  $parent The parent element.
     * @param  string      $name   The name of the child element.
     * @throws \Exception
     * @return string|null The value of the child element.
     */
    private static function getStringElement(\DOMElement $parent, string $name) : ?string
    {
        $e = self::getStringElements($parent, $name);
        if (empty($e)) {
            return null;
        }
        if (count($e) > 1) {
            throw new \Exception('More than one '.$name.' in '.$parent->tagName);
        }

        return $e[0];
    }


    /**
     * Collect the value of the contactType-property
     *
     * @return string
     */
    public function getContactType() : string
    {
        return $this->contactType;
    }


    /**
     * Set the value of the contactType-property
     *
     * @param string $contactType
     * @return void
     */
    public function setContactType(string $contactType) : void
    {
        $this->contactType = $contactType;
    }


    /**
     * Collect the value of the Company-property
     *
     * @return string|null
     */
    public function getCompany() : ?string
    {
        return $this->Company;
    }


    /**
     * Set the value of the Company-property
     *
     * @param string|null $company
     * @return void
     */
    public function setCompany(string $company = null) : void
    {
        $this->Company = $company;
    }


    /**
     * Collect the value of the GivenName-property
     *
     * @return string|null
     */
    public function getGivenName() : ?string
    {
        return $this->GivenName;
    }


    /**
     * Set the value of the GivenName-property
     *
     * @param string|null $givenName
     * @return void
     */
    public function setGivenName(string $givenName = null) : void
    {
        $this->GivenName = $givenName;
    }


    /**
     * Collect the value of the SurName-property
     *
     * @return string|null
     */
    public function getSurName() : ?string
    {
        return $this->SurName;
    }


    /**
     * Set the value of the SurName-property
     *
     * @param string|null $surName
     * @return void
     */
    public function setSurName(string $surName = null) : void
    {
        $this->SurName = $surName;
    }


    /**
     * Collect the value of the EmailAddress-property
     *
     * @return string[]
     */
    public function getEmailAddress() : array
    {
        return $this->EmailAddress;
    }


    /**
     * Set the value of the EmailAddress-property
     *
     * @param string[] $emailAddress
     * @return void
     */
    public function setEmailAddress(array $emailAddress) : void
    {
        $emailAddress = preg_replace('/^mailto:/i', '', $emailAddress);
        $this->EmailAddress = $emailAddress;
    }


    /**
     * Add the value to the EmailAddress-property
     *
     * @param string $emailAddress
     * @return void
     */
    public function addEmailAddress($emailAddress) : void
    {
        $emailAddress = preg_replace('/^mailto:/i', '', $emailAddress);
        $this->EmailAddress[] = $emailAddress;
    }


    /**
     * Collect the value of the TelephoneNumber-property
     *
     * @return string[]
     */
    public function getTelephoneNumber() : array
    {
        return $this->TelephoneNumber;
    }


    /**
     * Set the value of the TelephoneNumber-property
     *
     * @param string[] $telephoneNumber
     * @return void
     */
    public function setTelephoneNumber(array $telephoneNumber) : void
    {
        $this->TelephoneNumber = $telephoneNumber;
    }


    /**
     * Add the value to the TelephoneNumber-property
     *
     * @param string $telephoneNumber
     * @return void
     */
    public function addTelephoneNumber($telephoneNumber) : void
    {
        $this->TelephoneNumber[] = $telephoneNumber;
    }


    /**
     * Collect the value of the Extensions-property
     *
     * @return \SAML2\XML\Chunk[]
     */
    public function getExtensions() : array
    {
        return $this->Extensions;
    }


    /**
     * Set the value of the Extensions-property
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
    public function addExtension(Chunk $extension) : void
    {
        $this->Extensions[] = $extension;
    }


    /**
     * Collect the value of the ContactPersonAttributes-property
     *
     * @return string[]
     */
    public function getContactPersonAttributes() : array
    {
        return $this->ContactPersonAttributes;
    }


    /**
     * Set the value of the ContactPersonAttributes-property
     *
     * @param string[] $contactPersonAttributes
     * @return void
     */
    public function setContactPersonAttributes(array $contactPersonAttributes) : void
    {
        $this->ContactPersonAttributes = $contactPersonAttributes;
    }


    /**
     * Add the key/value of the ContactPersonAttributes-property
     *
     * @param string $attr
     * @param string $value
     * @return void
     */
    public function addContactPersonAttributes(string $attr, string $value) : void
    {
        $this->ContactPersonAttributes[$attr] = $value;
    }


    /**
     * Convert this ContactPerson to XML.
     *
     * @param  \DOMElement $parent The element we should add this contact to.
     * @return \DOMElement The new ContactPerson-element.
     */
    public function toXML(DOMElement $parent) : DOMElement
    {
        $doc = $parent->ownerDocument;

        $e = $doc->createElementNS(Constants::NS_MD, 'md:ContactPerson');
        $parent->appendChild($e);

        $e->setAttribute('contactType', $this->getContactType());

        foreach ($this->getContactPersonAttributes() as $attr => $val) {
            $e->setAttribute($attr, $val);
        }

        Extensions::addList($e, $this->getExtensions());

        if ($this->Company !== null) {
            Utils::addString($e, Constants::NS_MD, 'md:Company', $this->Company);
        }
        if ($this->GivenName !== null) {
            Utils::addString($e, Constants::NS_MD, 'md:GivenName', $this->GivenName);
        }
        if ($this->SurName !== null) {
            Utils::addString($e, Constants::NS_MD, 'md:SurName', $this->SurName);
        }
        if (!empty($this->getEmailAddress())) {
            /** @var array $addresses */
            $addresses = preg_filter('/^/', 'mailto:', $this->EmailAddress);

            Utils::addStrings($e, Constants::NS_MD, 'md:EmailAddress', false, $addresses);
        }
        if (!empty($this->getTelephoneNumber())) {
            Utils::addStrings($e, Constants::NS_MD, 'md:TelephoneNumber', false, $this->getTelephoneNumber());
        }

        return $e;
    }
}
