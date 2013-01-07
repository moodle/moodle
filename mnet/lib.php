<?php
/**
 * Library functions for mnet
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */
require_once $CFG->dirroot.'/mnet/xmlrpc/xmlparser.php';
require_once $CFG->dirroot.'/mnet/peer.php';
require_once $CFG->dirroot.'/mnet/environment.php';

/// CONSTANTS ///////////////////////////////////////////////////////////

define('RPC_OK',                0);
define('RPC_NOSUCHFILE',        1);
define('RPC_NOSUCHCLASS',       2);
define('RPC_NOSUCHFUNCTION',    3);
define('RPC_FORBIDDENFUNCTION', 4);
define('RPC_NOSUCHMETHOD',      5);
define('RPC_FORBIDDENMETHOD',   6);

/**
 * Strip extraneous detail from a URL or URI and return the hostname
 *
 * @param  string  $uri  The URI of a file on the remote computer, optionally
 *                       including its http:// prefix like
 *                       http://www.example.com/index.html
 * @return string        Just the hostname
 */
function mnet_get_hostname_from_uri($uri = null) {
    $count = preg_match("@^(?:http[s]?://)?([A-Z0-9\-\.]+).*@i", $uri, $matches);
    if ($count > 0) return $matches[1];
    return false;
}

/**
 * Get the remote machine's SSL Cert
 *
 * @param  string  $uri     The URI of a file on the remote computer, including
 *                          its http:// or https:// prefix
 * @return string           A PEM formatted SSL Certificate.
 */
function mnet_get_public_key($uri, $application=null) {
    global $CFG, $DB;
    $mnet = get_mnet_environment();
    // The key may be cached in the mnet_set_public_key function...
    // check this first
    $key = mnet_set_public_key($uri);
    if ($key != false) {
        return $key;
    }

    if (empty($application)) {
        $application = $DB->get_record('mnet_application', array('name'=>'moodle'));
    }

    $rq = xmlrpc_encode_request('system/keyswap', array($CFG->wwwroot, $mnet->public_key, $application->name), array("encoding" => "utf-8"));
    $ch = curl_init($uri . $application->xmlrpc_server_url);

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // check for proxy
    if (!empty($CFG->proxyhost) and !is_proxybypass($uri)) {
        // SOCKS supported in PHP5 only
        if (!empty($CFG->proxytype) and ($CFG->proxytype == 'SOCKS5')) {
            if (defined('CURLPROXY_SOCKS5')) {
                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            } else {
                curl_close($ch);
                print_error( 'socksnotsupported','mnet' );
            }
        }

        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);

        if (empty($CFG->proxyport)) {
            curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost);
        } else {
            curl_setopt($ch, CURLOPT_PROXY, $CFG->proxyhost.':'.$CFG->proxyport);
        }

        if (!empty($CFG->proxyuser) and !empty($CFG->proxypassword)) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $CFG->proxyuser.':'.$CFG->proxypassword);
            if (defined('CURLOPT_PROXYAUTH')) {
                // any proxy authentication if PHP 5.1
                curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC | CURLAUTH_NTLM);
            }
        }
    }

    $res = xmlrpc_decode(curl_exec($ch));

    // check for curl errors
    $curlerrno = curl_errno($ch);
    if ($curlerrno!=0) {
        debugging("Request for $uri failed with curl error $curlerrno");
    }

    // check HTTP error code
    $info =  curl_getinfo($ch);
    if (!empty($info['http_code']) and ($info['http_code'] != 200)) {
        debugging("Request for $uri failed with HTTP code ".$info['http_code']);
    }

    curl_close($ch);

    if (!is_array($res)) { // ! error
        $public_certificate = $res;
        $credentials=array();
        if (strlen(trim($public_certificate))) {
            $credentials = openssl_x509_parse($public_certificate);
            $host = $credentials['subject']['CN'];
            if (array_key_exists( 'subjectAltName', $credentials['subject'])) {
                $host = $credentials['subject']['subjectAltName'];
            }
            if (strpos($uri, $host) !== false) {
                mnet_set_public_key($uri, $public_certificate);
                return $public_certificate;
            }
            else {
                debugging("Request for $uri returned public key for different URI - $host");
            }
        }
        else {
            debugging("Request for $uri returned empty response");
        }
    }
    else {
        debugging( "Request for $uri returned unexpected result");
    }
    return false;
}

