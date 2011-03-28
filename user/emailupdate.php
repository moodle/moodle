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
 * Change a users email address
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editlib.php');

$key = required_param('key', PARAM_ALPHANUM);
$id  = required_param('id', PARAM_INT);

$PAGE->set_url('/user/emailupdate.php', array('id'=>$id, 'key'=>$key));
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

if (!$user = $DB->get_record('user', array('id' => $id))) {
    print_error('invaliduserid');
}

$preferences = get_user_preferences(null, null, $user->id);
$a = new stdClass();
$a->fullname = fullname($user, true);
$stremailupdate = get_string('emailupdate', 'auth', $a);

$PAGE->set_title(format_string($SITE->fullname) . ": $stremailupdate");
$PAGE->set_heading(format_string($SITE->fullname) . ": $stremailupdate");

echo $OUTPUT->header();

if (empty($preferences['newemailattemptsleft'])) {
    redirect("$CFG->wwwroot/user/view.php?id=$user->id");

} elseif ($preferences['newemailattemptsleft'] < 1) {
    cancel_email_update($user->id);
    $stroutofattempts = get_string('auth_outofnewemailupdateattempts', 'auth');
    echo $OUTPUT->box($stroutofattempts, 'center');

} elseif ($key == $preferences['newemailkey']) {
    $olduser = clone($user);
    cancel_email_update($user->id);
    $user->email = $preferences['newemail'];

    // Detect duplicate before saving
    if ($DB->get_record('user', array('email' => $user->email))) {
        $stremailnowexists = get_string('emailnowexists', 'auth');
        echo $OUTPUT->box($stremailnowexists, 'center');
        echo $OUTPUT->continue_button("$CFG->wwwroot/user/view.php?id=$user->id");
    } else {
        // update user email
        $DB->set_field('user', 'email', $user->email, array('id' => $user->id));
        $authplugin = get_auth_plugin($user->auth);
        $authplugin->user_update($olduser, $user);
        events_trigger('user_updated', $user);
        $a->email = $user->email;
        $stremailupdatesuccess = get_string('emailupdatesuccess', 'auth', $a);
        echo $OUTPUT->box($stremailupdatesuccess, 'center');
        echo $OUTPUT->continue_button("$CFG->wwwroot/user/view.php?id=$user->id");
    }

} else {
    $preferences['newemailattemptsleft']--;
    set_user_preference('newemailattemptsleft', $preferences['newemailattemptsleft'], $user->id);
    $strinvalidkey = get_string('auth_invalidnewemailkey', 'auth');
    echo $OUTPUT->box($strinvalidkey, 'center');
}

echo $OUTPUT->footer();
