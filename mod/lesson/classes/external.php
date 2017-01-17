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
 * Lesson external API
 *
 * @package    mod_lesson
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/lesson/locallib.php');

/**
 * Lesson external functions
 *
 * @package    mod_lesson
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_lesson_external extends external_api {

    /**
     * Describes the parameters for get_lessons_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_lessons_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of lessons in a provided list of courses,
     * if no list is provided all lessons that the user can view will be returned.
     *
     * @param array $courseids Array of course ids
     * @return array of lessons details
     * @since Moodle 3.3
     */
    public static function get_lessons_by_courses($courseids = array()) {
        global $USER;

        $warnings = array();
        $returnedlessons = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_lessons_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the lessons in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $lessons = get_all_instances_in_courses("lesson", $courses);
            foreach ($lessons as $lesson) {
                $context = context_module::instance($lesson->coursemodule);

                $lesson = new lesson($lesson);
                $lesson->update_effective_access($USER->id);

                // Entry to return.
                $lessondetails = array();
                // First, we return information that any user can see in the web interface.
                $lessondetails['id'] = $lesson->id;
                $lessondetails['coursemodule']      = $lesson->coursemodule;
                $lessondetails['course']            = $lesson->course;
                $lessondetails['name']              = external_format_string($lesson->name, $context->id);

                $lessonavailable = $lesson->get_time_restriction_status() === false;
                $lessonavailable = $lessonavailable && $lesson->get_password_restriction_status('') === false;
                $lessonavailable = $lessonavailable && $lesson->get_dependencies_restriction_status() === false;

                if ($lessonavailable) {
                    // Format intro.
                    list($lessondetails['intro'], $lessondetails['introformat']) = external_format_text($lesson->intro,
                                                                    $lesson->introformat, $context->id, 'mod_lesson', 'intro', null);

                    $lessondetails['introfiles'] = external_util::get_area_files($context->id, 'mod_lesson', 'intro', false, false);
                    $lessondetails['mediafiles'] = external_util::get_area_files($context->id, 'mod_lesson', 'mediafile', 0);
                    $viewablefields = array('practice', 'modattempts', 'usepassword', 'grade', 'custom', 'ongoing', 'usemaxgrade',
                                            'maxanswers', 'maxattempts', 'review', 'nextpagedefault', 'feedback', 'minquestions',
                                            'maxpages', 'timelimit', 'retake', 'mediafile', 'mediaheight', 'mediawidth',
                                            'mediaclose', 'slideshow', 'width', 'height', 'bgcolor', 'displayleft', 'displayleftif',
                                            'progressbar');

                    // Fields only for managers.
                    if ($lesson->can_manage()) {
                        $additionalfields = array('password', 'dependency', 'conditions', 'activitylink', 'available', 'deadline',
                                                  'timemodified', 'completionendreached', 'completiontimespent');
                        $viewablefields = array_merge($viewablefields, $additionalfields);
                    }

                    foreach ($viewablefields as $field) {
                        $lessondetails[$field] = $lesson->{$field};
                    }
                }
                $returnedlessons[] = $lessondetails;
            }
        }
        $result = array();
        $result['lessons'] = $returnedlessons;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_lessons_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_lessons_by_courses_returns() {
        return new external_single_structure(
            array(
                'lessons' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Standard Moodle primary key.'),
                            'course' => new external_value(PARAM_INT, 'Foreign key reference to the course this lesson is part of.'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id.'),
                            'name' => new external_value(PARAM_RAW, 'Lesson name.'),
                            'intro' => new external_value(PARAM_RAW, 'Lesson introduction text.', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                            'introfiles' => new external_files('Files in the introduction text', VALUE_OPTIONAL),
                            'practice' => new external_value(PARAM_INT, 'Practice lesson?', VALUE_OPTIONAL),
                            'modattempts' => new external_value(PARAM_INT, 'Allow student review?', VALUE_OPTIONAL),
                            'usepassword' => new external_value(PARAM_INT, 'Password protected lesson?', VALUE_OPTIONAL),
                            'password' => new external_value(PARAM_RAW, 'Password', VALUE_OPTIONAL),
                            'dependency' => new external_value(PARAM_INT, 'Dependent on (another lesson id)', VALUE_OPTIONAL),
                            'conditions' => new external_value(PARAM_RAW, 'Conditions to enable the lesson', VALUE_OPTIONAL),
                            'grade' => new external_value(PARAM_INT, 'The total that the grade is scaled to be out of',
                                                            VALUE_OPTIONAL),
                            'custom' => new external_value(PARAM_INT, 'Custom scoring?', VALUE_OPTIONAL),
                            'ongoing' => new external_value(PARAM_INT, 'Display ongoing score?', VALUE_OPTIONAL),
                            'usemaxgrade' => new external_value(PARAM_INT, 'How to calculate the final grade', VALUE_OPTIONAL),
                            'maxanswers' => new external_value(PARAM_INT, 'Maximum answers per page', VALUE_OPTIONAL),
                            'maxattempts' => new external_value(PARAM_INT, 'Maximum attempts', VALUE_OPTIONAL),
                            'review' => new external_value(PARAM_INT, 'Provide option to try a question again', VALUE_OPTIONAL),
                            'nextpagedefault' => new external_value(PARAM_INT, 'Action for a correct answer', VALUE_OPTIONAL),
                            'feedback' => new external_value(PARAM_INT, 'Display default feedback', VALUE_OPTIONAL),
                            'minquestions' => new external_value(PARAM_INT, 'Minimum number of questions', VALUE_OPTIONAL),
                            'maxpages' => new external_value(PARAM_INT, 'Number of pages to show', VALUE_OPTIONAL),
                            'timelimit' => new external_value(PARAM_INT, 'Time limit', VALUE_OPTIONAL),
                            'retake' => new external_value(PARAM_INT, 'Re-takes allowed', VALUE_OPTIONAL),
                            'activitylink' => new external_value(PARAM_INT, 'Link to next activity', VALUE_OPTIONAL),
                            'mediafile' => new external_value(PARAM_RAW, 'Local file path or full external URL', VALUE_OPTIONAL),
                            'mediafiles' => new external_files('Media files', VALUE_OPTIONAL),
                            'mediaheight' => new external_value(PARAM_INT, 'Popup for media file height', VALUE_OPTIONAL),
                            'mediawidth' => new external_value(PARAM_INT, 'Popup for media with', VALUE_OPTIONAL),
                            'mediaclose' => new external_value(PARAM_INT, 'Display a close button in the popup?', VALUE_OPTIONAL),
                            'slideshow' => new external_value(PARAM_INT, 'Display lesson as slideshow', VALUE_OPTIONAL),
                            'width' => new external_value(PARAM_INT, 'Slideshow width', VALUE_OPTIONAL),
                            'height' => new external_value(PARAM_INT, 'Slideshow height', VALUE_OPTIONAL),
                            'bgcolor' => new external_value(PARAM_TEXT, 'Slideshow bgcolor', VALUE_OPTIONAL),
                            'displayleft' => new external_value(PARAM_INT, 'Display left pages menu?', VALUE_OPTIONAL),
                            'displayleftif' => new external_value(PARAM_INT, 'Minimum grade to display menu', VALUE_OPTIONAL),
                            'progressbar' => new external_value(PARAM_INT, 'Display progress bar?', VALUE_OPTIONAL),
                            'available' => new external_value(PARAM_INT, 'Available from', VALUE_OPTIONAL),
                            'deadline' => new external_value(PARAM_INT, 'Available until', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Last time settings were updated', VALUE_OPTIONAL),
                            'completionendreached' => new external_value(PARAM_INT, 'Require end reached for completion?',
                                                                            VALUE_OPTIONAL),
                            'completiontimespent' => new external_value(PARAM_INT, 'Student must do this activity at least for',
                                                                        VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'Visible?', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id', VALUE_OPTIONAL),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Utility function for validating a lesson.
     *
     * @param int $lessonid lesson instance id
     * @return array array containing the lesson, course, context and course module objects
     * @since  Moodle 3.3
     */
    protected static function validate_lesson($lessonid) {
        global $DB, $USER;

        // Request and permission validation.
        $lesson = $DB->get_record('lesson', array('id' => $lessonid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($lesson, 'lesson');

        $lesson = new lesson($lesson, $cm, $course);
        $lesson->update_effective_access($USER->id);

        $context = $lesson->context;
        self::validate_context($context);

        return array($lesson, $course, $cm, $context);
    }

    /**
     * Validates a new attempt.
     *
     * @param  lesson  $lesson lesson instance
     * @param  array   $params request parameters
     * @param  boolean $return whether to return the errors or throw exceptions
     * @return array          the errors (if return set to true)
     * @since  Moodle 3.3
     */
    protected static function validate_attempt(lesson $lesson, $params, $return = false) {
        global $USER;

        $errors = array();

        // Avoid checkings for managers.
        if ($lesson->can_manage()) {
            return [];
        }

        // Dead line.
        if ($timerestriction = $lesson->get_time_restriction_status()) {
            $error = ["$timerestriction->reason" => userdate($timerestriction->time)];
            if (!$return) {
                throw new moodle_exception(key($error), 'lesson', '', current($error));
            }
            $errors[key($error)] = current($error);
        }

        // Password protected lesson code.
        if ($passwordrestriction = $lesson->get_password_restriction_status($params['password'])) {
            $error = ["passwordprotectedlesson" => external_format_string($lesson->name, $lesson->context->id)];
            if (!$return) {
                throw new moodle_exception(key($error), 'lesson', '', current($error));
            }
            $errors[key($error)] = current($error);
        }

        // Check for dependencies.
        if ($dependenciesrestriction = $lesson->get_dependencies_restriction_status()) {
            $errorhtmllist = implode(get_string('and', 'lesson') . ', ', $dependenciesrestriction->errors);
            $error = ["completethefollowingconditions" => $dependenciesrestriction->dependentlesson->name . $errorhtmllist];
            if (!$return) {
                throw new moodle_exception(key($error), 'lesson', '', current($error));
            }
            $errors[key($error)] = current($error);
        }

        // To check only when no page is set (starting or continuing a lesson).
        if (empty($params['pageid'])) {
            // To avoid multiple calls, store the magic property firstpage.
            $lessonfirstpage = $lesson->firstpage;
            $lessonfirstpageid = $lessonfirstpage ? $lessonfirstpage->id : false;

            // Check if the lesson does not have pages.
            if (!$lessonfirstpageid) {
                $error = ["lessonnotready2" => null];
                if (!$return) {
                    throw new moodle_exception(key($error), 'lesson');
                }
                $errors[key($error)] = current($error);
            }

            // Get the number of retries (also referenced as attempts), and the last page seen.
            $attemptscount = $lesson->count_user_retries($USER->id);
            $lastpageseen = $lesson->get_last_page_seen($attemptscount);

            // Check if the user left a timed session with no retakes.
            if ($lastpageseen !== false && $lastpageseen != LESSON_EOL) {
                if ($lesson->left_during_timed_session($attemptscount) && $lesson->timelimit && !$lesson->retake) {
                    $error = ["leftduringtimednoretake" => null];
                    if (!$return) {
                        throw new moodle_exception(key($error), 'lesson');
                    }
                    $errors[key($error)] = current($error);
                }
            } else if ($attemptscount > 0 && !$lesson->retake) {
                // The user finished the lesson and no retakes are allowed.
                $error = ["noretake" => null];
                if (!$return) {
                    throw new moodle_exception(key($error), 'lesson');
                }
                $errors[key($error)] = current($error);
            }
        }

        return $errors;
    }

    /**
     * Describes the parameters for get_lesson_access_information.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_lesson_access_information_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id')
            )
        );
    }

    /**
     * Return access information for a given lesson.
     *
     * @param int $lessonid lesson instance id
     * @return array of warnings and the access information
     * @since Moodle 3.3
     * @throws  moodle_exception
     */
    public static function get_lesson_access_information($lessonid) {
        global $DB, $USER;

        $warnings = array();

        $params = array(
            'lessonid' => $lessonid
        );
        $params = self::validate_parameters(self::get_lesson_access_information_parameters(), $params);

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        $result = array();
        // Capabilities first.
        $result['canmanage'] = $lesson->can_manage();
        $result['cangrade'] = has_capability('mod/lesson:grade', $context);
        $result['canviewreports'] = has_capability('mod/lesson:viewreports', $context);

        // Status information.
        $result['reviewmode'] = $lesson->is_in_review_mode();
        $result['attemptscount'] = $lesson->count_user_retries($USER->id);
        $lastpageseen = $lesson->get_last_page_seen($result['attemptscount']);
        $result['lastpageseen'] = ($lastpageseen !== false) ? $lastpageseen : 0;
        $result['leftduringtimedsession'] = $lesson->left_during_timed_session($result['attemptscount']);
        // To avoid multiple calls, store the magic property firstpage.
        $lessonfirstpage = $lesson->firstpage;
        $result['firstpageid'] = $lessonfirstpage ? $lessonfirstpage->id : 0;

        // Access restrictions now, we emulate a new attempt access to get the possible warnings.
        $result['preventaccessreasons'] = [];
        $validationerrors = self::validate_attempt($lesson, ['password' => ''], true);
        foreach ($validationerrors as $reason => $data) {
            $result['preventaccessreasons'][] = [
                'reason' => $reason,
                'data' => $data,
                'message' => get_string($reason, 'lesson', $data),
            ];
        }
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_lesson_access_information return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_lesson_access_information_returns() {
        return new external_single_structure(
            array(
                'canmanage' => new external_value(PARAM_BOOL, 'Whether the user can manage the lesson or not.'),
                'cangrade' => new external_value(PARAM_BOOL, 'Whether the user can grade the lesson or not.'),
                'canviewreports' => new external_value(PARAM_BOOL, 'Whether the user can view the lesson reports or not.'),
                'reviewmode' => new external_value(PARAM_BOOL, 'Whether the lesson is in review mode for the current user.'),
                'attemptscount' => new external_value(PARAM_INT, 'The number of attempts done by the user.'),
                'lastpageseen' => new external_value(PARAM_INT, 'The last page seen id.'),
                'leftduringtimedsession' => new external_value(PARAM_BOOL, 'Whether the user left during a timed session.'),
                'firstpageid' => new external_value(PARAM_INT, 'The lesson first page id.'),
                'preventaccessreasons' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'reason' => new external_value(PARAM_ALPHANUMEXT, 'Reason lang string code'),
                            'data' => new external_value(PARAM_RAW, 'Additional data'),
                            'message' => new external_value(PARAM_RAW, 'Complete html message'),
                        ),
                        'The reasons why the user cannot attempt the lesson'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_lesson.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function view_lesson_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'password' => new external_value(PARAM_RAW, 'lesson password', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $lessonid lesson instance id
     * @param str $password optional password (the lesson may be protected)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function view_lesson($lessonid, $password = '') {
        global $DB;

        $params = array('lessonid' => $lessonid, 'password' => $password);
        $params = self::validate_parameters(self::view_lesson_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);
        self::validate_attempt($lesson, $params);

        $lesson->set_module_viewed();

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_lesson return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function view_lesson_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Check if the current user can retrieve lesson information (grades, attempts) about the given user.
     *
     * @param int $userid the user to check
     * @param stdClass $course course object
     * @param stdClass $cm cm object
     * @param stdClass $context context object
     * @throws moodle_exception
     * @since Moodle 3.3
     */
    protected static function check_can_view_user_data($userid, $course, $cm, $context) {
        $user = core_user::get_user($userid, '*', MUST_EXIST);
        core_user::require_active_user($user);
        // Check permissions and that if users share group (if groups enabled).
        require_capability('mod/lesson:viewreports', $context);
        if (!groups_user_groups_visible($course, $user->id, $cm)) {
            throw new moodle_exception('notingroup');
        }
    }

    /**
     * Describes the parameters for get_questions_attempts.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_questions_attempts_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'attempt' => new external_value(PARAM_INT, 'lesson attempt number'),
                'correct' => new external_value(PARAM_BOOL, 'only fetch correct attempts', VALUE_DEFAULT, false),
                'pageid' => new external_value(PARAM_INT, 'only fetch attempts at the given page', VALUE_DEFAULT, null),
                'userid' => new external_value(PARAM_INT, 'only fetch attempts of the given user', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the list of page question attempts in a given lesson.
     *
     * @param int $lessonid lesson instance id
     * @param int $attempt the lesson attempt number
     * @param bool $correct only fetch correct attempts
     * @param int $pageid only fetch attempts at the given page
     * @param int $userid only fetch attempts of the given user
     * @return array of warnings and page attempts
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_questions_attempts($lessonid, $attempt, $correct = false, $pageid = null, $userid = null) {
        global $DB, $USER;

        $params = array(
            'lessonid' => $lessonid,
            'attempt' => $attempt,
            'correct' => $correct,
            'pageid' => $pageid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_questions_attempts_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $result = array();
        $result['attempts'] = $lesson->get_attempts($params['attempt'], $params['correct'], $params['pageid'], $params['userid']);
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_questions_attempts return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_questions_attempts_returns() {
        return new external_single_structure(
            array(
                'attempts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The attempt id'),
                            'lessonid' => new external_value(PARAM_INT, 'The attempt lessonid'),
                            'pageid' => new external_value(PARAM_INT, 'The attempt pageid'),
                            'userid' => new external_value(PARAM_INT, 'The user who did the attempt'),
                            'answerid' => new external_value(PARAM_INT, 'The attempt answerid'),
                            'retry' => new external_value(PARAM_INT, 'The lesson attempt number'),
                            'correct' => new external_value(PARAM_INT, 'If it was the correct answer'),
                            'useranswer' => new external_value(PARAM_RAW, 'The complete user answer'),
                            'timeseen' => new external_value(PARAM_INT, 'The time the question was seen'),
                        ),
                        'The question page attempts'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_grade.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_user_grade_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'userid' => new external_value(PARAM_INT, 'the user id (empty for current user)', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the final grade in the lesson for the given user.
     *
     * @param int $lessonid lesson instance id
     * @param int $userid only fetch grades of this user
     * @return array of warnings and page attempts
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_user_grade($lessonid, $userid = null) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/gradelib.php');

        $params = array(
            'lessonid' => $lessonid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_grade_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $grade = null;
        $formattedgrade = null;
        $grades = lesson_get_user_grades($lesson, $params['userid']);
        if (!empty($grades)) {
            $grade = $grades[$params['userid']]->rawgrade;
            $params = array(
                'itemtype' => 'mod',
                'itemmodule' => 'lesson',
                'iteminstance' => $lesson->id,
                'courseid' => $course->id,
                'itemnumber' => 0
            );
            $gradeitem = grade_item::fetch($params);
            $formattedgrade = grade_format_gradevalue($grade, $gradeitem);
        }

        $result = array();
        $result['grade'] = $grade;
        $result['formattedgrade'] = $formattedgrade;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_user_grade return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_user_grade_returns() {
        return new external_single_structure(
            array(
                'grade' => new external_value(PARAM_FLOAT, 'The lesson final raw grade'),
                'formattedgrade' => new external_value(PARAM_RAW, 'The lesson final grade formatted'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_attempt_grade.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_user_attempt_grade_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'lessonattempt' => new external_value(PARAM_INT, 'lesson attempt number'),
                'userid' => new external_value(PARAM_INT, 'the user id (empty for current user)', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return grade information in the attempt for a given user.
     *
     * @param int $lessonid lesson instance id
     * @param int $lessonattempt lesson attempt number
     * @param int $userid only fetch attempts of the given user
     * @return array of warnings and page attempts
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_user_attempt_grade($lessonid, $lessonattempt, $userid = null) {
        global $CFG, $USER;
        require_once($CFG->libdir . '/gradelib.php');

        $params = array(
            'lessonid' => $lessonid,
            'lessonattempt' => $lessonattempt,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_attempt_grade_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $result = (array) lesson_grade($lesson, $params['lessonattempt'], $params['userid']);
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_user_attempt_grade return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_user_attempt_grade_returns() {
        return new external_single_structure(
            array(
                'nquestions' => new external_value(PARAM_INT, 'Number of questions answered'),
                'attempts' => new external_value(PARAM_INT, 'Number of question attempts'),
                'total' => new external_value(PARAM_FLOAT, 'Max points possible'),
                'earned' => new external_value(PARAM_FLOAT, 'Points earned by student'),
                'grade' => new external_value(PARAM_FLOAT, 'Calculated percentage grade'),
                'nmanual' => new external_value(PARAM_INT, 'Number of manually graded questions'),
                'manualpoints' => new external_value(PARAM_FLOAT, 'Point value for manually graded questions'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_content_pages_viewed.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_content_pages_viewed_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'lessonattempt' => new external_value(PARAM_INT, 'lesson attempt number'),
                'userid' => new external_value(PARAM_INT, 'the user id (empty for current user)', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the list of content pages viewed by a user during a lesson attempt.
     *
     * @param int $lessonid lesson instance id
     * @param int $lessonattempt lesson attempt number
     * @param int $userid only fetch attempts of the given user
     * @return array of warnings and page attempts
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_content_pages_viewed($lessonid, $lessonattempt, $userid = null) {
        global $USER;

        $params = array(
            'lessonid' => $lessonid,
            'lessonattempt' => $lessonattempt,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_content_pages_viewed_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $pages = $lesson->get_content_pages_viewed($params['lessonattempt'], $params['userid']);

        $result = array();
        $result['pages'] = $pages;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_content_pages_viewed return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_content_pages_viewed_returns() {
        return new external_single_structure(
            array(
                'pages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The attempt id.'),
                            'lessonid' => new external_value(PARAM_INT, 'The lesson id.'),
                            'pageid' => new external_value(PARAM_INT, 'The page id.'),
                            'userid' => new external_value(PARAM_INT, 'The user who viewed the page.'),
                            'retry' => new external_value(PARAM_INT, 'The lesson attempt number.'),
                            'flag' => new external_value(PARAM_INT, '1 if the next page was calculated randomly.'),
                            'timeseen' => new external_value(PARAM_INT, 'The time the page was seen.'),
                            'nextpageid' => new external_value(PARAM_INT, 'The next page chosen id.'),
                        ),
                        'The content pages viewed.'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_timers.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_user_timers_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'userid' => new external_value(PARAM_INT, 'the user id (empty for current user)', VALUE_DEFAULT, null),
            )
        );
    }

    /**
     * Return the timers in the current lesson for the given user.
     *
     * @param int $lessonid lesson instance id
     * @param int $userid only fetch timers of the given user
     * @return array of warnings and timers
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_user_timers($lessonid, $userid = null) {
        global $USER;

        $params = array(
            'lessonid' => $lessonid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_timers_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $timers = $lesson->get_user_timers($params['userid']);

        $result = array();
        $result['timers'] = $timers;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_user_timers return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_user_timers_returns() {
        return new external_single_structure(
            array(
                'timers' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The attempt id'),
                            'lessonid' => new external_value(PARAM_INT, 'The lesson id'),
                            'userid' => new external_value(PARAM_INT, 'The user id'),
                            'starttime' => new external_value(PARAM_INT, 'First access time for a new timer session'),
                            'lessontime' => new external_value(PARAM_INT, 'Last access time to the lesson during the timer session'),
                            'completed' => new external_value(PARAM_INT, 'If the lesson for this timer was completed'),
                        ),
                        'The timers'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
