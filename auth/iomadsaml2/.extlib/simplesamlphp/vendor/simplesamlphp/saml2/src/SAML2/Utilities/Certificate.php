<?php

declare(strict_types=1);

namespace SAML2\Utilities;

/**
 * Collection of Utility functions specifically for certificates
 */
class Certificate
{
    /**
     * The pattern that the contents of a certificate should adhere to
     */
    const CERTIFICATE_PATTERN = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';

    /**
     * @param string $certificate
     *
     * @return bool
     */
    public static function hasValidStructure(string $certificate) : bool
    {
        return !!preg_match(self::CERTIFICATE_PATTERN, $certificate);
    }


    /**
     * @param string $X509CertificateContents
     *
     * @return string
     */
    public static function convertToCertificate(string $X509CertificateContents) : string
    {
        return "-----BEGIN CERTIFICATE-----\n"
                . chunk_split($X509CertificateContents, 64)
                . "-----END CERTIFICATE-----\n";
    }
}
