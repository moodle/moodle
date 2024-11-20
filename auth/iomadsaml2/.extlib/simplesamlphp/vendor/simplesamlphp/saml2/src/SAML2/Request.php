<?php

declare(strict_types=1);

namespace SAML2;

/**
 * Base class for all SAML 2 request messages.
 *
 * Implements samlp:RequestAbstractType. All of the elements in that type is
 * stored in the \SAML2\Message class, and this class is therefore empty. It
 * is included mainly to make it easy to separate requests from responses.
 *
 * @package SimpleSAMLphp
 */
abstract class Request extends Message
{
}
