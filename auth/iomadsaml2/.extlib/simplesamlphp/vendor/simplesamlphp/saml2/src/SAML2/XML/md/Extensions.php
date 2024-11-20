<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use DOMElement;

use SAML2\Constants;
use SAML2\Utils;
use SAML2\XML\alg\Common as ALG;
use SAML2\XML\alg\DigestMethod;
use SAML2\XML\alg\SigningMethod;
use SAML2\XML\Chunk;
use SAML2\XML\mdattr\EntityAttributes;
use SAML2\XML\mdrpi\Common as MDRPI;
use SAML2\XML\mdrpi\PublicationInfo;
use SAML2\XML\mdrpi\RegistrationInfo;
use SAML2\XML\mdui\Common as MDUI;
use SAML2\XML\mdui\DiscoHints;
use SAML2\XML\mdui\UIInfo;
use SAML2\XML\shibmd\Scope;

/**
 * Class for handling SAML2 metadata extensions.
 * @package SimpleSAMLphp
 */
class Extensions
{
    /**
     * Get a list of Extensions in the given element.
     *
     * @param \DOMElement $parent The element that may contain the md:Extensions element.
     * @return (\SAML2\XML\shibmd\Scope|
     *          \SAML2\XML\mdattr\EntityAttributes|
     *          \SAML2\XML\mdrpi\RegistrationInfo|
     *          \SAML2\XML\mdrpi\PublicationInfo|
     *          \SAML2\XML\mdui\UIInfo|
     *          \SAML2\XML\mdui\DiscoHints|
     *          \SAML2\XML\alg\DigestMethod|
     *          \SAML2\XML\alg\SigningMethod|
     *          \SAML2\XML\Chunk)[]  Array of extensions.
     */
    public static function getList(DOMElement $parent) : array
    {
        $ret = [];
        $supported = [
            Scope::NS => [
                'Scope' => Scope::class,
            ],
            EntityAttributes::NS => [
                'EntityAttributes' => EntityAttributes::class,
            ],
            MDRPI::NS_MDRPI => [
                'RegistrationInfo' => RegistrationInfo::class,
                'PublicationInfo' => PublicationInfo::class,
            ],
            MDUI::NS => [
                'UIInfo' => UIInfo::class,
                'DiscoHints' => DiscoHints::class,
            ],
            ALG::NS => [
                'DigestMethod' => DigestMethod::class,
                'SigningMethod' => SigningMethod::class,
            ],
        ];

        /** @var \DOMElement $node */
        foreach (Utils::xpQuery($parent, './saml_metadata:Extensions/*') as $node) {
            if (!is_null($node->namespaceURI) && array_key_exists($node->namespaceURI, $supported) &&
                array_key_exists($node->localName, $supported[$node->namespaceURI])
            ) {
                $ret[] = new $supported[$node->namespaceURI][$node->localName]($node);
            } else {
                $ret[] = new Chunk($node);
            }
        }

        return $ret;
    }


    /**
     * Add a list of Extensions to the given element.
     *
     * @param \DOMElement $parent The element we should add the extensions to.
     * @param \SAML2\XML\Chunk[] $extensions List of extension objects.
     * @return void
     */
    public static function addList(DOMElement $parent, array $extensions) : void
    {
        if (empty($extensions)) {
            return;
        }

        $extElement = $parent->ownerDocument->createElementNS(Constants::NS_MD, 'md:Extensions');
        $parent->appendChild($extElement);

        foreach ($extensions as $ext) {
            $ext->toXML($extElement);
        }
    }
}
