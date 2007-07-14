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

if (!is_enabled_auth('mnet')) {
    error('mnet is disabled');
}

// grab the GET params - wantsurl could be anything - take it
// with PARAM_RAW
$hostid   = required_param('hostid',        PARAM_INT);
$wantsurl = optional_param('wantsurl', '', PARAM_RAW);

// start the mnet session and redirect browser to remote URL
$mnetauth = get_auth_plugin('mnet');
$url      = $mnetauth->start_jump_session($hostid, $wantsurl);

if (empty($url)) {
    error('DEBUG: Jump session was not started correctly or blank URL returned.'); // TODO: errors
}
redirect($url);

?>
