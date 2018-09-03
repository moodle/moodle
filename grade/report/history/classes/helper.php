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
 * Helper class for gradehistory report.
 *
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_history;

defined('MOODLE_INTERNAL') || die;

/**
 * Helper class for gradehistory report.
 *
 * @since      Moodle 2.8
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Initialise the js to handle the user selection {@link gradereport_history_user_button}.
     *
     * @param int $courseid       course id.
     * @param array $currentusers List of currently selected users.
     *
     * @return output\user_button the user select button.
     */
    public static function init_js($courseid, array $currentusers = null) {
        global $PAGE;

        // Load the strings for js.
        $PAGE->requires->strings_for_js(array(
            'errajaxsearch',
            'finishselectingusers',
            'foundoneuser',
            'foundnusers',
            'loadmoreusers',
            'selectusers',
        ), 'gradereport_history');
        $PAGE->requires->strings_for_js(array(
            'loading'
        ), 'admin');
        $PAGE->requires->strings_for_js(array(
            'noresults',
            'search'
        ), 'moodle');

        $arguments = array(
            'courseid'            => $courseid,
            'ajaxurl'             => '/grade/report/history/users_ajax.php',
            'url'                 => $PAGE->url->out(false),
            'selectedUsers'       => $currentusers,
        );

        // Load the yui module.
        $PAGE->requires->yui_module(
            'moodle-gradereport_history-userselector',
            'Y.M.gradereport_history.UserSelector.init',
            array($arguments)
        );
    }

    /**
     * Retrieve a list of users.
     *
     * We're interested in anyone that had a grade history in this course. This api returns a list of such users based on various
     * criteria passed.
     *
     * @param \context $context Context of the page where the results would be shown.
     * @param string $search the text to search for (empty string = find all).
     * @param int $page page number, defaults to 0.
     * @param int $perpage Number of entries to display per page, defaults to 0.
     *
     * @return array list of users.
     */
    public static function get_users($context, $search = '', $page = 0, $perpage = 25) {
        global $DB;

        list($sql, $params) = self::get_users_sql_and_params($context, $search);
        $limitfrom = $page * $perpage;
        $limitto = $limitfrom + $perpage;
        $users = $DB->get_records_sql($sql, $params, $limitfrom, $limitto);
        return $users;
    }

    /**
     * Get total number of users present for the given search criteria.
     *
     * @param \context $context Context of the page where the results would be shown.
     * @param string $search the text to search for (empty string = find all).
     *
     * @return int number of users found.
     */
    public static function get_users_count($context, $search = '') {
        global $DB;

        list($sql, $params) = self::get_users_sql_and_params($context, $search, true);
        return $DB->count_records_sql($sql, $params);

    }

    /**
     * Get sql and params to use to get list of users.
     *
     * @param \context $context Context of the page where the results would be shown.
     * @param string $search the text to search for (empty string = find all).
     * @param bool $count setting this to true, returns an sql to get count only instead of the complete data records.
     *
     * @return array sql and params list
     */
    protected static function get_users_sql_and_params($context, $search = '', $count = false) {

        // Fields we need from the user table.
        $extrafields = get_extra_user_fields($context);
        $params = array();
        if (!empty($search)) {
            list($filtersql, $params) = users_search_sql($search, 'u', true, $extrafields);
            $filtersql .= ' AND ';
        } else {
            $filtersql = '';
        }

        $ufields = \user_picture::fields('u', $extrafields).',u.username';
        if ($count) {
            $select = "SELECT COUNT(DISTINCT u.id) ";
            $orderby = "";
        } else {
            $select = "SELECT DISTINCT $ufields ";
            $orderby = " ORDER BY u.lastname ASC, u.firstname ASC";
        }
        $sql = "$select
                 FROM {user} u
                 JOIN {grade_grades_history} ggh ON u.id = ggh.userid
                 JOIN {grade_items} gi ON gi.id = ggh.itemid
                WHERE $filtersql gi.courseid = :courseid";
        $sql .= $orderby;
        $params['courseid'] = $context->instanceid;

        return array($sql, $params);
    }

    /**
     * Get a list of graders.
     *
     * @param int $courseid Id of course for which we need to fetch graders.
     *
     * @return array list of graders.
     */
    public static function get_graders($courseid) {
        global $DB;

        $ufields = get_all_user_name_fields(true, 'u');
        $sql = "SELECT u.id, $ufields
                  FROM {user} u
                  JOIN {grade_grades_history} ggh ON ggh.usermodified = u.id
                  JOIN {grade_items} gi ON gi.id = ggh.itemid
                 WHERE gi.courseid = :courseid
              GROUP BY u.id, $ufields
              ORDER BY u.lastname ASC, u.firstname ASC";

        $graders = $DB->get_records_sql($sql, array('courseid' => $courseid));
        $return = array(0 => get_string('allgraders', 'gradereport_history'));
        foreach ($graders as $grader) {
            $return[$grader->id] = fullname($grader);
        }
        return $return;
    }
}
