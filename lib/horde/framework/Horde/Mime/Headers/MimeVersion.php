<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the MIME-Version header value (RFC 2046[4]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_MimeVersion
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Extension_Mime
{
    /**
     * Creates a MIME-Version header, conforming to the MIME specification as
     * detailed in RFC 2045.
     *
     * @return Horde_Mime_Headers_MimeVersion  MIME-Version header object.
     */
    public static function create()
    {
        return new self(null, '1.0');
    }

    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('MIME-Version', $value);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // MIME: RFC 2045
            'mime-version'
        );
    }

}
