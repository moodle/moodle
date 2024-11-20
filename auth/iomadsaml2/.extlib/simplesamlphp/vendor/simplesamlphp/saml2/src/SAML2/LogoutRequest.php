<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityKey;

use SAML2\XML\saml\NameID;

/**
 * Class for SAML 2 logout request messages.
 *
 * @package SimpleSAMLphp
 */
class LogoutRequest extends Request
{
    /**
     * The expiration time of this request.
     *
     * @var int|null
     */
    private $notOnOrAfter = null;

    /**
     * The encrypted NameID in the request.
     *
     * If this is not null, the NameID needs decryption before it can be accessed.
     *
     * @var \DOMElement|null
     */
    private $encryptedNameId = null;

    /**
     * The name identifier of the session that should be terminated.
     *
     * @var \SAML2\XML\saml\NameID|null
     */
    private $nameId = null;

    /**
     * The SessionIndexes of the sessions that should be terminated.
     *
     * @var array
     */
    private $sessionIndexes = [];

    /**
     * The optional reason for the logout, typically a URN
     * See \SAML2\Constants::LOGOUT_REASON_*
     * From the standard section 3.7.3: "other values MAY be agreed on between participants"
     *
     * @var string|null
     */
    protected $reason = null;


    /**
     * Constructor for SAML 2 logout request messages.
     *
     * @param \DOMElement|null $xml The input message.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('LogoutRequest', $xml);

        $this->sessionIndexes = [];

        if ($xml === null) {
            return;
        }

        if ($xml->hasAttribute('NotOnOrAfter')) {
            $this->notOnOrAfter = Utils::xsDateTimeToTimestamp($xml->getAttribute('NotOnOrAfter'));
        }

        if ($xml->hasAttribute('Reason')) {
            $this->reason = $xml->getAttribute('Reason');
        }

        /** @var \DOMElement[] $nameId */
        $nameId = Utils::xpQuery($xml, './saml_assertion:NameID | ./saml_assertion:EncryptedID/xenc:EncryptedData');
        if (empty($nameId)) {
            throw new \Exception('Missing <saml:NameID> or <saml:EncryptedID> in <samlp:LogoutRequest>.');
        } elseif (count($nameId) > 1) {
            throw new \Exception('More than one <saml:NameID> or <saml:EncryptedD> in <samlp:LogoutRequest>.');
        }
        if ($nameId[0]->localName === 'EncryptedData') {
            /* The NameID element is encrypted. */
            $this->encryptedNameId = $nameId[0];
        } else {
            $this->nameId = new NameID($nameId[0]);
        }