/**
 * Store a URI's public key in a static variable, or retrieve the key for a URI
 *
 * @param  string  $uri  The URI of a file on the remote computer, including its
 *                       https:// prefix
 * @param  mixed   $key  A public key to store in the array OR null. If the key
 *                       is null, the function will return the previously stored
 *                       key for the supplied URI, should it exist.
 * @return mixed         A public key OR true/false.
 */
function mnet_set_public_key($uri, $key = null) {
    static $keyarray = array();
    if (isset($keyarray[$uri]) && empty($key)) {
        return $keyarray[$uri];
    } elseif (!empty($key)) {
        $keyarray[$uri] = $key;
        return true;
    }
    return false;
}

/**
 * Sign a message and return it in an XML-Signature document
 *
 * This function can sign any content, but it was written to provide a system of
 * signing XML-RPC request and response messages. The message will be base64
 * encoded, so it does not need to be text.
 *
 * We compute the SHA1 digest of the message.
 * We compute a signature on that digest with our private key.
 * We link to the public key that can be used to verify our signature.
 * We base64 the message data.
 * We identify our wwwroot - this must match our certificate's CN
 *
 * The XML-RPC document will be parceled inside an XML-SIG document, which holds
 * the base64_encoded XML as an object, the SHA1 digest of that document, and a
 * signature of that document using the local private key. This signature will
 * uniquely identify the RPC document as having come from this server.
 *
 * See the {@Link http://www.w3.org/TR/xmldsig-core/ XML-DSig spec} at the W3c
 * site
 *
 * @param  string   $message              The data you want to sign
 * @param  resource $privatekey           The private key to sign the response with
 * @return string                         An XML-DSig document
 */
function mnet_sign_message($message, $privatekey = null) {
    global $CFG;
    $digest = sha1($message);

    $mnet = get_mnet_environment();
    // If the user hasn't supplied a private key (for example, one of our older,
    //  expired private keys, we get the current default private key and use that.
    if ($privatekey == null) {
        $privatekey = $mnet->get_private_key();
    }

    // The '$sig' value below is returned by reference.
    // We initialize it first to stop my IDE from complaining.
    $sig  = '';
    $bool = openssl_sign($message, $sig, $privatekey); // TODO: On failure?

    $message = '<?xml version="1.0" encoding="iso-8859-1"?>
    <signedMessage>
        <Signature Id="MoodleSignature" xmlns="http://www.w3.org/2000/09/xmldsig#">
            <SignedInfo>
                <CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/>
                <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#rsa-sha1"/>
                <Reference URI="#XMLRPC-MSG">
                    <DigestMethod Algorithm="http://www.w3.org/2000/09/xmldsig#sha1"/>
                    <DigestValue>'.$digest.'</DigestValue>
                </Reference>
            </SignedInfo>
            <SignatureValue>'.base64_encode($sig).'</SignatureValue>
            <KeyInfo>
                <RetrievalMethod URI="'.$CFG->wwwroot.'/mnet/publickey.php"/>
            </KeyInfo>
        </Signature>
        <object ID="XMLRPC-MSG">'.base64_encode($message).'</object>
        <wwwroot>'.$mnet->wwwroot.'</wwwroot>
        <timestamp>'.time().'</timestamp>
    </signedMessage>';
    return $message;
}

