<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An object representing a portion of an IMAP command that requires data
 * sent in a continuation response (RFC 3501 [2.2.1]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.10.0
 */
class Horde_Imap_Client_Interaction_Command_Continuation
{
    /**
     * Is this an optional continuation request?
     *
     * @since 2.13.0
     * @var boolean
     */
    public $optional = false;

    /**
     * Closure function to run after continuation response.
     *
     * @var Closure
     */
    protected $_closure;

    /**
     * Constructor.
     *
     * @param Closure $closure  A function to run after the continuation
     *                          response is received.  It receives one
     *                          argument - a Continuation object - and should
     *                          return a list of arguments to send to the
     *                          server (via a
     *                          Horde_Imap_Client_Data_Format_List object).
     */
    public function __construct($closure)
    {
        $this->_closure = $closure;
    }

    /**
     * Calls the closure object.
     *
     * @param Horde_Imap_Client_Interaction_Server_Continuation $ob  Continuation
     *                                                               object.
     *
     * @return Horde_Imap_Client_Data_Format_List  Further commands to issue
     *                                             to the server.
     */
    public function getCommands(
        Horde_Imap_Client_Interaction_Server_Continuation $ob
    )
    {
        $closure = $this->_closure;
        return $closure($ob);
    }

}
