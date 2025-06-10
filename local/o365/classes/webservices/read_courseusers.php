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
 * Get a list of students in a course by course id.
 *
 * @package local_o365
 * @author 2011 Jerome Mouneyrac, modified 2016 James McQuillan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2011 Jerome Mouneyrac
 */

namespace local_o365\webservices;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/course/modlib.php');

/**
 * Get a list of students in a course by course id.
 */
class read_courseusers extends \external_api {
    /**
     * Return description of method parameters.
     *
     * @return \external_function_parameters
     */
    public static function courseusers_read_parameters() {
        return new \external_function_parameters(
            [
                'courseid' => new \external_value(PARAM_INT, 'course id'),
                'limitfrom' => new \external_value(PARAM_INT, 'sql limit from', VALUE_DEFAULT, 0),
                'limitnumber' => new \external_value(PARAM_INT, 'maximum number of returned users', VALUE_DEFAULT, 0),
                'userids' => new \external_multiple_structure(
                    new \external_value(PARAM_INT, 'user id, empty to retrieve all users'),
                    '0 or more user ids',
                    VALUE_DEFAULT,
                    []
                ),
            ]
        );
    }

    /**
     * Get list of users enrolled in the specified course.
     *
     * @param int $courseid
     * @param int $limitfrom
     * @param int $limitnumber
     * @param array $userids
     * @return array
     */
    public static function courseusers_read($courseid, $limitfrom = 0, $limitnumber = 0, $userids = []) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/user/lib.php');

        $params = self::validate_parameters(
            self::courseusers_read_parameters(),
            [
                'courseid' => $courseid,
                'limitfrom' => $limitfrom,
                'limitnumber' => $limitnumber,
                'userids' => $userids,
            ]
        );

        $userids = (!empty($params['userids']) && is_array($params['userids'])) ? array_flip($params['userids']) : [];

        $withcapability = '';
        $userfields = [
            'id',
            'username',
            'fullname',
            'firstname',
            'lastname',
            'email',
            'idnumber',
            'lang',
            'timezone',
            'profileimageurlsmall',
            'profileimageurl',
        ];
        $limitfrom = clean_param($limitfrom, PARAM_INT);
        $limitnumber = clean_param($limitnumber, PARAM_INT);

        if ($courseid == SITEID) {
            // TODO exception.
        }
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        try {
            self::validate_context($context);
        } catch (\Exception $e) {
            $exceptionparam = new \stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new \moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }

        require_capability('moodle/course:viewparticipants', $context);

        [$enrolledsql, $enrolledparams] = get_enrolled_sql($context, $withcapability);

        // For user context preloading.
        $ctxselect = ', ' . \context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
        $enrolledparams['contextlevel'] = CONTEXT_USER;

        // In case the user does not have "accessallgroups", limit to groups they are a part of.
        $groupjoin = '';
        if (groups_get_course_groupmode($course) == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context)) {
            // Filter by groups the user can view.
            $usergroups = groups_get_user_groups($course->id);
            if (!empty($usergroups['0'])) {
                [$groupsql, $groupparams] = $DB->get_in_or_equal($usergroups['0'], SQL_PARAMS_NAMED);
                $groupjoin = "JOIN {groups_members} gm ON (u.id = gm.userid AND gm.groupid $groupsql)";
                $enrolledparams = array_merge($enrolledparams, $groupparams);
            } else {
                // User doesn't belong to any group, so they can't see any users.
                return [];
            }
        }

        // Get enrolled users.
        $sql = "SELECT us.*
                  FROM {user} us
                  JOIN (
                      SELECT DISTINCT u.id $ctxselect
                        FROM {user} u $ctxjoin $groupjoin
                       WHERE u.id IN ($enrolledsql)
                  ) q ON q.id = us.id
                ORDER BY us.id ASC";
        $enrolledusers = $DB->get_recordset_sql($sql, $enrolledparams, $limitfrom, $limitnumber);
        $users = [];
        foreach ($enrolledusers as $user) {
            // Check user filter.
            if (!empty($userids) && !isset($userids[$user->id])) {
                continue;
            }

            // Get user info.
            \context_helper::preload_from_record($user);
            if ($userdetails = user_get_user_details($user, $course, $userfields)) {
                $users[] = $userdetails;
            }
        }
        $enrolledusers->close();

        return $users;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function courseusers_read_returns() {
        return new \external_multiple_structure(
            new \external_single_structure(
                [
                    'id' => new \external_value(PARAM_INT, 'ID of the user'),
                    'username' => new \external_value(PARAM_RAW, 'Username policy is defined in Moodle security config',
                        VALUE_OPTIONAL),
                    'fullname' => new \external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'firstname' => new \external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname' => new \external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'email' => new \external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'idnumber' => new \external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                        VALUE_OPTIONAL),
                    'lang' => new \external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution',
                        VALUE_OPTIONAL),
                    'profileimageurlsmall' => new \external_value(PARAM_URL, 'User image profile URL - small version',
                        VALUE_OPTIONAL),
                    'profileimageurl' => new \external_value(PARAM_URL, 'User image profile URL - big version', VALUE_OPTIONAL),
                ]
            )
        );
    }
}
