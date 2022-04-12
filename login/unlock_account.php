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
 * Reset locked-out accounts.
 *
 * @package    core_auth
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir.'/authlib.php');

$userid = optional_param('u', 0, PARAM_INT);
$secret = optional_param('s', '', PARAM_RAW);

$PAGE->set_url('/login/unlock_account.php');
$PAGE->set_context(context_system::instance());

// Override wanted URL, we do not want to end up here again after login!
$SESSION->wantsurl = "$CFG->wwwroot/";

// Do not disclose details about existence or status of user accounts here.

if (!$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0, 'suspended'=>0))) {
    throw new \moodle_exception('lockouterrorunlock', 'admin', get_login_url());
}

$usersecret = get_user_preferences('login_lockout_secret', false, $user);

if ($secret === $usersecret) {
    login_unlock_account($user);
    if ($USER->id == $user->id) {
        redirect("$CFG->wwwroot/");
    } else {
        redirect(get_login_url());
    }
}

throw new \moodle_exception('lockouterrorunlock', 'admin', get_login_url());
