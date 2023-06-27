<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the User-Agent header value.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_UserAgent
extends Horde_Mime_Headers_Element_Single
{
    /**
     * Creates a default system User-Agent header.
     *
     * @return Horde_Mime_Headers_Single_UserAgent  User-Agent header object.
     */
    public static function create($prefix = 'Horde')
    {
        return new self(
            null,
            'Horde Application Framework 5'
        );
    }

    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('User-Agent', $value);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // Mail: RFC 5322
            'user-agent'
        );
    }

}
