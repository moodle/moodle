<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/*
Copyright (c) 2003, Michael Bretterklieber <michael@bretterklieber.com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. The names of the authors may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

This code cannot simply be copied and put under the GNU Public License or
any other GPL-like (LGPL, GPL2) License.

    $Id$
*/

require_once 'PEAR.php';

/**
* Client implementation of RADIUS. This are wrapper classes for
* the RADIUS PECL.
* Provides RADIUS Authentication (RFC2865) and RADIUS Accounting (RFC2866).
*
* @package Auth_RADIUS
* @author  Michael Bretterklieber <michael@bretterklieber.com>
* @access  public
* @version $Revision$
*/

PEAR::loadExtension('radius');

/**
 * class Auth_RADIUS
 *
 * Abstract base class for RADIUS
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS extends PEAR {

    /**
     * List of RADIUS servers.
     * @var  array
     * @see  addServer(), putServer()
     */
    var $_servers  = array();

    /**
     * Path to the configuration-file.
     * @var  string
     * @see  setConfigFile()
     */
    var $_configfile = null;

    /**
     * Resource.
     * @var  resource
     * @see  open(), close()
     */
    var $res = null;

    /**
     * Username for authentication and accounting requests.
     * @var  string
     */
    var $username = null;

    /**
     * Password for plaintext-authentication (PAP).
     * @var  string
     */
    var $password = null;

    /**
     * List of known attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    var $attributes = array();

    /**
     * List of raw attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    var $rawAttributes = array();

    /**
     * List of raw vendor specific attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    var $rawVendorAttributes = array();

    /**
     * Switch whether we should put standard attributes or not
     * @var  bool
     * @see  putStandardAttributes()
     */
    var $useStandardAttributes = true;

    /**
     * Constructor
     *
     * Loads the RADIUS PECL/extension
     *
     * @return void
     */
    function Auth_RADIUS()
    {
        $this->PEAR();
    }

    /**
     * Adds a RADIUS server to the list of servers for requests.
     *
     * At most 10 servers may be specified.	When multiple servers
     * are given, they are tried in round-robin fashion until a
     * valid response is received
     *
     * @access public
     * @param  string  $servername   Servername or IP-Address
     * @param  integer $port         Portnumber
     * @param  string  $sharedSecret Shared secret
     * @param  integer $timeout      Timeout for each request
     * @param  integer $maxtries     Max. retries for each request
     * @return void
     */
    function addServer($servername = 'localhost', $port = 0, $sharedSecret = 'testing123', $timeout = 3, $maxtries = 3)
    {
        $this->_servers[] = array($servername, $port, $sharedSecret, $timeout, $maxtries);
    }

    /**
     * Returns an error message, if an error occurred.
     *
     * @access public
     * @return string
     */
    function getError()
    {
        return radius_strerror($this->res);
    }

    /**
     * Sets the configuration-file.
     *
     * @access public
     * @param  string  $file Path to the configuration file
     * @return void
     */
    function setConfigfile($file)
    {
        $this->_configfile = $file;
    }

    /**
     * Puts an attribute.
     *
     * @access public
     * @param  integer $attrib       Attribute-number
     * @param  mixed   $port         Attribute-value
     * @param  type    $type         Attribute-type
     * @return bool  true on success, false on error
     */
    function putAttribute($attrib, $value, $type = null)
    {
        if ($type == null) {
            $type = gettype($value);
        }

        switch ($type) {
        case 'integer':
        case 'double':
            return radius_put_int($this->res, $attrib, $value);

        case 'addr':
            return radius_put_addr($this->res, $attrib, $value);

        case 'string':
        default:
            return radius_put_attr($this->res, $attrib, $value);
        }

    }

    /**
     * Puts a vendor-specific attribute.
     *
     * @access public
     * @param  integer $vendor       Vendor (MSoft, Cisco, ...)
     * @param  integer $attrib       Attribute-number
     * @param  mixed   $port         Attribute-value
     * @param  type    $type         Attribute-type
     * @return bool  true on success, false on error
     */
    function putVendorAttribute($vendor, $attrib, $value, $type = null)
    {

        if ($type == null) {
            $type = gettype($value);
        }

        switch ($type) {
        case 'integer':
        case 'double':
            return radius_put_vendor_int($this->res, $vendor, $attrib, $value);

        case 'addr':
            return radius_put_vendor_addr($this->res, $vendor,$attrib, $value);

        case 'string':
        default:
            return radius_put_vendor_attr($this->res, $vendor, $attrib, $value);
        }

    }

    /**
     * Prints known attributes received from the server.
     *
     * @access public
     */
    function dumpAttributes()
    {
        foreach ($this->attributes as $name => $data) {
            echo "$name:$data<br>\n";
        }
    }

    /**
     * Overwrite this.
     *
     * @access public
     */
    function open()
    {
    }

    /**
     * Overwrite this.
     *
     * @access public
     */
    function createRequest()
    {
    }

    /**
     * Puts standard attributes.
     *
     * @access public
     */
    function putStandardAttributes()
    {
        if (!$this->useStandardAttributes)
		return;

        if (isset($_SERVER)) {
            $var = &$_SERVER;
        } else {
            $var = &$GLOBALS['HTTP_SERVER_VARS'];
        }

        $this->putAttribute(RADIUS_NAS_IDENTIFIER, isset($var['HTTP_HOST']) ? $var['HTTP_HOST'] : 'localhost');
        $this->putAttribute(RADIUS_NAS_PORT_TYPE, RADIUS_VIRTUAL);
        $this->putAttribute(RADIUS_SERVICE_TYPE, RADIUS_FRAMED);
        $this->putAttribute(RADIUS_FRAMED_PROTOCOL, RADIUS_PPP);
        $this->putAttribute(RADIUS_CALLING_STATION_ID, isset($var['REMOTE_HOST']) ? $var['REMOTE_HOST'] : '127.0.0.1');
    }

    /**
     * Puts custom attributes.
     *
     * @access public
     */
    function putAuthAttributes()
    {
        if (isset($this->username)) {
            $this->putAttribute(RADIUS_USER_NAME, $this->username);
        }
    }

    /**
     * Configures the radius library.
     *
     * @access public
     * @param  string  $servername   Servername or IP-Address
     * @param  integer $port         Portnumber
     * @param  string  $sharedSecret Shared secret
     * @param  integer $timeout      Timeout for each request
     * @param  integer $maxtries     Max. retries for each request
     * @return bool  true on success, false on error
     * @see addServer()
     */
    function putServer($servername, $port = 0, $sharedsecret = 'testing123', $timeout = 3, $maxtries = 3)
    {
        if (!radius_add_server($this->res, $servername, $port, $sharedsecret, $timeout, $maxtries)) {
            return false;
        }
        return true;
    }

    /**
     * Configures the radius library via external configurationfile
     *
     * @access public
     * @param  string  $servername   Servername or IP-Address
     * @return bool  true on success, false on error
     */
    function putConfigfile($file)
    {
        if (!radius_config($this->res, $file)) {
            return false;
        }
        return true;
    }

    /**
     * Initiates a RADIUS request.
     *
     * @access public
     * @return bool  true on success, false on errors
     */
    function start()
    {
        if (!$this->open()) {
            return false;
        }

        foreach ($this->_servers as $s) {
	        // Servername, port, sharedsecret, timeout, retries
            if (!$this->putServer($s[0], $s[1], $s[2], $s[3], $s[4])) {
                return false;
            }
        }

        if (!empty($this->_configfile)) {
            if (!$this->putConfigfile($this->_configfile)) {
                return false;
            }
        }

        $this->createRequest();
        $this->putStandardAttributes();
        $this->putAuthAttributes();
        return true;
    }

    /**
     * Sends a prepared RADIUS request and waits for a response
     *
     * @access public
     * @return mixed  true on success, false on reject, PEAR_Error on error
     */
    function send()
    {
        $req = radius_send_request($this->res);
        if (!$req) {
            return $this->raiseError('Error sending request: ' . $this->getError());
        }

        switch($req) {
        case RADIUS_ACCESS_ACCEPT:
            if (is_subclass_of($this, 'auth_radius_acct')) {
                return $this->raiseError('RADIUS_ACCESS_ACCEPT is unexpected for accounting');
            }
            return true;

        case RADIUS_ACCESS_REJECT:
            return false;

        case RADIUS_ACCOUNTING_RESPONSE:
            if (is_subclass_of($this, 'auth_radius_pap')) {
                return $this->raiseError('RADIUS_ACCOUNTING_RESPONSE is unexpected for authentication');
            }
            return true;

        default:
            return $this->raiseError("Unexpected return value: $req");
        }

    }

    /**
     * Reads all received attributes after sending the request.
     *
     * This methods stores known attributes in the property attributes,
     * all attributes (including known attibutes) are stored in rawAttributes
     * or rawVendorAttributes.
     * NOTE: call this function also even if the request was rejected, because the
     * Server returns usualy an errormessage
     *
     * @access public
     * @return bool   true on success, false on error
     */
    function getAttributes()
    {

        while ($attrib = radius_get_attr($this->res)) {

            if (!is_array($attrib)) {
                return false;
            }

            $attr = $attrib['attr'];
            $data = $attrib['data'];

            $this->rawAttributes[$attr] = $data;

            switch ($attr) {
            case RADIUS_FRAMED_IP_ADDRESS:
                $this->attributes['framed_ip'] = radius_cvt_addr($data);
                break;

            case RADIUS_FRAMED_IP_NETMASK:
                $this->attributes['framed_mask'] = radius_cvt_addr($data);
                break;

            case RADIUS_FRAMED_MTU:
                $this->attributes['framed_mtu'] = radius_cvt_int($data);
                break;

            case RADIUS_FRAMED_COMPRESSION:
                $this->attributes['framed_compression'] = radius_cvt_int($data);
                break;

            case RADIUS_SESSION_TIMEOUT:
                $this->attributes['session_timeout'] = radius_cvt_int($data);
                break;

            case RADIUS_IDLE_TIMEOUT:
                $this->attributes['idle_timeout'] = radius_cvt_int($data);
                break;

            case RADIUS_SERVICE_TYPE:
                $this->attributes['service_type'] = radius_cvt_int($data);
                break;

            case RADIUS_CLASS:
                $this->attributes['class'] = radius_cvt_string($data);
                break;

            case RADIUS_FRAMED_PROTOCOL:
                $this->attributes['framed_protocol'] = radius_cvt_int($data);
                break;

            case RADIUS_FRAMED_ROUTING:
                $this->attributes['framed_routing'] = radius_cvt_int($data);
                break;

            case RADIUS_FILTER_ID:
                $this->attributes['filter_id'] = radius_cvt_string($data);
                break;

            case RADIUS_REPLY_MESSAGE:
                $this->attributes['reply_message'] = radius_cvt_string($data);
                break;

            case RADIUS_VENDOR_SPECIFIC:
                $attribv = radius_get_vendor_attr($data);
                if (!is_array($attribv)) {
                    return false;
                }

                $vendor = $attribv['vendor'];
                $attrv = $attribv['attr'];
                $datav = $attribv['data'];

                $this->rawVendorAttributes[$vendor][$attrv] = $datav;

                if ($vendor == RADIUS_VENDOR_MICROSOFT) {

                    switch ($attrv) {
                    case RADIUS_MICROSOFT_MS_CHAP2_SUCCESS:
                        $this->attributes['ms_chap2_success'] = radius_cvt_string($datav);
                        break;

                    case RADIUS_MICROSOFT_MS_CHAP_ERROR:
                        $this->attributes['ms_chap_error'] = radius_cvt_string(substr($datav,1));
                        break;

                    case RADIUS_MICROSOFT_MS_CHAP_DOMAIN:
                        $this->attributes['ms_chap_domain'] = radius_cvt_string($datav);
                        break;

                    case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_POLICY:
                        $this->attributes['ms_mppe_encryption_policy'] = radius_cvt_int($datav);
                        break;

                    case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_TYPES:
                        $this->attributes['ms_mppe_encryption_types'] = radius_cvt_int($datav);
                        break;

                    case RADIUS_MICROSOFT_MS_CHAP_MPPE_KEYS:
                        $demangled = radius_demangle($this->res, $datav);
                        $this->attributes['ms_chap_mppe_lm_key'] = substr($demangled, 0, 8);
                        $this->attributes['ms_chap_mppe_nt_key'] = substr($demangled, 8, RADIUS_MPPE_KEY_LEN);
                        break;

                    case RADIUS_MICROSOFT_MS_MPPE_SEND_KEY:
                        $this->attributes['ms_chap_mppe_send_key'] = radius_demangle_mppe_key($this->res, $datav);
                        break;

                    case RADIUS_MICROSOFT_MS_MPPE_RECV_KEY:
                        $this->attributes['ms_chap_mppe_recv_key'] = radius_demangle_mppe_key($this->res, $datav);
                        break;

                    case RADIUS_MICROSOFT_MS_PRIMARY_DNS_SERVER:
                        $this->attributes['ms_primary_dns_server'] = radius_cvt_string($datav);
                        break;
                    }
                }
                break;

            }
        }

        return true;
    }

    /**
     * Frees resources.
     *
     * Calling this method is always a good idea, because all security relevant
     * attributes are filled with Nullbytes to leave nothing in the mem.
     *
     * @access public
     */
    function close()
    {
        if ($this->res != null) {
            radius_close($this->res);
            $this->res = null;
        }
        $this->username = str_repeat("\0", strlen($this->username));
        $this->password = str_repeat("\0", strlen($this->password));
    }

}

