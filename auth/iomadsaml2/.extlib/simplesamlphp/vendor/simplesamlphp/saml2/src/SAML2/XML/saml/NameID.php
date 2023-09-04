<?php

declare(strict_types=1);

namespace SAML2\XML\saml;

/**
 * Class representing the saml:NameID element.
 *
 * @author Jaime Pérez Crespo, UNINETT AS <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */
class NameID extends NameIDType
{

    /**
     * Set the name of this XML element to "saml:NameID"
     *
     * @var string
     */
    protected $nodeName = 'saml:NameID';
}
