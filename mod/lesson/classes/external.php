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

use mod_lesson\external\lesson_summary_exporter;

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
     * Return a lesson record ready for being exported.
     *
     * @param  stdClass $lessonrecord lesson record
     * @param  string $password       lesson password
     * @return stdClass the lesson record ready for exporting.
     */
    protected static function get_lesson_summary_for_exporter($lessonrecord, $password = '') {
        global $USER;

        $lesson = new lesson($lessonrecord);
        $lesson->update_effective_access($USER->id);
        $lessonavailable = $lesson->get_time_restriction_status() === false;
        $lessonavailable = $lessonavailable && $lesson->get_password_restriction_status($password) === false;
        $lessonavailable = $lessonavailable && $lesson->get_dependencies_restriction_status() === false;
        $canmanage = $lesson->can_manage();

        if (!$canmanage && !$lessonavailable) {
            $fields = array('intro', 'introfiles', 'mediafiles', 'practice', 'modattempts', 'usepassword',
                'grade', 'custom', 'ongoing', 'usemaxgrade',
                'maxanswers', 'maxattempts', 'review', 'nextpagedefault', 'feedback', 'minquestions',
                'maxpages', 'timelimit', 'retake', 'mediafile', 'mediaheight', 'mediawidth',
                'mediaclose', 'slideshow', 'width', 'height', 'bgcolor', 'displayleft', 'displayleftif',
                'progressbar');

            foreach ($fields as $field) {
                unset($lessonrecord->{$field});
            }
        }

        // Fields only for managers.
        if (!$canmanage) {
            $fields = array('password', 'dependency', 'conditions', 'activitylink', 'available', 'deadline',
                            'timemodified', 'completionendreached', 'completiontimespent');

            foreach ($fields as $field) {
                unset($lessonrecord->{$field});
            }
        }
        return $lessonrecord;
    }

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
        global $PAGE;

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
            foreach ($lessons as $lessonrecord) {
                $context = context_module::instance($lessonrecord->coursemodule);

                // Remove fields added by get_all_instances_in_courses.
                unset($lessonrecord->coursemodule, $lessonrecord->section, $lessonrecord->visible, $lessonrecord->groupmode,
                    $lessonrecord->groupingid);

                $lessonrecord = self::get_lesson_summary_for_exporter($lessonrecord);

                $exporter = new lesson_summary_exporter($lessonrecord, array('context' => $context));
                $lesson = $exporter->export($PAGE->get_renderer('core'));
                $lesson->name = external_format_string($lesson->name, $context);
                $returnedlessons[] = $lesson;
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
                    lesson_summary_exporter::get_read_structure()
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
        $lessonrecord = $DB->get_record('lesson', array('id' => $lessonid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($lessonrecord, 'lesson');

        $lesson = new lesson($lessonrecord, $cm, $course);
        $lesson->update_effective_access($USER->id);

        $context = $lesson->context;
        self::validate_context($context);

        return array($lesson, $course, $cm, $context, $lessonrecord);
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
        global $USER, $CFG;

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
        } else {
            if (!$timers = $lesson->get_user_timers($USER->id, 'starttime DESC', '*', 0, 1)) {
                $error = ["cannotfindtimer" => null];
                if (!$return) {
                    throw new moodle_exception(key($error), 'lesson');
                }
                $errors[key($error)] = current($error);
            } else {
                $timer = current($timers);
                if (!$lesson->check_time($timer)) {
                    $error = ["eolstudentoutoftime" => null];
                    if (!$return) {
                        throw new moodle_exception(key($error), 'lesson');
                    }
                    $errors[key($error)] = current($error);
                }

                // Check if the user want to review an attempt he just finished.
                if (!empty($params['review'])) {
                    // Allow review only for attempts during active session time.
                    if ($timer->lessontime + $CFG->sessiontimeout > time()) {
                        $ntries = $lesson->count_user_retries($USER->id);
                        $ntries--;  // Need to look at the old attempts.
                        if ($params['pageid'] == LESSON_EOL) {
                            if ($attempts = $lesson->get_attempts($ntries)) {
                                $lastattempt = end($attempts);
                                $USER->modattempts[$lesson->id] = $lastattempt->pageid;
                            }
                        } else {
                            if ($attempts = $lesson->get_attempts($ntries, false, $params['pageid'])) {
                                $lastattempt = end($attempts);
                                $USER->modattempts[$lesson->id] = $lastattempt;
                            }
                        }
                    }

                    if (!isset($USER->modattempts[$lesson->id])) {
                        $error = ["studentoutoftimeforreview" => null];
                        if (!$return) {
                            throw new moodle_exception(key($error), 'lesson');
                        }
                        $errors[key($error)] = current($error);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Describes the parameters for get_lesson_access_information.
     *
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

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
     * @return external_function_parameters
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
     * @param string $password optional password (the lesson may be protected)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function view_lesson($lessonid, $password = '') {
        global $DB;

        $params = array('lessonid' => $lessonid, 'password' => $password);
        $params = self::validate_parameters(self::view_lesson_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);
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
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

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
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

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
     * Describes an attempt grade structure.
     *
     * @param  int $required if the structure is required or optional
     * @return external_single_structure the structure
     * @since  Moodle 3.3
     */
    protected static function get_user_attempt_grade_structure($required = VALUE_REQUIRED) {
        $data = array(
            'nquestions' => new external_value(PARAM_INT, 'Number of questions answered'),
            'attempts' => new external_value(PARAM_INT, 'Number of question attempts'),
            'total' => new external_value(PARAM_FLOAT, 'Max points possible'),
            'earned' => new external_value(PARAM_FLOAT, 'Points earned by student'),
            'grade' => new external_value(PARAM_FLOAT, 'Calculated percentage grade'),
            'nmanual' => new external_value(PARAM_INT, 'Number of manually graded questions'),
            'manualpoints' => new external_value(PARAM_FLOAT, 'Point value for manually graded questions'),
        );
        return new external_single_structure(
            $data, 'Attempt grade', $required
        );
    }

    /**
     * Describes the parameters for get_user_attempt_grade.
     *
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        $result = array();
        $result['grade'] = (array) lesson_grade($lesson, $params['lessonattempt'], $params['userid']);
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
                'grade' => self::get_user_attempt_grade_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_content_pages_viewed.
     *
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

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
     * @return external_function_parameters
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

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

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
                            'timemodifiedoffline' => new external_value(PARAM_INT, 'Last modified time via webservices.'),
                        ),
                        'The timers'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the external structure for a lesson page.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    protected static function get_page_structure($required = VALUE_REQUIRED) {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The id of this lesson page'),
                'lessonid' => new external_value(PARAM_INT, 'The id of the lesson this page belongs to'),
                'prevpageid' => new external_value(PARAM_INT, 'The id of the page before this one'),
                'nextpageid' => new external_value(PARAM_INT, 'The id of the next page in the page sequence'),
                'qtype' => new external_value(PARAM_INT, 'Identifies the page type of this page'),
                'qoption' => new external_value(PARAM_INT, 'Used to record page type specific options'),
                'layout' => new external_value(PARAM_INT, 'Used to record page specific layout selections'),
                'display' => new external_value(PARAM_INT, 'Used to record page specific display selections'),
                'timecreated' => new external_value(PARAM_INT, 'Timestamp for when the page was created'),
                'timemodified' => new external_value(PARAM_INT, 'Timestamp for when the page was last modified'),
                'title' => new external_value(PARAM_RAW, 'The title of this page', VALUE_OPTIONAL),
                'contents' => new external_value(PARAM_RAW, 'The contents of this page', VALUE_OPTIONAL),
                'contentsformat' => new external_format_value('contents', VALUE_OPTIONAL),
                'displayinmenublock' => new external_value(PARAM_BOOL, 'Toggles display in the left menu block'),
                'type' => new external_value(PARAM_INT, 'The type of the page [question | structure]'),
                'typeid' => new external_value(PARAM_INT, 'The unique identifier for the page type'),
                'typestring' => new external_value(PARAM_RAW, 'The string that describes this page type'),
            ),
            'Page fields', $required
        );
    }

    /**
     * Returns the fields of a page object
     * @param lesson_page $page the lesson page
     * @param bool $returncontents whether to return the page title and contents
     * @return stdClass          the fields matching the external page structure
     * @since Moodle 3.3
     */
    protected static function get_page_fields(lesson_page $page, $returncontents = false) {
        $lesson = $page->lesson;
        $context = $lesson->context;

        $pagedata = new stdClass; // Contains the data that will be returned by the WS.

        // Return the visible data.
        $visibleproperties = array('id', 'lessonid', 'prevpageid', 'nextpageid', 'qtype', 'qoption', 'layout', 'display',
                                    'displayinmenublock', 'type', 'typeid', 'typestring', 'timecreated', 'timemodified');
        foreach ($visibleproperties as $prop) {
            $pagedata->{$prop} = $page->{$prop};
        }

        // Check if we can see title (contents required custom rendering, we won't returning it here @see get_page_data).
        $canmanage = $lesson->can_manage();
        // If we are managers or the menu block is enabled and is a content page visible always return contents.
        if ($returncontents || $canmanage || (lesson_displayleftif($lesson) && $page->displayinmenublock && $page->display)) {
            $pagedata->title = external_format_string($page->title, $context->id);

            $options = array('noclean' => true);
            list($pagedata->contents, $pagedata->contentsformat) =
                external_format_text($page->contents, $page->contentsformat, $context->id, 'mod_lesson', 'page_contents', $page->id,
                    $options);

        }
        return $pagedata;
    }

    /**
     * Describes the parameters for get_pages.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_pages_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'password' => new external_value(PARAM_RAW, 'optional password (the lesson may be protected)', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Return the list of pages in a lesson (based on the user permissions).
     *
     * @param int $lessonid lesson instance id
     * @param string $password optional password (the lesson may be protected)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_pages($lessonid, $password = '') {

        $params = array('lessonid' => $lessonid, 'password' => $password);
        $params = self::validate_parameters(self::get_pages_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);
        self::validate_attempt($lesson, $params);

        $lessonpages = $lesson->load_all_pages();
        $pages = array();

        foreach ($lessonpages as $page) {
            $pagedata = new stdClass();

            // Get the page object fields.
            $pagedata->page = self::get_page_fields($page);

            // Now, calculate the file area files (maybe we need to download a lesson for offline usage).
            $pagedata->filescount = 0;
            $pagedata->filessizetotal = 0;
            $files = $page->get_files(false);   // Get files excluding directories.
            foreach ($files as $file) {
                $pagedata->filescount++;
                $pagedata->filessizetotal += $file->get_filesize();
            }

            // Now the possible answers and page jumps ids.
            $pagedata->answerids = array();
            $pagedata->jumps = array();
            $answers = $page->get_answers();
            foreach ($answers as $answer) {
                $pagedata->answerids[] = $answer->id;
                $pagedata->jumps[] = $answer->jumpto;
                $files = $answer->get_files(false);   // Get files excluding directories.
                foreach ($files as $file) {
                    $pagedata->filescount++;
                    $pagedata->filessizetotal += $file->get_filesize();
                }
            }
            $pages[] = $pagedata;
        }

        $result = array();
        $result['pages'] = $pages;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_pages return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_pages_returns() {
        return new external_single_structure(
            array(
                'pages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'page' => self::get_page_structure(),
                            'answerids' => new external_multiple_structure(
                                new external_value(PARAM_INT, 'Answer id'), 'List of answers ids (empty for content pages in  Moodle 1.9)'
                            ),
                            'jumps' => new external_multiple_structure(
                                new external_value(PARAM_INT, 'Page to jump id'), 'List of possible page jumps'
                            ),
                            'filescount' => new external_value(PARAM_INT, 'The total number of files attached to the page'),
                            'filessizetotal' => new external_value(PARAM_INT, 'The total size of the files'),
                        ),
                        'The lesson pages'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for launch_attempt.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function launch_attempt_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'password' => new external_value(PARAM_RAW, 'optional password (the lesson may be protected)', VALUE_DEFAULT, ''),
                'pageid' => new external_value(PARAM_INT, 'page id to continue from (only when continuing an attempt)', VALUE_DEFAULT, 0),
                'review' => new external_value(PARAM_BOOL, 'if we want to review just after finishing', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Return lesson messages formatted according the external_messages structure
     *
     * @param  lesson $lesson lesson instance
     * @return array          messages formatted
     * @since Moodle 3.3
     */
    protected static function format_lesson_messages($lesson) {
        $messages = array();
        foreach ($lesson->messages as $message) {
            $messages[] = array(
                'message' => $message[0],
                'type' => $message[1],
            );
        }
        return $messages;
    }

    /**
     * Return a external structure representing messages.
     *
     * @return external_multiple_structure messages structure
     * @since Moodle 3.3
     */
    protected static function external_messages() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'message' => new external_value(PARAM_RAW, 'Message.'),
                    'type' => new external_value(PARAM_ALPHANUMEXT, 'Message type: usually a CSS identifier like:
                                success, info, warning, error, notifyproblem, notifyerror, notifytiny, notifysuccess')
                ), 'The lesson generated messages'
            )
        );
    }

    /**
     * Starts a new attempt or continues an existing one.
     *
     * @param int $lessonid lesson instance id
     * @param string $password optional password (the lesson may be protected)
     * @param int $pageid page id to continue from (only when continuing an attempt)
     * @param bool $review if we want to review just after finishing
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function launch_attempt($lessonid, $password = '', $pageid = 0, $review = false) {
        global $CFG, $USER;

        $params = array('lessonid' => $lessonid, 'password' => $password, 'pageid' => $pageid, 'review' => $review);
        $params = self::validate_parameters(self::launch_attempt_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);
        self::validate_attempt($lesson, $params);

        $newpageid = 0;
        // Starting a new lesson attempt.
        if (empty($params['pageid'])) {
            // Check if there is a recent timer created during the active session.
            $alreadystarted = false;
            if ($timers = $lesson->get_user_timers($USER->id, 'starttime DESC', '*', 0, 1)) {
                $timer = array_shift($timers);
                $endtime = $lesson->timelimit > 0 ? min($CFG->sessiontimeout, $lesson->timelimit) : $CFG->sessiontimeout;
                if (!$timer->completed && $timer->starttime > time() - $endtime) {
                    $alreadystarted = true;
                }
            }
            if (!$alreadystarted && !$lesson->can_manage()) {
                $lesson->start_timer();
            }
        } else {
            if ($params['pageid'] == LESSON_EOL) {
                throw new moodle_exception('endoflesson', 'lesson');
            }
            $timer = $lesson->update_timer(true, true);
            if (!$lesson->check_time($timer)) {
                throw new moodle_exception('eolstudentoutoftime', 'lesson');
            }
        }
        $messages = self::format_lesson_messages($lesson);

        $result = array(
            'status' => true,
            'messages' => $messages,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the launch_attempt return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function launch_attempt_returns() {
        return new external_single_structure(
            array(
                'messages' => self::external_messages(),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_page_data.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_page_data_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'pageid' => new external_value(PARAM_INT, 'the page id'),
                'password' => new external_value(PARAM_RAW, 'optional password (the lesson may be protected)', VALUE_DEFAULT, ''),
                'review' => new external_value(PARAM_BOOL, 'if we want to review just after finishing (1 hour margin)',
                    VALUE_DEFAULT, false),
                'returncontents' => new external_value(PARAM_BOOL, 'if we must return the complete page contents once rendered',
                    VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Return information of a given page, including its contents.
     *
     * @param int $lessonid lesson instance id
     * @param int $pageid page id
     * @param string $password optional password (the lesson may be protected)
     * @param bool $review if we want to review just after finishing (1 hour margin)
     * @param bool $returncontents if we must return the complete page contents once rendered
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_page_data($lessonid, $pageid,  $password = '', $review = false, $returncontents = false) {
        global $PAGE, $USER;

        $params = array('lessonid' => $lessonid, 'password' => $password, 'pageid' => $pageid, 'review' => $review,
            'returncontents' => $returncontents);
        $params = self::validate_parameters(self::get_page_data_parameters(), $params);

        $warnings = $contentfiles = $answerfiles = $responsefiles = $answers = array();
        $pagecontent = $ongoingscore = '';
        $progress = $pagedata = null;

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);
        self::validate_attempt($lesson, $params);

        $pageid = $params['pageid'];

        // This is called if a student leaves during a lesson.
        if ($pageid == LESSON_UNSEENBRANCHPAGE) {
            $pageid = lesson_unseen_question_jump($lesson, $USER->id, $pageid);
        }

        if ($pageid != LESSON_EOL) {
            $reviewmode = $lesson->is_in_review_mode();
            $lessonoutput = $PAGE->get_renderer('mod_lesson');
            // Prepare page contents avoiding redirections.
            list($pageid, $page, $pagecontent) = $lesson->prepare_page_and_contents($pageid, $lessonoutput, $reviewmode, false);

            if ($pageid > 0) {

                $pagedata = self::get_page_fields($page, true);

                // Files.
                $contentfiles = external_util::get_area_files($context->id, 'mod_lesson', 'page_contents', $page->id);

                // Answers.
                $answers = array();
                $pageanswers = $page->get_answers();
                foreach ($pageanswers as $a) {
                    $answer = array(
                        'id' => $a->id,
                        'answerfiles' => external_util::get_area_files($context->id, 'mod_lesson', 'page_answers', $a->id),
                        'responsefiles' => external_util::get_area_files($context->id, 'mod_lesson', 'page_responses', $a->id),
                    );
                    // For managers, return all the information (including correct answers, jumps).
                    // If the teacher enabled offline attempts, this information will be downloaded too.
                    if ($lesson->can_manage() || $lesson->allowofflineattempts) {
                        $extraproperties = array('jumpto', 'grade', 'score', 'flags', 'timecreated', 'timemodified');
                        foreach ($extraproperties as $prop) {
                            $answer[$prop] = $a->{$prop};
                        }

                        $options = array('noclean' => true);
                        list($answer['answer'], $answer['answerformat']) =
                            external_format_text($a->answer, $a->answerformat, $context->id, 'mod_lesson', 'page_answers', $a->id,
                                $options);
                        list($answer['response'], $answer['responseformat']) =
                            external_format_text($a->response, $a->responseformat, $context->id, 'mod_lesson', 'page_responses',
                                $a->id, $options);
                    }
                    $answers[] = $answer;
                }

                // Additional lesson information.
                if (!$lesson->can_manage()) {
                    if ($lesson->ongoing && !$reviewmode) {
                        $ongoingscore = $lesson->get_ongoing_score_message();
                    }
                    if ($lesson->progressbar) {
                        $progress = $lesson->calculate_progress();
                    }
                }
            }
        }

        $messages = self::format_lesson_messages($lesson);

        $result = array(
            'newpageid' => $pageid,
            'ongoingscore' => $ongoingscore,
            'progress' => $progress,
            'contentfiles' => $contentfiles,
            'answers' => $answers,
            'messages' => $messages,
            'warnings' => $warnings,
            'displaymenu' => !empty(lesson_displayleftif($lesson)),
        );

        if (!empty($pagedata)) {
            $result['page'] = $pagedata;
        }
        if ($params['returncontents']) {
            $result['pagecontent'] = $pagecontent;  // Return the complete page contents rendered.
        }

        return $result;
    }

    /**
     * Describes the get_page_data return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_page_data_returns() {
        return new external_single_structure(
            array(
                'page' => self::get_page_structure(VALUE_OPTIONAL),
                'newpageid' => new external_value(PARAM_INT, 'New page id (if a jump was made)'),
                'pagecontent' => new external_value(PARAM_RAW, 'Page html content', VALUE_OPTIONAL),
                'ongoingscore' => new external_value(PARAM_TEXT, 'The ongoing score message'),
                'progress' => new external_value(PARAM_INT, 'Progress percentage in the lesson'),
                'contentfiles' => new external_files(),
                'answers' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The ID of this answer in the database'),
                            'answerfiles' => new external_files(),
                            'responsefiles' => new external_files(),
                            'jumpto' => new external_value(PARAM_INT, 'Identifies where the user goes upon completing a page with this answer',
                                                            VALUE_OPTIONAL),
                            'grade' => new external_value(PARAM_INT, 'The grade this answer is worth', VALUE_OPTIONAL),
                            'score' => new external_value(PARAM_INT, 'The score this answer will give', VALUE_OPTIONAL),
                            'flags' => new external_value(PARAM_INT, 'Used to store options for the answer', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'A timestamp of when the answer was created', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'A timestamp of when the answer was modified', VALUE_OPTIONAL),
                            'answer' => new external_value(PARAM_RAW, 'Possible answer text', VALUE_OPTIONAL),
                            'answerformat' => new external_format_value('answer', VALUE_OPTIONAL),
                            'response' => new external_value(PARAM_RAW, 'Response text for the answer', VALUE_OPTIONAL),
                            'responseformat' => new external_format_value('response', VALUE_OPTIONAL),
                        ), 'The page answers'

                    )
                ),
                'messages' => self::external_messages(),
                'displaymenu' => new external_value(PARAM_BOOL, 'Whether we should display the menu or not in this page.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for process_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function process_page_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'pageid' => new external_value(PARAM_INT, 'the page id'),
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'data name'),
                            'value' => new external_value(PARAM_RAW, 'data value'),
                        )
                    ), 'the data to be saved'
                ),
                'password' => new external_value(PARAM_RAW, 'optional password (the lesson may be protected)', VALUE_DEFAULT, ''),
                'review' => new external_value(PARAM_BOOL, 'if we want to review just after finishing (1 hour margin)',
                    VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Processes page responses
     *
     * @param int $lessonid lesson instance id
     * @param int $pageid page id
     * @param array $data the data to be saved
     * @param string $password optional password (the lesson may be protected)
     * @param bool $review if we want to review just after finishing (1 hour margin)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function process_page($lessonid, $pageid,  $data, $password = '', $review = false) {
        global $USER;

        $params = array('lessonid' => $lessonid, 'pageid' => $pageid, 'data' => $data, 'password' => $password,
            'review' => $review);
        $params = self::validate_parameters(self::process_page_parameters(), $params);

        $warnings = array();
        $pagecontent = $ongoingscore = '';
        $progress = null;

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

        // Update timer so the validation can check the time restrictions.
        $timer = $lesson->update_timer();
        self::validate_attempt($lesson, $params);

        // Create the $_POST object required by the lesson question engine.
        $_POST = array();
        foreach ($data as $element) {
            // First check if we are handling editor fields like answer[text].
            if (preg_match('/(.+)\[(.+)\]$/', $element['name'], $matches)) {
                $_POST[$matches[1]][$matches[2]] = $element['value'];
            } else {
                $_POST[$element['name']] = $element['value'];
            }
        }

        // Ignore sesskey (deep in some APIs), the request is already validated.
        $USER->ignoresesskey = true;

        // Process page.
        $page = $lesson->load_page($params['pageid']);
        $result = $lesson->process_page_responses($page);

        // Prepare messages.
        $reviewmode = $lesson->is_in_review_mode();
        $lesson->add_messages_on_page_process($page, $result, $reviewmode);

        // Additional lesson information.
        if (!$lesson->can_manage()) {
            if ($lesson->ongoing && !$reviewmode) {
                $ongoingscore = $lesson->get_ongoing_score_message();
            }
            if ($lesson->progressbar) {
                $progress = $lesson->calculate_progress();
            }
        }

        // Check conditionally everything coming from result (except newpageid because is always set).
        $result = array(
            'newpageid'         => (int) $result->newpageid,
            'inmediatejump'     => $result->inmediatejump,
            'nodefaultresponse' => !empty($result->nodefaultresponse),
            'feedback'          => (isset($result->feedback)) ? $result->feedback : '',
            'attemptsremaining' => (isset($result->attemptsremaining)) ? $result->attemptsremaining : null,
            'correctanswer'     => !empty($result->correctanswer),
            'noanswer'          => !empty($result->noanswer),
            'isessayquestion'   => !empty($result->isessayquestion),
            'maxattemptsreached' => !empty($result->maxattemptsreached),
            'response'          => (isset($result->response)) ? $result->response : '',
            'studentanswer'     => (isset($result->studentanswer)) ? $result->studentanswer : '',
            'userresponse'      => (isset($result->userresponse)) ? $result->userresponse : '',
            'reviewmode'        => $reviewmode,
            'ongoingscore'      => $ongoingscore,
            'progress'          => $progress,
            'displaymenu'       => !empty(lesson_displayleftif($lesson)),
            'messages'          => self::format_lesson_messages($lesson),
            'warnings'          => $warnings,
        );
        return $result;
    }

    /**
     * Describes the process_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function process_page_returns() {
        return new external_single_structure(
            array(
                'newpageid' => new external_value(PARAM_INT, 'New page id (if a jump was made).'),
                'inmediatejump' => new external_value(PARAM_BOOL, 'Whether the page processing redirect directly to anoter page.'),
                'nodefaultresponse' => new external_value(PARAM_BOOL, 'Whether there is not a default response.'),
                'feedback' => new external_value(PARAM_RAW, 'The response feedback.'),
                'attemptsremaining' => new external_value(PARAM_INT, 'Number of attempts remaining.'),
                'correctanswer' => new external_value(PARAM_BOOL, 'Whether the answer is correct.'),
                'noanswer' => new external_value(PARAM_BOOL, 'Whether there aren\'t answers.'),
                'isessayquestion' => new external_value(PARAM_BOOL, 'Whether is a essay question.'),
                'maxattemptsreached' => new external_value(PARAM_BOOL, 'Whether we reachered the max number of attempts.'),
                'response' => new external_value(PARAM_RAW, 'The response.'),
                'studentanswer' => new external_value(PARAM_RAW, 'The student answer.'),
                'userresponse' => new external_value(PARAM_RAW, 'The user response.'),
                'reviewmode' => new external_value(PARAM_BOOL, 'Whether the user is reviewing.'),
                'ongoingscore' => new external_value(PARAM_TEXT, 'The ongoing message.'),
                'progress' => new external_value(PARAM_INT, 'Progress percentage in the lesson.'),
                'displaymenu' => new external_value(PARAM_BOOL, 'Whether we should display the menu or not in this page.'),
                'messages' => self::external_messages(),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for finish_attempt.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function finish_attempt_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'Lesson instance id.'),
                'password' => new external_value(PARAM_RAW, 'Optional password (the lesson may be protected).', VALUE_DEFAULT, ''),
                'outoftime' => new external_value(PARAM_BOOL, 'If the user run out of time.', VALUE_DEFAULT, false),
                'review' => new external_value(PARAM_BOOL, 'If we want to review just after finishing (1 hour margin).',
                    VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Finishes the current attempt.
     *
     * @param int $lessonid lesson instance id
     * @param string $password optional password (the lesson may be protected)
     * @param bool $outoftime optional if the user run out of time
     * @param bool $review if we want to review just after finishing (1 hour margin)
     * @return array of warnings and information about the finished attempt
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function finish_attempt($lessonid, $password = '', $outoftime = false, $review = false) {

        $params = array('lessonid' => $lessonid, 'password' => $password, 'outoftime' => $outoftime, 'review' => $review);
        $params = self::validate_parameters(self::finish_attempt_parameters(), $params);

        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

        // Update timer so the validation can check the time restrictions.
        $timer = $lesson->update_timer();

        // Return the validation to avoid exceptions in case the user is out of time.
        $params['pageid'] = LESSON_EOL;
        $validation = self::validate_attempt($lesson, $params, true);

        if (array_key_exists('eolstudentoutoftime', $validation)) {
            // Maybe we run out of time just now.
            $params['outoftime'] = true;
            unset($validation['eolstudentoutoftime']);
        }
        // Check if there are more errors.
        if (!empty($validation)) {
            reset($validation);
            throw new moodle_exception(key($validation), 'lesson', '', current($validation));   // Throw first error.
        }

        // Set out of time to normal (it is the only existing mode).
        $outoftimemode = $params['outoftime'] ? 'normal' : '';
        $result = $lesson->process_eol_page($outoftimemode);

        // Return the data.
         $validmessages = array(
            'notenoughtimespent', 'numberofpagesviewed', 'youshouldview', 'numberofcorrectanswers',
            'displayscorewithessays', 'displayscorewithoutessays', 'yourcurrentgradeisoutof', 'eolstudentoutoftimenoanswers',
            'welldone', 'displayofgrade', 'modattemptsnoteacher', 'progresscompleted');

        $data = array();
        foreach ($result as $el => $value) {
            if ($value !== false) {
                $message = '';
                if (in_array($el, $validmessages)) { // Check if the data comes with an informative message.
                    $a = (is_bool($value)) ? null : $value;
                    $message = get_string($el, 'lesson', $a);
                }
                // Return the data.
                $data[] = array(
                    'name' => $el,
                    'value' => (is_bool($value)) ? 1 : json_encode($value), // The data can be a php object.
                    'message' => $message
                );
            }
        }

        $result = array(
            'data'     => $data,
            'messages' => self::format_lesson_messages($lesson),
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the finish_attempt return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function finish_attempt_returns() {
        return new external_single_structure(
            array(
                'data' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'Data name.'),
                            'value' => new external_value(PARAM_RAW, 'Data value.'),
                            'message' => new external_value(PARAM_RAW, 'Data message (translated string).'),
                        )
                    ), 'The EOL page information data.'
                ),
                'messages' => self::external_messages(),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_attempts_overview.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_attempts_overview_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'groupid' => new external_value(PARAM_INT, 'group id, 0 means that the function will determine the user group',
                                                VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get a list of all the attempts made by users in a lesson.
     *
     * @param int $lessonid lesson instance id
     * @param int $groupid group id, 0 means that the function will determine the user group
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_attempts_overview($lessonid, $groupid = 0) {

        $params = array('lessonid' => $lessonid, 'groupid' => $groupid);
        $params = self::validate_parameters(self::get_attempts_overview_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);
        require_capability('mod/lesson:viewreports', $context);

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                $groupid = groups_get_activity_group($cm);
                // Determine is the group is visible to user (this is particullary for the group 0 -> all groups).
                if (!groups_group_visible($groupid, $course, $cm)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }

        $result = array(
            'warnings' => $warnings
        );

        list($table, $data) = lesson_get_overview_report_table_and_data($lesson, $groupid);
        if ($data !== false) {
            $result['data'] = $data;
        }

        return $result;
    }

    /**
     * Describes the get_attempts_overview return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_attempts_overview_returns() {
        return new external_single_structure(
            array(
                'data' => new external_single_structure(
                    array(
                        'lessonscored' => new external_value(PARAM_BOOL, 'True if the lesson was scored.'),
                        'numofattempts' => new external_value(PARAM_INT, 'Number of attempts.'),
                        'avescore' => new external_value(PARAM_FLOAT, 'Average score.'),
                        'highscore' => new external_value(PARAM_FLOAT, 'High score.'),
                        'lowscore' => new external_value(PARAM_FLOAT, 'Low score.'),
                        'avetime' => new external_value(PARAM_INT, 'Average time (spent in taking the lesson).'),
                        'hightime' => new external_value(PARAM_INT, 'High time.'),
                        'lowtime' => new external_value(PARAM_INT, 'Low time.'),
                        'students' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_INT, 'User id.'),
                                    'fullname' => new external_value(PARAM_TEXT, 'User full name.'),
                                    'bestgrade' => new external_value(PARAM_FLOAT, 'Best grade.'),
                                    'attempts' => new external_multiple_structure(
                                        new external_single_structure(
                                            array(
                                                'try' => new external_value(PARAM_INT, 'Attempt number.'),
                                                'grade' => new external_value(PARAM_FLOAT, 'Attempt grade.'),
                                                'timestart' => new external_value(PARAM_INT, 'Attempt time started.'),
                                                'timeend' => new external_value(PARAM_INT, 'Attempt last time continued.'),
                                                'end' => new external_value(PARAM_INT, 'Attempt time ended.'),
                                            )
                                        )
                                    )
                                )
                            ), 'Students data, including attempts.', VALUE_OPTIONAL
                        ),
                    ),
                    'Attempts overview data (empty for no attemps).', VALUE_OPTIONAL
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_attempt.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_user_attempt_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'Lesson instance id.'),
                'userid' => new external_value(PARAM_INT, 'The user id. 0 for current user.'),
                'lessonattempt' => new external_value(PARAM_INT, 'The attempt number.'),
            )
        );
    }

    /**
     * Return information about the given user attempt (including answers).
     *
     * @param int $lessonid lesson instance id
     * @param int $userid the user id
     * @param int $lessonattempt the attempt number
     * @return array of warnings and page attempts
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_user_attempt($lessonid, $userid, $lessonattempt) {
        global $USER;

        $params = array(
            'lessonid' => $lessonid,
            'userid' => $userid,
            'lessonattempt' => $lessonattempt,
        );
        $params = self::validate_parameters(self::get_user_attempt_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $params['userid']) {
            self::check_can_view_user_data($params['userid'], $course, $cm, $context);
        }

        list($answerpages, $userstats) = lesson_get_user_detailed_report_data($lesson, $userid, $params['lessonattempt']);
        // Convert page object to page record.
        foreach ($answerpages as $answerp) {
            $answerp->page = self::get_page_fields($answerp->page);
        }

        $result = array(
            'answerpages' => $answerpages,
            'userstats' => $userstats,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the get_user_attempt return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_user_attempt_returns() {
        return new external_single_structure(
            array(
                'answerpages' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'page' => self::get_page_structure(VALUE_OPTIONAL),
                            'title' => new external_value(PARAM_RAW, 'Page title.'),
                            'contents' => new external_value(PARAM_RAW, 'Page contents.'),
                            'qtype' => new external_value(PARAM_TEXT, 'Identifies the page type of this page.'),
                            'grayout' => new external_value(PARAM_INT, 'If is required to apply a grayout.'),
                            'answerdata' => new external_single_structure(
                                array(
                                    'score' => new external_value(PARAM_TEXT, 'The score (text version).'),
                                    'response' => new external_value(PARAM_RAW, 'The response text.'),
                                    'responseformat' => new external_format_value('response.'),
                                    'answers' => new external_multiple_structure(
                                        new external_multiple_structure(new external_value(PARAM_RAW, 'Possible answers and info.')),
                                        'User answers',
                                        VALUE_OPTIONAL
                                    ),
                                ), 'Answer data (empty in content pages created in Moodle 1.x).', VALUE_OPTIONAL
                            )
                        )
                    )
                ),
                'userstats' => new external_single_structure(
                    array(
                        'grade' => new external_value(PARAM_FLOAT, 'Attempt final grade.'),
                        'completed' => new external_value(PARAM_INT, 'Time completed.'),
                        'timetotake' => new external_value(PARAM_INT, 'Time taken.'),
                        'gradeinfo' => self::get_user_attempt_grade_structure(VALUE_OPTIONAL)
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_pages_possible_jumps.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_pages_possible_jumps_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
            )
        );
    }

    /**
     * Return all the possible jumps for the pages in a given lesson.
     *
     * You may expect different results on consecutive executions due to the random nature of the lesson module.
     *
     * @param int $lessonid lesson instance id
     * @return array of warnings and possible jumps
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_pages_possible_jumps($lessonid) {
        global $USER;

        $params = array('lessonid' => $lessonid);
        $params = self::validate_parameters(self::get_pages_possible_jumps_parameters(), $params);

        $warnings = $jumps = array();

        list($lesson, $course, $cm, $context) = self::validate_lesson($params['lessonid']);

        // Only return for managers or if offline attempts are enabled.
        if ($lesson->can_manage() || $lesson->allowofflineattempts) {

            $lessonpages = $lesson->load_all_pages();
            foreach ($lessonpages as $page) {
                $jump = array();
                $jump['pageid'] = $page->id;

                $answers = $page->get_answers();
                if (count($answers) > 0) {
                    foreach ($answers as $answer) {
                        $jump['answerid'] = $answer->id;
                        $jump['jumpto'] = $answer->jumpto;
                        $jump['calculatedjump'] = $lesson->calculate_new_page_on_jump($page, $answer->jumpto);
                        // Special case, only applies to branch/end of branch.
                        if ($jump['calculatedjump'] == LESSON_RANDOMBRANCH) {
                            $jump['calculatedjump'] = lesson_unseen_branch_jump($lesson, $USER->id);
                        }
                        $jumps[] = $jump;
                    }
                } else {
                    // Imported lessons from 1.x.
                    $jump['answerid'] = 0;
                    $jump['jumpto'] = $page->nextpageid;
                    $jump['calculatedjump'] = $lesson->calculate_new_page_on_jump($page, $page->nextpageid);
                    $jumps[] = $jump;
                }
            }
        }

        $result = array(
            'jumps' => $jumps,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the get_pages_possible_jumps return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_pages_possible_jumps_returns() {
        return new external_single_structure(
            array(
                'jumps' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'pageid' => new external_value(PARAM_INT, 'The page id'),
                            'answerid' => new external_value(PARAM_INT, 'The answer id'),
                            'jumpto' => new external_value(PARAM_INT, 'The jump (page id or type of jump)'),
                            'calculatedjump' => new external_value(PARAM_INT, 'The real page id (or EOL) to jump'),
                        ), 'Jump for a page answer'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_lesson.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_lesson_parameters() {
        return new external_function_parameters (
            array(
                'lessonid' => new external_value(PARAM_INT, 'lesson instance id'),
                'password' => new external_value(PARAM_RAW, 'lesson password', VALUE_DEFAULT, ''),
            )
        );
    }

    /**
     * Return information of a given lesson.
     *
     * @param int $lessonid lesson instance id
     * @param string $password optional password (the lesson may be protected)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_lesson($lessonid, $password = '') {
        global $PAGE;

        $params = array('lessonid' => $lessonid, 'password' => $password);
        $params = self::validate_parameters(self::get_lesson_parameters(), $params);
        $warnings = array();

        list($lesson, $course, $cm, $context, $lessonrecord) = self::validate_lesson($params['lessonid']);

        $lessonrecord = self::get_lesson_summary_for_exporter($lessonrecord, $params['password']);
        $exporter = new lesson_summary_exporter($lessonrecord, array('context' => $context));

        $result = array();
        $result['lesson'] = $exporter->export($PAGE->get_renderer('core'));
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_lesson return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_lesson_returns() {
        return new external_single_structure(
            array(
                'lesson' => lesson_summary_exporter::get_read_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }
}