/**
 * Encrypt a message and return it in an XML-Encrypted document
 *
 * This function can encrypt any content, but it was written to provide a system
 * of encrypting XML-RPC request and response messages. The message will be
 * base64 encoded, so it does not need to be text - binary data should work.
 *
 * We compute the SHA1 digest of the message.
 * We compute a signature on that digest with our private key.
 * We link to the public key that can be used to verify our signature.
 * We base64 the message data.
 * We identify our wwwroot - this must match our certificate's CN
 *
 * The XML-RPC document will be parceled inside an XML-SIG document, which holds
 * the base64_encoded XML as an object, the SHA1 digest of that document, and a
 * signature of that document using the local private key. This signature will
 * uniquely identify the RPC document as having come from this server.
 *
 * See the {@Link http://www.w3.org/TR/xmlenc-core/ XML-ENC spec} at the W3c
 * site
 *
 * @param  string   $message              The data you want to sign
 * @param  string   $remote_certificate   Peer's certificate in PEM format
 * @return string                         An XML-ENC document
 */
function mnet_encrypt_message($message, $remote_certificate) {
    $mnet = get_mnet_environment();

    // Generate a key resource from the remote_certificate text string
    $publickey = openssl_get_publickey($remote_certificate);

    if ( gettype($publickey) != 'resource' ) {
        // Remote certificate is faulty.
        return false;
    }

    // Initialize vars
    $encryptedstring = '';
    $symmetric_keys = array();

    //        passed by ref ->     &$encryptedstring &$symmetric_keys
    $bool = openssl_seal($message, $encryptedstring, $symmetric_keys, array($publickey));
    $message = $encryptedstring;
    $symmetrickey = array_pop($symmetric_keys);

    $message = '<?xml version="1.0" encoding="iso-8859-1"?>
    <encryptedMessage>
        <EncryptedData Id="ED" xmlns="http://www.w3.org/2001/04/xmlenc#">
            <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#arcfour"/>
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:RetrievalMethod URI="#EK" Type="http://www.w3.org/2001/04/xmlenc#EncryptedKey"/>
                <ds:KeyName>XMLENC</ds:KeyName>
            </ds:KeyInfo>
            <CipherData>
                <CipherValue>'.base64_encode($message).'</CipherValue>
            </CipherData>
        </EncryptedData>
        <EncryptedKey Id="EK" xmlns="http://www.w3.org/2001/04/xmlenc#">
            <EncryptionMethod Algorithm="http://www.w3.org/2001/04/xmlenc#rsa-1_5"/>
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:KeyName>SSLKEY</ds:KeyName>
            </ds:KeyInfo>
            <CipherData>
                <CipherValue>'.base64_encode($symmetrickey).'</CipherValue>
            </CipherData>
            <ReferenceList>
                <DataReference URI="#ED"/>
            </ReferenceList>
            <CarriedKeyName>XMLENC</CarriedKeyName>
        </EncryptedKey>
        <wwwroot>'.$mnet->wwwroot.'</wwwroot>
    </encryptedMessage>';
    return $message;
}

/**
 * Get your SSL keys from the database, or create them (if they don't exist yet)
 *
 * Get your SSL keys from the database, or (if they don't exist yet) call
 * mnet_generate_keypair to create them
 *
 * @param   string  $string     The text you want to sign
 * @return  string              The signature over that text
 */
function mnet_get_keypair() {
    global $CFG, $DB;;
    static $keypair = null;
    if (!is_null($keypair)) return $keypair;
    if ($result = get_config('mnet', 'openssl')) {
        list($keypair['certificate'], $keypair['keypair_PEM']) = explode('@@@@@@@@', $result);
        $keypair['privatekey'] = openssl_pkey_get_private($keypair['keypair_PEM']);
        $keypair['publickey']  = openssl_pkey_get_public($keypair['certificate']);
        return $keypair;
    } else {
        $keypair = mnet_generate_keypair();
        return $keypair;
    }
}

/**
 * Generate public/private keys and store in the config table
 *
 * Use the distinguished name provided to create a CSR, and then sign that CSR
 * with the same credentials. Store the keypair you create in the config table.
 * If a distinguished name is not provided, create one using the fullname of
 * 'the course with ID 1' as your organization name, and your hostname (as
 * detailed in $CFG->wwwroot).
 *
 * @param   array  $dn  The distinguished name of the server
 * @return  string      The signature over that text
 */
