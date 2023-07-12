<?php

declare(strict_types=1);

namespace SAML2\Exception\Protocol;

use SAML2\Exception\ProtocolViolationException;

/**
 * A SAML error indicating that unexpected or invalid content was encountered
 *   within a <saml:Attribute> or <saml:AttributeValue> element.
 *
 * @package simplesamlphp/saml2
 */
class InvalidAttrNameOrValueException extends ProtocolViolationException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = 'Invalid attribute name or value.')
    {
        parent::__construct($message);
    }
}
