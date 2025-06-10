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
 * Class lateststudents implements bot intent interface for teacher-latest-students intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Class lateststudents implements bot intent interface for teacher-latest-students intent.
 */
class lateststudents implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message for teachers with the list of students who logged in longest time ago.
     *
     * @param string $language - Message language
     * @param mixed $entities - Intent entities. Gives student name.
     * @return array|string - Bot message structure with data
     */
    public static function get_message($language, $entities = null) {
        global $USER, $DB, $PAGE;
        $listitems = [];
        $warnings = [];
        $listtitle = '';
        $message = '';

        $lastloggedsql = "SELECT u.id, u.username, CONCAT(u.firstname, ' ', u.lastname) as fullname, u.lastaccess ".
                           "FROM {user} u ".
                          "WHERE u.suspended = 0 AND u.deleted = 0 AND u.lastaccess > 0 ";
        $lastloggedparams = [];
        if (!is_siteadmin()) {
            $courses = \local_o365\bot\intents\intentshelper::getteachercourses($USER->id);
            if (!empty($courses)) {
                list($userssql, $userssqlparams) = \local_o365\bot\intents\intentshelper::getcoursesstudentslistsql($courses,
                    'u.id');
                $userslist = $DB->get_fieldset_sql($userssql, $userssqlparams);
                list($userslastlogedsql, $userslastloggedparams) = $DB->get_in_or_equal($userslist, SQL_PARAMS_NAMED);
                $lastloggedsql .= " AND u.id $userslastlogedsql";
                $lastloggedparams = array_merge($lastloggedparams, $userslastloggedparams);
            } else {
                $lastloggedsql .= ' AND u.id = :userid ';
                $lastloggedparams['userid'] = $USER->id;
            }
        }
        $lastloggedsql .= ' ORDER BY u.lastaccess ASC';
        $users = $DB->get_records_sql($lastloggedsql, $lastloggedparams, 0, self::DEFAULT_LIMIT_NUMBER);

        if (empty($users)) {
            $message = get_string_manager()->get_string('no_users_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'users',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No  users found'
            );
        } else {
            $message = get_string_manager()->get_string('list_of_latest_logged_students', 'local_o365', null, $language);
            foreach ($users as $user) {
                $userpicture = new \user_picture($user);
                $userpicture->size = 1;
                $pictureurl = $userpicture->get_url($PAGE)->out(false);
                $subtitledata = \local_o365\bot\intents\intentshelper::formatdate($user->lastaccess, true);
                $listitems[] = [
                        'title' => $user->fullname,
                        'subtitle' => get_string_manager()->get_string('last_login_date', 'local_o365', $subtitledata, $language),
                        'icon' => $pictureurl,
                        'action' => null,
                        'actionType' => null
                ];
            }
        }

        return array(
                'message' => $message,
                'listTitle' => $listtitle,
                'listItems' => $listitems,
                'warnings' => $warnings
        );
    }
}