function mnet_generate_keypair($dn = null, $days=28) {
    global $CFG, $USER, $DB;

    // check if lifetime has been overriden
    if (!empty($CFG->mnetkeylifetime)) {
        $days = $CFG->mnetkeylifetime;
    }

    $host = strtolower($CFG->wwwroot);
    $host = preg_replace("~^http(s)?://~",'',$host);
    $break = strpos($host.'/' , '/');
    $host   = substr($host, 0, $break);

    $site = get_site();
    $organization = $site->fullname;

    $keypair = array();

    $country  = 'NZ';
    $province = 'Wellington';
    $locality = 'Wellington';
    $email    = !empty($CFG->noreplyaddress) ? $CFG->noreplyaddress : 'noreply@'.$_SERVER['HTTP_HOST'];

    if(!empty($USER->country)) {
        $country  = $USER->country;
    }
    if(!empty($USER->city)) {
        $province = $USER->city;
        $locality = $USER->city;
    }
    if(!empty($USER->email)) {
        $email    = $USER->email;
    }

    if (is_null($dn)) {
        $dn = array(
           "countryName" => $country,
           "stateOrProvinceName" => $province,
           "localityName" => $locality,
           "organizationName" => $organization,
           "organizationalUnitName" => 'Moodle',
           "commonName" => substr($CFG->wwwroot, 0, 64),
           "subjectAltName" => $CFG->wwwroot,
           "emailAddress" => $email
        );
    }

    $dnlimits = array(
           'countryName'            => 2,
           'stateOrProvinceName'    => 128,
           'localityName'           => 128,
           'organizationName'       => 64,
           'organizationalUnitName' => 64,
           'commonName'             => 64,
           'emailAddress'           => 128
    );

    foreach ($dnlimits as $key => $length) {
        $dn[$key] = substr($dn[$key], 0, $length);
    }

    // ensure we remove trailing slashes
    $dn["commonName"] = preg_replace(':/$:', '', $dn["commonName"]);
    if (!empty($CFG->opensslcnf)) { //allow specification of openssl.cnf especially for Windows installs
        $new_key = openssl_pkey_new(array("config" => $CFG->opensslcnf));
    } else {
        $new_key = openssl_pkey_new();
    }
    if ($new_key === false) {
        // can not generate keys - missing openssl.cnf??
        return null;
    }
    if (!empty($CFG->opensslcnf)) { //allow specification of openssl.cnf especially for Windows installs
        $csr_rsc = openssl_csr_new($dn, $new_key, array("config" => $CFG->opensslcnf));
        $selfSignedCert = openssl_csr_sign($csr_rsc, null, $new_key, $days, array("config" => $CFG->opensslcnf));
    } else {
        $csr_rsc = openssl_csr_new($dn, $new_key, array('private_key_bits',2048));
        $selfSignedCert = openssl_csr_sign($csr_rsc, null, $new_key, $days);
    }
    unset($csr_rsc); // Free up the resource

    // We export our self-signed certificate to a string.
    openssl_x509_export($selfSignedCert, $keypair['certificate']);
    openssl_x509_free($selfSignedCert);

    // Export your public/private key pair as a PEM encoded string. You
    // can protect it with an optional passphrase if you wish.
    if (!empty($CFG->opensslcnf)) { //allow specification of openssl.cnf especially for Windows installs
        $export = openssl_pkey_export($new_key, $keypair['keypair_PEM'], null, array("config" => $CFG->opensslcnf));
    } else {
        $export = openssl_pkey_export($new_key, $keypair['keypair_PEM'] /* , $passphrase */);
    }
    openssl_pkey_free($new_key);
    unset($new_key); // Free up the resource

    return $keypair;
}


