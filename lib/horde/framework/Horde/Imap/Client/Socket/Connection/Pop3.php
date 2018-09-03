<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * PHP stream connection to the POP3 server.
 *
 * NOTE: This class is NOT intended to be accessed outside of the package.
 * There is NO guarantees that the API of this class will not change across
 * versions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @internal
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Socket_Connection_Pop3
extends Horde_Imap_Client_Socket_Connection_Base
{
    /**
     */
    protected $_protocol = 'pop3';

    /**
     * Writes data to the POP3 output stream.
     *
     * @param string $data    String data.
     * @param boolean $debug  Output line to debug?
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function write($data, $debug = true)
    {
        if (fwrite($this->_stream, $data . "\r\n") === false) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Server write error."),
                Horde_Imap_Client_Exception::SERVER_WRITEERROR
            );
        }

        if ($debug) {
            $this->_params['debug']->client($data);
        }
    }

    /**
     * Read data from incoming POP3 stream.
     *
     * @param integer $size  UNUSED: The number of bytes to read from the
     *                       socket.
     *
     * @return string  Line of data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function read($size = null)
    {
        if (feof($this->_stream)) {
            $this->close();
            $this->_params['debug']->info(
                'ERROR: Server closed the connection.'
            );
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Server closed the connection unexpectedly."),
                Horde_Imap_Client_Exception::DISCONNECT
            );
        }

        if (($read = fgets($this->_stream)) === false) {
            $this->_params['debug']->info('ERROR: read/timeout error.');
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Error when communicating with the mail server."),
                Horde_Imap_Client_Exception::SERVER_READERROR
            );
        }

        $this->_params['debug']->server(rtrim($read, "\r\n"));

        return $read;
    }

}
