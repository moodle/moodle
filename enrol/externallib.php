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
 * External course participation api.
 *
 * This api is mostly read only, the actual enrol and unenrol
 * support is in each enrol plugin.
 *
 * @package    core_enrol
 * @category   external
 * @copyright  2010 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * Enrol external functions
 *
 * @package    core_enrol
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_enrol_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.4
     */
    public static function get_enrolled_users_with_capability_parameters() {
        return new external_function_parameters(
            array (
                'coursecapabilities' => new external_multiple_structure(
                    new external_single_structure(
                        array (
                            'courseid' => new external_value(PARAM_INT, 'Course ID number in the Moodle course table'),
                            'capabilities' => new external_multiple_structure(
                                new external_value(PARAM_CAPABILITY, 'Capability name, such as mod/forum:viewdiscussion')),
                        )
                    )
                , 'course id and associated capability name'),
                 'options'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name'  => new external_value(PARAM_ALPHANUMEXT, 'option name'),
                            'value' => new external_value(PARAM_RAW, 'option value')
                        )
                    ), 'Option names:
                            * groupid (integer) return only users in this group id. Requires \'moodle/site:accessallgroups\' .
                            * onlyactive (integer) only users with active enrolments. Requires \'moodle/course:enrolreview\' .
                            * userfields (\'string, string, ...\') return only the values of these user fields.
                            * limitfrom (integer) sql limit from.
                            * limitnumber (integer) max number of users per course and capability.', VALUE_DEFAULT, array())
            )
        );
    }

    /**
     * Return users that have the capabilities for each course specified. For each course and capability specified,
     * a list of the users that are enrolled in the course and have that capability are returned.
     *
     * @param array $coursecapabilities array of course ids and associated capability names {courseid, {capabilities}}
     * @return array An array of arrays describing users for each associated courseid and capability
     * @since  Moodle 2.4
     */
    public static function get_enrolled_users_with_capability($coursecapabilities, $options) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        if (empty($coursecapabilities)) {
            throw new invalid_parameter_exception('Parameter can not be empty');
        }
        $params = self::validate_parameters(self::get_enrolled_users_with_capability_parameters(),
            array ('coursecapabilities' => $coursecapabilities,  'options'=>$options));
        $result = array();
        $userlist = array();
        $groupid        = 0;
        $onlyactive     = false;
        $userfields     = array();
        $limitfrom = 0;
        $limitnumber = 0;
        foreach ($params['options'] as $option) {
            switch ($option['name']) {
                case 'groupid':
                    $groupid = (int)$option['value'];
                    break;
                case 'onlyactive':
                    $onlyactive = !empty($option['value']);
                    break;
                case 'userfields':
                    $thefields = explode(',', $option['value']);
                    foreach ($thefields as $f) {
                        $userfields[] = clean_param($f, PARAM_ALPHANUMEXT);
                    }
                    break;
                case 'limitfrom' :
                    $limitfrom = clean_param($option['value'], PARAM_INT);
                    break;
                case 'limitnumber' :
                    $limitnumber = clean_param($option['value'], PARAM_INT);
                    break;
            }
        }

        foreach ($params['coursecapabilities'] as $coursecapability) {
            $courseid = $coursecapability['courseid'];
            $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
            $coursecontext = context_course::instance($courseid);
            if (!$coursecontext) {
                throw new moodle_exception('cannotfindcourse', 'error', '', null,
                        'The course id ' . $courseid . ' doesn\'t exist.');
            }
            if ($courseid == SITEID) {
                $context = context_system::instance();
            } else {
                $context = $coursecontext;
            }
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->courseid = $params['courseid'];
                throw new moodle_exception(get_string('errorcoursecontextnotvalid' , 'webservice', $exceptionparam));
            }

            course_require_view_participants($context);

            // The accessallgroups capability is needed to use this option.
            if (!empty($groupid) && groups_is_member($groupid)) {
                require_capability('moodle/site:accessallgroups', $coursecontext);
            }
            // The course:enrolereview capability is needed to use this option.
            if ($onlyactive) {
                require_capability('moodle/course:enrolreview', $coursecontext);
            }

            // To see the permissions of others role:review capability is required.
            require_capability('moodle/role:review', $coursecontext);
            foreach ($coursecapability['capabilities'] as $capability) {
                $courseusers['courseid'] = $courseid;
                $courseusers['capability'] = $capability;

                list($enrolledsql, $enrolledparams) = get_enrolled_sql($coursecontext, $capability, $groupid, $onlyactive);
                $enrolledparams['courseid'] = $courseid;

                $sql = "SELECT u.*, COALESCE(ul.timeaccess, 0) AS lastcourseaccess
                          FROM {user} u
                     LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = :courseid)
                         WHERE u.id IN ($enrolledsql)
                      ORDER BY u.id ASC";

                $enrolledusers = $DB->get_recordset_sql($sql, $enrolledparams, $limitfrom, $limitnumber);
                $users = array();
                foreach ($enrolledusers as $courseuser) {
                    if ($userdetails = user_get_user_details($courseuser, $course, $userfields)) {
                        $users[] = $userdetails;
                    }
                }
                $enrolledusers->close();
                $courseusers['users'] = $users;
                $result[] = $courseusers;
            }
        }
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_multiple_structure
     * @since Moodle 2.4
     */
    public static function get_enrolled_users_with_capability_returns() {
        return  new external_multiple_structure( new external_single_structure (
                array (
                    'courseid' => new external_value(PARAM_INT, 'Course ID number in the Moodle course table'),
                    'capability' => new external_value(PARAM_CAPABILITY, 'Capability name'),
                    'users' => new external_multiple_structure(
                        new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_INT, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW, 'Username', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'Email address', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastcourseaccess'  => new external_value(PARAM_INT, 'last access to the course (0 if never)', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small', VALUE_OPTIONAL),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big', VALUE_OPTIONAL),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field'),
                            )
                        ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'groups' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'group id'),
                                'name' => new external_value(PARAM_RAW, 'group name'),
                                'description' => new external_value(PARAM_RAW, 'group description'),
                            )
                        ), 'user groups', VALUE_OPTIONAL),
                    'roles' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'roleid'       => new external_value(PARAM_INT, 'role id'),
                                'name'         => new external_value(PARAM_RAW, 'role name'),
                                'shortname'    => new external_value(PARAM_ALPHANUMEXT, 'role shortname'),
                                'sortorder'    => new external_value(PARAM_INT, 'role sortorder')
                            )
                        ), 'user roles', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_RAW, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname' => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
                        ), 'List of users that are enrolled in the course and have the specified capability'),
                    )
                )
            );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_users_courses_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'user id'),
                'returnusercount' => new external_value(PARAM_BOOL,
                        'Include count of enrolled users for each course? This can add several seconds to the response time'
                            . ' if a user is on several large courses, so set this to false if the value will not be used to'
                            . ' improve performance.',
                        VALUE_DEFAULT, true),
            )
        );
    }

    /**
     * Get list of courses user is enrolled in (only active enrolments are returned).
     * Please note the current user must be able to access the course, otherwise the course is not included.
     *
     * @param int $userid
     * @param bool $returnusercount
     * @return array of courses
     */
    public static function get_users_courses($userid, $returnusercount = true) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/completionlib.php');

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::get_users_courses_parameters(),
                ['userid' => $userid, 'returnusercount' => $returnusercount]);
        $userid = $params['userid'];
        $returnusercount = $params['returnusercount'];

        $courses = enrol_get_users_courses($userid, true, '*');
        $result = array();

        // Get user data including last access to courses.
        $user = get_complete_user_data('id', $userid);
        $sameuser = $USER->id == $userid;

        // Retrieve favourited courses (starred).
        $favouritecourseids = array();
        if ($sameuser) {
            $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($userid));
            $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');

            if ($favourites) {
                $favouritecourseids = array_flip(array_map(
                    function($favourite) {
                        return $favourite->itemid;
                    }, $favourites));
            }
        }

        foreach ($courses as $course) {
            $context = context_course::instance($course->id, IGNORE_MISSING);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                // current user can not access this course, sorry we can not disclose who is enrolled in this course!
                continue;
            }

            if (!$sameuser and !course_can_view_participants($context)) {
                // we need capability to view participants
                continue;
            }

            if ($returnusercount) {
                list($enrolledsqlselect, $enrolledparams) = get_enrolled_sql($context);
                $enrolledsql = "SELECT COUNT('x') FROM ($enrolledsqlselect) enrolleduserids";
                $enrolledusercount = $DB->count_records_sql($enrolledsql, $enrolledparams);
            }

            $displayname = external_format_string(get_course_display_name_for_list($course), $context->id);
            list($course->summary, $course->summaryformat) =
                external_format_text($course->summary, $course->summaryformat, $context->id, 'course', 'summary', null);
            $course->fullname = external_format_string($course->fullname, $context->id);
            $course->shortname = external_format_string($course->shortname, $context->id);

            $progress = null;
            $completed = null;
            $completionhascriteria = false;

            // Return only private information if the user should be able to see it.
            if ($sameuser || completion_can_view_data($userid, $course)) {
                if ($course->enablecompletion) {
                    $completion = new completion_info($course);
                    $completed = $completion->is_course_complete($userid);
                    $completionhascriteria = $completion->has_criteria();
                    $progress = \core_completion\progress::get_course_progress_percentage($course, $userid);
                }
            }

            $lastaccess = null;
            // Check if last access is a hidden field.
            $hiddenfields = array_flip(explode(',', $CFG->hiddenuserfields));
            $canviewlastaccess = $sameuser || !isset($hiddenfields['lastaccess']);
            if (!$canviewlastaccess) {
                $canviewlastaccess = has_capability('moodle/course:viewhiddenuserfields', $context);
            }

            if ($canviewlastaccess && isset($user->lastcourseaccess[$course->id])) {
                $lastaccess = $user->lastcourseaccess[$course->id];
            }

            $hidden = false;
            if ($sameuser) {
                $hidden = boolval(get_user_preferences('block_myoverview_hidden_course_' . $course->id, 0));
            }

            // Retrieve course overview used files.
            $courselist = new core_course_list_element($course);
            $overviewfiles = array();
            foreach ($courselist->get_course_overviewfiles() as $file) {
                $fileurl = moodle_url::make_webservice_pluginfile_url($file->get_contextid(), $file->get_component(),
                                                                        $file->get_filearea(), null, $file->get_filepath(),
                                                                        $file->get_filename())->out(false);
                $overviewfiles[] = array(
                    'filename' => $file->get_filename(),
                    'fileurl' => $fileurl,
                    'filesize' => $file->get_filesize(),
                    'filepath' => $file->get_filepath(),
                    'mimetype' => $file->get_mimetype(),
                    'timemodified' => $file->get_timemodified(),
                );
            }

            $courseresult = [
                'id' => $course->id,
                'shortname' => $course->shortname,
                'fullname' => $course->fullname,
                'displayname' => $displayname,
                'idnumber' => $course->idnumber,
                'visible' => $course->visible,
                'summary' => $course->summary,
                'summaryformat' => $course->summaryformat,
                'format' => $course->format,
                'showgrades' => $course->showgrades,
                'lang' => clean_param($course->lang, PARAM_LANG),
                'enablecompletion' => $course->enablecompletion,
                'completionhascriteria' => $completionhascriteria,
                'category' => $course->category,
                'progress' => $progress,
                'completed' => $completed,
                'startdate' => $course->startdate,
                'enddate' => $course->enddate,
                'marker' => $course->marker,
                'lastaccess' => $lastaccess,
                'isfavourite' => isset($favouritecourseids[$course->id]),
                'hidden' => $hidden,
                'overviewfiles' => $overviewfiles,
            ];
            if ($returnusercount) {
                $courseresult['enrolledusercount'] = $enrolledusercount;
            }
            $result[] = $courseresult;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_users_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new external_value(PARAM_RAW, 'long name of course'),
                    'displayname' => new external_value(PARAM_TEXT, 'course display name for lists.', VALUE_OPTIONAL),
                    'enrolledusercount' => new external_value(PARAM_INT, 'Number of enrolled users in this course',
                            VALUE_OPTIONAL),
                    'idnumber'  => new external_value(PARAM_RAW, 'id number of course'),
                    'visible'   => new external_value(PARAM_INT, '1 means visible, 0 means not yet visible course'),
                    'summary'   => new external_value(PARAM_RAW, 'summary', VALUE_OPTIONAL),
                    'summaryformat' => new external_format_value('summary', VALUE_OPTIONAL),
                    'format'    => new external_value(PARAM_PLUGIN, 'course format: weeks, topics, social, site', VALUE_OPTIONAL),
                    'showgrades' => new external_value(PARAM_BOOL, 'true if grades are shown, otherwise false', VALUE_OPTIONAL),
                    'lang'      => new external_value(PARAM_LANG, 'forced course language', VALUE_OPTIONAL),
                    'enablecompletion' => new external_value(PARAM_BOOL, 'true if completion is enabled, otherwise false',
                                                                VALUE_OPTIONAL),
                    'completionhascriteria' => new external_value(PARAM_BOOL, 'If completion criteria is set.', VALUE_OPTIONAL),
                    'category' => new external_value(PARAM_INT, 'course category id', VALUE_OPTIONAL),
                    'progress' => new external_value(PARAM_FLOAT, 'Progress percentage', VALUE_OPTIONAL),
                    'completed' => new external_value(PARAM_BOOL, 'Whether the course is completed.', VALUE_OPTIONAL),
                    'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                    'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                    'marker' => new external_value(PARAM_INT, 'Course section marker.', VALUE_OPTIONAL),
                    'lastaccess' => new external_value(PARAM_INT, 'Last access to the course (timestamp).', VALUE_OPTIONAL),
                    'isfavourite' => new external_value(PARAM_BOOL, 'If the user marked this course a favourite.', VALUE_OPTIONAL),
                    'hidden' => new external_value(PARAM_BOOL, 'If the user hide the course from the dashboard.', VALUE_OPTIONAL),
                    'overviewfiles' => new external_files('Overview files attached to this course.', VALUE_OPTIONAL),
                )
            )
        );
    }

    /**
     * Returns description of method parameters value
     *
     * @return external_description
     */
    public static function get_potential_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'enrolid' => new external_value(PARAM_INT, 'enrolment id'),
                'search' => new external_value(PARAM_RAW, 'query'),
                'searchanywhere' => new external_value(PARAM_BOOL, 'find a match anywhere, or only at the beginning'),
                'page' => new external_value(PARAM_INT, 'Page number'),
                'perpage' => new external_value(PARAM_INT, 'Number per page'),
            )
        );
    }

    /**
     * Get potential users.
     *
     * @param int $courseid Course id
     * @param int $enrolid Enrolment id
     * @param string $search The query
     * @param boolean $searchanywhere Match anywhere in the string
     * @param int $page Page number
     * @param int $perpage Max per page
     * @return array An array of users
     */
    public static function get_potential_users($courseid, $enrolid, $search, $searchanywhere, $page, $perpage) {
        global $PAGE, $DB, $CFG;

        require_once($CFG->dirroot.'/enrol/locallib.php');
        require_once($CFG->dirroot.'/user/lib.php');

        $params = self::validate_parameters(
            self::get_potential_users_parameters(),
            array(
                'courseid' => $courseid,
                'enrolid' => $enrolid,
                'search' => $search,
                'searchanywhere' => $searchanywhere,
                'page' => $page,
                'perpage' => $perpage
            )
        );
        $context = context_course::instance($params['courseid']);
        try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }
        require_capability('moodle/course:enrolreview', $context);

        $course = $DB->get_record('course', array('id' => $params['courseid']));
        $manager = new course_enrolment_manager($PAGE, $course);

        $users = $manager->get_potential_users($params['enrolid'],
                                               $params['search'],
                                               $params['searchanywhere'],
                                               $params['page'],
                                               $params['perpage']);

        $results = array();
        // Add also extra user fields.
        $requiredfields = array_merge(
            ['id', 'fullname', 'profileimageurl', 'profileimageurlsmall'],
            get_extra_user_fields($context)
        );
        foreach ($users['users'] as $id => $user) {
            // Note: We pass the course here to validate that the current user can at least view user details in this course.
            // The user we are looking at is not in this course yet though - but we only fetch the minimal set of
            // user records, and the user has been validated to have course:enrolreview in this course. Otherwise
            // there is no way to find users who aren't in the course in order to enrol them.
            if ($userdetails = user_get_user_details($user, $course, $requiredfields)) {
                $results[] = $userdetails;
            }
        }
        return $results;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_potential_users_returns() {
        global $CFG;
        require_once($CFG->dirroot . '/user/externallib.php');
        return new external_multiple_structure(core_user_external::user_description());
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_enrolled_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'options'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name'  => new external_value(PARAM_ALPHANUMEXT, 'option name'),
                            'value' => new external_value(PARAM_RAW, 'option value')
                        )
                    ), 'Option names:
                            * withcapability (string) return only users with this capability. This option requires \'moodle/role:review\' on the course context.
                            * groupid (integer) return only users in this group id. If the course has groups enabled and this param
                                                isn\'t defined, returns all the viewable users.
                                                This option requires \'moodle/site:accessallgroups\' on the course context if the
                                                user doesn\'t belong to the group.
                            * onlyactive (integer) return only users with active enrolments and matching time restrictions. This option requires \'moodle/course:enrolreview\' on the course context.
                            * userfields (\'string, string, ...\') return only the values of these user fields.
                            * limitfrom (integer) sql limit from.
                            * limitnumber (integer) maximum number of returned users.
                            * sortby (string) sort by id, firstname or lastname. For ordering like the site does, use siteorder.
                            * sortdirection (string) ASC or DESC',
                            VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Get course participants details
     *
     * @param int $courseid  course id
     * @param array $options options {
     *                                'name' => option name
     *                                'value' => option value
     *                               }
     * @return array An array of users
     */
    public static function get_enrolled_users($courseid, $options = array()) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(
            self::get_enrolled_users_parameters(),
            array(
                'courseid'=>$courseid,
                'options'=>$options
            )
        );
        $withcapability = '';
        $groupid        = 0;
        $onlyactive     = false;
        $userfields     = array();
        $limitfrom = 0;
        $limitnumber = 0;
        $sortby = 'us.id';
        $sortparams = array();
        $sortdirection = 'ASC';
        foreach ($options as $option) {
            switch ($option['name']) {
            case 'withcapability':
                $withcapability = $option['value'];
                break;
            case 'groupid':
                $groupid = (int)$option['value'];
                break;
            case 'onlyactive':
                $onlyactive = !empty($option['value']);
                break;
            case 'userfields':
                $thefields = explode(',', $option['value']);
                foreach ($thefields as $f) {
                    $userfields[] = clean_param($f, PARAM_ALPHANUMEXT);
                }
                break;
            case 'limitfrom' :
                $limitfrom = clean_param($option['value'], PARAM_INT);
                break;
            case 'limitnumber' :
                $limitnumber = clean_param($option['value'], PARAM_INT);
                break;
            case 'sortby':
                $sortallowedvalues = array('id', 'firstname', 'lastname', 'siteorder');
                if (!in_array($option['value'], $sortallowedvalues)) {
                    throw new invalid_parameter_exception('Invalid value for sortby parameter (value: ' . $option['value'] . '),' .
                        'allowed values are: ' . implode(',', $sortallowedvalues));
                }
                if ($option['value'] == 'siteorder') {
                    list($sortby, $sortparams) = users_order_by_sql('us');
                } else {
                    $sortby = 'us.' . $option['value'];
                }
                break;
            case 'sortdirection':
                $sortdirection = strtoupper($option['value']);
                $directionallowedvalues = array('ASC', 'DESC');
                if (!in_array($sortdirection, $directionallowedvalues)) {
                    throw new invalid_parameter_exception('Invalid value for sortdirection parameter
                        (value: ' . $sortdirection . '),' . 'allowed values are: ' . implode(',', $directionallowedvalues));
                }
                break;
            }
        }

        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $coursecontext = context_course::instance($courseid, IGNORE_MISSING);
        if ($courseid == SITEID) {
            $context = context_system::instance();
        } else {
            $context = $coursecontext;
        }
        try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception('errorcoursecontextnotvalid' , 'webservice', '', $exceptionparam);
        }

        course_require_view_participants($context);

        // to overwrite this parameter, you need role:review capability
        if ($withcapability) {
            require_capability('moodle/role:review', $coursecontext);
        }
        // need accessallgroups capability if you want to overwrite this option
        if (!empty($groupid) && !groups_is_member($groupid)) {
            require_capability('moodle/site:accessallgroups', $coursecontext);
        }
        // to overwrite this option, you need course:enrolereview permission
        if ($onlyactive) {
            require_capability('moodle/course:enrolreview', $coursecontext);
        }

        list($enrolledsql, $enrolledparams) = get_enrolled_sql($coursecontext, $withcapability, $groupid, $onlyactive);
        $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)";
        $enrolledparams['contextlevel'] = CONTEXT_USER;

        $groupjoin = '';
        if (empty($groupid) && groups_get_course_groupmode($course) == SEPARATEGROUPS &&
                !has_capability('moodle/site:accessallgroups', $coursecontext)) {
            // Filter by groups the user can view.
            $usergroups = groups_get_user_groups($course->id);
            if (!empty($usergroups['0'])) {
                list($groupsql, $groupparams) = $DB->get_in_or_equal($usergroups['0'], SQL_PARAMS_NAMED);
                $groupjoin = "JOIN {groups_members} gm ON (u.id = gm.userid AND gm.groupid $groupsql)";
                $enrolledparams = array_merge($enrolledparams, $groupparams);
            } else {
                // User doesn't belong to any group, so he can't see any user. Return an empty array.
                return array();
            }
        }
        $sql = "SELECT us.*, COALESCE(ul.timeaccess, 0) AS lastcourseaccess
                  FROM {user} us
                  JOIN (
                      SELECT DISTINCT u.id $ctxselect
                        FROM {user} u $ctxjoin $groupjoin
                       WHERE u.id IN ($enrolledsql)
                  ) q ON q.id = us.id
             LEFT JOIN {user_lastaccess} ul ON (ul.userid = us.id AND ul.courseid = :courseid)
                ORDER BY $sortby $sortdirection";
        $enrolledparams = array_merge($enrolledparams, $sortparams);
        $enrolledparams['courseid'] = $courseid;

        $enrolledusers = $DB->get_recordset_sql($sql, $enrolledparams, $limitfrom, $limitnumber);
        $users = array();
        foreach ($enrolledusers as $user) {
            context_helper::preload_from_record($user);
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
    public static function get_enrolled_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_INT, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_TEXT, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'idnumber'    => new external_value(PARAM_RAW, 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastcourseaccess'  => new external_value(PARAM_INT, 'last access to the course (0 if never)', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_format_value('description', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version', VALUE_OPTIONAL),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version', VALUE_OPTIONAL),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
                            )
                        ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'groups' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'group id'),
                                'name' => new external_value(PARAM_RAW, 'group name'),
                                'description' => new external_value(PARAM_RAW, 'group description'),
                                'descriptionformat' => new external_format_value('description'),
                            )
                        ), 'user groups', VALUE_OPTIONAL),
                    'roles' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'roleid'       => new external_value(PARAM_INT, 'role id'),
                                'name'         => new external_value(PARAM_RAW, 'role name'),
                                'shortname'    => new external_value(PARAM_ALPHANUMEXT, 'role shortname'),
                                'sortorder'    => new external_value(PARAM_INT, 'role sortorder')
                            )
                        ), 'user roles', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_RAW, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname' => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
            )
        );
    }

    /**
     * Returns description of get_course_enrolment_methods() parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_enrolment_methods_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id')
            )
        );
    }

    /**
     * Get list of active course enrolment methods for current user.
     *
     * @param int $courseid
     * @return array of course enrolment methods
     * @throws moodle_exception
     */
    public static function get_course_enrolment_methods($courseid) {
        global $DB;

        $params = self::validate_parameters(self::get_course_enrolment_methods_parameters(), array('courseid' => $courseid));
        self::validate_context(context_system::instance());

        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);
        if (!core_course_category::can_view_course_info($course) && !can_access_course($course)) {
            throw new moodle_exception('coursehidden');
        }

        $result = array();
        $enrolinstances = enrol_get_instances($params['courseid'], true);
        foreach ($enrolinstances as $enrolinstance) {
            if ($enrolplugin = enrol_get_plugin($enrolinstance->enrol)) {
                if ($instanceinfo = $enrolplugin->get_enrol_info($enrolinstance)) {
                    $result[] = (array) $instanceinfo;
                }
            }
        }
        return $result;
    }

    /**
     * Returns description of get_course_enrolment_methods() result value
     *
     * @return external_description
     */
    public static function get_course_enrolment_methods_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id of course enrolment instance'),
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'type' => new external_value(PARAM_PLUGIN, 'type of enrolment plugin'),
                    'name' => new external_value(PARAM_RAW, 'name of enrolment plugin'),
                    'status' => new external_value(PARAM_RAW, 'status of enrolment plugin'),
                    'wsfunction' => new external_value(PARAM_ALPHANUMEXT, 'webservice function to get more information', VALUE_OPTIONAL),
                )
            )
        );
    }

    /**
     * Returns description of edit_user_enrolment() parameters
     *
     * @return external_function_parameters
     */
    public static function edit_user_enrolment_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'User enrolment ID'),
                'ueid' => new external_value(PARAM_INT, 'User enrolment ID'),
                'status' => new external_value(PARAM_INT, 'Enrolment status'),
                'timestart' => new external_value(PARAM_INT, 'Enrolment start timestamp', VALUE_DEFAULT, 0),
                'timeend' => new external_value(PARAM_INT, 'Enrolment end timestamp', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * External function that updates a given user enrolment.
     *
     * @param int $courseid The course ID.
     * @param int $ueid The user enrolment ID.
     * @param int $status The enrolment status.
     * @param int $timestart Enrolment start timestamp.
     * @param int $timeend Enrolment end timestamp.
     * @return array An array consisting of the processing result, errors and form output, if available.
     */
    public static function edit_user_enrolment($courseid, $ueid, $status, $timestart = 0, $timeend = 0) {
        global $CFG, $DB, $PAGE;

        $params = self::validate_parameters(self::edit_user_enrolment_parameters(), [
            'courseid' => $courseid,
            'ueid' => $ueid,
            'status' => $status,
            'timestart' => $timestart,
            'timeend' => $timeend,
        ]);

        $course = get_course($courseid);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        $userenrolment = $DB->get_record('user_enrolments', ['id' => $params['ueid']], '*', MUST_EXIST);
        $userenroldata = [
            'status' => $params['status'],
            'timestart' => $params['timestart'],
            'timeend' => $params['timeend'],
        ];

        $result = false;
        $errors = [];

        // Validate data against the edit user enrolment form.
        $instance = $DB->get_record('enrol', ['id' => $userenrolment->enrolid], '*', MUST_EXIST);
        $plugin = enrol_get_plugin($instance->enrol);
        require_once("$CFG->dirroot/enrol/editenrolment_form.php");
        $customformdata = [
            'ue' => $userenrolment,
            'modal' => true,
            'enrolinstancename' => $plugin->get_instance_name($instance)
        ];
        $mform = new \enrol_user_enrolment_form(null, $customformdata, 'post', '', null, true, $userenroldata);
        $mform->set_data($userenroldata);
        $validationerrors = $mform->validation($userenroldata, null);
        if (empty($validationerrors)) {
            require_once($CFG->dirroot . '/enrol/locallib.php');
            $manager = new course_enrolment_manager($PAGE, $course);
            $result = $manager->edit_enrolment($userenrolment, (object)$userenroldata);
        } else {
            foreach ($validationerrors as $key => $errormessage) {
                $errors[] = (object)[
                    'key' => $key,
                    'message' => $errormessage
                ];
            }
        }

        return [
            'result' => $result,
            'errors' => $errors,
        ];
    }

    /**
     * Returns description of edit_user_enrolment() result value
     *
     * @return external_description
     */
    public static function edit_user_enrolment_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_BOOL, 'True if the user\'s enrolment was successfully updated'),
                'errors' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_TEXT, 'The data that failed the validation'),
                            'message' => new external_value(PARAM_TEXT, 'The error message'),
                        )
                    ), 'List of validation errors'
                ),
            )
        );
    }

    /**
     * Returns description of unenrol_user_enrolment() parameters
     *
     * @return external_function_parameters
     */
    public static function unenrol_user_enrolment_parameters() {
        return new external_function_parameters(
            array(
                'ueid' => new external_value(PARAM_INT, 'User enrolment ID')
            )
        );
    }

    /**
     * External function that unenrols a given user enrolment.
     *
     * @param int $ueid The user enrolment ID.
     * @return array An array consisting of the processing result, errors.
     */
    public static function unenrol_user_enrolment($ueid) {
        global $CFG, $DB, $PAGE;

        $params = self::validate_parameters(self::unenrol_user_enrolment_parameters(), [
            'ueid' => $ueid
        ]);

        $result = false;
        $errors = [];

        $userenrolment = $DB->get_record('user_enrolments', ['id' => $params['ueid']], '*');
        if ($userenrolment) {
            $userid = $userenrolment->userid;
            $enrolid = $userenrolment->enrolid;
            $enrol = $DB->get_record('enrol', ['id' => $enrolid], '*', MUST_EXIST);
            $courseid = $enrol->courseid;
            $course = get_course($courseid);
            $context = context_course::instance($course->id);
            self::validate_context($context);
        } else {
            $validationerrors['invalidrequest'] = get_string('invalidrequest', 'enrol');
        }

        // If the userenrolment exists, unenrol the user.
        if (!isset($validationerrors)) {
            require_once($CFG->dirroot . '/enrol/locallib.php');
            $manager = new course_enrolment_manager($PAGE, $course);
            $result = $manager->unenrol_user($userenrolment);
        } else {
            foreach ($validationerrors as $key => $errormessage) {
                $errors[] = (object)[
                    'key' => $key,
                    'message' => $errormessage
                ];
            }
        }

        return [
            'result' => $result,
            'errors' => $errors,
        ];
    }

    /**
     * Returns description of unenrol_user_enrolment() result value
     *
     * @return external_description
     */
    public static function unenrol_user_enrolment_returns() {
        return new external_single_structure(
            array(
                'result' => new external_value(PARAM_BOOL, 'True if the user\'s enrolment was successfully updated'),
                'errors' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'key' => new external_value(PARAM_TEXT, 'The data that failed the validation'),
                            'message' => new external_value(PARAM_TEXT, 'The error message'),
                        )
                    ), 'List of validation errors'
                ),
            )
        );
    }
}

