<?php

declare(strict_types=1);

namespace SAML2\Signature;

use SAML2\Configuration\CertificateProvider;
use SAML2\SignedElement;

/**
 * Interface \SAML2\Validator\Responsible
 *
 * should be renamed.
 */
interface ChainedValidator extends ValidatorInterface
{
    /**
     * Test whether or not this link in the chain can validate the signedElement signature.
     *
     * @param \SAML2\SignedElement $signedElement
     * @param \SAML2\Configuration\CertificateProvider $configuration
     *
     * @return bool
     */
    public function canValidate(
        SignedElement $signedElement,
        CertificateProvider $configuration
    ) : bool;
}
