<?php
/**
 * An XML-RPC server
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */

// Make certain that config.php doesn't display any errors, and that it doesn't
// override our do-not-display-errors setting:
// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);
// cookies are not used, makes sure there is empty global $USER
define('NO_MOODLE_COOKIES', true);

define('MNET_SERVER', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$mnet = get_mnet_environment();
// Include MNET stuff:
require_once $CFG->dirroot.'/mnet/lib.php';
require_once $CFG->dirroot.'/mnet/remote_client.php';
require_once $CFG->dirroot.'/mnet/xmlrpc/serverlib.php';


if ($CFG->mnet_dispatcher_mode === 'off') {
    print_error('mnetdisabled', 'mnet');
}

// Content type for output is not html:
header('Content-type: text/xml; charset=utf-8');

// PHP 5.2.2: $HTTP_RAW_POST_DATA not populated bug:
// http://bugs.php.net/bug.php?id=41293
if (empty($HTTP_RAW_POST_DATA)) {
    $HTTP_RAW_POST_DATA = file_get_contents('php://input');
}

if (!empty($CFG->mnet_rpcdebug)) {
    error_log("HTTP_RAW_POST_DATA");
    error_log($HTTP_RAW_POST_DATA);
}

if (!isset($_SERVER)) {
    exit(mnet_server_fault(712, "phperror"));
}


// New global variable which ONLY gets set in this server page, so you know that
// if you've been called by a remote Moodle, this should be set:
$remoteclient = new mnet_remote_client();
set_mnet_remote_client($remoteclient);

try {
    $plaintextmessage = mnet_server_strip_encryption($HTTP_RAW_POST_DATA);
    $xmlrpcrequest = mnet_server_strip_signature($plaintextmessage);
} catch (Exception $e) {
    exit(mnet_server_fault($e->getCode(), $e->getMessage(), $e->a));
}

if (!empty($CFG->mnet_rpcdebug)) {
    error_log("XMLRPC Payload");
    error_log(print_r($xmlrpcrequest,1));
}

if($remoteclient->pushkey == true) {
    // The peer used one of our older public keys, we will return a
    // signed/encrypted error message containing our new public key
    // Sign message with our old key, and encrypt to the peer's private key.
    exit(mnet_server_fault_xml(7025, $mnet->public_key, $remoteclient->useprivatekey));
}
// Have a peek at what the request would be if we were to process it
$params = xmlrpc_decode_request($xmlrpcrequest, $method);

// One of three conditions need to be met before we continue processing this request:
// 1. Request is properly encrypted and signed
// 2. Request is for a keyswap (we don't mind enencrypted or unsigned requests for a public key)
// 3. Request is properly signed and we're happy with it being unencrypted
if ((($remoteclient->request_was_encrypted == true) && ($remoteclient->signatureok == true))
    || (($method == 'system.keyswap') || ($method == 'system/keyswap'))
    || (($remoteclient->signatureok == true) && ($remoteclient->plaintext_is_ok() == true))) {
    try {
        // main dispatch call.  will echo the response directly
        mnet_server_dispatch($xmlrpcrequest);
        exit;
    } catch (Exception $e) {
        exit(mnet_server_fault($e->getCode(), $e->getMessage(), $e->a));
    }
}
// if we get to here, something is wrong
// so detect a few common cases and send appropriate errors
if (($remoteclient->request_was_encrypted == false) && ($remoteclient->plaintext_is_ok() == false)) {
    exit(mnet_server_fault(7021, 'forbidden-transport'));
}

if ($remoteclient->request_was_signed == false) {
    // Request was not signed
    exit(mnet_server_fault(711, 'verifysignature-error'));
}

if ($remoteclient->signatureok == false) {
    // We were unable to verify the signature
    exit(mnet_server_fault(710, 'verifysignature-invalid'));
}
exit(mnet_server_fault(7000, 'unknownerror'));
