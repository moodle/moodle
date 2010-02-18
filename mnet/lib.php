<?php // $Id$
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

$MNET = new mnet_environment();
$MNET->init();

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
    global $CFG, $MNET;
    // The key may be cached in the mnet_set_public_key function...
    // check this first
    $key = mnet_set_public_key($uri);
    if ($key != false) {
        return $key;
    }

    if (empty($application)) {
        $application = get_record('mnet_application', 'name', 'moodle');
    }

    $rq = xmlrpc_encode_request('system/keyswap', array($CFG->wwwroot, $MNET->public_key, $application->name), array("encoding" => "utf-8"));
    $ch = curl_init($uri . $application->xmlrpc_server_url);

    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Moodle');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rq);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml charset=UTF-8"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

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
    global $CFG, $MNET;
    $digest = sha1($message);

    // If the user hasn't supplied a private key (for example, one of our older,
    //  expired private keys, we get the current default private key and use that.
    if ($privatekey == null) {
        $privatekey = $MNET->get_private_key();
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
                <SignatureMethod Algorithm="http://www.w3.org/2000/09/xmldsig#dsa-sha1"/>
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
        <wwwroot>'.$MNET->wwwroot.'</wwwroot>
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
    global $MNET;

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
        <wwwroot>'.$MNET->wwwroot.'</wwwroot>
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
    global $CFG;
    static $keypair = null;
    if (!is_null($keypair)) return $keypair;
    if ($result = get_field('config_plugins', 'value', 'plugin', 'mnet', 'name', 'openssl')) {
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
    global $CFG, $USER;

    // check if lifetime has been overriden
    if (!empty($CFG->mnetkeylifetime)) {
        $days = $CFG->mnetkeylifetime;
    }

    $host = strtolower($CFG->wwwroot);
    $host = ereg_replace("^http(s)?://",'',$host);
    $break = strpos($host.'/' , '/');
    $host   = substr($host, 0, $break);

    if ($result = get_record_select('course'," id ='".SITEID."' ")) {
        $organization = $result->fullname;
    } else {
        $organization = 'None';
    }

    $keypair = array();

    $country  = 'NZ';
    $province = 'Wellington';
    $locality = 'Wellington';
    $email    = $CFG->noreplyaddress;

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
           "commonName" => $CFG->wwwroot,
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
        $csr_rsc = openssl_csr_new($dn, $new_key, array("config" => $CFG->opensslcnf));
        $selfSignedCert = openssl_csr_sign($csr_rsc, null, $new_key, $days, array("config" => $CFG->opensslcnf));
    } else {
        $new_key = openssl_pkey_new();
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

/**
 * Check that an IP address falls within the given network/mask
 * ok for export
 *
 * @param  string   $address        Dotted quad
 * @param  string   $network        Dotted quad
 * @param  string   $mask           A number, e.g. 16, 24, 32
 * @return bool
 */
function ip_in_range($address, $network, $mask) {
   $lnetwork  = ip2long($network);
   $laddress  = ip2long($address);

   $binnet    = str_pad( decbin($lnetwork),32,"0","STR_PAD_LEFT" );
   $firstpart = substr($binnet,0,$mask);

   $binip     = str_pad( decbin($laddress),32,"0","STR_PAD_LEFT" );
   $firstip   = substr($binip,0,$mask);
   return(strcmp($firstpart,$firstip)==0);
}

/**
 * Check that a given function (or method) in an include file has been designated
 * ok for export
 *
 * @param  string   $includefile    The path to the include file
 * @param  string   $functionname   The name of the function (or method) to
 *                                  execute
 * @param  mixed    $class          A class name, or false if we're just testing
 *                                  a function
 * @return int                      Zero (RPC_OK) if all ok - appropriate
 *                                  constant otherwise
 */
function mnet_permit_rpc_call($includefile, $functionname, $class=false) {
    global $CFG, $MNET_REMOTE_CLIENT;

    if (file_exists($CFG->dirroot . $includefile)) {
        include_once $CFG->dirroot . $includefile;
        // $callprefix matches the rpc convention
        // of not having a leading slash
        $callprefix = preg_replace('!^/!', '', $includefile);
    } else {
        return RPC_NOSUCHFILE;
    }

    if ($functionname != clean_param($functionname, PARAM_PATH)) {
        // Under attack?
        // Todo: Should really return a much more BROKEN! response
        return RPC_FORBIDDENMETHOD;
    }

    $id_list = $MNET_REMOTE_CLIENT->id;
    if (!empty($CFG->mnet_all_hosts_id)) {
        $id_list .= ', '.$CFG->mnet_all_hosts_id;
    }

    // TODO: change to left-join so we can disambiguate:
    // 1. method doesn't exist
    // 2. method exists but is prohibited
    $sql = "
        SELECT
            count(r.id)
        FROM
            {$CFG->prefix}mnet_host2service h2s,
            {$CFG->prefix}mnet_service2rpc s2r,
            {$CFG->prefix}mnet_rpc r
        WHERE
            h2s.serviceid = s2r.serviceid AND
            s2r.rpcid = r.id AND
            r.xmlrpc_path = '$callprefix/$functionname' AND
            h2s.hostid in ($id_list) AND
            h2s.publish = '1'";

    $permission = count_records_sql($sql);

    if (!$permission && 'dangerous' != $CFG->mnet_dispatcher_mode) {
        return RPC_FORBIDDENMETHOD;
    }

    // WE'RE LOOKING AT A CLASS/METHOD
    if (false != $class) {
        if (!class_exists($class)) {
            // Generate error response - unable to locate class
            return RPC_NOSUCHCLASS;
        }

        $object = new $class();

        if (!method_exists($object, $functionname)) {
            // Generate error response - unable to locate method
            return RPC_NOSUCHMETHOD;
        }

        if (!method_exists($object, 'mnet_publishes')) {
            // Generate error response - the class doesn't publish
            // *any* methods, because it doesn't have an mnet_publishes
            // method
            return RPC_FORBIDDENMETHOD;
        }

        // Get the list of published services - initialise method array
        $servicelist = $object->mnet_publishes();
        $methodapproved = false;

        // If the method is in the list of approved methods, set the
        // methodapproved flag to true and break
        foreach($servicelist as $service) {
            if (in_array($functionname, $service['methods'])) {
                $methodapproved = true;
                break;
            }
        }

        if (!$methodapproved) {
            return RPC_FORBIDDENMETHOD;
        }

        // Stash the object so we can call the method on it later
        $MNET_REMOTE_CLIENT->object_to_call($object);
    // WE'RE LOOKING AT A FUNCTION
    } else {
        if (!function_exists($functionname)) {
            // Generate error response - unable to locate function
            return RPC_NOSUCHFUNCTION;
        }

    }

    return RPC_OK;
}

function mnet_update_sso_access_control($username, $mnet_host_id, $accessctrl) {
    $mnethost = get_record('mnet_host', 'id', $mnet_host_id);
    if ($aclrecord = get_record('mnet_sso_access_control', 'username', $username, 'mnet_host_id', $mnet_host_id)) {
        // update
        $aclrecord->accessctrl = $accessctrl;
        if (update_record('mnet_sso_access_control', $aclrecord)) {
            add_to_log(SITEID, 'admin/mnet', 'update', 'admin/mnet/access_control.php',
                    "SSO ACL: $accessctrl user '$username' from {$mnethost->name}");
        } else {
            print_error('failedaclwrite', 'mnet', '', $username);
            return false;
        }
    } else {
        // insert
        $aclrecord->username = $username;
        $aclrecord->accessctrl = $accessctrl;
        $aclrecord->mnet_host_id = $mnet_host_id;
        if ($id = insert_record('mnet_sso_access_control', $aclrecord)) {
            add_to_log(SITEID, 'admin/mnet', 'add', 'admin/mnet/access_control.php',
                    "SSO ACL: $accessctrl user '$username' from {$mnethost->name}");
        } else {
            print_error('failedaclwrite', 'mnet', '', $username);
            return false;
        }
    }
    return true;
}

function mnet_get_peer_host ($mnethostid) {
    static $hosts;
    if (!isset($hosts[$mnethostid])) {
        $host = get_record('mnet_host', 'id', $mnethostid);
        $hosts[$mnethostid] = $host;
    }
    return $hosts[$mnethostid];
}

/**
 * Inline function to modify a url string so that mnet users are requested to
 * log in at their mnet identity provider (if they are not already logged in)
 * before ultimately being directed to the original url.
 *
 * uses global MNETIDPJUMPURL the url which user should initially be directed to
 *     MNETIDPJUMPURL is a URL associated with a moodle networking peer when it
 *     is fulfiling a role as an identity provider (IDP). Different urls for
 *     different peers, the jumpurl is formed partly from the IDP's webroot, and
 *     partly from a predefined local path within that webwroot.
 *     The result of the user hitting MNETIDPJUMPURL is that they will be asked
 *     to login (at their identity provider (if they aren't already)), mnet
 *     will prepare the necessary authentication information, then redirect
 *     them back to somewhere at the content provider(CP) moodle (this moodle)
 * @param array $url array with 2 elements
 *     0 - context the url was taken from, possibly just the url, possibly href="url"
 *     1 - the destination url
 * @return string the url the remote user should be supplied with.
 */
function mnet_sso_apply_indirection ($url) {
    global $MNETIDPJUMPURL;
    global $CFG;

    $localpart='';
    $urlparts = parse_url($url[1]);
    if($urlparts) {
        if (isset($urlparts['path'])) {
            $path = $urlparts['path'];
            // if our wwwroot has a path component, need to strip that path from beginning of the
            // 'localpart' to make it relative to moodle's wwwroot
            $wwwrootparts = parse_url($CFG->wwwroot);
            if (!empty($wwwrootparts['path']) and strpos($path, $wwwrootparts['path']) === 0) {
                $path = substr($path, strlen($wwwrootparts['path']));
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
    $indirecturl = $MNETIDPJUMPURL . urlencode($localpart);
    //If we matched on more than just a url (ie an html link), return the url to an href format
    if ($url[0] != $url[1]) {
        $indirecturl = 'href="'.$indirecturl.'"';
    }
    return $indirecturl;
}

?>
