<?php

declare(strict_types=1);

namespace SAML2;

use DOMElement;

/**
 * Class for SAML 2 LogoutResponse messages.
 *
 * @package SimpleSAMLphp
 */
class LogoutResponse extends StatusResponse
{
    /**
     * Constructor for SAML 2 response messages.
     *
     * @param \DOMElement|null $xml     The input message.
     */
    public function __construct(DOMElement $xml = null)
    {
        parent::__construct('LogoutResponse', $xml);

        /* No new fields added by LogoutResponse. */
    }
}
