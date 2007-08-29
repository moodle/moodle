<?php // $Id$
/**
 * A template to test Moodle's XML-RPC feature
 *
 * This script 'remotely' executes the mnet_concatenate_strings function in
 * mnet/testlib.php
 * It steps through each stage of the process, printing some data as it goes
 * along. It should help you to get your remote method working.
 *
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once $CFG->dirroot.'/mnet/xmlrpc/client.php';

error_reporting(E_ALL);

if (isset($_GET['func']) && is_numeric($_GET['func'])) {
    $func = $_GET['func'];


// Some HTML sugar
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head><title>Moodle MNET Test Client</title></head><body>
<?php

// For the demo, our 'remote' host is actually our local host.
$wwwroot = $CFG->wwwroot;

// Enter the complete path to the file that contains the function you want to 
// call on the remote server. In our example the function is in 
// mnet/testlib/
// The function itself is added to that path to complete the $path_to_function 
// variable 
$path_to_function[0] = 'mnet/rpclib/mnet_concatenate_strings';
$path_to_function[1] = 'mod/scorm/rpclib/scorm_add_floats';
$path_to_function[2] = 'system/listMethods';
$path_to_function[3] = 'system/methodSignature';
$path_to_function[4] = 'system/methodHelp';
$path_to_function[5] = 'system/listServices';
$path_to_function[6] = 'system/listMethods';
$path_to_function[7] = 'system/listMethods';

$paramArray[0] = array(array('some string, ', 'string'),
array('some other string, ', 'string'),
array('and a final string', 'string'));

$paramArray[1] = array(array(5.3, 'string'),
array(7.1, 'string'),
array(8.25323, 'string'));

$paramArray[2] = array();

$paramArray[3] = array(array('auth/mnet/auth/user_authorise', 'string'));

$paramArray[4] = array(array('auth/mnet/auth/user_authorise', 'string'));

$paramArray[5] = array();

$paramArray[6] = array(array('sso', 'string'));

$paramArray[7] = array(array('concatenate', 'string'));

echo 'Your local wwwroot appears to be <strong>'. $wwwroot ."</strong>.<br />\n";
echo "We will use this as the local <em>and</em> remote hosts.<br /><br />\n";
flush();

// mnet_peer pulls information about a remote host from the database.
$mnet_peer = new mnet_peer();
$mnet_peer->set_wwwroot($wwwroot);

echo "Your \$mnet_peer from the database looks like:<br />\n<pre>";
$h2 = get_object_vars($mnet_peer);
while(list($key, $val) = each($h2)) {
    if (!is_numeric($key)) echo '<strong>'.$key.':</strong> '. $val."\n";
}
echo "</pre><br/>It's ok if that info is not complete - the required field is:<br />\nwwwroot: <b>{$mnet_peer->wwwroot}</b>.<br /><br/>\n";
flush();

// The transport id is one of:
// RPC_HTTPS_VERIFIED 1
// RPC_HTTPS_SELF_SIGNED 2
// RPC_HTTP_VERIFIED 3
// RPC_HTTP_SELF_SIGNED 4

if (!$mnet_peer->transport) exit('No transport method is approved for this host in your DB table. Please enable a transport method and try again.');
$t[1]  = 'http2 (port 443 encrypted) with a verified certificate.';
$t[2]  = 'https (port 443 encrypted) with a self-signed certificate.';
$t[4]  = 'http (port 80 unencrypted) with a verified certificate.';
$t[8]  = 'http (port 80 unencrypted) with a self-signed certificate.';
$t[16] = 'http (port 80 unencrypted) unencrypted with no certificate.';

echo 'Your transportid is  <strong>'.$mnet_peer->transport.'</strong> which represents <em>'.$t[$mnet_peer->transport]."</em><br /><br />\n";
flush();

// Create a new request object
$mnet_request = new mnet_xmlrpc_client();

// Tell it the path to the method that we want to execute
$mnet_request->set_method($path_to_function[$func]);
// Add parameters for your function. The mnet_concatenate_strings takes three
// parameters, like mnet_concatenate_strings($string1, $string2, $string3)
// PHP is weakly typed, so you can get away with calling most things strings, 
// unless it's non-scalar (i.e. an array or object or something).
foreach($paramArray[$func] as $param) {
    $mnet_request->add_param($param[0], $param[1]);
}

if (count($mnet_request->params)) {
    echo 'Your parameters are:<br />';
    while(list($key, $val) = each($mnet_request->params)) {
        echo '&nbsp;&nbsp; <strong>'.$key.':</strong> '. $val."<br/>\n";
    }
}
flush();

// We send the request:
$mnet_request->send($mnet_peer);

?>

A var_dump of the decoded response:  <strong><pre><?php var_dump($mnet_request->response); ?></pre></strong><br />

<?php
    if (count($mnet_request->params)) {
?>
    A var_dump of the parameters you sent:  <strong><pre><?php var_dump($mnet_request->params); ?></pre></strong><br />
<?php
    }
}
    ?>
    <p>
    Choose a function to call:<br />
    <a href="testclient.php?func=2">system/listMethods</a><br />
    <a href="testclient.php?func=3">system/methodSignature</a><br />
    <a href="testclient.php?func=4">system/methodHelp</a><br />
    <a href="testclient.php?func=5">listServices</a><br />
    <a href="testclient.php?func=6">system/listMethods(SSO)</a><br />
    <a href="testclient.php?func=7">system/listMethods(concatenate)</a><br />

</body></html>
