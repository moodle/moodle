<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error used by a session authority to indicate to a session participant
 *   that it was not able to propagate logout to all other session participants.
 *
 * @package simplesamlphp/saml2
 */
class PartialLogoutException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Unable to propagate logout to all other session participants.')
    {
        parent::__construct($message);
    }
}