/**
 * class Auth_RADIUS_PAP
 *
 * Class for authenticating using PAP (Plaintext)
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_PAP extends Auth_RADIUS
{

    /**
     * Constructor
     *
     * @param  string  $username   Username
     * @param  string  $password   Password
     * @return void
     */
    function Auth_RADIUS_PAP($username = null, $password = null)
    {
        $this->Auth_RADIUS();
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Creates a RADIUS resource
     *
     * Creates a RADIUS resource for authentication. This should be the first
     * call before you make any other things with the library.
     *
     * @return bool   true on success, false on error
     */
    function open()
    {
        $this->res = radius_auth_open();
        if (!$this->res) {
            return false;
        }
        return true;
    }

    /**
     * Creates an authentication request
     *
     * Creates an authentication request.
     * You MUST call this method before you can put any attribute
     *
     * @return bool   true on success, false on error
     */
    function createRequest()
    {
        if (!radius_create_request($this->res, RADIUS_ACCESS_REQUEST)) {
            return false;
        }
        return true;
    }

    /**
     * Put authentication specific attributes
     *
     * @return void
     */
    function putAuthAttributes()
    {
        if (isset($this->username)) {
            $this->putAttribute(RADIUS_USER_NAME, $this->username);
        }
        if (isset($this->password)) {
            $this->putAttribute(RADIUS_USER_PASSWORD, $this->password);
        }
    }

}

