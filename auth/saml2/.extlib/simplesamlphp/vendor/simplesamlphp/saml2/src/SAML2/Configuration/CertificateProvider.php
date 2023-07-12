<?php

declare(strict_types=1);

namespace SAML2\Configuration;

/**
 * CertificateProvider interface.
 */
interface CertificateProvider extends Queryable
{
    /**
     * Returns an array or \Traversable of keys, where each element represents a configured key.
     * A configured key itself is an array or object implementing ArrayAccess where the array key/property is the
     * configuration key and the value is the configured value.
     *
     * @return null|array|\Traversable
     */
    public function getKeys();


    /**
     * Returns the contents of an X509 pem certificate, without the '-----BEGIN CERTIFICATE-----' and
     * '-----END CERTIFICATE-----'.
     *
     * @return null|string
     */
    public function getCertificateData() : ?string;


    /**
     * Returns the full path to the (local) file that contains the X509 pem certificate.
     *
     * @return null|string
     */
    public function getCertificateFile() : ?string;
}
