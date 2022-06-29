<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class handles all MIME headers that don't have a specific class
 * defined for them.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 */
class Horde_Mime_Headers_Mime
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Extension_Mime
{
    /**
     */
    public static function getHandles()
    {
        return array(
            // MIME: RFC 1864
            'content-md5',
            // MIME: RFC 2110
            'content-base',
            // MIME: RFC 2424
            'content-duration',
            // MIME: RFC 2557
            'content-location',
            // MIME: RFC 2912 [3]
            'content-features',
            // MIME: RFC 3297
            'content-alternative'
        );
    }

}