/**
 * class Auth_RADIUS_CHAP_MD5
 *
 * Class for authenticating using CHAP-MD5 see RFC1994.
 * Instead og the plaintext password the challenge and
 * the response are needed.
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_CHAP_MD5 extends Auth_RADIUS_PAP
{
    /**
     * 8 Bytes binary challenge
     * @var  string
     */
    var $challenge = null;

    /**
     * 16 Bytes MD5 response binary
     * @var  string
     */
    var $response = null;

    /**
     * Id of the authentication request. Should incremented after every request.
     * @var  integer
     */
    var $chapid = 1;

    /**
     * Constructor
     *
     * @param  string  $username   Username
     * @param  string  $challenge  8 Bytes Challenge (binary)
     * @param  integer $chapid     Requestnumber
     * @return void
     */
    function Auth_RADIUS_CHAP_MD5($username = null, $challenge = null, $chapid = 1)
    {
        $this->Auth_RADIUS_PAP();
        $this->username = $username;
        $this->challenge = $challenge;
        $this->chapid = $chapid;
    }

    /**
     * Put CHAP-MD5 specific attributes
     *
     * For authenticating using CHAP-MD5 via RADIUS you have to put the challenge
     * and the response. The chapid is inserted in the first byte of the response.
     *
     * @return void
     */
    function putAuthAttributes()
    {
        if (isset($this->username)) {
            $this->putAttribute(RADIUS_USER_NAME, $this->username);
        }
        if (isset($this->response)) {
            $response = pack('C', $this->chapid) . $this->response;
            $this->putAttribute(RADIUS_CHAP_PASSWORD, $response);
        }
        if (isset($this->challenge)) {
            $this->putAttribute(RADIUS_CHAP_CHALLENGE, $this->challenge);
        }
    }

    /**
     * Frees resources.
     *
     * Calling this method is always a good idea, because all security relevant
     * attributes are filled with Nullbytes to leave nothing in the mem.
     *
     * @access public
     */
    function close()
    {
        Auth_RADIUS_PAP::close();
        $this->challenge =  str_repeat("\0", strlen($this->challenge));
        $this->response =  str_repeat("\0", strlen($this->response));
    }

}

