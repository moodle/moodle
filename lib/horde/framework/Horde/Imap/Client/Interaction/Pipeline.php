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
 * An object representing a series of IMAP client commands (RFC 3501 [2.2.1])
 * to be processed at the same time.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.10.0
 *
 * @property-read boolean $finished  True if all commands have finished.
 */
class Horde_Imap_Client_Interaction_Pipeline implements Countable, IteratorAggregate
{
    /**
     * Data storage from server responses.
     *
     * @var array
     */
    public $data = array(
        'modseqs' => array(),
        'modseqs_nouid' => array()
    );

    /**
     * Fetch results.
     *
     * @var Horde_Imap_Client_Fetch_Results
     */
    public $fetch;

    /**
     * The list of commands.
     *
     * @var array
     */
    protected $_commands = array();

    /**
     * The list of commands to complete.
     *
     * @var array
     */
    protected $_todo = array();

    /**
     * Constructor.
     *
     * @param Horde_Imap_Client_Fetch_Results $fetch  Fetch results object.
     */
    public function __construct(Horde_Imap_Client_Fetch_Results $fetch)
    {
        $this->fetch = $fetch;
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'finished':
            return empty($this->_todo);
        }
    }

    /**
     * Add a command to the pipeline.
     *
     * @param Horde_Imap_Client_Interaction_Command $cmd  Command object.
     * @param boolean $top                                Add command to top
     *                                                    of queue?
     */
    public function add(Horde_Imap_Client_Interaction_Command $cmd,
                        $top = false)
    {
        if ($top) {
            // This won't re-index keys, which may be numerical.
            $this->_commands = array($cmd->tag => $cmd) + $this->_commands;
        } else {
            $this->_commands[$cmd->tag] = $cmd;
        }
        $this->_todo[$cmd->tag] = true;
    }

    /**
     * Mark a command as completed.
     *
     * @param Horde_Imap_Client_Interaction_Server_Tagged $resp  Tagged server
     *                                                           response.
     *
     * @return Horde_Imap_Client_Interaction_Command  Command that was
     *                                                completed. Returns null
     *                                                if tagged response
     *                                                is not contained in this
     *                                                pipeline object.
     */
    public function complete(Horde_Imap_Client_Interaction_Server_Tagged $resp)
    {
        if (isset($this->_commands[$resp->tag])) {
            $cmd = $this->_commands[$resp->tag];
            $cmd->response = $resp;
            unset($this->_todo[$resp->tag]);
        } else {
            /* This can be reached if a previous pipeline action was aborted,
             * e.g. via an Exception. */
            $cmd = null;
        }

        return $cmd;
    }

    /**
     * Return the command for a given tag.
     *
     * @param string $tag  The command tag.
     *
     * @return Horde_Imap_Client_Interaction_Command  A command object (or
     *                                                null if the tag does
     *                                                not exist).
     */
    public function getCmd($tag)
    {
        return isset($this->_commands[$tag])
            ? $this->_commands[$tag]
            : null;
    }

    /* Countable methods. */

    /**
     */
    public function count()
    {
        return count($this->_commands);
    }

    /* IteratorAggregate methods. */

    /**
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_commands);
    }

}
