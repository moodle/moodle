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
 * Exception thrown for server error responses.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property-read string $command  The command that caused the BAD/NO error
 *                                 status.
 * @property-read array $resp_data  The response data array.
 * @property-read integer $status  Server error status.
 */
class Horde_Imap_Client_Exception_ServerResponse extends Horde_Imap_Client_Exception
{
    /**
     * Pipeline object.
     *
     * @var Horde_Imap_Client_Interaction_Pipeline
     */
    protected $_pipeline;

    /**
     * Server response object.
     *
     * @var Horde_Imap_Client_Interaction_Server
     */
    protected $_server;

    /**
     * Constructor.
     *
     * @param string $msg                                       Error message.
     * @param integer $code                                     Error code.
     * @param Horde_Imap_Client_Interaction_Server $server      Server ob.
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline ob.
     */
    public function __construct(
        $msg = null,
        $code = 0,
        Horde_Imap_Client_Interaction_Server $server,
        Horde_Imap_Client_Interaction_Pipeline $pipeline
    )
    {
        $this->details = strval($server->token);

        $this->_pipeline = $pipeline;
        $this->_server = $server;

        parent::__construct($msg, $code);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'command':
            return ($this->_server instanceof Horde_Imap_Client_Interaction_Server_Tagged)
                ? $this->_pipeline->getCmd($this->_server->tag)->getCommand()
                : null;

        case 'resp_data':
            return $this->_pipeline->data;

        case 'status':
            return $this->_server->status;

        default:
            return parent::__get($name);
        }
    }

}
