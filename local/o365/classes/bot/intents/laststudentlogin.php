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
 * Class laststudentlogin implements bot intent interface for teacher-last-student-login intent.
 *
 * @package local_o365
 * @author  Enovation Solutions
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_o365\bot\intents;

defined('MOODLE_INTERNAL') || die();

/**
 * Class laststudentlogin implements bot intent interface for teacher-last-student-login intent.
 */
class laststudentlogin implements \local_o365\bot\intents\intentinterface {

    /**
     * Gets a message for teacher with details about student last login.
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
        $users = [];

        $name = (empty($entities->personName) ? false : $entities->personName[0]);

        if ($name) {
            $concat = $DB->sql_concat("u.firstname", "' '", "u.lastname");
            $where = $DB->sql_like($concat, ":name", false);
            $lastloginsql = "SELECT u.id, u.username, CONCAT(u.firstname, ' ', u.lastname) as fullname, u.lastaccess
                               FROM {user} u
                              WHERE $where AND u.suspended = 0 AND u.deleted = 0";
            $lastloginparams = ['name' => '%'.$DB->sql_like_escape($name).'%'];
            if (!is_siteadmin()) {
                $courses = \local_o365\bot\intents\intentshelper::getteachercourses($USER->id);
                if (!empty($courses)) {
                    list($userssql, $userssqlparams) = \local_o365\bot\intents\intentshelper::getcoursesstudentslistsql($courses,
                        'u.id');
                    $userslist = $DB->get_fieldset_sql($userssql, $userssqlparams);
                    list($usersloginsql, $usersloginparams) = $DB->get_in_or_equal($userslist, SQL_PARAMS_NAMED);
                    $lastloginsql .= " AND u.id $usersloginsql";
                    $lastloginparams = array_merge($lastloginparams, $usersloginparams);
                } else {
                    $lastloginsql .= ' AND u.id = :userid ';
                    $lastloginparams['userid'] = $USER->id;
                }
            }
            $lastloginsql .= ' ORDER BY u.lastaccess DESC';
            $users = $DB->get_records_sql($lastloginsql, $lastloginparams);
        }

        if (empty($users)) {
            $message = get_string_manager()->get_string('no_user_with_name_found', 'local_o365', null, $language);
            $warnings[] = array(
                    'item' => 'users',
                    'itemid' => 0,
                    'warningcode' => '1',
                    'message' => 'No  user with such name found'
            );
        } else {
            $message = get_string_manager()->get_string('list_of_students_with_name', 'local_o365', $name, $language);
            foreach ($users as $user) {
                $userpicture = new \user_picture($user);
                $userpicture->size = 1;
                $pictureurl = $userpicture->get_url($PAGE)->out(false);
                if (empty($user->lastaccess)) {
                    $subtitledata = get_string('never', 'local_o365');
                } else {
                    $subtitledata = \local_o365\bot\intents\intentshelper::formatdate($user->lastaccess, true);
                }
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
