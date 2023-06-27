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

require(__DIR__.'/../../config.php');

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

$rawpostdata = file_get_contents("php://input");
mnet_debug("RAW POST DATA", 2);
mnet_debug($rawpostdata, 2);

if (!isset($_SERVER)) {
    exit(mnet_server_fault(712, get_string('phperror', 'mnet')));
}


// New global variable which ONLY gets set in this server page, so you know that
// if you've been called by a remote Moodle, this should be set:
$remoteclient = new mnet_remote_client();
set_mnet_remote_client($remoteclient);

try {
    $plaintextmessage = mnet_server_strip_encryption($rawpostdata);
    $xmlrpcrequest = mnet_server_strip_signature($plaintextmessage);
} catch (Exception $e) {
    mnet_debug('encryption strip exception thrown: ' . $e->getMessage());
    exit(mnet_server_fault($e->getCode(), $e->getMessage(), $e->a));
}

mnet_debug('XMLRPC Payload', 2);
mnet_debug($xmlrpcrequest, 2);

if($remoteclient->pushkey == true) {
    // The peer used one of our older public keys, we will return a
    // signed/encrypted error message containing our new public key
    // Sign message with our old key, and encrypt to the peer's private key.
    mnet_debug('sending back new key');
    exit(mnet_server_fault_xml(7025, $mnet->public_key, $remoteclient->useprivatekey));
}
// Have a peek at what the request would be if we were to process it
$params = xmlrpc_decode_request($xmlrpcrequest, $method);
mnet_debug("incoming mnet request $method");

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
        mnet_debug('exiting cleanly');
        exit;
    } catch (Exception $e) {
        mnet_debug('dispatch exception thrown: ' . $e->getMessage());
        exit(mnet_server_fault($e->getCode(), $e->getMessage(), $e->a));
    }
}
// if we get to here, something is wrong
// so detect a few common cases and send appropriate errors
if (($remoteclient->request_was_encrypted == false) && ($remoteclient->plaintext_is_ok() == false)) {
    mnet_debug('non encrypted request');
    exit(mnet_server_fault(7021, get_string('forbidden-transport', 'mnet')));
}

if ($remoteclient->request_was_signed == false) {
    // Request was not signed
    mnet_debug('non signed request');
    exit(mnet_server_fault(711, get_string('verifysignature-error', 'mnet')));
}

if ($remoteclient->signatureok == false) {
    // We were unable to verify the signature
    mnet_debug('non verified signature');
    exit(mnet_server_fault(710, get_string('verifysignature-invalid', 'mnet')));
}
mnet_debug('unknown error');
exit(mnet_server_fault(7000, get_string('unknownerror', 'mnet')));
