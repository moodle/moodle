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
 * This file contains the polyfil to allow a plugin to operate with Moodle 3.3 up.
 *
 * @package    mod_quiz
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_quiz\privacy;

use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * The trait used to provide a backwards compatibility for third-party plugins.
 *
 * @package    mod_quiz
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait legacy_quizaccess_polyfill {

    /**
     * Export all user data for the specified user, for the specified quiz.
     *
     * @param   \mod_quiz\quiz_settings           $quiz The quiz being exported
     * @param   \stdClass       $user The user to export data for
     * @return  \stdClass       The data to be exported for this access rule.
     */
    public static function export_quizaccess_user_data(\mod_quiz\quiz_settings $quiz, \stdClass $user) : \stdClass {
        return static::_export_quizaccess_user_data($quiz, $user);
    }

    /**
     * Delete all data for all users in the specified quiz.
     *
     * @param   \mod_quiz\quiz_settings           $quiz The quiz being deleted
     */
    public static function delete_quizaccess_data_for_all_users_in_context(\mod_quiz\quiz_settings $quiz) {
        static::_delete_quizaccess_data_for_all_users_in_context($quiz);
    }

    /**
     * Delete all user data for the specified user, in the specified quiz.
     *
     * @param   \mod_quiz\quiz_settings           $quiz The quiz being deleted
     * @param   \stdClass       $user The user to export data for
     */
    public static function delete_quizaccess_data_for_user(\mod_quiz\quiz_settings $quiz, \stdClass $user) {
        static::_delete_quizaccess_data_for_user($quiz, $user);
    }

    /**
     * Delete all user data for the specified users, in the specified context.
     *
     * @param   approved_userlist $userlist   The approved context and user information to delete information for.
     */
    public static function delete_quizaccess_data_for_users(approved_userlist $userlist) {
        static::_delete_quizaccess_data_for_users($userlist);
    }
}
