<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error used by an intermediary to indicate that none of the identity providers
 *   in an IDPList are supported by the intermediary.
 *
 * @package simplesamlphp/saml2
 */
class NoSupportedIDPException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'No supported IDP.')
    {
        parent::__construct($message);
    }
}
