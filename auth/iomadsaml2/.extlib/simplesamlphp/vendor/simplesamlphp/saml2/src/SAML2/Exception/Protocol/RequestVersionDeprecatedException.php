<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the SAML responder cannot any requests with the protocol
 *   version specified in the request.
 *
 * @package simplesamlphp/saml2
 */
class RequestVersionDeprecatedException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Deprecated version used.')
    {
        parent::__construct($message);
    }
}