/**
 * class Auth_RADIUS_MSCHAPv1
 *
 * Class for authenticating using MS-CHAPv1 see RFC2433
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_MSCHAPv1 extends Auth_RADIUS_CHAP_MD5
{
    /**
     * LAN-Manager-Response
     * @var  string
     */
    var $lmResponse = null;

    /**
     * Wether using deprecated LM-Responses or not.
     * 0 = use LM-Response, 1 = use NT-Response
     * @var  bool
     */
    var $flags = 1;

    /**
     * Put MS-CHAPv1 specific attributes
     *
     * For authenticating using MS-CHAPv1 via RADIUS you have to put the challenge
     * and the response. The response has this structure:
     * struct rad_mschapvalue {
     *   u_char ident;
     *   u_char flags;
     *   u_char lm_response[24];
     *   u_char response[24];
     * };
     *
     * @return void
     */
    function putAuthAttributes()
    {
        if (isset($this->username)) {
            $this->putAttribute(RADIUS_USER_NAME, $this->username);
        }
        if (isset($this->response) || isset($this->lmResponse)) {
            $lmResp = isset($this->lmResponse) ? $this->lmResponse : str_repeat ("\0", 24);
            $ntResp = isset($this->response)   ? $this->response :   str_repeat ("\0", 24);
            $resp = pack('CC', $this->chapid, $this->flags) . $lmResp . $ntResp;
            $this->putVendorAttribute(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP_RESPONSE, $resp);
        }
        if (isset($this->challenge)) {
            $this->putVendorAttribute(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP_CHALLENGE, $this->challenge);
        }
    }
}

