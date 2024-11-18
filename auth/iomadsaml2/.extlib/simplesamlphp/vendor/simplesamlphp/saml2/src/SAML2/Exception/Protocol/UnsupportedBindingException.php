<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the SAML provider cannot properly fullfil the request using
 *   the protocol binding specified in the request.
 *
 * @package simplesamlphp/saml2
 */
class UnsupportedBindingException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Unsupported binding.')
    {
        parent::__construct($message);
    }
}