/**
 * Role external functions
 *
 * @package    core_role
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class core_role_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function assign_roles_parameters() {
        return new external_function_parameters(
            array(
                'assignments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_INT, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_INT, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_INT, 'The context to assign the user role in', VALUE_OPTIONAL),
                            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level to assign the user role in
                                    (block, course, coursecat, system, user, module)', VALUE_OPTIONAL),
                            'instanceid' => new external_value(PARAM_INT, 'The Instance id of item where the role needs to be assigned', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

    /**
     * Manual role assignments to users
     *
     * @param array $assignments An array of manual role assignment
     */
    public static function assign_roles($assignments) {
        global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::assign_roles_parameters(), array('assignments'=>$assignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['assignments'] as $assignment) {
            // Ensure correct context level with a instance id or contextid is passed.
            $context = self::get_context_from_params($assignment);

            // Ensure the current user is allowed to run this function in the enrolment context.
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            // throw an exception if user is not able to assign the role in this context
            $roles = get_assignable_roles($context, ROLENAME_SHORT);

            if (!array_key_exists($assignment['roleid'], $roles)) {
                throw new invalid_parameter_exception('Can not assign roleid='.$assignment['roleid'].' in contextid='.$assignment['contextid']);
            }

            role_assign($assignment['roleid'], $assignment['userid'], $context->id);
        }

        $transaction->allow_commit();
    }

    /**
     * Returns description of method result value
     *
     * @return null
     */
    public static function assign_roles_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function unassign_roles_parameters() {
        return new external_function_parameters(
            array(
                'unassignments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_INT, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_INT, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_INT, 'The context to unassign the user role from', VALUE_OPTIONAL),
                            'contextlevel' => new external_value(PARAM_ALPHA, 'The context level to unassign the user role in
+                                    (block, course, coursecat, system, user, module)', VALUE_OPTIONAL),
                            'instanceid' => new external_value(PARAM_INT, 'The Instance id of item where the role needs to be unassigned', VALUE_OPTIONAL),
                        )
                    )
                )
            )
        );
    }

     /**
     * Unassign roles from users
     *
     * @param array $unassignments An array of unassignment
     */
    public static function unassign_roles($unassignments) {
         global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::unassign_roles_parameters(), array('unassignments'=>$unassignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['unassignments'] as $unassignment) {
            // Ensure the current user is allowed to run this function in the unassignment context
            $context = self::get_context_from_params($unassignment);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            // throw an exception if user is not able to unassign the role in this context
            $roles = get_assignable_roles($context, ROLENAME_SHORT);
            if (!array_key_exists($unassignment['roleid'], $roles)) {
                throw new invalid_parameter_exception('Can not unassign roleid='.$unassignment['roleid'].' in contextid='.$unassignment['contextid']);
            }

            role_unassign($unassignment['roleid'], $unassignment['userid'], $context->id);
        }

        $transaction->allow_commit();
    }

   /**
     * Returns description of method result value
     *
     * @return null
     */
    public static function unassign_roles_returns() {
        return null;
    }
}
