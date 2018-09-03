<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the Content-Description header value (RFC 2045[8]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.7.0
 */
class Horde_Mime_Headers_ContentDescription
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Extension_Mime
{
    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Content-Description', $value);
    }

    /**
     */
    protected function _sendEncode($opts)
    {
        return array(Horde_Mime::encode($this->value, $opts['charset']));
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // MIME: RFC 2045
            'content-description'
        );
    }

}
