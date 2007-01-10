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
require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

// grab the GET params
$token         = required_param('token',    PARAM_BASE64);
$remotewwwroot = required_param('idp',      PARAM_URL);
$wantsurl      = required_param('wantsurl', PARAM_LOCALURL);

// confirm the MNET session
$mnetauth = get_auth_plugin('mnet');
$localuser = $mnetauth->confirm_mnet_session($token, $remotewwwroot);

// log in
$CFG->auth = 'mnet';
$USER = get_complete_user_data('id', $localuser->id, $localuser->mnethostid);
load_all_capabilities();

// redirect
redirect($CFG->wwwroot . $wantsurl);

?>
