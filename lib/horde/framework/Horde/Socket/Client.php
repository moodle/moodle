<?php

namespace Horde\Socket;

/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Socket_Client
 */

/**
 * Utility interface for establishing a stream socket client.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Socket_Client
 *
 * @property-read boolean $connected  Is there an active connection?
 * @property-read boolean $secure  Is the active connection secure?
 */
class Client
{
    /**
     * Is there an active connection?
     *
     * @var boolean
     */
    protected $_connected = false;

    /**
     * Configuration parameters.
     *
     * @var array
     */
    protected $_params;

    /**
     * Is the connection secure?
     *
     * @var boolean
     */
    protected $_secure = false;

    /**
     * The actual socket.
     *
     * @var resource
     */
    protected $_stream;

    /**
     * Constructor.
     *
     * @param string $host      Hostname of remote server (can contain
     *                          protocol prefx).
     * @param integer $port     Port number of remote server.
     * @param integer $timeout  Connection timeout (in seconds).
     * @param mixed $secure     Security layer requested. One of:
     *   - false: (No encryption) [DEFAULT]
     *   - 'ssl': (Auto-detect SSL version)
     *   - 'sslv2': (Force SSL version 3)
     *   - 'sslv3': (Force SSL version 2)
     *   - 'tls': (TLS; started via protocol-level negotation over unencrypted
     *     channel)
     *   - 'tlsv1': (TLS version 1.x connection)
     *   - true: (TLS if available/necessary)
     * @param array $context    Any context parameters passed to
     *                          stream_create_context().
     * @param array $params     Additional options.
     *
     * @throws Horde\Socket\Client\Exception
     */
    public function __construct(
        $host, $port = null, $timeout = 30, $secure = false,
        $context = array(), array $params = array()
    )
    {
        if ($secure && !extension_loaded('openssl')) {
            if ($secure !== true) {
                throw new \InvalidArgumentException('Secure connections require the PHP openssl extension.');
            }
            $secure = false;
        }

        $context = array_replace_recursive(
            array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false
                )
            ),
            $context
        );

        $this->_params = $params;

        $this->_connect($host, $port, $timeout, $secure, $context);
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'connected':
            return $this->_connected;

        case 'secure':
            return $this->_secure;
        }
    }

    /**
     * This object can not be cloned.
     */
    public function __clone()
    {
        throw new \LogicException('Object cannot be cloned.');
    }

    /**
     * This object can not be serialized.
     */
    public function __sleep()
    {
        throw new \LogicException('Object can not be serialized.');
    }

    /**
     * Start a TLS connection.
     *
     * @return boolean  Whether TLS was successfully started.
     */
    public function startTls()
    {
        if ($this->connected && !$this->secure) {
            if (defined('STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT')) {
                $mode = STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT
                    | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT
                    | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            } else {
                $mode = STREAM_CRYPTO_METHOD_TLS_CLIENT;
            }
            if (@stream_socket_enable_crypto($this->_stream, true, $mode) === true) {
                $this->_secure = true;
                return true;
            }
        }

        return false;
    }

    /**
     * Close the connection.
     */
    public function close()
    {
        if ($this->connected) {
            @fclose($this->_stream);
            $this->_connected = $this->_secure = false;
            $this->_stream = null;
        }
    }

    /**
     * Returns information about the connection.
     *
     * Currently returns four entries in the result array:
     *  - timed_out (bool): The socket timed out waiting for data
     *  - blocked (bool): The socket was blocked
     *  - eof (bool): Indicates EOF event
     *  - unread_bytes (int): Number of bytes left in the socket buffer
     *
     * @throws Horde\Socket\Client\Exception
     * @return array  Information about existing socket resource.
     */
    public function getStatus()
    {
        $this->_checkStream();
        return stream_get_meta_data($this->_stream);
    }

    /**
     * Returns a line of data.
     *
     * @param int $size  Reading ends when $size - 1 bytes have been read,
     *                   or a newline or an EOF (whichever comes first).
     *
     * @throws Horde\Socket\Client\Exception
     * @return string  $size bytes of data from the socket
     */
    public function gets($size)
    {
        $this->_checkStream();
        $data = @fgets($this->_stream, $size);
        if ($data === false) {
            throw new Client\Exception('Error reading data from socket');
        }
        return $data;
    }

    /**
     * Returns a specified amount of data.
     *
     * @param integer $size  The number of bytes to read from the socket.
     *
     * @throws Horde\Socket\Client\Exception
     * @return string  $size bytes of data from the socket.
     */
    public function read($size)
    {
        $this->_checkStream();
        $data = @fread($this->_stream, $size);
        if ($data === false) {
            throw new Client\Exception('Error reading data from socket');
        }
        return $data;
    }

    /**
     * Writes data to the stream.
     *
     * @param string $data  Data to write.
     *
     * @throws Horde\Socket\Client\Exception
     */
    public function write($data)
    {
        $this->_checkStream();
        if (!@fwrite($this->_stream, $data)) {
            $meta_data = $this->getStatus();
            if (!empty($meta_data['timed_out'])) {
                throw new Client\Exception('Timed out writing data to socket');
            }
            throw new Client\Exception('Error writing data to socket');
        }
    }

    /* Internal methods. */

    /**
     * Connect to the remote server.
     *
     * @see __construct()
     *
     * @throws Horde\Socket\Client\Exception
     */
    protected function _connect(
        $host, $port, $timeout, $secure, $context, $retries = 0
    )
    {
        $conn = '';
        if (!strpos($host, '://')) {
            switch (strval($secure)) {
            case 'ssl':
            case 'sslv2':
            case 'sslv3':
                $conn = $secure . '://';
                $this->_secure = true;
                break;

            case 'tlsv1':
                $conn = 'tls://';
                $this->_secure = true;
                break;

            case 'tls':
            default:
                $conn = 'tcp://';
                break;
            }
        }
        $conn .= $host;
        if ($port) {
            $conn .= ':' . $port;
        }

        $this->_stream = @stream_socket_client(
            $conn,
            $error_number,
            $error_string,
            $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create($context)
        );

        if ($this->_stream === false) {
            /* From stream_socket_client() page: a function return of false,
             * with an error code of 0, indicates a "problem initializing the
             * socket". These kind of issues are seen on the same server
             * (and even the same user account) as sucessful connections, so
             * these are likely transient issues. Retry up to 3 times in these
             * instances. */
            if (!$error_number && ($retries < 3)) {
                return $this->_connect($host, $port, $timeout, $secure, $context, ++$retries);
            }

            $e = new Client\Exception(
                'Error connecting to server.'
            );
            $e->details = sprintf("[%u] %s", $error_number, $error_string);
            throw $e;
        }

        stream_set_timeout($this->_stream, $timeout);

        if (function_exists('stream_set_read_buffer')) {
            stream_set_read_buffer($this->_stream, 0);
        }
        stream_set_write_buffer($this->_stream, 0);

        $this->_connected = true;
    }

    /**
     * Throws an exception is the stream is not a resource.
     *
     * @throws Horde\Socket\Client\Exception
     */
    protected function _checkStream()
    {
        if (!is_resource($this->_stream)) {
            throw new Client\Exception('Not connected');
        }
    }

}