function mnet_update_sso_access_control($username, $mnet_host_id, $accessctrl) {
    global $DB;

    $mnethost = $DB->get_record('mnet_host', array('id'=>$mnet_host_id));
    if ($aclrecord = $DB->get_record('mnet_sso_access_control', array('username'=>$username, 'mnet_host_id'=>$mnet_host_id))) {
        // update
        $aclrecord->accessctrl = $accessctrl;
        $DB->update_record('mnet_sso_access_control', $aclrecord);
        add_to_log(SITEID, 'admin/mnet', 'update', 'admin/mnet/access_control.php',
                "SSO ACL: $accessctrl user '$username' from {$mnethost->name}");
    } else {
        // insert
        $aclrecord = new stdClass();
        $aclrecord->username = $username;
        $aclrecord->accessctrl = $accessctrl;
        $aclrecord->mnet_host_id = $mnet_host_id;
        $id = $DB->insert_record('mnet_sso_access_control', $aclrecord);
        add_to_log(SITEID, 'admin/mnet', 'add', 'admin/mnet/access_control.php',
                "SSO ACL: $accessctrl user '$username' from {$mnethost->name}");
    }
    return true;
}

function mnet_get_peer_host ($mnethostid) {
    global $DB;
    static $hosts;
    if (!isset($hosts[$mnethostid])) {
        $host = $DB->get_record('mnet_host', array('id' => $mnethostid));
        $hosts[$mnethostid] = $host;
    }
    return $hosts[$mnethostid];
}

/**
 * Inline function to modify a url string so that mnet users are requested to
 * log in at their mnet identity provider (if they are not already logged in)
 * before ultimately being directed to the original url.
 *
 * @param string $jumpurl the url which user should initially be directed to.
 *     This is a URL associated with a moodle networking peer when it
 *     is fulfiling a role as an identity provider (IDP). Different urls for
 *     different peers, the jumpurl is formed partly from the IDP's webroot, and
 *     partly from a predefined local path within that webwroot.
 *     The result of the user hitting this jump url is that they will be asked
 *     to login (at their identity provider (if they aren't already)), mnet
 *     will prepare the necessary authentication information, then redirect
 *     them back to somewhere at the content provider(CP) moodle (this moodle)
 * @param array $url array with 2 elements
 *     0 - context the url was taken from, possibly just the url, possibly href="url"
 *     1 - the destination url
 * @return string the url the remote user should be supplied with.
 */
function mnet_sso_apply_indirection ($jumpurl, $url) {
    global $USER, $CFG;

    $localpart='';
    $urlparts = parse_url($url[1]);
    if($urlparts) {
        if (isset($urlparts['path'])) {
            $path = $urlparts['path'];
            // if our wwwroot has a path component, need to strip that path from beginning of the
            // 'localpart' to make it relative to moodle's wwwroot
            $wwwrootpath = parse_url($CFG->wwwroot, PHP_URL_PATH);
            if (!empty($wwwrootpath) and strpos($path, $wwwrootpath) === 0) {
                $path = substr($path, strlen($wwwrootpath));
            }
            $localpart .= $path;
        }
        if (isset($urlparts['query'])) {
            $localpart .= '?'.$urlparts['query'];
        }
        if (isset($urlparts['fragment'])) {
            $localpart .= '#'.$urlparts['fragment'];
        }
    }
    $indirecturl = $jumpurl . urlencode($localpart);
    //If we matched on more than just a url (ie an html link), return the url to an href format
    if ($url[0] != $url[1]) {
        $indirecturl = 'href="'.$indirecturl.'"';
    }
    return $indirecturl;
}

function mnet_get_app_jumppath ($applicationid) {
    global $DB;
    static $appjumppaths;
    if (!isset($appjumppaths[$applicationid])) {
        $ssojumpurl = $DB->get_field('mnet_application', 'sso_jump_url', array('id' => $applicationid));
        $appjumppaths[$applicationid] = $ssojumpurl;
    }
    return $appjumppaths[$applicationid];
}