/**
 * class Auth_RADIUS_MSCHAPv2
 *
 * Class for authenticating using MS-CHAPv2 see RFC2759
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_MSCHAPv2 extends Auth_RADIUS_MSCHAPv1
{
    /**
     * 16 Bytes binary challenge
     * @var  string
     */
    var $challenge = null;

    /**
     * 16 Bytes binary Peer Challenge
     * @var  string
     */
    var $peerChallenge = null;

  /**
     * Put MS-CHAPv2 specific attributes
     *
     * For authenticating using MS-CHAPv1 via RADIUS you have to put the challenge
     * and the response. The response has this structure:
     * struct rad_mschapv2value {
     *   u_char ident;
     *   u_char flags;
     *   u_char pchallenge[16];
     *   u_char reserved[8];
     *   u_char response[24];
     * };
     * where pchallenge is the peer challenge. Like for MS-CHAPv1 we set the flags field to 1.
     * @return void
     */
    function putAuthAttributes()
    {
        if (isset($this->username)) {
            $this->putAttribute(RADIUS_USER_NAME, $this->username);
        }
        if (isset($this->response) && isset($this->peerChallenge)) {
            // Response: chapid, flags (1 = use NT Response), Peer challenge, reserved, Response
            $resp = pack('CCa16a8a24',$this->chapid , 1, $this->peerChallenge, str_repeat("\0", 8), $this->response);
            $this->putVendorAttribute(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP2_RESPONSE, $resp);
        }
        if (isset($this->challenge)) {
            $this->putVendorAttribute(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP_CHALLENGE, $this->challenge);
        }
    }

    /**
     * Frees resources.
     *
     * Calling this method is always a good idea, because all security relevant
     * attributes are filled with Nullbytes to leave nothing in the mem.
     *
     * @access public
     */
    function close()
    {
        Auth_RADIUS_MSCHAPv1::close();
        $this->peerChallenge = str_repeat("\0", strlen($this->peerChallenge));
    }
}

