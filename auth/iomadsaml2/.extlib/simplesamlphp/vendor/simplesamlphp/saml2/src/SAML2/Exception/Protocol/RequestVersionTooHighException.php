<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the SAML responder cannot process the request because the protocol version
 *   specified in the request message is a major upgrade from the highest protocol version supported.
 *
 * @package simplesamlphp/saml2
 */
class RequestVersionTooHighException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Protocol version too high.')
    {
        parent::__construct($message);
    }
}
