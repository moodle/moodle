<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the response message would contain more elements than
 *   the SAML responder is able to return.
 *
 * @package simplesamlphp/saml2
 */
class TooManyResponsesException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Too many responses.')
    {
        parent::__construct($message);
    }
}
