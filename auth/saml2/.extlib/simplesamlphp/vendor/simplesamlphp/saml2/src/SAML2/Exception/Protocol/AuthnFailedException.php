<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the provider was unable to succesfully authenticate the principal.
 *
 * @package simplesamlphp/saml2
 */
class AuthnFailedException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Authentication failed.')
    {
        parent::__construct($message);
    }
}
