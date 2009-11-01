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
$wantsremoteurl = optional_param('remoteurl', false, PARAM_BOOL);

$url = new moodle_url($CFG->wwwroot.'/auth/mnet/jump.php', array('token'=>$token, 'idp'=>$remotewwwroot, 'wantsurl'=>$wantsurl));
if ($wantsremoteurl !== false) $url->param('remoteurl', $wantsremoteurl);
$PAGE->set_url($url);

$site = get_site();

if (!is_enabled_auth('mnet')) {
    print_error('mnetdisable');
}

// confirm the MNET session
$mnetauth = get_auth_plugin('mnet');
$localuser = $mnetauth->confirm_mnet_session($token, $remotewwwroot);

// log in
$user = get_complete_user_data('id', $localuser->id, $localuser->mnethostid);
complete_user_login($user);

if (!empty($localuser->mnet_foreign_host_array)) {
    $USER->mnet_foreign_host_array = $localuser->mnet_foreign_host_array;
}

// redirect
if ($wantsremoteurl) {
    redirect($remotewwwroot . $wantsurl);
}
redirect($CFG->wwwroot . $wantsurl);


