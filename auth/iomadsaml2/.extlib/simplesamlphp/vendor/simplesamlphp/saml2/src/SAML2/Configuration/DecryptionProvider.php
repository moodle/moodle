<?php

declare(strict_types=1);

namespace SAML2\Configuration;

interface DecryptionProvider
{
    /**
     * @return null|bool
     */
    public function isAssertionEncryptionRequired() : ?bool;


    /**
     * @return null|string
     */
    public function getSharedKey() : ?string;


    /**
     * @param string $name The name of the private key
     * @param bool $required Whether or not the private key must exist
     *
     * @return mixed
     */
    public function getPrivateKey(string $name, bool $required = null);



    /**
     * @return array|null
     */
    public function getBlacklistedAlgorithms() : ?array;
}