/**
 * Output debug information about mnet.  this will go to the <b>error_log</b>.
 *
 * @param mixed $debugdata this can be a string, or array or object.
 * @param int   $debuglevel optional , defaults to 1. bump up for very noisy debug info
 */
function mnet_debug($debugdata, $debuglevel=1) {
    global $CFG;
    $setlevel = get_config('', 'mnet_rpcdebug');
    if (empty($setlevel) || $setlevel < $debuglevel) {
        return;
    }
    if (is_object($debugdata)) {
        $debugdata = (array)$debugdata;
    }
    if (is_array($debugdata)) {
        mnet_debug('DUMPING ARRAY');
        foreach ($debugdata as $key => $value) {
            mnet_debug("$key: $value");
        }
        mnet_debug('END DUMPING ARRAY');
        return;
    }
    $prefix = 'MNET DEBUG ';
    if (defined('MNET_SERVER')) {
        $prefix .= " (server $CFG->wwwroot";
        if ($peer = get_mnet_remote_client() && !empty($peer->wwwroot)) {
            $prefix .= ", remote peer " . $peer->wwwroot;
        }
        $prefix .= ')';
    } else {
        $prefix .= " (client $CFG->wwwroot) ";
    }
    error_log("$prefix $debugdata");
}

/**
 * Return an array of information about all moodle's profile fields
 * which ones are optional, which ones are forced.
 * This is used as the basis of providing lists of profile fields to the administrator
 * to pick which fields to import/export over MNET
 *
 * @return array(forced => array, optional => array)
 */
function mnet_profile_field_options() {
    global $DB;
    static $info;
    if (!empty($info)) {
        return $info;
    }

    $excludes = array(
        'id',              // makes no sense
        'mnethostid',      // makes no sense
        'timecreated',     // will be set to relative to the host anyway
        'timemodified',    // will be set to relative to the host anyway
        'auth',            // going to be set to 'mnet'
        'deleted',         // we should never get deleted users sent over, but don't send this anyway
        'confirmed',       // unconfirmed users can't log in to their home site, all remote users considered confirmed
        'password',        // no password for mnet users
        'theme',           // handled separately
        'lastip',          // will be set to relative to the host anyway
    );

    // these are the ones that user_not_fully_set_up will complain about
    // and also special case ones
    $forced = array(
        'username',
        'email',
        'firstname',
        'lastname',
        'auth',
        'wwwroot',
        'session.gc_lifetime',
        '_mnet_userpicture_timemodified',
        '_mnet_userpicture_mimetype',
    );

    // these are the ones we used to send/receive (pre 2.0)
    $legacy = array(
        'username',
        'email',
        'auth',
        'deleted',
        'firstname',
        'lastname',
        'city',
        'country',
        'lang',
        'timezone',
        'description',
        'mailformat',
        'maildigest',
        'maildisplay',
        'htmleditor',
        'wwwroot',
        'picture',
    );

    // get a random user record from the database to pull the fields off
    $randomuser = $DB->get_record('user', array(), '*', IGNORE_MULTIPLE);
    foreach ($randomuser as $key => $discard) {
        if (in_array($key, $excludes) || in_array($key, $forced)) {
            continue;
        }
        $fields[$key] = $key;
    }
    $info = array(
        'forced'   => $forced,
        'optional' => $fields,
        'legacy'   => $legacy,
    );
    return $info;
}


/**
 * Returns information about MNet peers
 *
 * @param bool $withdeleted should the deleted peers be returned too
 * @return array
 */
function mnet_get_hosts($withdeleted = false) {
    global $CFG, $DB;

    $sql = "SELECT h.id, h.deleted, h.wwwroot, h.ip_address, h.name, h.public_key, h.public_key_expires,
                   h.transport, h.portno, h.last_connect_time, h.last_log_id, h.applicationid,
                   a.name as app_name, a.display_name as app_display_name, a.xmlrpc_server_url
              FROM {mnet_host} h
              JOIN {mnet_application} a ON h.applicationid = a.id
             WHERE h.id <> ?";

    if (!$withdeleted) {
        $sql .= "  AND h.deleted = 0";
    }

    $sql .= " ORDER BY h.deleted, h.name, h.id";

    return $DB->get_records_sql($sql, array($CFG->mnet_localhost_id));
}


