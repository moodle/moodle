<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * o Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * o The names of the authors may not be used to endorse or promote
 *   products derived from this software without specific prior written
 *   permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * SMTP implementation.
 *
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @author     Jon Parise <jon@php.net>
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @copyright  2010-2016 Horde LLC
 * @deprecated Use Horde_Mail_Transport_Hordesmtp instead
 * @license    http://www.horde.org/licenses/bsd New BSD License
 * @package    Mail
 */
class Horde_Mail_Transport_Smtp extends Horde_Mail_Transport
{
    /* Error: Failed to create a Net_SMTP object */
    const ERROR_CREATE = 10000;

    /* Error: Failed to connect to SMTP server */
    const ERROR_CONNECT = 10001;

    /* Error: SMTP authentication failure */
    const ERROR_AUTH = 10002;

    /* Error: No From: address has been provided */
    const ERROR_FROM = 10003;

    /* Error: Failed to set sender */
    const ERROR_SENDER = 10004;

    /* Error: Failed to add recipient */
    const ERROR_RECIPIENT = 10005;

    /* Error: Failed to send data */
    const ERROR_DATA = 10006;

    /**
     * The SMTP greeting.
     *
     * @var string
     */
    public $greeting = null;

    /**
     * The SMTP queued response.
     *
     * @var string
     */
    public $queuedAs = null;

    /**
     * SMTP connection object.
     *
     * @var Net_SMTP
     */
    protected $_smtp = null;

    /**
     * The list of service extension parameters to pass to the Net_SMTP
     * mailFrom() command.
     *
     * @var array
     */
    protected $_extparams = array();

    /**
     * Constructor.
     *
     * @param array $params  Additional parameters:
     *   - auth: (mixed) SMTP authentication.
     *           This value may be set to true, false or the name of a
     *           specific authentication method. If the value is set to true,
     *           the Net_SMTP package will attempt to use the best
     *           authentication method advertised by the remote SMTP server.
     *           DEFAULT: false.
     *   - debug: (boolean) Activate SMTP debug mode?
     *            DEFAULT: false
     *   - host: (string) The server to connect to.
     *           DEFAULT: localhost
     *   - localhost: (string) Hostname or domain that will be sent to the
     *                remote SMTP server in the HELO / EHLO message.
     *                DEFAULT: localhost
     *   - password: (string) The password to use for SMTP auth.
     *               DEFAULT: NONE
     *   - persist: (boolean) Should the SMTP connection persist?
     *              DEFAULT: false
     *   - pipelining: (boolean) Use SMTP command pipelining.
     *                 Use SMTP command pipelining (specified in RFC 2920) if
     *                 the SMTP server supports it. This speeds up delivery
     *                 over high-latency connections.
     *                 DEFAULT: false (use default value from Net_SMTP)
     *   - port: (integer) The port to connect to.
     *           DEFAULT: 25
     *   - timeout: (integer) The SMTP connection timeout.
     *              DEFAULT: NONE
     *   - username: (string) The username to use for SMTP auth.
     *               DEFAULT: NONE
     */
    public function __construct(array $params = array())
    {
        $this->_params = array_merge(array(
            'auth' => false,
            'debug' => false,
            'host' => 'localhost',
            'localhost' => 'localhost',
            'password' => '',
            'persist' => false,
            'pipelining' => false,
            'port' => 25,
            'timeout' => null,
            'username' => ''
        ), $params);

        /* Destructor implementation to ensure that we disconnect from any
         * potentially-alive persistent SMTP connections. */
        register_shutdown_function(array($this, 'disconnect'));

        /* SMTP requires CRLF line endings. */
        $this->sep = "\r\n";
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        /* If we don't already have an SMTP object, create one. */
        $this->getSMTPObject();

        $headers = $this->_sanitizeHeaders($headers);

        /* Make sure the message has a trailing newline. */
        if (is_resource($body)) {
            fseek($body, -1, SEEK_END);
            switch (fgetc($body)) {
            case "\r":
                if (fgetc($body) != "\n") {
                    fputs($body, "\n");
                }
                break;

            default:
                fputs($body, "\r\n");
                break;
            }
            rewind($body);
        } elseif (substr($body, -2, 0) != "\r\n") {
            $body .= "\r\n";
        }

        try {
            list($from, $textHeaders) = $this->prepareHeaders($headers);
        } catch (Horde_Mail_Exception $e) {
            $this->_smtp->rset();
            throw $e;
        }

        try {
            $from = $this->_getFrom($from, $headers);
        } catch (Horde_Mail_Exception $e) {
            $this->_smtp->rset();
            throw new Horde_Mail_Exception('No From: address has been provided', self::ERROR_FROM);
        }

        $params = '';
        foreach ($this->_extparams as $key => $val) {
            $params .= ' ' . $key . (is_null($val) ? '' : '=' . $val);
        }

        $res = $this->_smtp->mailFrom($from, ltrim($params));
        if ($res instanceof PEAR_Error) {
            $this->_error(sprintf("Failed to set sender: %s", $from), $res, self::ERROR_SENDER);
        }

        try {
            $recipients = $this->parseRecipients($recipients);
        } catch (Horde_Mail_Exception $e) {
            $this->_smtp->rset();
            throw $e;
        }

        foreach ($recipients as $recipient) {
            $res = $this->_smtp->rcptTo($recipient);
            if ($res instanceof PEAR_Error) {
                $this->_error("Failed to add recipient: $recipient", $res, self::ERROR_RECIPIENT);
            }
        }

        /* Send the message's headers and the body as SMTP data. Net_SMTP does
         * the necessary EOL conversions. */
        $res = $this->_smtp->data($body, $textHeaders);
        list(,$args) = $this->_smtp->getResponse();

        if (preg_match("/Ok: queued as (.*)/", $args, $queued)) {
            $this->queuedAs = $queued[1];
        }

        /* We need the greeting; from it we can extract the authorative name
         * of the mail server we've really connected to. Ideal if we're
         * connecting to a round-robin of relay servers and need to track
         * which exact one took the email */
        $this->greeting = $this->_smtp->getGreeting();

        if ($res instanceof PEAR_Error) {
            $this->_error('Failed to send data', $res, self::ERROR_DATA);
        }

        /* If persistent connections are disabled, destroy our SMTP object. */
        if (!$this->_params['persist']) {
            $this->disconnect();
        }
    }

