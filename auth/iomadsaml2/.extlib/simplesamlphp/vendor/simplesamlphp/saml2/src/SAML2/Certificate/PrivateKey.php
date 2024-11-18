<?php

declare(strict_types=1);

namespace SAML2\Certificate;

use SAML2\Exception\InvalidArgumentException;

class PrivateKey extends Key
{
    /**
     * @param string $keyContents
     * @param string|null $passphrase
     * @throws InvalidArgumentException
     * @return PrivateKey
     */
    public static function create(string $keyContents, string $passphrase = null) : PrivateKey
    {
        $keyData = ['PEM' => $keyContents, self::USAGE_ENCRYPTION => true];
        if ($passphrase) {
            $keyData['passphrase'] = $passphrase;
        }

        return new self($keyData);
    }


    /**
     * @return string
     */
    public function getKeyAsString() : string
    {
        return $this->keyData['PEM'];
    }


    /**
     * @return string|null
     */
    public function getPassphrase() : ?string
    {
        return isset($this->keyData['passphrase']) ? $this->keyData['passphrase'] : null;
    }
}