/**
 * return an array information about services enabled for the given peer.
 * in two modes, fulldata or very basic data.
 *
 * @param mnet_peer $mnet_peer the peer to get information abut
 * @param boolean   $fulldata whether to just return which services are published/subscribed, or more information (defaults to full)
 *
 * @return array  If $fulldata is false, an array is returned like:
 *                publish => array(
 *                    serviceid => boolean,
 *                    serviceid => boolean,
 *                ),
 *                subscribe => array(
 *                    serviceid => boolean,
 *                    serviceid => boolean,
 *                )
 *                If $fulldata is true, an array is returned like:
 *                servicename => array(
 *                   apiversion => array(
 *                        name           => string
 *                        offer          => boolean
 *                        apiversion     => int
 *                        plugintype     => string
 *                        pluginname     => string
 *                        hostsubscribes => boolean
 *                        hostpublishes  => boolean
 *                   ),
 *               )
 */
function mnet_get_service_info(mnet_peer $mnet_peer, $fulldata=true) {
    global $CFG, $DB;

    $requestkey = (!empty($fulldata) ? 'fulldata' : 'mydata');

    static $cache = array();
    if (array_key_exists($mnet_peer->id, $cache)) {
        return $cache[$mnet_peer->id][$requestkey];
    }

    $id_list = $mnet_peer->id;
    if (!empty($CFG->mnet_all_hosts_id)) {
        $id_list .= ', '.$CFG->mnet_all_hosts_id;
    }

    $concat = $DB->sql_concat('COALESCE(h2s.id,0) ', ' \'-\' ', ' svc.id', '\'-\'', 'r.plugintype', '\'-\'', 'r.pluginname');

    $query = "
        SELECT DISTINCT
            $concat as id,
            svc.id as serviceid,
            svc.name,
            svc.offer,
            svc.apiversion,
            r.plugintype,
            r.pluginname,
            h2s.hostid,
            h2s.publish,
            h2s.subscribe
        FROM
            {mnet_service2rpc} s2r,
            {mnet_rpc} r,
            {mnet_service} svc
        LEFT JOIN
            {mnet_host2service} h2s
        ON
            h2s.hostid in ($id_list) AND
            h2s.serviceid = svc.id
        WHERE
            svc.offer = '1' AND
            s2r.serviceid = svc.id AND
            s2r.rpcid = r.id
        ORDER BY
            svc.name ASC";

    $resultset = $DB->get_records_sql($query);

    if (is_array($resultset)) {
        $resultset = array_values($resultset);
    } else {
        $resultset = array();
    }

    require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

    $remoteservices = array();
    if ($mnet_peer->id != $CFG->mnet_all_hosts_id) {
        // Create a new request object
        $mnet_request = new mnet_xmlrpc_client();

        // Tell it the path to the method that we want to execute
        $mnet_request->set_method('system/listServices');
        $mnet_request->send($mnet_peer);
        if (is_array($mnet_request->response)) {
            foreach($mnet_request->response as $service) {
                $remoteservices[$service['name']][$service['apiversion']] = $service;
            }
        }
    }

    $myservices = array();
    $mydata = array();
    foreach($resultset as $result) {
        $result->hostpublishes  = false;
        $result->hostsubscribes = false;
        if (isset($remoteservices[$result->name][$result->apiversion])) {
            if ($remoteservices[$result->name][$result->apiversion]['publish'] == 1) {
                $result->hostpublishes  = true;
            }
            if ($remoteservices[$result->name][$result->apiversion]['subscribe'] == 1) {
                $result->hostsubscribes  = true;
            }
        }

        if (empty($myservices[$result->name][$result->apiversion])) {
            $myservices[$result->name][$result->apiversion] = array('serviceid' => $result->serviceid,
                                                                    'name' => $result->name,
                                                                    'offer' => $result->offer,
                                                                    'apiversion' => $result->apiversion,
                                                                    'plugintype' => $result->plugintype,
                                                                    'pluginname' => $result->pluginname,
                                                                    'hostsubscribes' => $result->hostsubscribes,
                                                                    'hostpublishes' => $result->hostpublishes
                                                                    );
        }

        // allhosts_publish allows us to tell the admin that even though he
        // is disabling a service, it's still available to the host because
        // he's also publishing it to 'all hosts'
        if ($result->hostid == $CFG->mnet_all_hosts_id && $CFG->mnet_all_hosts_id != $mnet_peer->id) {
            $myservices[$result->name][$result->apiversion]['allhosts_publish'] = $result->publish;
            $myservices[$result->name][$result->apiversion]['allhosts_subscribe'] = $result->subscribe;
        } elseif (!empty($result->hostid)) {
            $myservices[$result->name][$result->apiversion]['I_publish'] = $result->publish;
            $myservices[$result->name][$result->apiversion]['I_subscribe'] = $result->subscribe;
        }
        $mydata['publish'][$result->serviceid] = $result->publish;
        $mydata['subscribe'][$result->serviceid] = $result->subscribe;

    }

    $cache[$mnet_peer->id]['fulldata'] = $myservices;
    $cache[$mnet_peer->id]['mydata'] = $mydata;

    return $cache[$mnet_peer->id][$requestkey];
}

