<?php

/**
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: Moodle Network Authentication
 *
 * Multiple host authentication support for Moodle Network.
 *
 * 2006-11-01  File created.
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';

require_login(SITEID,false);

if (!is_enabled_auth('mnet')) {
    error('mnet is disabled');
}

// grab the GET params - wantsurl could be anything - take it
// with PARAM_RAW
$hostid = optional_param('hostid', '0', PARAM_INT);
$hostwwwroot = optional_param('hostwwwroot', '', PARAM_URL);
$wantsurl = optional_param('wantsurl', '', PARAM_RAW);

// If hostid hasn't been specified, try getting it using wwwroot
if (!$hostid) {
    $hostid = get_field('mnet_host', 'id', 'wwwroot', $hostwwwroot);
}

// start the mnet session and redirect browser to remote URL
$mnetauth = get_auth_plugin('mnet');
$url      = $mnetauth->start_jump_session($hostid, $wantsurl);

if (empty($url)) {
    error('DEBUG: Jump session was not started correctly or blank URL returned.'); // TODO: errors
}
redirect($url);

?>
