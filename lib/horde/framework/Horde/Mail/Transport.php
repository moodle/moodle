<?php
/**
 * Copyright 1997-2017 Horde LLC (http://www.horde.org/)
 * Copyright (c) 2002-2007, Richard Heyes
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
 * @copyright 1997-2017 Horde LLC (http://www.horde.org/)
 * @copyright 2002-2007 Richard Heyes
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Mail transport base class.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Richard Heyes <richard@phpguru.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 1997-2017 Horde LLC (http://www.horde.org/)
 * @copyright 2002-2007 Richard Heyes
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 *
 * @property-read boolean $eai  Does the transport driver support EAI (RFC
 *                              6532) headers? (@since 2.5.0)
 */
abstract class Horde_Mail_Transport
{
    /**
     * Line terminator used for separating header lines.
     *
     * @var string
     */
    public $sep = PHP_EOL;

    /**
     * Configuration parameters.
     *
     * @var array
     */
    protected $_params = array();

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'eai':
            return false;
        }
    }

    /**
     * Send a message.
     *
     * @param mixed $recipients  Either a comma-seperated list of recipients
     *                           (RFC822 compliant), or an array of
     *                           recipients, each RFC822 valid. This may
     *                           contain recipients not specified in the
     *                           headers, for Bcc:, resending messages, etc.
     * @param array $headers     The headers to send with the mail, in an
     *                           associative array, where the array key is the
     *                           header name (ie, 'Subject'), and the array
     *                           value is the header value (ie, 'test'). The
     *                           header produced from those values would be
     *                           'Subject: test'.
     *                           If the '_raw' key exists, the value of this
     *                           key will be used as the exact text for
     *                           sending the message.
     * @param mixed $body        The full text of the message body, including
     *                           any Mime parts, etc. Either a string or a
     *                           stream resource.
     *
     * @throws Horde_Mail_Exception
     */
    abstract public function send($recipients, array $headers, $body);

    /**
     * Take an array of mail headers and return a string containing text
     * usable in sending a message.
     *
     * @param array $headers  The array of headers to prepare, in an
     *                        associative array, where the array key is the
     *                        header name (ie, 'Subject'), and the array value
     *                        is the header value (ie, 'test'). The header
     *                        produced from those values would be 'Subject:
     *                        test'.
     *                        If the '_raw' key exists, the value of this key
     *                        will be used as the exact text for sending the
     *                        message.
     *
     * @return mixed  Returns false if it encounters a bad address; otherwise
     *                returns an array containing two elements: Any From:
     *                address found in the headers, and the plain text version
     *                of the headers.
     * @throws Horde_Mail_Exception
     */
    public function prepareHeaders(array $headers)
    {
        $from = null;
        $lines = array();
        $raw = isset($headers['_raw'])
            ? $headers['_raw']
            : null;

        foreach ($headers as $key => $value) {
            if (strcasecmp($key, 'From') === 0) {
                $parser = new Horde_Mail_Rfc822();
                $addresses = $parser->parseAddressList($value, array(
                    'validate' => $this->eai ? 'eai' : true
                ));
                $from = $addresses[0]->bare_address;

                // Reject envelope From: addresses with spaces.
                if (strstr($from, ' ')) {
                    return false;
                }

                $lines[] = $key . ': ' . $this->_normalizeEOL($value);
            } elseif (!$raw && (strcasecmp($key, 'Received') === 0)) {
                $received = array();
                if (!is_array($value)) {
                    $value = array($value);
                }

                foreach ($value as $line) {
                    $received[] = $key . ': ' . $this->_normalizeEOL($line);
                }

                // Put Received: headers at the top.  Spam detectors often
                // flag messages with Received: headers after the Subject:
                // as spam.
                $lines = array_merge($received, $lines);
            } elseif (!$raw) {
                // If $value is an array (i.e., a list of addresses), convert
                // it to a comma-delimited string of its elements (addresses).
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $lines[] = $key . ': ' . $this->_normalizeEOL($value);
            }
        }

        return array($from, $raw ? $raw : implode($this->sep, $lines));
    }

    /**
     * Take a set of recipients and parse them, returning an array of bare
     * addresses (forward paths) that can be passed to sendmail or an SMTP
     * server with the 'RCPT TO:' command.
     *
     * @param mixed $recipients  Either a comma-separated list of recipients
     *                           (RFC822 compliant), or an array of
     *                           recipients, each RFC822 valid.
     *
     * @return array  Forward paths (bare addresses, IDN encoded).
     * @throws Horde_Mail_Exception
     */
    public function parseRecipients($recipients)
    {
        // Parse recipients, leaving out all personal info. This is
        // for smtp recipients, etc. All relevant personal information
        // should already be in the headers.
        $rfc822 = new Horde_Mail_Rfc822();
        return $rfc822->parseAddressList($recipients, array(
            'validate' => $this->eai ? 'eai' : true
        ))->bare_addresses_idn;
    }

    /**
     * Sanitize an array of mail headers by removing any additional header
     * strings present in a legitimate header's value.  The goal of this
     * filter is to prevent mail injection attacks.
     *
     * Raw headers are sent as-is.
     *
     * @param array $headers  The associative array of headers to sanitize.
     *
     * @return array  The sanitized headers.
     */
    protected function _sanitizeHeaders($headers)
    {
        foreach (array_diff(array_keys($headers), array('_raw')) as $key) {
            $headers[$key] = preg_replace('=((<CR>|<LF>|0x0A/%0A|0x0D/%0D|\\n|\\r)\S).*=i', '', $headers[$key]);
        }

        return $headers;
    }

    /**
     * Normalizes EOLs in string data.
     *
     * @param string $data  Data.
     *
     * @return string  Normalized data.
     */
    protected function _normalizeEOL($data)
    {
        return strtr($data, array(
            "\r\n" => $this->sep,
            "\r" => $this->sep,
            "\n" => $this->sep
        ));
    }

    /**
     * Get the from address.
     *
     * @param string $from    From address.
     * @param array $headers  Headers array.
     *
     * @return string  Address object.
     * @throws Horde_Mail_Exception
     */
    protected function _getFrom($from, $headers)
    {
        /* Since few MTAs are going to allow this header to be forged unless
         * it's in the MAIL FROM: exchange, we'll use Return-Path instead of
         * From: if it's set. */
        foreach (array_keys($headers) as $hdr) {
            if (strcasecmp($hdr, 'Return-Path') === 0) {
                $from = $headers[$hdr];
                break;
            }
        }

        if (empty($from)) {
            throw new Horde_Mail_Exception('No from address provided.');
        }

        $from = new Horde_Mail_Rfc822_Address($from);

        return $from->bare_address_idn;
    }

}
