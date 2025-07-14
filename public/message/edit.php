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
 * Edit user message preferences
 *
 * @package    core_message
 * @copyright  2008 Luis Rodrigues and Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/message/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

$userid = optional_param('id', 0, PARAM_INT);    // User id.
$currentuser = true;

if (!$userid) {
    $userid = $USER->id;
}

$url = new moodle_url('/message/edit.php');
$url->param('id', $userid);

$PAGE->set_url($url);

require_login();

if (isguestuser()) {
    throw new \moodle_exception('guestnoeditmessage', 'message');
}

if (!$user = $DB->get_record('user', array('id' => $userid))) {
    throw new \moodle_exception('invaliduserid');
}

$systemcontext   = context_system::instance();
$personalcontext = context_user::instance($user->id);

$PAGE->set_context($personalcontext);
$PAGE->set_pagelayout('admin');

// check access control
if ($user->id == $USER->id) {
    //editing own message profile
    require_capability('moodle/user:editownmessageprofile', $systemcontext);
} else {
    $currentuser = false;
    // teachers, parents, etc.
    require_capability('moodle/user:editmessageprofile', $personalcontext);
    // no editing of guest user account
    if (isguestuser($user->id)) {
        throw new \moodle_exception('guestnoeditmessageother', 'message');
    }
    // no editing of admins by non admins!
    if (is_siteadmin($user) and !is_siteadmin($USER)) {
        throw new \moodle_exception('useradmineditadmin');
    }
    $PAGE->navbar->includesettingsbase = true;
    $PAGE->navigation->extend_for_user($user);
}

/// Display page header
$strmessaging = get_string('messagepreferences', 'message');
$PAGE->set_title($strmessaging);
$PAGE->set_heading(fullname($user));

echo $OUTPUT->header();
if ($currentuser) {
    // Open the message drawer to show the settings.
    echo $OUTPUT->heading(get_string('messagepreferences', 'core_message'));
    $PAGE->requires->js_call_amd('core_message/message_drawer_helper', 'showSettings');
} else {
    // Viewing another user's preferences so render the old page.
    $renderer = $PAGE->get_renderer('core', 'message');
    echo $renderer->render_user_message_preferences($user);
}

echo $OUTPUT->footer();

