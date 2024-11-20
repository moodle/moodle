<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use DOMNode;
use DOMNodeList;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use Webmozart\Assert\Assert;

use SAML2\Exception\RuntimeException;
use SAML2\Utilities\Temporal;
use SAML2\XML\Chunk;
use SAML2\XML\saml\Issuer;
use SAML2\XML\saml\NameID;
use SAML2\XML\saml\SubjectConfirmation;

/**
 * Class representing a SAML 2 assertion.
 *
 * @package SimpleSAMLphp
 */
class Assertion extends SignedElement
{
    /**
     * The identifier of this assertion.
     *
     * @var string
     */
    private $id;

    /**
     * The issue timestamp of this assertion, as an UNIX timestamp.
     *
     * @var int
     */
    private $issueInstant;

    /**
     * The issuer of this assertion.
     *
     * If the issuer's format is \SAML2\Constants::NAMEID_ENTITY, this property will just take the issuer's string
     * value.
     *
     * @var \SAML2\XML\saml\Issuer
     */
    private $issuer;

    /**
     * The NameId of the subject in the assertion.
     *
     * If the NameId is null, no subject was included in the assertion.
     *
     * @var \SAML2\XML\saml\NameID|null
     */
    private $nameId;

    /**
     * The encrypted NameId of the subject.
     *
     * If this is not null, the NameId needs decryption before it can be accessed.
     *
     * @var \DOMElement|null
     */
    private $encryptedNameId;

    /**
     * The encrypted Attributes.
     *
     * If this is not an empty array, these Attributes need decryption before they can be used.
     *
     * @var \DOMElement[]
     */
    private $encryptedAttributes;

    /**
     * Private key we should use to encrypt the attributes.
     *
     * @var XMLSecurityKey|null
     */
    private $encryptionKey;

    /**
     * The earliest time this assertion is valid, as an UNIX timestamp.
     *
     * @var int|null
     */
    private $notBefore;

    /**
     * The time this assertion expires, as an UNIX timestamp.
     *
     * @var int|null
     */
    private $notOnOrAfter;

    /**
     * The set of audiences that are allowed to receive this assertion.
     *
     * This is an array of valid service providers.
     *
     * If no restrictions on the audience are present, this variable contains null.
     *
     * @var array|null
     */
    private $validAudiences;

    /**
     * The session expiration timestamp.
     *
     * @var int|null
     */
    private $sessionNotOnOrAfter = null;

    /**
     * The session index for this user on the IdP.
     *
     * Contains null if no session index is present.
     *
     * @var string|null
     */
    private $sessionIndex = null;

    /**
     * The timestamp the user was authenticated, as an UNIX timestamp.
     *
     * @var int|null
     */
    private $authnInstant = null;

    /**
     * The authentication context reference for this assertion.
     *
     * @var string|null
     */
    private $authnContextClassRef = null;

    /**
     * Authentication context declaration provided by value.
     *
     * See:
     * @url http://docs.oasis-open.org/security/saml/v2.0/saml-authn-context-2.0-os.pdf
     *
     * @var \SAML2\XML\Chunk|null
     */
    private $authnContextDecl = null;

    /**
     * URI reference that identifies an authentication context declaration.
     *
     * The URI reference MAY directly resolve into an XML document containing the referenced declaration.
     *
     * @var string|null
     */
    private $authnContextDeclRef = null;

    /**
     * The list of AuthenticatingAuthorities for this assertion.
     *
     * @var array
     */
    private $AuthenticatingAuthority = [];

    /**
     * The attributes, as an associative array, indexed by attribute name
     *
     * To ease handling, all attribute values are represented as an array of values, also for values with a multiplicity
     * of single. There are 5 possible variants of datatypes for the values: a string, an integer, an array, a
     * DOMNodeList or a SAML2\XML\saml\NameID object.
     *
     * If the attribute is an eduPersonTargetedID, the values will be SAML2\XML\saml\NameID objects.
     * If the attribute value has an type-definition (xsi:string or xsi:int), the values will be of that type.
     * If the attribute value contains a nested XML structure, the values will be a DOMNodeList
     * In all other cases the values are treated as strings
     *
     * **WARNING** a DOMNodeList cannot be serialized without data-loss and should be handled explicitly
     *
     * @var array multi-dimensional array of \DOMNodeList|\SAML2\XML\saml\NameID|string|int|array
     */
    private $attributes = [];

    /**
     * The attributes values types as per http://www.w3.org/2001/XMLSchema definitions
     * the variable is as an associative array, indexed by attribute name
     *
     * when parsing assertion, the variable will be:
     * - <attribute name> => [<Value1's xs type>|null, <xs type Value2>|null, ...]
     * array will always have the same size of the array of vaules in $attributes for the same <attribute name>
     *
     * when generating assertion, the varuable can be:
     * - null : backward compatibility
     * - <attribute name> => <xs type> : all values for the given attribute will have the same xs type
     * - <attribute name> => [<Value1's xs type>|null, <xs type Value2>|null, ...] : Nth value will have type of the
     *   Nth in the array
     *
     * @var array multi-dimensional array of array
     */
    private $attributesValueTypes = [];

    /**
     * The NameFormat used on all attributes.
     *
     * If more than one NameFormat is used, this will contain the unspecified nameformat.
     *
     * @var string
     */
    private $nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;

    /**
     * The data needed to verify the signature.
     *
     * @var array|null
     */
    private $signatureData = null;

    /**
     * Boolean that indicates if attributes are encrypted in the assertion or not.
     *
     * @var boolean
     */
    private $requiredEncAttributes = false;

    /**
     * The SubjectConfirmation elements of the Subject in the assertion.
     *
     * @var \SAML2\XML\saml\SubjectConfirmation[]
     */
    private $SubjectConfirmation = [];

    /**
     * @var bool
     */
    protected $wasSignedAtConstruction = false;

    /**
     * @var string|null
     */
    private $signatureMethod;


