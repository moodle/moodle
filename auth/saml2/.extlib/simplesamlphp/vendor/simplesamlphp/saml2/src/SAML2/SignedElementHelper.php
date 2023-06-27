<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;
use DOMNode;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Helper class for processing signed elements.
 *
 * Can either be inherited from, or can be used by proxy.
 *
 * @package SimpleSAMLphp
 */
class SignedElementHelper extends SignedElement
{
    /**
     * Available methods for validating this message.
     *
     * @var array
     */
    private $validators;

    /**
     * How long this element is valid, as a unix timestamp.
     *
     * @var int|null
     */
    public $validUntil;

    /**
     * The length of time this element can be cached, as string.
     *
     * @var string|null
     */
    public $cacheDuration;


    /**
     * Initialize the helper class.
     *
     * @param \DOMElement|null $xml The XML element which may be signed.
     */
    protected function __construct(DOMElement $xml = null)
    {
        $this->certificates = [];
        $this->validators = [];

        if ($xml === null) {
            return;
        }

        /* Validate the signature element of the message. */
        try {
            $sig = Utils::validateElement($xml);

            if ($sig) {
                $this->certificates = $sig['Certificates'];
                $this->validators[] = [
                    'Function' => [Utils::class, 'validateSignature'],
                    'Data' => $sig,
                ];
            }
        } catch (\Exception $e) {
            /* Ignore signature validation errors. */
        }
    }


    /**
     * Add a method for validating this element.
     *
     * This function is used for custom validation extensions
     *
     * @param callable $function The function which should be called.
     * @param mixed $data The data that should be included as the first parameter to the function.
     * @return void
     */
    public function addValidator(callable $function, $data) : void
    {
        $this->validators[] = [
            'Function' => $function,
            'Data' => $data,
        ];
    }


    /**
     * Validate this element against a public key.
     *
     * true is returned on success, false is returned if we don't have any
     * signature we can validate. An exception is thrown if the signature
     * validation fails.
     *
     * @param  XMLSecurityKey $key The key we should check against.
     * @return bool True on success, false when we don't have a signature.
     * @throws \Exception
     * @return bool        true on success, false when we don't have a signature.
     */
    public function validate(XMLSecurityKey $key) : bool
    {
        if (count($this->validators) === 0) {
            return false;
        }

        $exceptions = [];

        foreach ($this->validators as $validator) {
            $function = $validator['Function'];
            $data = $validator['Data'];

            try {
                call_user_func($function, $data, $key);
                /* We were able to validate the message with this validator. */

                return true;
            } catch (\Exception $e) {
                $exceptions[] = $e;
            }
        }

        /* No validators were able to validate the message. */
        throw $exceptions[0];
    }


    /**
     * Retrieve the private key we should use to sign the message.
     *
     * @return XMLSecurityKey|null The key, or NULL if no key is specified.
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


    /**
     * Set the certificates that should be included in the message.
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
     * Retrieve the certificates that are included in the message.
     *
     * @return array An array of certificates.
     */
    public function getCertificates() : array
    {
        return $this->certificates;
    }


    /**
     * Retrieve certificates that sign this element.
     *
     * @return array Array with certificates.
     */
    public function getValidatingCertificates() : array
    {
        $ret = [];
        foreach ($this->certificates as $cert) {
            /* Construct a PEM formatted certificate */
            $pemCert = "-----BEGIN CERTIFICATE-----\n".
                chunk_split($cert, 64).
                "-----END CERTIFICATE-----\n";

            /* Extract the public key from the certificate for validation. */
            $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type'=>'public']);
            $key->loadKey($pemCert);

            try {
                /* Check the signature. */
                if ($this->validate($key)) {
                    $ret[] = $cert;
                }
            } catch (\Exception $e) {
                /* This certificate does not sign this element. */
            }
        }

        return $ret;
    }


    /**
     * Collect the value of the validUntil property.
     *
     * @return int|null
     */
    public function getValidUntil() : ?int
    {
        return $this->validUntil;
    }

    /**
     * Set the value of the validUntil property.
     *
     * @param int|null $validUntil
     * @return void
     */
    public function setValidUntil(int $validUntil = null) : void
    {
        $this->validUntil = $validUntil;
    }


    /**
     * Collect the value of the cacheDuration property.
     *
     * @return string|null
     */
    public function getCacheDuration() : ?string
    {
        return $this->cacheDuration;
    }


    /**
     * Set the value of the cacheDuration property.
     *
     * @param string|null $cacheDuration
     * @return void
     */
    public function setCacheDuration(string $cacheDuration = null) : void
    {
        $this->cacheDuration = $cacheDuration;
    }


    /**
     * Sign the given XML element.
     *
     * @param \DOMElement $root The element we should sign.
     * @param \DOMNode|null $insertBefore The element we should insert the signature node before.
     * @return \DOMElement|null
     */
    protected function signElement(DOMElement $root, DOMNode $insertBefore = null) : ?DOMElement
    {
        if ($this->signatureKey === null) {
            /* We cannot sign this element. */
            return null;
        }

        Utils::insertSignature($this->signatureKey, $this->certificates, $root, $insertBefore);

        return $root;
    }
}
