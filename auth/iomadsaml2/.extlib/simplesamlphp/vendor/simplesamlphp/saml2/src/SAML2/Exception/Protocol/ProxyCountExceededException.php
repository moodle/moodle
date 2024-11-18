<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that the responding provider cannot authenticate the principal
 *   and is not permitted to proxy the request further.
 *
 * @package simplesamlphp/saml2
 */
class ProxyCountExceededException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Proxy count exceeded.')
    {
        parent::__construct($message);
    }
}
