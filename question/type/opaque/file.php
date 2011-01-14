<?php
/**
 * Serves files from the Opaque resource cache.
 *
 * @copyright &copy; 2007 The Open University
 * @author T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package package_name
 *//** */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');

$engineid = required_param('engineid', PARAM_INT);
$remoteid = required_param('remoteid', PARAM_PATH);
$remoteversion = required_param('remoteversion', PARAM_PATH);
$filename = required_param('filename', PARAM_FILE);

// The Open University found it necessary to comment out the whole of the following if statement
// to make things work reliably. However, I think that was only problems with synchronising
// the session between our load-balanced servers, and I think it is better to leave
// this code in. (OU bug 7991.)
global $SESSION;
if ($SESSION->cached_opaque_state->engineid != $engineid ||
        $SESSION->cached_opaque_state->remoteid != $remoteid ||
        $SESSION->cached_opaque_state->remoteversion != $remoteversion) {
            print_error('cannotaccessfile');
}

$resourcecache = new opaque_resource_cache($engineid, $remoteid, $remoteversion);
$resourcecache->serve_file($filename);
?>
