<?php

namespace SimpleSAML\Module\adfs\SAML2\XML\fed;

/**
 * Class representing fed TokenTypesOffered.
 *
 * @package SimpleSAMLphp
 */

class TokenTypesOffered
{
    /**
     * Add tokentypesoffered to an XML element.
     *
     * @param \DOMElement $parent  The element we should append this endpoint to.
     * @return \DOMElement
     */
    public static function appendXML(\DOMElement $parent)
    {
        $e = $parent->ownerDocument->createElementNS(Constants::NS_FED, 'fed:TokenTypesOffered');
        $parent->appendChild($e);

        $tokentype = $parent->ownerDocument->createElementNS(Constants::NS_FED, 'fed:TokenType');
        $tokentype->setAttribute('Uri', 'urn:oasis:names:tc:SAML:1.0:assertion');
        $e->appendChild($tokentype);

        return $e;
    }
}
