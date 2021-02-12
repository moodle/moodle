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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');


$nuggetid = required_param('nuggetid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$accesskey = required_param('accesskey', PARAM_CLEAN);

// Check the user id still valid.
if (!$user = $DB->get_record('user', array('id' => $userid, 'deleted' => 0, 'suspended' => 0))) {
    print_error('invaliduser', 'block_iomad_microlearning');
}

// Check the nugget id still valid.
if (!$nugget = $DB->get_record('microlearning_nugget', array('id' => $nuggetid))) {
    print_error('invalidnugget', 'block_iomad_microlearning');
}

// Are we already logged in?
$allowcontinue = false;
if (isloggedin() and !isguestuser()) {
    $allowcontinue = true;
} else if ($DB->get_record_sql("SELECT id FROM {microlearning_thread_user}
                         WHERE userid = :userid
                         AND nuggetid = :nuggetid
                         AND accesskey = :accesskey
                         AND schedule_date > :expirytime
                         AND schedule_date < :time",
                         array('userid' => $userid,
                               'nuggetid' => $nuggetid,
                               'accesskey' => $accesskey,
                               'time' => time(),
                               'expirytime' => time() - $CFG->microlearninglinkexpires * 24 * 60 * 60))) {

    // Valid access token.  Log in the user.
    $allowcontinue = true;
    complete_user_login($user);

    \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

    // sets the username cookie
    if (!empty($CFG->nolastloggedin)) {
        // do not store last logged in user in cookie
        // auth plugins can temporarily override this from loginpage_hook()
        // do not save $CFG->nolastloggedin in database!

    } else if (empty($CFG->rememberusername) or ($CFG->rememberusername == 2 and empty($frm->rememberusername))) {
        // no permanent cookies, delete old one if exists
        set_moodle_cookie('');

    } else {
        set_moodle_cookie($USER->username);
    }
    // Add something to the SESSION so we can trap where they came from.
    $SESSION->came_via_microlearning = true;
}

// Get the nugget url.
$linkurl = microlearning::get_nugget_url($nugget);

// Are we going straight there?
if ($allowcontinue) {
    redirect($linkurl);
} else {
    // Got to log in first.
    $SESSION->wantsurl = $linkurl;
    redirect(new moodle_url('/login/index.php'));
}
