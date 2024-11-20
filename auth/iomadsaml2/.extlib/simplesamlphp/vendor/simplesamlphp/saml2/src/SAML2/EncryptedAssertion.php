<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use DOMNode;
use RobRichards\XMLSecLibs\XMLSecEnc;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Class handling encrypted assertions.
 *
 * @package SimpleSAMLphp
 */
class EncryptedAssertion
{
    /**
     * The current encrypted assertion.
     *
     * @var \DOMElement
     */
    private $encryptedData;


    /**
     * @var bool
     */
    protected $wasSignedAtConstruction = false;

    /**
     * Constructor for SAML 2 encrypted assertions.
     *
     * @param \DOMElement|null $xml The encrypted assertion XML element.
     * @throws \Exception
     */
    public function __construct(DOMElement $xml = null)
    {
        if ($xml === null) {
            return;
        }

        /** @var \DOMElement[] $data */
        $data = Utils::xpQuery($xml, './xenc:EncryptedData');
        if (empty($data)) {
            throw new \Exception('Missing encrypted data in <saml:EncryptedAssertion>.');
        } elseif (count($data) > 1) {
            throw new \Exception('More than one encrypted data element in <saml:EncryptedAssertion>.');
        }
        $this->encryptedData = $data[0];
    }


    /**
     * @return bool
     */
    public function wasSignedAtConstruction() : bool
    {
        return $this->wasSignedAtConstruction;
    }

    /**
     * Set the assertion.
     *
     * @param \SAML2\Assertion $assertion The assertion.
     * @param XMLSecurityKey  $key       The key we should use to encrypt the assertion.
     * @throws \Exception
     * @return void
     */
    public function setAssertion(Assertion $assertion, XMLSecurityKey $key) : void
    {
        $xml = $assertion->toXML();

        Utils::getContainer()->debugMessage($xml, 'encrypt');

        $enc = new XMLSecEnc();
        $enc->setNode($xml);
        $enc->type = XMLSecEnc::Element;

        switch ($key->type) {
            case XMLSecurityKey::TRIPLEDES_CBC:
            case XMLSecurityKey::AES128_CBC:
            case XMLSecurityKey::AES192_CBC:
            case XMLSecurityKey::AES256_CBC:
            case XMLSecurityKey::AES128_GCM:
            case XMLSecurityKey::AES192_GCM:
            case XMLSecurityKey::AES256_GCM:
                $symmetricKey = $key;
                break;

            case XMLSecurityKey::RSA_1_5:
            case XMLSecurityKey::RSA_OAEP_MGF1P:
                $symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
                $symmetricKey->generateSessionKey();

                $enc->encryptKey($key, $symmetricKey);

                break;

            default:
                throw new \Exception('Unknown key type for encryption: '.$key->type);
        }

        /**
         * @var \DOMElement encryptedData
         * @psalm-suppress UndefinedClass
         */
        $this->encryptedData = $enc->encryptNode($symmetricKey);
    }


    /**
     * Retrieve the assertion.
     *
     * @param  XMLSecurityKey  $inputKey  The key we should use to decrypt the assertion.
     * @param  array           $blacklist Blacklisted decryption algorithms.
     * @return \SAML2\Assertion The decrypted assertion.
     */
    public function getAssertion(XMLSecurityKey $inputKey, array $blacklist = []) : Assertion
    {
        $assertionXML = Utils::decryptElement($this->encryptedData, $inputKey, $blacklist);

        Utils::getContainer()->debugMessage($assertionXML, 'decrypt');

        return new Assertion($assertionXML);
    }


    /**
     * Convert this encrypted assertion to an XML element.
     *
     * @param  \DOMNode|null $parentElement The DOM node the assertion should be created in.
     * @return \DOMElement   This encrypted assertion.
     */
    public function toXML(DOMNode $parentElement = null) : DOMElement
    {
        if ($parentElement === null) {
            $document = DOMDocumentFactory::create();
            $parentElement = $document;
        } else {
            $document = $parentElement->ownerDocument;
        }

        $root = $document->createElementNS(Constants::NS_SAML, 'saml:'.'EncryptedAssertion');
        $parentElement->appendChild($root);

        $root->appendChild($document->importNode($this->encryptedData, true));

        return $root;
    }
}