        /** @var \DOMElement[] $sessionIndexes */
        $sessionIndexes = Utils::xpQuery($xml, './saml_protocol:SessionIndex');
        foreach ($sessionIndexes as $sessionIndex) {
            $this->sessionIndexes[] = trim($sessionIndex->textContent);
        }
    }


    /**
     * Retrieve the expiration time of this request.
     *
     * @return int|null The expiration time of this request.
     */
    public function getNotOnOrAfter() : ?int
    {
        return $this->notOnOrAfter;
    }


    /**
     * Set the expiration time of this request.
     *
     * @param int|null $notOnOrAfter The expiration time of this request.
     * @return void
     */
    public function setNotOnOrAfter(int $notOnOrAfter = null) : void
    {
        $this->notOnOrAfter = $notOnOrAfter;
    }

    /**
     * Retrieve the reason for this request.
     *
     * @return string|null The reason for this request.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }


    /**
     * Set the reason for this request.
     *
     * @param string|null $reason The optional reason for this request in URN format
     * @return void
     */
    public function setReason($reason = null): void
    {
        $this->reason = $reason;
    }


    /**
     * Check whether the NameId is encrypted.
     *
     * @return bool True if the NameId is encrypted, false if not.
     */
    public function isNameIdEncrypted() : bool
    {
        if ($this->encryptedNameId !== null) {
            return true;
        }

        return false;
    }


    /**
     * Encrypt the NameID in the LogoutRequest.
     *
     * @param XMLSecurityKey $key The encryption key.
     * @return void
     */
    public function encryptNameId(XMLSecurityKey $key) : void
    {
        if ($this->nameId === null) {
            throw new \Exception('Cannot encrypt NameID without a NameID set.');
        }
        /* First create a XML representation of the NameID. */
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
        $enc->type = XMLSecEnc::Element;

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
     * Decrypt the NameID in the LogoutRequest.
     *
     * @param XMLSecurityKey $key The decryption key.
     * @param array $blacklist Blacklisted decryption algorithms.
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
     * Retrieve the name identifier of the session that should be terminated.
     *
     * @throws \Exception
     * @return \SAML2\XML\saml\NameID|null The name identifier of the session that should be terminated.
     */
    public function getNameId() : ?NameID
    {
        if ($this->encryptedNameId !== null) {
            throw new \Exception('Attempted to retrieve encrypted NameID without decrypting it first.');
        }

        return $this->nameId;
    }


    /**
     * Set the name identifier of the session that should be terminated.
     *
     * @param \SAML2\XML\saml\NameID $nameId The name identifier of the session that should be terminated.
     * @return void
     */
    public function setNameId(NameID $nameId) : void
    {
        $this->nameId = $nameId;
    }


    /**
     * Retrieve the SessionIndexes of the sessions that should be terminated.
     *
     * @return array The SessionIndexes, or an empty array if all sessions should be terminated.
     */
    public function getSessionIndexes() : array
    {
        return $this->sessionIndexes;
    }


    /**
     * Set the SessionIndexes of the sessions that should be terminated.
     *
     * @param array $sessionIndexes The SessionIndexes, or an empty array if all sessions should be terminated.
     * @return void
     */
    public function setSessionIndexes(array $sessionIndexes) : void
    {
        $this->sessionIndexes = $sessionIndexes;
    }


    /**
     * Retrieve the sesion index of the session that should be terminated.
     *
     * @return string|null The sesion index of the session that should be terminated.
     */
    public function getSessionIndex() : ?string
    {
        if (empty($this->sessionIndexes)) {
            return null;
        }

        return $this->sessionIndexes[0];
    }


    /**
     * Set the sesion index of the session that should be terminated.
     *
     * @param string|null $sessionIndex The sesion index of the session that should be terminated.
     * @return void
     */
    public function setSessionIndex(string $sessionIndex = null) : void
    {
        if (is_null($sessionIndex)) {
            $this->sessionIndexes = [];
        } else {
            $this->sessionIndexes = [$sessionIndex];
        }
    }


    /**
     * Convert this logout request message to an XML element.
     *
     * @return \DOMElement This logout request.
     */
    public function toUnsignedXML() : DOMElement
    {
        if ($this->encryptedNameId === null && $this->nameId === null) {
            throw new \Exception('Cannot convert LogoutRequest to XML without a NameID set.');
        }

        $root = parent::toUnsignedXML();

        if ($this->notOnOrAfter !== null) {
            $root->setAttribute('NotOnOrAfter', gmdate('Y-m-d\TH:i:s\Z', $this->notOnOrAfter));
        }

        if ($this->reason !== null) {
            $root->setAttribute('Reason', $this->reason);
        }

        if ($this->encryptedNameId === null) {
            $this->nameId->toXML($root);
        } else {
            $eid = $root->ownerDocument->createElementNS(Constants::NS_SAML, 'saml:'.'EncryptedID');
            $root->appendChild($eid);
            $eid->appendChild($root->ownerDocument->importNode($this->encryptedNameId, true));
        }

        foreach ($this->sessionIndexes as $sessionIndex) {
            Utils::addString($root, Constants::NS_SAMLP, 'SessionIndex', $sessionIndex);
        }

        return $root;
    }
}
