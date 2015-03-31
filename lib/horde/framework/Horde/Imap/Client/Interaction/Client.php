<?php
/**
 * Copyright 2012-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An object representing an IMAP client command interaction (RFC 3501
 * [2.2.1]).
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @copyright  2012-2014 Horde LLC
 * @deprecated
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Imap_Client
 */
class Horde_Imap_Client_Interaction_Client extends Horde_Imap_Client_Data_Format_List
{
    /**
     * The command tag.
     *
     * @var string
     */
    public $tag;

    /**
     * Constructor.
     *
     * @param string $tag  The tag to use. If not set, will be automatically
     *                     generated.
     */
    public function __construct($tag = null)
    {
        $this->tag = is_null($tag)
            ? substr(strval(new Horde_Support_Randomid()), 0, 10)
            : strval($tag);

        parent::__construct($this->tag);
    }

    /**
     * Get the command.
     *
     * @return string  The command.
     */
    public function getCommand()
    {
        return isset($this->_data[1])
            ? $this->_data[1]
            : null;
    }

}
