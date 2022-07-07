<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
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
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * SMTP implementation using Horde_Smtp.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Transport_Smtphorde extends Horde_Mail_Transport
{
    /**
     * @deprecated
     */
    public $send8bit = false;

    /**
     * SMTP object.
     *
     * @var Horde_Smtp
     */
    protected $_smtp = null;

    /**
     * Constructor.
     *
     * @param array $params  Additional parameters:
     *   - chunk_size: (integer) If CHUNKING is supported on the server, the
     *                 chunk size (in octets) to send. 0 will disable chunking.
     *                 @since Horde_Smtp 1.7.0
     *   - context: (array) Any context parameters passed to
     *              stream_create_context(). @since Horde_Smtp 1.9.0
     *   - debug: (string) If set, will output debug information to the stream
     *            provided. The value can be any PHP supported wrapper that
     *            can be opened via fopen().
     *            DEFAULT: No debug output
     *   - host: (string) The SMTP server.
     *           DEFAULT: localhost
     *   - localhost: (string) The hostname of the localhost. (since Horde_Smtp
                      1.9.0)
     *                DEFAULT: Auto-determined.
     *   - password: (string) The SMTP password.
     *               DEFAULT: NONE
     *   - port: (string) The SMTP port.
     *           DEFAULT: 587
     *   - secure: (string) Use SSL or TLS to connect.
     *             DEFAULT: true (use 'tls' option, if available)
     *     - false (No encryption)
     *     - 'ssl' (Auto-detect SSL version)
     *     - 'sslv2' (Force SSL version 2)
     *     - 'sslv3' (Force SSL version 3)
     *     - 'tls' (TLS; started via protocol-level negotation over
     *       unencrypted channel; RECOMMENDED way of initiating secure
     *       connection)
     *     - 'tlsv1' (TLS direct version 1.x connection to server) [@since
     *       Horde_Smtp .3.0]
     *     - true (Use TLS, if available) [@since Horde_Smtp 1.2.0]
     *             DEFAULT: No encryption
     *   - timeout: (integer) Connection timeout, in seconds.
     *              DEFAULT: 30 seconds
     *   - username: (string) The SMTP username.
     *               DEFAULT: NONE
     *   - xoauth2_token: (string) If set, will authenticate via the XOAUTH2
     *                    mechanism (if available) with this token. Either a
     *                    string or a Horde_Smtp_Password object (since
     *                    Horde_Smtp 1.1.0).
     */
    public function __construct(array $params = array())
    {
        $this->_params = $params;

        /* SMTP requires CRLF line endings. */
        $this->sep = "\r\n";
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'eai':
            $this->getSMTPObject();
            return $this->_smtp->data_intl;
        }

        return parent::__get($name);
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        /* If we don't already have an SMTP object, create one. */
        $this->getSMTPObject();

        $headers = $this->_sanitizeHeaders($headers);
        list($from, $textHeaders) = $this->prepareHeaders($headers);
        $from = $this->_getFrom($from, $headers);

        $combine = Horde_Stream_Wrapper_Combine::getStream(array(
            rtrim($textHeaders, $this->sep),
            $this->sep . $this->sep,
            $body
        ));

        try {
            $this->_smtp->send($from, $recipients, $combine);
        } catch (Horde_Smtp_Exception $e) {
            throw new Horde_Mail_Exception($e);
        }
    }

    /**
     * Connect to the SMTP server by instantiating a Horde_Smtp object.
     *
     * @return Horde_Smtp  The SMTP object.
     * @throws Horde_Mail_Exception
     */
    public function getSMTPObject()
    {
        if (!$this->_smtp) {
            $this->_smtp = new Horde_Smtp($this->_params);
            try {
                $this->_smtp->login();
            } catch (Horde_Smtp_Exception $e) {
                throw new Horde_Mail_Exception($e);
            }
        }

        return $this->_smtp;
    }

}
