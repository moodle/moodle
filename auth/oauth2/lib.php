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
 * Callbacks for auth_oauth2
 *
 * @package   auth_oauth2
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Navigation hook to add to preferences page.
 *
 * @param navigation_node $useraccount
 * @param stdClass $user
 * @param context_user $context
 * @param stdClass $course
 * @param context_course $coursecontext
 */
function auth_oauth2_extend_navigation_user_settings(navigation_node $useraccount,
                                                     stdClass $user,
                                                     context_user $context,
                                                     stdClass $course,
                                                     context_course $coursecontext) {
    global $USER;

    if (\auth_oauth2\api::is_enabled() && !\core\session\manager::is_loggedinas()) {
        if (has_capability('auth/oauth2:managelinkedlogins', $context) && $user->id == $USER->id) {

            $parent = $useraccount->parent->find('useraccount', navigation_node::TYPE_CONTAINER);
            $parent->add(get_string('linkedlogins', 'auth_oauth2'), new moodle_url('/auth/oauth2/linkedlogins.php'));
        }
    }
}

/**
 * Callback to remove linked logins for deleted users.
 *
 * @param stdClass $user
 */
function auth_oauth2_pre_user_delete($user) {
    global $DB;
    $DB->delete_records(auth_oauth2\linked_login::TABLE, ['userid' => $user->id]);
}
