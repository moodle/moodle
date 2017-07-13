<?PHP
/**
 * Copyright 2010-2014 Horde LLC (http://www.horde.org/)
 * Copyright (c) 2010 Gerd Schaufelberger
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
 * @copyright 2010-2014 Horde LLC
 * @copyright 2010 Gerd Schaufelberger
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * SMTP MX implementation.
 *
 * @author    Gerd Schaufelberger <gerd@php-tools.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2010-2014 Horde LLC
 * @copyright 2010 Gerd Schaufelberger
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Transport_Smtpmx extends Horde_Mail_Transport
{
    /**
     * SMTP connection object.
     *
     * @var Net_SMTP
     */
    protected $_smtp = null;

    /**
     * Net_DNS2_Resolver object.
     *
     * @var Net_DNS2_Resolver
     */
    protected $_resolver;

    /**
     * Internal error codes.
     * Translate internal error identifier to human readable messages.
     *
     * @var array
     */
    protected $_errorCode = array(
        'not_connected' => array(
            'code' => 1,
            'msg' => 'Could not connect to any mail server ({HOST}) at port {PORT} to send mail to {RCPT}.'
        ),
        'failed_vrfy_rcpt' => array(
            'code' => 2,
            'msg' => 'Recipient "{RCPT}" could not be veryfied.'
        ),
        'failed_set_from' => array(
            'code' => 3,
            'msg' => 'Failed to set sender: {FROM}.'
        ),
        'failed_set_rcpt' => array(
            'code' => 4,
            'msg' => 'Failed to set recipient: {RCPT}.'
        ),
        'failed_send_data' => array(
            'code' => 5,
            'msg' => 'Failed to send mail to: {RCPT}.'
        ),
        'no_from' => array(
            'code' => 5,
            'msg' => 'No from address has be provided.'
        ),
        'send_data' => array(
            'code' => 7,
            'msg' => 'Failed to create Net_SMTP object.'
        ),
        'no_mx' => array(
            'code' => 8,
            'msg' => 'No MX-record for {RCPT} found.'
        ),
        'no_resolver' => array(
            'code' => 9,
            'msg' => 'Could not start resolver! Install PEAR:Net_DNS2 or switch off "netdns"'
        ),
        'failed_rset' => array(
            'code' => 10,
            'msg' => 'RSET command failed, SMTP-connection corrupt.'
        )
    );

    /**
     * @param array $params  Additional options:
     *   - debug: (boolean) Activate SMTP debug mode?
     *            DEFAULT: false
     *   - mailname: (string) The name of the local mail system (a valid
     *               hostname which matches the reverse lookup)
     *               DEFAULT: Auto-determined
     *   - netdns: (boolean) Use PEAR:Net_DNS2 (true) or the PHP builtin
     *             getmxrr().
     *             DEFAULT: true
     *   - port: (integer) Port.
     *           DEFAULT: Auto-determined
     *   - test: (boolean) Activate test mode?
     *           DEFAULT: false
     *   - timeout: (integer) The SMTP connection timeout (in seconds).
     *              DEFAULT: 10
     *   - verp: (boolean) Whether to use VERP.
     *           If not a boolean, the string value will be used as the VERP
     *           separators.
     *           DEFAULT: false
     *   - vrfy: (boolean) Whether to use VRFY.
     *           DEFAULT: false
     */
    public function __construct(array $params = array())
    {
        /* Try to find a valid mailname. */
        if (!isset($params['mailname']) && function_exists('posix_uname')) {
            $uname = posix_uname();
            $params['mailname'] = $uname['nodename'];
        }

        if (!isset($params['port'])) {
            $params['port'] = getservbyname('smtp', 'tcp');
        }

        $this->_params = array_merge(array(
            'debug' => false,
            'mailname' => 'localhost',
            'netdns' => true,
            'port' => 25,
            'test' => false,
            'timeout' => 10,
            'verp' => false,
            'vrfy' => false
        ), $params);

        /* SMTP requires CRLF line endings. */
        $this->sep = "\r\n";
    }

    /**
     * Destructor implementation to ensure that we disconnect from any
     * potentially-alive persistent SMTP connections.
     */
    public function __destruct()
    {
        if (is_object($this->_smtp)) {
            $this->_smtp->disconnect();
            $this->_smtp = null;
        }
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        $headers = $this->_sanitizeHeaders($headers);

        // Prepare headers
        list($from, $textHeaders) = $this->prepareHeaders($headers);

        try {
            $from = $this->_getFrom($from, $headers);
        } catch (Horde_Mail_Exception $e) {
            $this->_error('no_from');
        }

        // Prepare recipients
        foreach ($this->parseRecipients($recipients) as $rcpt) {
            list(,$host) = explode('@', $rcpt);

            $mx = $this->_getMx($host);
            if (!$mx) {
                $this->_error('no_mx', array('rcpt' => $rcpt));
            }

            $connected = false;
            foreach (array_keys($mx) as $mserver) {
                $this->_smtp = new Net_SMTP($mserver, $this->_params['port'], $this->_params['mailname']);

                // configure the SMTP connection.
                if ($this->_params['debug']) {
                    $this->_smtp->setDebug(true);
                }

                // attempt to connect to the configured SMTP server.
                $res = $this->_smtp->connect($this->_params['timeout']);
                if ($res instanceof PEAR_Error) {
                    $this->_smtp = null;
                    continue;
                }

                // connection established
                if ($res) {
                    $connected = true;
                    break;
                }
            }

            if (!$connected) {
                $this->_error('not_connected', array(
                    'host' => implode(', ', array_keys($mx)),
                    'port' => $this->_params['port'],
                    'rcpt' => $rcpt
                ));
            }

            // Verify recipient
            if ($this->_params['vrfy']) {
                $res = $this->_smtp->vrfy($rcpt);
                if ($res instanceof PEAR_Error) {
                    $this->_error('failed_vrfy_rcpt', array('rcpt' => $rcpt));
                }
            }

            // mail from:
            $args['verp'] = $this->_params['verp'];
            $res = $this->_smtp->mailFrom($from, $args);
            if ($res instanceof PEAR_Error) {
                $this->_error('failed_set_from', array('from' => $from));
            }

            // rcpt to:
            $res = $this->_smtp->rcptTo($rcpt);
            if ($res instanceof PEAR_Error) {
                $this->_error('failed_set_rcpt', array('rcpt' => $rcpt));
            }

            // Don't send anything in test mode
            if ($this->_params['test']) {
                $res = $this->_smtp->rset();
                if ($res instanceof PEAR_Error) {
                    $this->_error('failed_rset');
                }

                $this->_smtp->disconnect();
                $this->_smtp = null;
                return;
            }

            // Send data. Net_SMTP does necessary EOL conversions.
            $res = $this->_smtp->data($body, $textHeaders);
            if ($res instanceof PEAR_Error) {
                $this->_error('failed_send_data', array('rcpt' => $rcpt));
            }

            $this->_smtp->disconnect();
            $this->_smtp = null;
        }
    }

    /**
     * Recieve MX records for a host.
     *
     * @param string $host  Mail host.
     *
     * @return mixed  Sorted MX list or false on error.
     */
    protected function _getMx($host)
    {
        $mx = array();

        if ($this->params['netdns']) {
            $this->_loadNetDns();

            try {
                $response = $this->_resolver->query($host, 'MX');
                if (!$response) {
                    return false;
                }
            } catch (Exception $e) {
                throw new Horde_Mail_Exception($e);
            }

            foreach ($response->answer as $rr) {
                if ($rr->type == 'MX') {
                    $mx[$rr->exchange] = $rr->preference;
                }
            }
        } else {
            $mxHost = $mxWeight = array();

            if (!getmxrr($host, $mxHost, $mxWeight)) {
                return false;
            }

            for ($i = 0; $i < count($mxHost); ++$i) {
                $mx[$mxHost[$i]] = $mxWeight[$i];
            }
        }

        asort($mx);

        return $mx;
    }

    /**
     * Initialize Net_DNS2_Resolver.
     */
    protected function _loadNetDns()
    {
        if (!$this->_resolver) {
            if (!class_exists('Net_DNS2_Resolver')) {
                $this->_error('no_resolver');
            }
            $this->_resolver = new Net_DNS2_Resolver();
        }
    }

    /**
     * Format error message.
     *
     * @param string $id   Maps error ids to codes and message.
     * @param array $info  Optional information in associative array.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _error($id, $info = array())
    {
        $msg = $this->_errorCode[$id]['msg'];

        // include info to messages
        if (!empty($info)) {
            $replace = $search = array();

            foreach ($info as $key => $value) {
                $search[] = '{' . strtoupper($key) . '}';
                $replace[] = $value;
            }

            $msg = str_replace($search, $replace, $msg);
        }

        throw new Horde_Mail_Exception($msg, $this->_errorCode[$id]['code']);
    }

}
