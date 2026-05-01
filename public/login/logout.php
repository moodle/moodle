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
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

$PAGE->set_url('/login/logout.php');
$PAGE->set_context(context_system::instance());

// We want not null default to prevent required sesskey warning.
$sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW);
$login   = optional_param('loginpage', 0, PARAM_BOOL);

// Can be overridden by auth plugins.
if ($login) {
    $redirect = get_login_url();
} else {
    $redirect = $CFG->wwwroot . '/';
}

if (!isloggedin()) {
    // No confirmation, user has already logged out.
    require_logout();
    redirect($redirect);

} else if (!confirm_sesskey($sesskey)) {
    $PAGE->set_title(get_string('logout'));
    $PAGE->set_heading($SITE->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('logoutconfirm'), new moodle_url($PAGE->url, array('sesskey'=>sesskey())), $CFG->wwwroot.'/');
    echo $OUTPUT->footer();
    die;
}

$authentication = \core\di::get(\core\authentication::class);
$authsequence = $authentication->get_enabled_plugins(); // Authentication plugins, in sequence.
foreach ($authsequence as $authname) {
    $authplugin = $authentication->get_plugin($authname);
    $authplugin->logoutpage_hook();
}

require_logout();

redirect($redirect);
