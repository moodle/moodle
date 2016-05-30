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
 * Lib API functions.
 *
 * @package   report_usersessions
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the course navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $user
 * @param stdClass $course The course to object for the report
 */
function report_usersessions_extend_navigation_user($navigation, $user, $course) {
    global $USER;

    if (isguestuser() or !isloggedin()) {
        return;
    }

    if (\core\session\manager::is_loggedinas() or $USER->id != $user->id) {
        // No peeking at somebody else's sessions!
        return;
    }

    $context = context_user::instance($USER->id);
    if (has_capability('report/usersessions:manageownsessions', $context)) {
        $navigation->add(get_string('navigationlink', 'report_usersessions'),
            new moodle_url('/report/usersessions/user.php'), $navigation::TYPE_SETTING);
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function report_usersessions_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;

    if (isguestuser() or !isloggedin()) {
        return;
    }

    if (\core\session\manager::is_loggedinas() or $USER->id != $user->id) {
        // No peeking at somebody else's sessions!
        return;
    }

    $context = context_user::instance($USER->id);
    if (has_capability('report/usersessions:manageownsessions', $context)) {
        $node = new core_user\output\myprofile\node('reports', 'usersessions',
                get_string('navigationlink', 'report_usersessions'), null, new moodle_url('/report/usersessions/user.php'));
        $tree->add_node($node);
    }
    return true;
}