    /**
     * Connect to the SMTP server by instantiating a Net_SMTP object.
     *
     * @return Net_SMTP  The SMTP object.
     * @throws Horde_Mail_Exception
     */
    public function getSMTPObject()
    {
        if ($this->_smtp) {
            return $this->_smtp;
        }

        $this->_smtp = new Net_SMTP(
            $this->_params['host'],
            $this->_params['port'],
            $this->_params['localhost']
        );

        /* Set pipelining. */
        if ($this->_params['pipelining']) {
            $this->_smtp->pipelining = true;
        }

        /* If we still don't have an SMTP object at this point, fail. */
        if (!($this->_smtp instanceof Net_SMTP)) {
            throw new Horde_Mail_Exception('Failed to create a Net_SMTP object', self::ERROR_CREATE);
        }

        /* Configure the SMTP connection. */
        if ($this->_params['debug']) {
            $this->_smtp->setDebug(true);
        }

        /* Attempt to connect to the configured SMTP server. */
        $res = $this->_smtp->connect($this->_params['timeout']);
        if ($res instanceof PEAR_Error) {
            $this->_error('Failed to connect to ' . $this->_params['host'] . ':' . $this->_params['port'], $res, self::ERROR_CONNECT);
        }

        /* Attempt to authenticate if authentication has been enabled. */
        if ($this->_params['auth']) {
            $method = is_string($this->_params['auth'])
                ? $this->_params['auth']
                : '';

            $res = $this->_smtp->auth($this->_params['username'], $this->_params['password'], $method);
            if ($res instanceof PEAR_Error) {
                $this->_error("$method authentication failure", $res, self::ERROR_AUTH);
            }
        }

        return $this->_smtp;
    }

    /**
     * Add parameter associated with a SMTP service extension.
     *
     * @param string $keyword  Extension keyword.
     * @param string $value    Any value the keyword needs.
     */
    public function addServiceExtensionParameter($keyword, $value = null)
    {
        $this->_extparams[$keyword] = $value;
    }

    /**
     * Disconnect and destroy the current SMTP connection.
     *
     * @return boolean True if the SMTP connection no longer exists.
     */
    public function disconnect()
    {
        /* If we have an SMTP object, disconnect and destroy it. */
        if (is_object($this->_smtp) && $this->_smtp->disconnect()) {
            $this->_smtp = null;
        }

        /* We are disconnected if we no longer have an SMTP object. */
        return ($this->_smtp === null);
    }

    /**
     * Build a standardized string describing the current SMTP error.
     *
     * @param string $text       Custom string describing the error context.
     * @param PEAR_Error $error  PEAR_Error object.
     * @param integer $e_code    Error code.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _error($text, $error, $e_code)
    {
        /* Split the SMTP response into a code and a response string. */
        list($code, $response) = $this->_smtp->getResponse();

        /* Abort current SMTP transaction. */
        $this->_smtp->rset();

        /* Build our standardized error string. */
        throw new Horde_Mail_Exception($text . ' [SMTP: ' . $error->getMessage() . " (code: $code, response: $response)]", $e_code);
    }

}
