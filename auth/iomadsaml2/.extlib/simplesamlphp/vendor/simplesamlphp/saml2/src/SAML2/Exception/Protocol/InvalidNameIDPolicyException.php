<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the provider cannot or will not support the requested NameIDPolicy.
 *
 * @package simplesamlphp/saml2
 */
class InvalidNameIDPolicyException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Invalid NameID policy.')
    {
        parent::__construct($message);
    }
}
