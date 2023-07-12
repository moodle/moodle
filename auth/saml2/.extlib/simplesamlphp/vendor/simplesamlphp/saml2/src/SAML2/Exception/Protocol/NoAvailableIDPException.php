<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error used by an intermediary to indicate that none of the IDPEntry Loc-attributes
 *   can be resolved or that none of the supported identity providers are available.
 *
 * @package simplesamlphp/saml2
 */
class NoAvailableIDPException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'No IDP available.')
    {
        parent::__construct($message);
    }
}