/**
 * return an array of the profile fields to send
 * with user information to the given mnet host.
 *
 * @param mnet_peer $peer the peer to send the information to
 *
 * @return array (like 'username', 'firstname', etc)
 */
function mnet_fields_to_send(mnet_peer $peer) {
    return _mnet_field_helper($peer, 'export');
}

/**
 * return an array of the profile fields to import
 * from the given host, when creating/updating user accounts
 *
 * @param mnet_peer $peer the peer we're getting the information from
 *
 * @return array (like 'username', 'firstname', etc)
 */
function mnet_fields_to_import(mnet_peer $peer) {
    return _mnet_field_helper($peer, 'import');
}

/**
 * helper for {@see mnet_fields_to_import} and {@mnet_fields_to_send}
 *
 * @access private
 *
 * @param mnet_peer $peer the peer object
 * @param string    $key 'import' or 'export'
 *
 * @return array (like 'username', 'firstname', etc)
 */
function _mnet_field_helper(mnet_peer $peer, $key) {
    $tmp = mnet_profile_field_options();
    $defaults = explode(',', get_config('moodle', 'mnetprofile' . $key . 'fields'));
    if ('1' === get_config('mnet', 'host' . $peer->id . $key . 'default')) {
        return array_merge($tmp['forced'], $defaults);
    }
    $hostsettings = get_config('mnet', 'host' . $peer->id . $key . 'fields');
    if (false === $hostsettings) {
        return array_merge($tmp['forced'], $defaults);
    }
    return array_merge($tmp['forced'], explode(',', $hostsettings));
}


/**
 * given a user object (or array) and a list of allowed fields,
 * strip out all the fields that should not be included.
 * This can be used both for outgoing data and incoming data.
 *
 * @param mixed $user array or object representing a database record
 * @param array $fields an array of allowed fields (usually from mnet_fields_to_{send,import}
 *
 * @return mixed array or object, depending what type of $user object was passed (datatype is respected)
 */
function mnet_strip_user($user, $fields) {
    if (is_object($user)) {
        $user = (array)$user;
        $wasobject = true; // so we can cast back before we return
    }

    foreach ($user as $key => $value) {
        if (!in_array($key, $fields)) {
            unset($user[$key]);
        }
    }
    if (!empty($wasobject)) {
        $user = (object)$user;
    }
    return $user;
}
