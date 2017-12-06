<?php
/**
 * Copyright (c) 2002-2003 Richard Heyes
 * Copyright 2011-2014 Horde LLC (http://www.horde.org/)
 *
 * This code is based on the original code contained in the PEAR Auth_SASL
 * package (v0.5.1):
 *   $Id: DigestMD5.php 294702 2010-02-07 16:03:55Z cweiske $
 *
 * That code is covered by the BSD 3-Clause license, as set forth below:
 * +-----------------------------------------------------------------------+
 * | Copyright (c) 2002-2003 Richard Heyes                                 |
 * | All rights reserved.                                                  |
 * |                                                                       |
 * | Redistribution and use in source and binary forms, with or without    |
 * | modification, are permitted provided that the following conditions    |
 * | are met:                                                              |
 * |                                                                       |
 * | o Redistributions of source code must retain the above copyright      |
 * |   notice, this list of conditions and the following disclaimer.       |
 * | o Redistributions in binary form must reproduce the above copyright   |
 * |   notice, this list of conditions and the following disclaimer in the |
 * |   documentation and/or other materials provided with the distribution.|
 * | o The names of the authors may not be used to endorse or promote      |
 * |   products derived from this software without specific prior written  |
 * |   permission.                                                         |
 * |                                                                       |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
 * +-----------------------------------------------------------------------+
 *
 * @category  Horde
 * @copyright 2002-2003 Richard Heyes
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Provides the code needed to authenticate via the DIGEST-MD5 SASL mechanism
 * (defined in RFC 2831). This method has been obsoleted by RFC 6331, but
 * still is in use on legacy servers.
 *
 * @author    Richard Heyes <richard@php.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @copyright 2002-2003 Richard Heyes
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Auth_DigestMD5
{
    /**
     * Digest response components.
     *
     * @var string
     */
    protected $_response;

    /**
     * Generate the Digest-MD5 response.
     *
     * @param string $id         Authentication id (username).
     * @param string $pass       Password.
     * @param string $challenge  The digest challenge sent by the server.
     * @param string $hostname   The hostname of the machine connecting to.
     * @param string $service    The service name (e.g. 'imap', 'pop3').
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function __construct($id, $pass, $challenge, $hostname, $service)
    {
        $challenge = $this->_parseChallenge($challenge);
        $cnonce = $this->_getCnonce();
        $digest_uri = sprintf('%s/%s', $service, $hostname);

        /* Get response value. */
        $A1 = sprintf('%s:%s:%s', pack('H32', hash('md5', sprintf('%s:%s:%s', $id, $challenge['realm'], $pass))), $challenge['nonce'], $cnonce);
        $A2 = 'AUTHENTICATE:' . $digest_uri;
        $response_value = hash('md5', sprintf('%s:%s:00000001:%s:auth:%s', hash('md5', $A1), $challenge['nonce'], $cnonce, hash('md5', $A2)));

        $this->_response = array(
            'cnonce' => '"' . $cnonce . '"',
            'digest-uri' => '"' . $digest_uri . '"',
            'maxbuf' => $challenge['maxbuf'],
            'nc' => '00000001',
            'nonce' => '"' . $challenge['nonce'] . '"',
            'qop' => 'auth',
            'response' => $response_value,
            'username' => '"' . $id . '"'
        );

        if (strlen($challenge['realm'])) {
            $this->_response['realm'] = '"' . $challenge['realm'] . '"';
        }
    }

    /**
     * Cooerce to string.
     *
     * @return string  The digest response (not base64 encoded).
     */
    public function __toString()
    {
        $out = array();
        foreach ($this->_response as $key => $val) {
            $out[] = $key . '=' . $val;
        }
        return implode(',', $out);
    }

    /**
     * Return specific digest response directive.
     *
     * @return mixed  Requested directive, or null if it does not exist.
     */
    public function __get($name)
    {
        return isset($this->_response[$name])
            ? $this->_response[$name]
            : null;
    }

    /**
    * Parses and verifies the digest challenge.
    *
    * @param string $challenge  The digest challenge
    *
    * @return array  The parsed challenge as an array with directives as keys.
    *
    * @throws Horde_Imap_Client_Exception
    */
    protected function _parseChallenge($challenge)
    {
        $tokens = array(
            'maxbuf' => 65536,
            'realm' => ''
        );

        preg_match_all('/([a-z-]+)=("[^"]+(?<!\\\)"|[^,]+)/i', $challenge, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            $tokens[$val[1]] = trim($val[2], '"');
        }

        // Required directives.
        if (!isset($tokens['nonce']) || !isset($tokens['algorithm'])) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Authentication failure."),
                Horde_Imap_Client_Exception::SERVER_CONNECT
            );
        }

        return $tokens;
    }

    /**
     * Creates the client nonce for the response
     *
     * @return string  The cnonce value.
     */
    protected function _getCnonce()
    {
        if ((@is_readable('/dev/urandom') &&
             ($fd = @fopen('/dev/urandom', 'r'))) ||
            (@is_readable('/dev/random') &&
             ($fd = @fopen('/dev/random', 'r')))) {
            $str = fread($fd, 32);
            fclose($fd);
        } else {
            $str = '';
            for ($i = 0; $i < 32; ++$i) {
                $str .= chr(mt_rand(0, 255));
            }
        }

        return base64_encode($str);
    }

}
