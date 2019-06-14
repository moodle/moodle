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
 * PHP stream connection to the IMAP server.
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
class Horde_Imap_Client_Socket_Connection_Socket
extends Horde_Imap_Client_Socket_Connection_Base
{
    /**
     * If false, does not outpt the current line of client output to debug.
     *
     * @var boolean
     */
    public $client_debug = true;

    /**
     * Sending buffer.
     *
     * @var string
     */
    protected $_buffer = '';

    /**
     * Writes data to the IMAP output stream.
     *
     * @param string $data  String data.
     * @param boolean $eol  Append EOL?
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function write($data, $eol = false)
    {
        if ($eol) {
            $buffer = $this->_buffer;
            $debug = $this->client_debug;
            $this->_buffer = '';

            $this->client_debug = true;

            if (fwrite($this->_stream, $buffer . $data . ($eol ? "\r\n" : '')) === false) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Server write error."),
                    Horde_Imap_Client_Exception::SERVER_WRITEERROR
                );
            }

            if ($debug) {
                $this->_params['debug']->client($buffer . $data);
            }
        } else {
            $this->_buffer .= $data;
        }
    }

    /**
     * Writes literal data to the IMAP output stream.
     *
     * @param mixed $data      Either a stream resource, or Horde_Stream
     *                         object.
     * @param integer $length  The literal length.
     * @param boolean $binary  If true, this is binary data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function writeLiteral($data, $length, $binary = false)
    {
        $this->_buffer = '';
        $success = false;

        if ($data instanceof Horde_Stream) {
            $data = $data->stream;
        }

        if (rewind($data)) {
            $success = true;
            while (!feof($data)) {
                if ((($read_data = fread($data, 8192)) === false) ||
                    (fwrite($this->_stream, $read_data) === false)) {
                    $success = false;
                    break;
                }
            }
        }

        if (!$success) {
            $this->client_debug = true;
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Server write error."),
                Horde_Imap_Client_Exception::SERVER_WRITEERROR
            );
        }

        if ($this->client_debug && !empty($this->_params['debugliteral'])) {
            rewind($data);
            while (!feof($data)) {
                $this->_params['debug']->raw(fread($data, 8192));
            }
        } else {
            $this->_params['debug']->client('[' . ($binary ? 'BINARY' : 'LITERAL') . ' DATA: ' . $length . ' bytes]');
        }
    }

    /**
     * Read data from incoming IMAP stream.
     *
     * @param integer $size  UNUSED: The number of bytes to read from the
     *                       socket.
     *
     * @return Horde_Imap_Client_Tokenize  The tokenized data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function read($size = null)
    {
        $got_data = false;
        $literal_len = null;
        $token = new Horde_Imap_Client_Tokenize();

        do {
            if (feof($this->_stream)) {
                $this->close();
                $this->_params['debug']->info(
                    'ERROR: Server closed the connection.'
                );
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Mail server closed the connection unexpectedly."),
                    Horde_Imap_Client_Exception::DISCONNECT
                );
            }

            if (is_null($literal_len)) {
                $buffer = '';

                while (($in = fgets($this->_stream)) !== false) {
                    $got_data = true;

                    if (substr($in, -1) === "\n") {
                        $in = rtrim($in);
                        $this->_params['debug']->server($buffer . $in);
                        $token->add($in);
                        break;
                    }

                    $buffer .= $in;
                    $token->add($in);
                }

                /* Check for literal data. */
                if (is_null($len = $token->getLiteralLength())) {
                    break;
                }

                // Skip 0-length literal data.
                if ($len['length']) {
                    $binary = $len['binary'];
                    $literal_len = $len['length'];
                }

                continue;
            }

            $old_len = $literal_len;

            while (($literal_len > 0) && !feof($this->_stream)) {
                $in = fread($this->_stream, min($literal_len, 8192));
                /* Only store in stream if this is something more than a
                 * nominal number of bytes. */
                if ($old_len > 256) {
                    $token->addLiteralStream($in);
                } else {
                    $token->add($in);
                }

                if (!empty($this->_params['debugliteral'])) {
                    $this->_params['debug']->raw($in);
                }

                $got_data = true;
                $literal_len -= strlen($in);
            }

            $literal_len = null;

            if (empty($this->_params['debugliteral'])) {
                $this->_params['debug']->server('[' . ($binary ? 'BINARY' : 'LITERAL') . ' DATA: ' . $old_len . ' bytes]');
            }
        } while (true);

        if (!$got_data) {
            $this->_params['debug']->info('ERROR: read/timeout error.');
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Error when communicating with the mail server."),
                Horde_Imap_Client_Exception::SERVER_READERROR
            );
        }

        return $token;
    }

}
