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
 * Logs the user out and sends them to the home page
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package login
 */

require_once("../config.php");

// can be overriden by auth plugins
$redirect = $CFG->wwwroot.'/';

$sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

if (!isloggedin()) {
    // no confirmation, user has already logged out
    require_logout();
    redirect($redirect);

} else if (!confirm_sesskey($sesskey)) {
    $url = new moodle_url('/login/logout.php');
    if ($sesskey !== '__notpresent__') $url->param('sesskey', $sesskey);
    $PAGE->set_url($url);
    $PAGE->set_title($SITE->fullname);
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('logoutconfirm'), 'logout.php', $CFG->wwwroot.'/');
    echo $OUTPUT->footer();
    die;
}

$authsequence = get_enabled_auth_plugins(); // auths, in sequence
foreach($authsequence as $authname) {
    $authplugin = get_auth_plugin($authname);
    $authplugin->logoutpage_hook();
}

require_logout();

redirect($redirect);