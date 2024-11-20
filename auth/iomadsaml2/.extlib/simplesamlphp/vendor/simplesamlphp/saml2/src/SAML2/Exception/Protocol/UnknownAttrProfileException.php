<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that an entity that has no knowledge of a particular attribute profile
 *   has been presented with an attribute drawn from that profile.
 *
 * @package simplesamlphp/saml2
 */
class UnknownAttrProfileException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Unknown attribute profile.')
    {
        parent::__construct($message);
    }
}