    /**
     * Constructor for SAML 2 assertions.
     *
     * @param \DOMElement|null $xml The input assertion.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        // Create an Issuer
        $issuer = new Issuer();
        $issuer->setValue('');

        $this->id = Utils::getContainer()->generateId();
        $this->issueInstant = Temporal::getTime();
        $this->issuer = $issuer;
        $this->authnInstant = Temporal::getTime();

        if ($xml === null) {
            return;
        }

        if (!$xml->hasAttribute('ID')) {
            throw new \Exception('Missing ID attribute on SAML assertion.');
        }
        $this->id = $xml->getAttribute('ID');

        if ($xml->getAttribute('Version') !== '2.0') {
            /* Currently a very strict check. */
            throw new \Exception('Unsupported version: '.$xml->getAttribute('Version'));
        }

        $this->issueInstant = Utils::xsDateTimeToTimestamp($xml->getAttribute('IssueInstant'));

        /** @var \DOMElement[] $issuer */
        $issuer = Utils::xpQuery($xml, './saml_assertion:Issuer');
        if (empty($issuer)) {
            throw new \Exception('Missing <saml:Issuer> in assertion.');
        }

        $this->issuer = new Issuer($issuer[0]);

        $this->parseSubject($xml);
        $this->parseConditions($xml);
        $this->parseAuthnStatement($xml);
        $this->parseAttributes($xml);
        $this->parseEncryptedAttributes($xml);
        $this->parseSignature($xml);
    }


    /**
     * Parse subject in assertion.
     *
     * @param \DOMElement $xml The assertion XML element.
     * @throws \Exception
     * @return void
     */
    private function parseSubject(DOMElement $xml) : void
    {
        /** @var \DOMElement[] $subject */
        $subject = Utils::xpQuery($xml, './saml_assertion:Subject');
        if (empty($subject)) {
            /* No Subject node. */

            return;
        } elseif (count($subject) > 1) {
            throw new \Exception('More than one <saml:Subject> in <saml:Assertion>.');
        }
        $subject = $subject[0];

        /** @var \DOMElement[] $nameId */
        $nameId = Utils::xpQuery(
            $subject,
            './saml_assertion:NameID | ./saml_assertion:EncryptedID/xenc:EncryptedData'
        );
        if (count($nameId) > 1) {
            throw new \Exception('More than one <saml:NameID> or <saml:EncryptedID> in <saml:Subject>.');
        } elseif (!empty($nameId)) {
            $nameId = $nameId[0];
            if ($nameId->localName === 'EncryptedData') {
                /* The NameID element is encrypted. */
                $this->encryptedNameId = $nameId;
            } else {
                $this->nameId = new NameID($nameId);
            }
        }

        /** @var \DOMElement[] $subjectConfirmation */
        $subjectConfirmation = Utils::xpQuery($subject, './saml_assertion:SubjectConfirmation');
        if (empty($subjectConfirmation) && empty($nameId)) {
            throw new \Exception('Missing <saml:SubjectConfirmation> in <saml:Subject>.');
        }

        foreach ($subjectConfirmation as $sc) {
            $this->SubjectConfirmation[] = new SubjectConfirmation($sc);
        }
    }


    /**
     * Parse conditions in assertion.
     *
     * @param \DOMElement $xml The assertion XML element.
     * @throws \Exception
     * @return void
     */
    private function parseConditions(DOMElement $xml) : void
    {
        /** @var \DOMElement[] $conditions */
        $conditions = Utils::xpQuery($xml, './saml_assertion:Conditions');
        if (empty($conditions)) {
            /* No <saml:Conditions> node. */

            return;
        } elseif (count($conditions) > 1) {
            throw new \Exception('More than one <saml:Conditions> in <saml:Assertion>.');
        }
        $conditions = $conditions[0];

        if ($conditions->hasAttribute('NotBefore')) {
            $notBefore = Utils::xsDateTimeToTimestamp($conditions->getAttribute('NotBefore'));
            if ($this->getNotBefore() === null || $this->getNotBefore() < $notBefore) {
                $this->setNotBefore($notBefore);
            }
        }
        if ($conditions->hasAttribute('NotOnOrAfter')) {
            $notOnOrAfter = Utils::xsDateTimeToTimestamp($conditions->getAttribute('NotOnOrAfter'));
            if ($this->getNotOnOrAfter() === null || $this->getNotOnOrAfter() > $notOnOrAfter) {
                $this->setNotOnOrAfter($notOnOrAfter);
            }
        }

        foreach ($conditions->childNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }
            if ($node->namespaceURI !== Constants::NS_SAML) {
                throw new \Exception('Unknown namespace of condition: '.var_export($node->namespaceURI, true));
            }
            switch ($node->localName) {
                case 'AudienceRestriction':
                    $audiences = Utils::extractStrings($node, Constants::NS_SAML, 'Audience');
                    if ($this->validAudiences === null) {
                        /* The first (and probably last) AudienceRestriction element. */
                        $this->validAudiences = $audiences;
                    } else {
                        /*
                         * The set of AudienceRestriction are ANDed together, so we need
                         * the subset that are present in all of them.
                         */
                        $this->validAudiences = array_intersect($this->validAudiences, $audiences);
                    }
                    break;
                case 'OneTimeUse':
                    /* Currently ignored. */
                    break;
                case 'ProxyRestriction':
                    /* Currently ignored. */
                    break;
                default:
                    throw new \Exception('Unknown condition: '.var_export($node->localName, true));
            }
        }
    }


    /**
     * Parse AuthnStatement in assertion.
     *
     * @param \DOMElement $xml The assertion XML element.
     * @throws \Exception
     * @return void
     */
    private function parseAuthnStatement(DOMElement $xml) : void
    {
        /** @var \DOMElement[] $authnStatements */
        $authnStatements = Utils::xpQuery($xml, './saml_assertion:AuthnStatement');
        if (empty($authnStatements)) {
            $this->authnInstant = null;

            return;
        } elseif (count($authnStatements) > 1) {
            throw new \Exception('More than one <saml:AuthnStatement> in <saml:Assertion> not supported.');
        }
        $authnStatement = $authnStatements[0];

        if (!$authnStatement->hasAttribute('AuthnInstant')) {
            throw new \Exception('Missing required AuthnInstant attribute on <saml:AuthnStatement>.');
        }
        $this->authnInstant = Utils::xsDateTimeToTimestamp($authnStatement->getAttribute('AuthnInstant'));

        if ($authnStatement->hasAttribute('SessionNotOnOrAfter')) {
            $this->sessionNotOnOrAfter = Utils::xsDateTimeToTimestamp(
                $authnStatement->getAttribute('SessionNotOnOrAfter')
            );
        }

        if ($authnStatement->hasAttribute('SessionIndex')) {
            $this->sessionIndex = $authnStatement->getAttribute('SessionIndex');
        }

        $this->parseAuthnContext($authnStatement);
    }


    /**
     * Parse AuthnContext in AuthnStatement.
     *
     * @param \DOMElement $authnStatementEl
     * @throws \Exception
     * @return void
     */
    private function parseAuthnContext(DOMElement $authnStatementEl) : void
    {
        // Get the AuthnContext element
        /** @var \DOMElement[] $authnContexts */
        $authnContexts = Utils::xpQuery($authnStatementEl, './saml_assertion:AuthnContext');
        if (count($authnContexts) > 1) {
            throw new \Exception('More than one <saml:AuthnContext> in <saml:AuthnStatement>.');
        } elseif (empty($authnContexts)) {
            throw new \Exception('Missing required <saml:AuthnContext> in <saml:AuthnStatement>.');
        }
        $authnContextEl = $authnContexts[0];

        // Get the AuthnContextDeclRef (if available)
        /** @var \DOMElement[] $authnContextDeclRefs */
        $authnContextDeclRefs = Utils::xpQuery($authnContextEl, './saml_assertion:AuthnContextDeclRef');
        if (count($authnContextDeclRefs) > 1) {
            throw new \Exception(
                'More than one <saml:AuthnContextDeclRef> found?'
            );
        } elseif (count($authnContextDeclRefs) === 1) {
            $this->setAuthnContextDeclRef(trim($authnContextDeclRefs[0]->textContent));
        }

        // Get the AuthnContextDecl (if available)
        /** @var \DOMElement[] $authnContextDecls */
        $authnContextDecls = Utils::xpQuery($authnContextEl, './saml_assertion:AuthnContextDecl');
        if (count($authnContextDecls) > 1) {
            throw new \Exception(
                'More than one <saml:AuthnContextDecl> found?'
            );
        } elseif (count($authnContextDecls) === 1) {
            $this->setAuthnContextDecl(new Chunk($authnContextDecls[0]));
        }

        // Get the AuthnContextClassRef (if available)
        /** @var \DOMElement[] $authnContextClassRefs */
        $authnContextClassRefs = Utils::xpQuery($authnContextEl, './saml_assertion:AuthnContextClassRef');
        if (count($authnContextClassRefs) > 1) {
            throw new \Exception('More than one <saml:AuthnContextClassRef> in <saml:AuthnContext>.');
        } elseif (count($authnContextClassRefs) === 1) {
            $this->setAuthnContextClassRef(trim($authnContextClassRefs[0]->textContent));
        }

        // Constraint from XSD: MUST have one of the three
        if (empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef)) {
            throw new \Exception(
                'Missing either <saml:AuthnContextClassRef> or <saml:AuthnContextDeclRef> or <saml:AuthnContextDecl>'
            );
        }

        $this->AuthenticatingAuthority = Utils::extractStrings(
            $authnContextEl,
            Constants::NS_SAML,
            'AuthenticatingAuthority'
        );
    }


    /**
     * Parse attribute statements in assertion.
     *
     * @param \DOMElement $xml The XML element with the assertion.
     * @throws \Exception
     * @return void
     */
    private function parseAttributes(DOMElement $xml) : void
    {
        $firstAttribute = true;
        /** @var \DOMElement[] $attributes */
        $attributes = Utils::xpQuery($xml, './saml_assertion:AttributeStatement/saml_assertion:Attribute');
        foreach ($attributes as $attribute) {
            if (!$attribute->hasAttribute('Name')) {
                throw new \Exception('Missing name on <saml:Attribute> element.');
            }
            $name = $attribute->getAttribute('Name');

            if ($attribute->hasAttribute('NameFormat')) {
                $nameFormat = $attribute->getAttribute('NameFormat');
            } else {
                $nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
            }

            if ($firstAttribute) {
                $this->nameFormat = $nameFormat;
                $firstAttribute = false;
            } else {
                if ($this->nameFormat !== $nameFormat) {
                    $this->nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
                }
            }

            if (!array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = [];
                $this->attributesValueTypes[$name] = [];
            }

            $this->parseAttributeValue($attribute, $name);
        }
    }


    /**
     * @param \DOMNode $attribute
     * @param string   $attributeName
     * @return void
     */
    private function parseAttributeValue(DOMNode $attribute, string $attributeName) : void
    {
        /** @var \DOMElement[] $values */
        $values = Utils::xpQuery($attribute, './saml_assertion:AttributeValue');

        if ($attributeName === Constants::EPTI_URN_MACE || $attributeName === Constants::EPTI_URN_OID) {
            foreach ($values as $index => $eptiAttributeValue) {
                /** @var \DOMElement[] $eptiNameId */
                $eptiNameId = Utils::xpQuery($eptiAttributeValue, './saml_assertion:NameID');

                if (count($eptiNameId) === 1) {
                    $this->attributes[$attributeName][] = new NameID($eptiNameId[0]);
                } else {
                    /* Fall back for legacy IdPs sending string value (e.g. SSP < 1.15) */
                    Utils::getContainer()->getLogger()->warning(
                        sprintf("Attribute %s (EPTI) value %d is not an XML NameId", $attributeName, $index)
                    );
                    $nameId = new NameID();
                    $nameId->setValue($eptiAttributeValue->textContent);
                    $this->attributes[$attributeName][] = $nameId;
                }
            }

            return;
        }

        foreach ($values as $value) {
            $hasNonTextChildElements = false;
            foreach ($value->childNodes as $childNode) {
                /** @var \DOMNode $childNode */
                if ($childNode->nodeType !== XML_TEXT_NODE) {
                    $hasNonTextChildElements = true;
                    break;
                }
            }

            $type = $value->getAttribute('xsi:type');
            if ($type === '') {
                $type = null;
            }
            $this->attributesValueTypes[$attributeName][] = $type;

            if ($hasNonTextChildElements) {
                $this->attributes[$attributeName][] = $value->childNodes;
                continue;
            }
            
            if ($type === 'xs:integer') {
                $this->attributes[$attributeName][] = (int) $value->textContent;
            } else {
                $this->attributes[$attributeName][] = trim($value->textContent);
            }
        }
    }


    /**
     * Parse encrypted attribute statements in assertion.
     *
     * @param \DOMElement $xml The XML element with the assertion.
     * @return void
     */
    private function parseEncryptedAttributes(DOMElement $xml) : void
    {
        /** @var \DOMElement[] encryptedAttributes */
        $this->encryptedAttributes = Utils::xpQuery(
            $xml,
            './saml_assertion:AttributeStatement/saml_assertion:EncryptedAttribute'
        );
    }


    /**
     * Parse signature on assertion.
     *
     * @param \DOMElement $xml The assertion XML element.
     * @return void
     */
    private function parseSignature(DOMElement $xml) : void
    {
        /** @var \DOMAttr[] $signatureMethod */
        $signatureMethod = Utils::xpQuery($xml, './ds:Signature/ds:SignedInfo/ds:SignatureMethod/@Algorithm');

        /* Validate the signature element of the message. */
        $sig = Utils::validateElement($xml);
        if ($sig !== false) {
            $this->wasSignedAtConstruction = true;
            $this->setCertificates($sig['Certificates']);
            $this->setSignatureData($sig);
            $this->setSignatureMethod($signatureMethod[0]->value);
        }
    }


    /**
     * Validate this assertion against a public key.
     *
     * If no signature was present on the assertion, we will return false.
     * Otherwise, true will be returned. An exception is thrown if the
     * signature validation fails.
     *
     * @param  XMLSecurityKey $key The key we should check against.
     * @return boolean        true if successful, false if it is unsigned.
     */
    public function validate(XMLSecurityKey $key) : bool
    {
        Assert::same($key->type, XMLSecurityKey::RSA_SHA256);

        if ($this->signatureData === null) {
            return false;
        }

        Utils::validateSignature($this->signatureData, $key);

        return true;
    }


    /**
     * Retrieve the identifier of this assertion.
     *
     * @return string The identifier of this assertion.
     */
    public function getId() : string
    {
        return $this->id;
    }


    /**
     * Set the identifier of this assertion.
     *
     * @param string $id The new identifier of this assertion.
     * @return void
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }


    /**
     * Retrieve the issue timestamp of this assertion.
     *
     * @return int The issue timestamp of this assertion, as an UNIX timestamp.
     */
    public function getIssueInstant() : int
    {
        return $this->issueInstant;
    }


    /**
     * Set the issue timestamp of this assertion.
     *
     * @param int $issueInstant The new issue timestamp of this assertion, as an UNIX timestamp.
     * @return void
     */
    public function setIssueInstant(int $issueInstant) : void
    {
        $this->issueInstant = $issueInstant;
    }


    /**
     * Retrieve the issuer if this assertion.
     *
     * @return \SAML2\XML\saml\Issuer The issuer of this assertion.
     */
    public function getIssuer() : Issuer
    {
        return $this->issuer;
    }


    /**
     * Set the issuer of this message.
     *
     * @param \SAML2\XML\saml\Issuer $issuer The new issuer of this assertion.
     * @return void
     */
    public function setIssuer(Issuer $issuer) : void
    {
        $this->issuer = $issuer;
    }


    /**
     * Retrieve the NameId of the subject in the assertion.
     *
     * @throws \Exception
     * @return \SAML2\XML\saml\NameID|null The name identifier of the assertion.
     */
    public function getNameId() : ?NameID
    {
        if ($this->encryptedNameId !== null) {
            throw new \Exception('Attempted to retrieve encrypted NameID without decrypting it first.');
        }

        return $this->nameId;
    }


    /**
     * Set the NameId of the subject in the assertion.
     *
     * The NameId must be a \SAML2\XML\saml\NameID object.
     *
     * @see \SAML2\Utils::addNameId()
     * @param \SAML2\XML\saml\NameID|null $nameId The name identifier of the assertion.
     * @return void
     */
    public function setNameId(NameID $nameId = null) : void
    {
        $this->nameId = $nameId;
    }


    /**
     * Check whether the NameId is encrypted.
     *
     * @return bool True if the NameId is encrypted, false if not.
     */
    public function isNameIdEncrypted() : bool
    {
        return $this->encryptedNameId !== null;
    }


    /**
     * Encrypt the NameID in the Assertion.
     *
     * @param XMLSecurityKey $key The encryption key.
     * @return void
     */
    public function encryptNameId(XMLSecurityKey $key) : void
    {
        if ($this->nameId === null) {
            throw new \Exception('Cannot encrypt NameID, no NameID set.');
        }
        /* First create an XML representation of the NameID. */
        $doc = DOMDocumentFactory::create();
        $root = $doc->createElement('root');
        $doc->appendChild($root);
        $this->nameId->toXML($root);
        /** @var \DOMElement $nameId */
        $nameId = $root->firstChild;

        Utils::getContainer()->debugMessage($nameId, 'encrypt');

        /* Encrypt the NameID. */
        $enc = new XMLSecEnc();
        $enc->setNode($nameId);
        // @codingStandardsIgnoreStart
        $enc->type = XMLSecEnc::Element;
        // @codingStandardsIgnoreEnd

        $symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
        $symmetricKey->generateSessionKey();
        $enc->encryptKey($key, $symmetricKey);

        /**
         * @var \DOMElement encryptedNameId
         * @psalm-suppress UndefinedClass
         */
        $this->encryptedNameId = $enc->encryptNode($symmetricKey);
        $this->nameId = null;
    }


    /**
     * Decrypt the NameId of the subject in the assertion.
     *
     * @param XMLSecurityKey $key       The decryption key.
     * @param array          $blacklist Blacklisted decryption algorithms.
     * @return void
     */
    public function decryptNameId(XMLSecurityKey $key, array $blacklist = []) : void
    {
        if ($this->encryptedNameId === null) {
            /* No NameID to decrypt. */

            return;
        }

        $nameId = Utils::decryptElement($this->encryptedNameId, $key, $blacklist);
        Utils::getContainer()->debugMessage($nameId, 'decrypt');
        $this->nameId = new NameID($nameId);

        $this->encryptedNameId = null;
    }


    /**
     * Did this Assertion contain encrypted Attributes?
     *
     * @return bool
     */
    public function hasEncryptedAttributes() : bool
    {
        return $this->encryptedAttributes !== [];
    }


    /**
     * Decrypt the assertion attributes.
     *
     * @param XMLSecurityKey $key
     * @param array $blacklist
     * @throws \Exception
     * @return void
     */
    public function decryptAttributes(XMLSecurityKey $key, array $blacklist = []) : void
    {
        if (!$this->hasEncryptedAttributes()) {
            return;
        }
        $firstAttribute = true;
        $attributes = $this->getEncryptedAttributes();
        foreach ($attributes as $attributeEnc) {
            /* Decrypt node <EncryptedAttribute> */
            $attribute = Utils::decryptElement(
                $attributeEnc->getElementsByTagName('EncryptedData')->item(0),
                $key,
                $blacklist
            );

            if (!$attribute->hasAttribute('Name')) {
                throw new \Exception('Missing name on <saml:Attribute> element.');
            }
            $name = $attribute->getAttribute('Name');

            if ($attribute->hasAttribute('NameFormat')) {
                $nameFormat = $attribute->getAttribute('NameFormat');
            } else {
                $nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
            }

            if ($firstAttribute) {
                $this->nameFormat = $nameFormat;
                $firstAttribute = false;
            } else {
                if ($this->nameFormat !== $nameFormat) {
                    $this->nameFormat = Constants::NAMEFORMAT_UNSPECIFIED;
                }
            }

            if (!array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = [];
            }

            $this->parseAttributeValue($attribute, $name);
        }
    }


    /**
     * Retrieve the earliest timestamp this assertion is valid.
     *
     * This function returns null if there are no restrictions on how early the
     * assertion can be used.
     *
     * @return int|null The earliest timestamp this assertion is valid.
     */
    public function getNotBefore() : ?int
    {
        return $this->notBefore;
    }


    /**
     * Set the earliest timestamp this assertion can be used.
     *
     * Set this to null if no limit is required.
     *
     * @param int|null $notBefore The earliest timestamp this assertion is valid.
     * @return void
     */
    public function setNotBefore(int $notBefore = null) : void
    {
        $this->notBefore = $notBefore;
    }


    /**
     * Retrieve the expiration timestamp of this assertion.
     *
     * This function returns null if there are no restrictions on how
     * late the assertion can be used.
     *
     * @return int|null The latest timestamp this assertion is valid.
     */
    public function getNotOnOrAfter() : ?int
    {
        return $this->notOnOrAfter;
    }


    /**
     * Set the expiration timestamp of this assertion.
     *
     * Set this to null if no limit is required.
     *
     * @param int|null $notOnOrAfter The latest timestamp this assertion is valid.
     * @return void
     */
    public function setNotOnOrAfter(int $notOnOrAfter = null) : void
    {
        $this->notOnOrAfter = $notOnOrAfter;
    }


    /**
     * Retrieve $requiredEncAttributes if attributes will be send encrypted
     *
     * @return bool True to encrypt attributes in the assertion.
     */
    public function getRequiredEncAttributes() : bool
    {
        return $this->requiredEncAttributes;
    }


    /**
     * Set $requiredEncAttributes if attributes will be send encrypted
     *
     * @param bool $ea true to encrypt attributes in the assertion.
     * @return void
     */
    public function setRequiredEncAttributes(bool $ea) : void
    {
        $this->requiredEncAttributes = $ea;
    }


    /**
     * Retrieve the audiences that are allowed to receive this assertion.
     *
     * This may be null, in which case all audiences are allowed.
     *
     * @return array|null The allowed audiences.
     */
    public function getValidAudiences() : ?array
    {
        return $this->validAudiences;
    }


    /**
     * Set the audiences that are allowed to receive this assertion.
     *
     * This may be null, in which case all audiences are allowed.
     *
     * @param array|null $validAudiences The allowed audiences.
     * @return void
     */
    public function setValidAudiences(array $validAudiences = null) : void
    {
        $this->validAudiences = $validAudiences;
    }


    /**
     * Retrieve the AuthnInstant of the assertion.
     *
     * @return int|null The timestamp the user was authenticated, or NULL if the user isn't authenticated.
     */
    public function getAuthnInstant() : ?int
    {
        return $this->authnInstant;
    }


    /**
     * Set the AuthnInstant of the assertion.
     *
     * @param int|null $authnInstant Timestamp the user was authenticated, or NULL if we don't want an AuthnStatement.
     * @return void
     */
    public function setAuthnInstant(int $authnInstant = null) : void
    {
        $this->authnInstant = $authnInstant;
    }


    /**
     * Retrieve the session expiration timestamp.
     *
     * This function returns null if there are no restrictions on the
     * session lifetime.
     *
     * @return int|null The latest timestamp this session is valid.
     */
    public function getSessionNotOnOrAfter() : ?int
    {
        return $this->sessionNotOnOrAfter;
    }


    /**
     * Set the session expiration timestamp.
     *
     * Set this to null if no limit is required.
     *
     * @param int|null $sessionNotOnOrAfter The latest timestamp this session is valid.
     * @return void
     */
    public function setSessionNotOnOrAfter(int $sessionNotOnOrAfter = null) : void
    {
        $this->sessionNotOnOrAfter = $sessionNotOnOrAfter;
    }


    /**
     * Retrieve the session index of the user at the IdP.
     *
     * @return string|null The session index of the user at the IdP.
     */
    public function getSessionIndex() : ?string
    {
        return $this->sessionIndex;
    }


    /**
     * Set the session index of the user at the IdP.
     *
     * Note that the authentication context must be set before the
     * session index can be inluded in the assertion.
     *
     * @param string|null $sessionIndex The session index of the user at the IdP.
     * @return void
     */
    public function setSessionIndex(string $sessionIndex = null) : void
    {
        $this->sessionIndex = $sessionIndex;
    }


    /**
     * Retrieve the authentication method used to authenticate the user.
     *
     * This will return null if no authentication statement was
     * included in the assertion.
     *
     * @return string|null The authentication method.
     */
    public function getAuthnContextClassRef() : ?string
    {
        return $this->authnContextClassRef;
    }


    /**
     * Set the authentication method used to authenticate the user.
     *
     * If this is set to null, no authentication statement will be
     * included in the assertion. The default is null.
     *
     * @param string|null $authnContextClassRef The authentication method.
     * @return void
     */
    public function setAuthnContextClassRef(string $authnContextClassRef = null) : void
    {
        $this->authnContextClassRef = $authnContextClassRef;
    }


    /**
     * Retrieve the signature method.
     *
     * @return string|null The signature method.
     */
    public function getSignatureMethod() : ?string
    {
        return $this->signatureMethod;
    }


    /**
     * Set the signature method used.
     *
     * @param string|null $signatureMethod
     * @return void
     */
    public function setSignatureMethod(string $signatureMethod = null) : void
    {
        $this->signatureMethod = $signatureMethod;
    }


    /**
     * Set the authentication context declaration.
     *
     * @param \SAML2\XML\Chunk $authnContextDecl
     * @throws \Exception
     * @return void
     */
    public function setAuthnContextDecl(Chunk $authnContextDecl) : void
    {
        if (!empty($this->authnContextDeclRef)) {
            throw new \Exception(
                'AuthnContextDeclRef is already registered! May only have either a Decl or a DeclRef, not both!'
            );
        }

        $this->authnContextDecl = $authnContextDecl;
    }


    /**
     * Get the authentication context declaration.
     *
     * See:
     * @url http://docs.oasis-open.org/security/saml/v2.0/saml-authn-context-2.0-os.pdf
     *
     * @return \SAML2\XML\Chunk|null
     */
    public function getAuthnContextDecl() : ?Chunk
    {
        return $this->authnContextDecl;
    }


    /**
     * Set the authentication context declaration reference.
     *
     * @param string|null $authnContextDeclRef
     * @throws \Exception
     * @return void
     */
    public function setAuthnContextDeclRef(string $authnContextDeclRef = null) : void
    {
        if (!empty($this->authnContextDecl)) {
            throw new \Exception(
                'AuthnContextDecl is already registered! May only have either a Decl or a DeclRef, not both!'
            );
        }

        $this->authnContextDeclRef = $authnContextDeclRef;
    }


    /**
     * Get the authentication context declaration reference.
     * URI reference that identifies an authentication context declaration.
     *
     * The URI reference MAY directly resolve into an XML document containing the referenced declaration.
     *
     * @return string|null
     */
    public function getAuthnContextDeclRef() : ?string
    {
        return $this->authnContextDeclRef;
    }


    /**
     * Retrieve the AuthenticatingAuthority.
     *
     * @return array
     */
    public function getAuthenticatingAuthority() : array
    {
        return $this->AuthenticatingAuthority;
    }


    /**
     * Set the AuthenticatingAuthority
     *
     * @param array $authenticatingAuthority
     * @return void
     */
    public function setAuthenticatingAuthority(array $authenticatingAuthority) : void
    {
        $this->AuthenticatingAuthority = $authenticatingAuthority;
    }


    /**
     * Retrieve all attributes.
     *
     * @return array All attributes, as an associative array.
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }


    /**
     * Replace all attributes.
     *
     * @param array $attributes All new attributes, as an associative array.
     * @return void
     */
    public function setAttributes(array $attributes) : void
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array|null
     */
    public function getSignatureData() : ?array
    {
        return $this->signatureData;
    }


    /**
     * @param array|null $signatureData
     * @return void
     */
    public function setSignatureData(array $signatureData = null) : void
    {
        $this->signatureData = $signatureData;
    }


    /**
     * Retrieve all attributes value types.
     *
     * @return array All attributes value types, as an associative array.
     */
    public function getAttributesValueTypes() : array
    {
        return $this->attributesValueTypes;
    }


    /**
     * Replace all attributes value types..
     *
     * @param array $attributesValueTypes All new attribute value types, as an associative array.
     * @return void
     */
    public function setAttributesValueTypes(array $attributesValueTypes) : void
    {
        $this->attributesValueTypes = $attributesValueTypes;
    }


    /**
     * Retrieve the NameFormat used on all attributes.
     *
     * If more than one NameFormat is used in the received attributes, this
     * returns the unspecified NameFormat.
     *
     * @return string The NameFormat used on all attributes.
     */
    public function getAttributeNameFormat() : string
    {
        return $this->nameFormat;
    }


    /**
     * Set the NameFormat used on all attributes.
     *
     * @param string $nameFormat The NameFormat used on all attributes.
     * @return void
     */
    public function setAttributeNameFormat(string $nameFormat) : void
    {
        $this->nameFormat = $nameFormat;
    }


    /**
     * Retrieve the SubjectConfirmation elements we have in our Subject element.
     *
     * @return array Array of \SAML2\XML\saml\SubjectConfirmation elements.
     */
    public function getSubjectConfirmation() : array
    {
        return $this->SubjectConfirmation;
    }


    /**
     * Set the SubjectConfirmation elements that should be included in the assertion.
     *
     * @param array $SubjectConfirmation Array of \SAML2\XML\saml\SubjectConfirmation elements.
     * @return void
     */
    public function setSubjectConfirmation(array $SubjectConfirmation) : void
    {
        $this->SubjectConfirmation = $SubjectConfirmation;
    }


    /**
     * Retrieve the encryptedAttributes elements we have.
     *
     * @return array Array of \DOMElement elements.
     */
    public function getEncryptedAttributes() : array
    {
        return $this->encryptedAttributes;
    }


    /**
     * Set the encryptedAttributes elements
     *
     * @param array $encAttrs Array of \DOMElement elements.
     * @return void
     */
    public function setEncryptedAttributes(array $encAttrs) : void
    {
        $this->encryptedAttributes = $encAttrs;
    }


    /**
     * Retrieve the private key we should use to sign the assertion.
     *
     * @return XMLSecurityKey|null The key, or NULL if no key is specified.
     */
    public function getSignatureKey() : ?XMLSecurityKey
    {
        return $this->signatureKey;
    }


    /**
     * Set the private key we should use to sign the assertion.
     *
     * If the key is null, the assertion will be sent unsigned.
     *
     * @param XMLSecurityKey|null $signatureKey
     * @return void
     */
    public function setSignatureKey(XMLSecurityKey $signatureKey = null) : void
    {
        $this->signatureKey = $signatureKey;
    }


    /**
     * Return the key we should use to encrypt the assertion.
     *
     * @return XMLSecurityKey|null The key, or NULL if no key is specified..
     *
     */
    public function getEncryptionKey() : ?XMLSecurityKey
    {
        return $this->encryptionKey;
    }


    /**
     * Set the private key we should use to encrypt the attributes.
     *
     * @param XMLSecurityKey|null $Key
     * @return void
     */
    public function setEncryptionKey(XMLSecurityKey $Key = null) : void
    {
        $this->encryptionKey = $Key;
    }


    /**
     * Set the certificates that should be included in the assertion.
     *
     * The certificates should be strings with the PEM encoded data.
     *
     * @param array $certificates An array of certificates.
     * @return void
     */
    public function setCertificates(array $certificates) : void
    {
        $this->certificates = $certificates;
    }


    /**
     * Retrieve the certificates that are included in the assertion.
     *
     * @return array An array of certificates.
     */
    public function getCertificates() : array
    {
        return $this->certificates;
    }


    /**
     * @return bool
     */
    public function wasSignedAtConstruction() : bool
    {
        return $this->wasSignedAtConstruction;
    }


    /**
     * Convert this assertion to an XML element.
     *
     * @param  \DOMNode|null $parentElement The DOM node the assertion should be created in.
     * @return \DOMElement   This assertion.
     */
    public function toXML(\DOMNode $parentElement = null) : DOMElement
    {
        if ($parentElement === null) {
            $document = DOMDocumentFactory::create();
            $parentElement = $document;
        } else {
            $document = $parentElement->ownerDocument;
        }

        $root = $document->createElementNS(Constants::NS_SAML, 'saml:'.'Assertion');
        $parentElement->appendChild($root);

        /* Ugly hack to add another namespace declaration to the root element. */
        $root->setAttributeNS(Constants::NS_SAMLP, 'samlp:tmp', 'tmp');
        $root->removeAttributeNS(Constants::NS_SAMLP, 'tmp');
        $root->setAttributeNS(Constants::NS_XSI, 'xsi:tmp', 'tmp');
        $root->removeAttributeNS(Constants::NS_XSI, 'tmp');
        $root->setAttributeNS(Constants::NS_XS, 'xs:tmp', 'tmp');
        $root->removeAttributeNS(Constants::NS_XS, 'tmp');

        $root->setAttribute('ID', $this->id);
        $root->setAttribute('Version', '2.0');
        $root->setAttribute('IssueInstant', gmdate('Y-m-d\TH:i:s\Z', $this->issueInstant));

        $issuer = $this->issuer->toXML($root);

        $this->addSubject($root);
        $this->addConditions($root);
        $this->addAuthnStatement($root);
        if ($this->getRequiredEncAttributes() === false) {
            $this->addAttributeStatement($root);
        } else {
            $this->addEncryptedAttributeStatement($root);
        }

        if ($this->signatureKey !== null) {
            Utils::insertSignature($this->signatureKey, $this->certificates, $root, $issuer->nextSibling);
        }

        return $root;
    }


    /**
     * Add a Subject-node to the assertion.
     *
     * @param \DOMElement $root The assertion element we should add the subject to.
     * @return void
     */
    private function addSubject(DOMElement $root) : void
    {
        if ($this->nameId === null && $this->encryptedNameId === null) {
            /* We don't have anything to create a Subject node for. */

            return;
        }

        $subject = $root->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:Subject');
        $root->appendChild($subject);

        if ($this->encryptedNameId === null) {
            $this->nameId->toXML($subject);
        } else {
            $eid = $subject->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:'.'EncryptedID');
            $subject->appendChild($eid);
            $eid->appendChild($subject->ownerDocument->importNode($this->encryptedNameId, true));
        }

        foreach ($this->SubjectConfirmation as $sc) {
            $sc->toXML($subject);
        }
    }


    /**
     * Add a Conditions-node to the assertion.
     *
     * @param \DOMElement $root The assertion element we should add the conditions to.
     * @return void
     */
    private function addConditions(DOMElement $root) : void
    {
        $document = $root->ownerDocument;

        $conditions = $document->createElementNS(Constants::NS_SAML, 'saml:Conditions');
        $root->appendChild($conditions);

        if ($this->notBefore !== null) {
            $conditions->setAttribute('NotBefore', gmdate('Y-m-d\TH:i:s\Z', $this->notBefore));
        }
        if ($this->notOnOrAfter !== null) {
            $conditions->setAttribute('NotOnOrAfter', gmdate('Y-m-d\TH:i:s\Z', $this->notOnOrAfter));
        }

        if ($this->validAudiences !== null) {
            $ar = $document->createElementNS(Constants::NS_SAML, 'saml:AudienceRestriction');
            $conditions->appendChild($ar);

            Utils::addStrings($ar, Constants::NS_SAML, 'saml:Audience', false, $this->validAudiences);
        }
    }


    /**
     * Add a AuthnStatement-node to the assertion.
     *
     * @param \DOMElement $root The assertion element we should add the authentication statement to.
     * @return void
     */
    private function addAuthnStatement(DOMElement $root) : void
    {
        if ($this->authnInstant === null ||
            (
                $this->authnContextClassRef === null &&
                $this->authnContextDecl === null &&
                $this->authnContextDeclRef === null
            )
        ) {
            /* No authentication context or AuthnInstant => no authentication statement. */

            return;
        }

        $document = $root->ownerDocument;

        $authnStatementEl = $document->createElementNS(Constants::NS_SAML, 'saml:AuthnStatement');
        $root->appendChild($authnStatementEl);

        $authnStatementEl->setAttribute('AuthnInstant', gmdate('Y-m-d\TH:i:s\Z', $this->authnInstant));

        if ($this->sessionNotOnOrAfter !== null) {
            $authnStatementEl->setAttribute(
                'SessionNotOnOrAfter',
                gmdate('Y-m-d\TH:i:s\Z', $this->sessionNotOnOrAfter)
            );
        }
        if ($this->sessionIndex !== null) {
            $authnStatementEl->setAttribute('SessionIndex', $this->sessionIndex);
        }

        $authnContextEl = $document->createElementNS(Constants::NS_SAML, 'saml:AuthnContext');
        $authnStatementEl->appendChild($authnContextEl);

        if (!empty($this->authnContextClassRef)) {
            Utils::addString(
                $authnContextEl,
                Constants::NS_SAML,
                'saml:AuthnContextClassRef',
                $this->authnContextClassRef
            );
        }
        if (!empty($this->authnContextDecl)) {
            $this->authnContextDecl->toXML($authnContextEl);
        }
        if (!empty($this->authnContextDeclRef)) {
            Utils::addString(
                $authnContextEl,
                Constants::NS_SAML,
                'saml:AuthnContextDeclRef',
                $this->authnContextDeclRef
            );
        }

        Utils::addStrings(
            $authnContextEl,
            Constants::NS_SAML,
            'saml:AuthenticatingAuthority',
            false,
            $this->AuthenticatingAuthority
        );
    }


    /**
     * Add an AttributeStatement-node to the assertion.
     *
     * @param \DOMElement $root The assertion element we should add the subject to.
     * @return void
     */
    private function addAttributeStatement(DOMElement $root) : void
    {
        if (empty($this->attributes)) {
            return;
        }

        $document = $root->ownerDocument;

        $attributeStatement = $document->createElementNS(Constants::NS_SAML, 'saml:AttributeStatement');
        $root->appendChild($attributeStatement);

        foreach ($this->attributes as $name => $values) {
            $attribute = $document->createElementNS(Constants::NS_SAML, 'saml:Attribute');
            $attributeStatement->appendChild($attribute);
            $attribute->setAttribute('Name', $name);

            if ($this->nameFormat !== Constants::NAMEFORMAT_UNSPECIFIED) {
                $attribute->setAttribute('NameFormat', $this->nameFormat);
            }

            // make sure eduPersonTargetedID can be handled properly as a NameID
            if ($name === Constants::EPTI_URN_MACE || $name === Constants::EPTI_URN_OID) {
                foreach ($values as $eptiValue) {
                    $attributeValue = $document->createElementNS(Constants::NS_SAML, 'saml:AttributeValue');
                    $attribute->appendChild($attributeValue);
                    if ($eptiValue instanceof NameID) {
                        $eptiValue->toXML($attributeValue);
                    } elseif ($eptiValue instanceof DOMNodeList) {
                        /** @var \DOMElement $value */
                        $value = $eptiValue->item(0);
                        $node = $root->ownerDocument->importNode($value, true);
                        $attributeValue->appendChild($node);
                    } else {
                        $attributeValue->textContent = $eptiValue;
                    }
                }

                continue;
            }

            // get value type(s) for the current attribute
            if (array_key_exists($name, $this->attributesValueTypes)) {
                $valueTypes = $this->attributesValueTypes[$name];
                if (is_array($valueTypes) && count($valueTypes) != count($values)) {
                    throw new \Exception('Array of value types and array of values have different size for attribute '.
                        var_export($name, true));
                }
            } else {
                // if no type(s), default behaviour
                $valueTypes = null;
            }

            $vidx = -1;
            foreach ($values as $value) {
                $vidx++;

                // try to get type from current types
                $type = null;
                if (!is_null($valueTypes)) {
                    if (is_array($valueTypes)) {
                        $type = $valueTypes[$vidx];
                    } else {
                        $type = $valueTypes;
                    }
                }

                // if no type get from types, use default behaviour
                if (is_null($type)) {
                    if (is_string($value)) {
                        $type = 'xs:string';
                    } elseif (is_int($value)) {
                        $type = 'xs:integer';
                    } else {
                        $type = null;
                    }
                }

                $attributeValue = $document->createElementNS(Constants::NS_SAML, 'saml:AttributeValue');
                $attribute->appendChild($attributeValue);
                if ($type !== null) {
                    $attributeValue->setAttributeNS(Constants::NS_XSI, 'xsi:type', $type);
                }
                if (is_null($value)) {
                    $attributeValue->setAttributeNS(Constants::NS_XSI, 'xsi:nil', 'true');
                }

                if ($value instanceof \DOMNodeList) {
                    foreach ($value as $v) {
                        $node = $document->importNode($v, true);
                        $attributeValue->appendChild($node);
                    }
                } else {
                    $value = strval($value);
                    $attributeValue->appendChild($document->createTextNode($value));
                }
            }
        }
    }


    /**
     * Add an EncryptedAttribute Statement-node to the assertion.
     *
     * @param \DOMElement $root The assertion element we should add the Encrypted Attribute Statement to.
     * @return void
     */
    private function addEncryptedAttributeStatement(DOMElement $root) : void
    {
        if ($this->getRequiredEncAttributes() === false) {
            return;
        }
        Assert::notNull($this->encryptionKey);

        $document = $root->ownerDocument;

        $attributeStatement = $document->createElementNS(Constants::NS_SAML, 'saml:AttributeStatement');
        $root->appendChild($attributeStatement);

        foreach ($this->attributes as $name => $values) {
            $document2 = DOMDocumentFactory::create();
            $attribute = $document2->createElementNS(Constants::NS_SAML, 'saml:Attribute');
            $attribute->setAttribute('Name', $name);
            $document2->appendChild($attribute);

            if ($this->nameFormat !== Constants::NAMEFORMAT_UNSPECIFIED) {
                $attribute->setAttribute('NameFormat', $this->getAttributeNameFormat());
            }

            foreach ($values as $value) {
                if (is_string($value)) {
                    $type = 'xs:string';
                } elseif (is_int($value)) {
                    $type = 'xs:integer';
                } else {
                    $type = null;
                }

                $attributeValue = $document2->createElementNS(Constants::NS_SAML, 'saml:AttributeValue');
                $attribute->appendChild($attributeValue);
                if ($type !== null) {
                    $attributeValue->setAttributeNS(Constants::NS_XSI, 'xsi:type', $type);
                }

                if ($value instanceof DOMNodeList) {
                    foreach ($value as $v) {
                        $node = $document2->importNode($v, true);
                        $attributeValue->appendChild($node);
                    }
                } else {
                    $value = strval($value);
                    $attributeValue->appendChild($document2->createTextNode($value));
                }
            }
            /*Once the attribute nodes are built, the are encrypted*/
            $EncAssert = new XMLSecEnc();
            $EncAssert->setNode($document2->documentElement);
            $EncAssert->type = 'http://www.w3.org/2001/04/xmlenc#Element';
            /*
             * Attributes are encrypted with a session key and this one with
             * $EncryptionKey
             */
            $symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $symmetricKey->generateSessionKey();
            /** @psalm-suppress PossiblyNullArgument */
            $EncAssert->encryptKey($this->encryptionKey, $symmetricKey);
            /** @psalm-suppress UndefinedClass */
            $EncrNode = $EncAssert->encryptNode($symmetricKey);

            $EncAttribute = $document->createElementNS(Constants::NS_SAML, 'saml:EncryptedAttribute');
            $attributeStatement->appendChild($EncAttribute);
            /** @psalm-suppress InvalidArgument */
            $n = $document->importNode($EncrNode, true);
            $EncAttribute->appendChild($n);
        }
    }
}
