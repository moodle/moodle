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
 * This class represents the Subject header value (RFC 5322).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_Subject
extends Horde_Mime_Headers_Element_Single
{
    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Subject', $value);
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
            // Mail: RFC 5322
            'subject'
        );
    }

}
