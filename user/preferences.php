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
 * My preferences.
 *
 * @package    core_user
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/navigationlib.php');

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception();
}

$userid = optional_param('userid', $USER->id, PARAM_INT);
$currentuser = $userid == $USER->id;

// Only administrators can access another user's preferences.
if (!$currentuser && !is_siteadmin($USER)) {
    throw new moodle_exception('cannotedituserpreferences', 'error');
}

// Check that the user is a valid user.
$user = core_user::get_user($userid);
if (!$user || !core_user::is_real_user($userid)) {
    throw new moodle_exception('invaliduser', 'error');
}

$PAGE->set_context(context_user::instance($userid));
$PAGE->set_url('/user/preferences.php', array('userid' => $userid));
$PAGE->set_pagelayout('admin');
$PAGE->set_pagetype('user-preferences');
$PAGE->set_title(fullname($user));
$PAGE->set_heading(fullname($user));

if (!$currentuser) {
    $PAGE->navigation->extend_for_user($user);
    $settings = $PAGE->settingsnav->find('userviewingsettings' . $user->id, null);
    $settings->make_active();
    $url = new moodle_url('/user/preferences.php', array('userid' => $userid));
    $navbar = $PAGE->navbar->add(get_string('preferences', 'moodle'), $url);
} else {
    // Shutdown the users node in the navigation menu.
    $usernode = $PAGE->navigation->find('users', null);
    $usernode->make_inactive();

    $settings = $PAGE->settingsnav->find('usercurrentsettings', null);
    $settings->make_active();
}

// Identifying the nodes.
$groups = array();
$orphans = array();
foreach ($settings->children as $setting) {
    if ($setting->has_children()) {
        $groups[] = new preferences_group($setting->get_content(), $setting->children);
    } else {
        $orphans[] = $setting;
    }
}
if (!empty($orphans)) {
    $groups[] = new preferences_group(get_string('miscellaneous'), $orphans);
}
$preferences = new preferences_groups($groups);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('preferences'));
echo $OUTPUT->render($preferences);
echo $OUTPUT->footer();
