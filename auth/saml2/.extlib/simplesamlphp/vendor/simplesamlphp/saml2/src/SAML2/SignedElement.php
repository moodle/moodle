<?php

declare(strict_types=1);

namespace SAML2;

use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Abstract class to a SAML 2 element which may be signed.
 *
 * @package SimpleSAMLphp
 */
abstract class SignedElement
{
    /**
     * The private key we should use to sign the message.
     *
     * The private key can be null, in which case the message is sent unsigned.
     *
     * @var XMLSecurityKey|null
     */
    protected $signatureKey;

    /**
     * List of certificates that should be included in the message.
     *
     * @var array
     */
    protected $certificates = [];


    /**
     * Validate this element against a public key.
     *
     * If no signature is present, false is returned. If a signature is present,
     * but cannot be verified, an exception will be thrown.
     *
     * @param  XMLSecurityKey $key The key we should check against.
     * @return bool True if successful, false if we don't have a signature that can be verified.
     */
    abstract public function validate(XMLSecurityKey $key) : bool;


    /**
     * Set the certificates that should be included in the element.
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
     * Retrieve the certificates that are included in the message.
     *
     * @return array An array of certificates
     */
    public function getCertificates() : array
    {
        return $this->certificates;
    }


    /**
     * Retrieve the private key we should use to sign the message.
     *
     * @return XMLSecurityKey|null The key, or NULL if no key is specified
     */
    public function getSignatureKey() : ?XMLSecurityKey
    {
        return $this->signatureKey;
    }


    /**
     * Set the private key we should use to sign the message.
     *
     * If the key is null, the message will be sent unsigned.
     *
     * @param XMLSecurityKey|null $signatureKey
     * @return void
     */
    public function setSignatureKey(XMLSecurityKey $signatureKey = null) : void
    {
        $this->signatureKey = $signatureKey;
    }
}