/**
 * class Auth_RADIUS_Acct
 *
 * Class for RADIUS accounting
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_Acct extends Auth_RADIUS
{
    /**
     * Defines where the Authentication was made, possible values are:
     * RADIUS_AUTH_RADIUS, RADIUS_AUTH_LOCAL, RADIUS_AUTH_REMOTE
     * @var  integer
     */
    var $authentic = null;

   /**
     * Defines the type of the accounting request, on of:
     * RADIUS_START, RADIUS_STOP, RADIUS_ACCOUNTING_ON, RADIUS_ACCOUNTING_OFF
     * @var  integer
     */
    var $status_type = null;

   /**
     * The time the user was logged in in seconds
     * @var  integer
     */
    var $session_time = null;

   /**
     * A uniq identifier for the session of the user, maybe the PHP-Session-Id
     * @var  string
     */
    var $session_id = null;

    /**
     * Constructor
     *
     * Generates a predefined session_id. We use the Remote-Address, the PID, and the Current user.
     * @return void
     */
    function Auth_RADIUS_Acct()
    {
        $this->Auth_RADIUS();

        if (isset($_SERVER)) {
            $var = &$_SERVER;
        } else {
            $var = &$GLOBALS['HTTP_SERVER_VARS'];
        }

        $this->session_id = sprintf("%s:%d-%s", isset($var['REMOTE_ADDR']) ? $var['REMOTE_ADDR'] : '127.0.0.1' , getmypid(), get_current_user());
    }

    /**
     * Creates a RADIUS resource
     *
     * Creates a RADIUS resource for accounting. This should be the first
     * call before you make any other things with the library.
     *
     * @return bool   true on success, false on error
     */
    function open()
    {
        $this->res = radius_acct_open();
        if (!$this->res) {
            return false;
        }
        return true;
    }

   /**
     * Creates an accounting request
     *
     * Creates an accounting request.
     * You MUST call this method before you can put any attribute.
     *
     * @return bool   true on success, false on error
     */
    function createRequest()
    {
        if (!radius_create_request($this->res, RADIUS_ACCOUNTING_REQUEST)) {
            return false;
        }
        return true;
    }

  /**
     * Put attributes for accounting.
     *
     * Here we put some accounting values. There many more attributes for accounting,
     * but for web-applications only certain attributes make sense.
     * @return void
     */
    function putAuthAttributes()
    {
        $this->putAttribute(RADIUS_ACCT_SESSION_ID, $this->session_id);
        $this->putAttribute(RADIUS_ACCT_STATUS_TYPE, $this->status_type);
        if (isset($this->session_time) && $this->status_type == RADIUS_STOP) {
            $this->putAttribute(RADIUS_ACCT_SESSION_TIME, $this->session_time);
        }
        if (isset($this->authentic)) {
            $this->putAttribute(RADIUS_ACCT_AUTHENTIC, $this->authentic);
        }

    }

}

/**
 * class Auth_RADIUS_Acct_Start
 *
 * Class for RADIUS accounting. Its usualy used, after the user has logged in.
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_Acct_Start extends Auth_RADIUS_Acct
{
   /**
     * Defines the type of the accounting request.
     * It is set to RADIUS_START by default in this class.
     * @var  integer
     */
    var $status_type = RADIUS_START;
}

/**
 * class Auth_RADIUS_Acct_Start
 *
 * Class for RADIUS accounting. Its usualy used, after the user has logged out.
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_Acct_Stop extends Auth_RADIUS_Acct
{
   /**
     * Defines the type of the accounting request.
     * It is set to RADIUS_STOP by default in this class.
     * @var  integer
     */
    var $status_type = RADIUS_STOP;
}

if (!defined('RADIUS_UPDATE'))
    define('RADIUS_UPDATE', 3);

/**
 * class Auth_RADIUS_Acct_Update
 *
 * Class for interim RADIUS accounting updates.
 *
 * @package Auth_RADIUS
 */
class Auth_RADIUS_Acct_Update extends Auth_RADIUS_Acct
{
   /**
     * Defines the type of the accounting request.
     * It is set to RADIUS_UPDATE by default in this class.
     * @var  integer
     */
    var $status_type = RADIUS_UPDATE;
}

?>
