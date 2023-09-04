<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating the responding provider does not recognize the principal
 *   specified or implied by the request.
 *
 * @package simplesamlphp/saml2
 */
class UnknownPrincipalException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Unknown principal.')
    {
        parent::__construct($message);
    }
}
