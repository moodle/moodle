<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication Plugin: Moodle Network Authentication
 * Multiple host authentication support for Moodle Network.
 *
 * @package auth_mnet
 * @author Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';

// grab the GET params
$token         = required_param('token',    PARAM_BASE64);
$remotewwwroot = required_param('idp',      PARAM_URL);
$wantsurl      = required_param('wantsurl', PARAM_LOCALURL);
$wantsremoteurl = optional_param('remoteurl', false, PARAM_BOOL);

$url = new moodle_url('/auth/mnet/jump.php', array('token'=>$token, 'idp'=>$remotewwwroot, 'wantsurl'=>$wantsurl));
if ($wantsremoteurl !== false) $url->param('remoteurl', $wantsremoteurl);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$site = get_site();

if (!is_enabled_auth('mnet')) {
    print_error('mnetdisable');
}

// confirm the MNET session
$mnetauth = get_auth_plugin('mnet');
$remotepeer = new mnet_peer();
$remotepeer->set_wwwroot($remotewwwroot);
// this creates the local user account if necessary, or updates it if it already exists
$localuser = $mnetauth->confirm_mnet_session($token, $remotepeer);

// log in
$user = get_complete_user_data('id', $localuser->id, $localuser->mnethostid);
complete_user_login($user);
// now that we've logged in, set up the mnet session properly
$mnetauth->update_mnet_session($user, $token, $remotepeer);

if (!empty($localuser->mnet_foreign_host_array)) {
    $USER->mnet_foreign_host_array = $localuser->mnet_foreign_host_array;
}

// redirect
if ($wantsremoteurl) {
    redirect($remotewwwroot . $wantsurl);
}
redirect($CFG->wwwroot . $wantsurl);


