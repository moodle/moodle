<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the resource value provided in the request
 *   message is invalid or unrecognized.
 *
 * @package simplesamlphp/saml2
 */
class ResourceNotRecognizedException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Resource not recognized.')
    {
        parent::__construct($message);
    }
}
