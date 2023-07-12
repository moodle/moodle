<?php
/**
 * Class for verifying Yubico One-Time-Passcodes
 *
 * LICENSE:
 *
 * Copyright (c) 2007, 2008  Simon Josefsson.  All rights reserved.
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
 * @category    Auth
 * @package     Auth_Yubico
 * @author      Simon Josefsson <simon@yubico.com>
 * @copyright   2008 Simon Josefsson
 * @license     http://opensource.org/licenses/bsd-license.php New BSD License
 * @version     CVS: $Id: Yubico.php,v 1.7 2007-10-22 12:56:14 jas Exp $
 * @link        http://yubico.com/
 */

/**
 * Class for verifying Yubico One-Time-Passcodes
 *
 * Simple example:
 * <code>
 * require_once 'Auth/Yubico.php';
 * $yubi = &new Auth_Yubico('42');
 * $auth = $yubi->verify("ccbbddeertkrctjkkcglfndnlihhnvekchkcctif");
 * if (PEAR::isError($auth)) {
 *    print "<p>Authentication failed: " . $auth->getMessage();
 *    print "<p>Debug output from server: " . $yubi->getLastResponse();
 * } else {
 *    print "<p>You are authenticated!";
 * }
 * </code>
 */
class Auth_Yubico
{
    /**#@+
     * @access private
     */

    /**
     * Yubico client ID
     * @var string
     */
    private $id;

    /**
     * Yubico client key
     * @var string
     */
    private $key;

    /**
     * Response from server
     * @var string
     */
    private $response;

    /**
     * Constructor
     *
     * Sets up the object
     * @param string $id    The client identity
     * @param string $key   The client MAC key (optional)
     * @access public
     */
    public function __construct($id, $key = '')
    {
        $this->id = $id;
        $this->key = base64_decode($key);
    }

    /**
     * Return the last data received from the server, if any.
     *
     * @return string Output from server.
     * @access public
     */
    public function getLastResponse()
    {
        return $this->response;
    }

    // TODO? Add functions to get parsed parts of server response?

    /**
     * Verify Yubico OTP
     *
     * @param string $token     Yubico OTP
     * @return mixed            PEAR error on error, true otherwise
     * @access public
     */
    public function verify($token)
    {
        $parameters = "id=".$this->id."&otp=".$token;
        // Generate signature
        if ($this->key <> "") {
            $signature = base64_encode(hash_hmac('sha1', $parameters, $this->key, true));
            $parameters .= '&h='.$signature;
        }
        // Support https
        $url = "https://api.yubico.com/wsapi/verify?".$parameters;

        /** @var string $responseMsg */
        $responseMsg = \SimpleSAML\Utils\HTTP::fetch($url);

        $out = [];
        if (preg_match("/status=([a-zA-Z0-9_]+)/", $responseMsg, $out) !== 1) {
            throw new Exception('Could not parse response');
        }

        $status = $out[1];

        // Verify signature
        if ($this->key <> "") {
            $rows = explode("\r\n", $responseMsg);
            $response = [];
            foreach ($rows as $val) {
                // = is also used in BASE64 encoding so we only replace the first = by # which is not used in BASE64
                $val = preg_replace('/=/', '#', $val, 1);
                $row = explode("#", $val);
                $response[$row[0]] = (isset($row[1])) ? $row[1] : "";
            }

            $check = 'status='.$response['status'].'&t='.$response['t'];
            $checksignature = base64_encode(hash_hmac('sha1', $check, $this->key, true));

            if ($response['h'] != $checksignature) {
                throw new Exception('Checked Signature failed');
            }
        }

        if ($status != 'OK') {
            throw new Exception('Status was not OK: '.$status);
        }

        return true;
    }
}
