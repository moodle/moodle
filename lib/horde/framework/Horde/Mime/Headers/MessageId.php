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
 * This class represents the Message-ID header value (RFC 2822 [3.6.4]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_MessageId
extends Horde_Mime_Headers_Identification
{
    /**
     * Creates a Message-ID header conforming to RFC 2822 [3.6.4] and the
     * standards outlined in 'draft-ietf-usefor-message-id-01.txt'.
     *
     * @param string $prefix  A unique prefix to use.
     *
     * @return Horde_Mime_Headers_MessageId  Message-ID header object.
     */
    public static function create($prefix = 'Horde')
    {
        return new self(
            null,
            '<' . strval(new Horde_Support_Guid(array('prefix' => $prefix))) . '>'
        );
    }

    /**
     */
    public function __construct($name, $value)
    {
        parent::__construct('Message-ID', $value);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // Mail: RFC 5322
            'message-id'
        );
    }

}
