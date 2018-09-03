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
 * This class represents identification headers (RFC 5322 [3.6.4]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_Identification
extends Horde_Mime_Headers_Element_Single
{
    /**
     * Get the identification object for the header value.
     *
     * @return Horde_Mail_Rfc822_Identification  Identification object.
     */
    public function getIdentificationOb()
    {
        return new Horde_Mail_Rfc822_Identification($this->value);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // Mail: RFC 5322
            'in-reply-to',
            'references'
        );
    }

}
