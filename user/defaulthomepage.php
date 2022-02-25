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
 * Allow user to set their default home page
 *
 * @package     core_user
 * @copyright   2019 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/user/lib.php');

$userid = optional_param('id', $USER->id, PARAM_INT);

$PAGE->set_url('/user/defaulthomepage.php', ['id' => $userid]);

list($user, $course) = useredit_setup_preference_page($userid, SITEID);

$form = new core_user\form\defaulthomepage_form();

$defaulthomepage = get_default_home_page();
$user->defaulthomepage = get_user_preferences('user_home_page_preference', $defaulthomepage, $user);
if (empty($CFG->enabledashboard) && $user->defaulthomepage == HOMEPAGE_MY) {
    // If the user was using the dashboard but it's disabled, return the default home page.
    $user->defaulthomepage = $defaulthomepage;
}
$form->set_data($user);

$redirect = new moodle_url('/user/preferences.php', ['userid' => $user->id]);
if ($form->is_cancelled()) {
    redirect($redirect);
} else if ($data = $form->get_data()) {
    $userupdate = [
        'id' => $user->id,
        'preference_user_home_page_preference' => $data->defaulthomepage,
    ];

    useredit_update_user_preference($userupdate);

    \core\event\user_updated::create_from_userid($userupdate['id'])->trigger();

    redirect($redirect);
}

$PAGE->navbar->includesettingsbase = true;

$strdefaulthomepageuser = get_string('defaulthomepageuser');

$PAGE->set_title("$course->shortname: $strdefaulthomepageuser");
$PAGE->set_heading(fullname($user, true));

echo $OUTPUT->header();
echo $OUTPUT->heading($strdefaulthomepageuser);

$form->display();

echo $OUTPUT->footer();
