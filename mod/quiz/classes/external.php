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
 * Quiz external API
 *
 * @package    mod_quiz
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Quiz external functions
 *
 * @package    mod_quiz
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_quiz_external extends external_api {

    /**
     * Describes the parameters for get_quizzes_by_courses.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_quizzes_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of quizzes in a provided list of courses,
     * if no list is provided all quizzes that the user can view will be returned.
     *
     * @param array $courseids Array of course ids
     * @return array of quizzes details
     * @since Moodle 3.1
     */
    public static function get_quizzes_by_courses($courseids = array()) {
        global $USER;

        $warnings = array();
        $returnedquizzes = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_quizzes_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the quizzes in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $quizzes = get_all_instances_in_courses("quiz", $courses);
            foreach ($quizzes as $quiz) {
                $context = context_module::instance($quiz->coursemodule);

                // Update quiz with override information.
                $quiz = quiz_update_effective_access($quiz, $USER->id);

                // Entry to return.
                $quizdetails = array();
                // First, we return information that any user can see in the web interface.
                $quizdetails['id'] = $quiz->id;
                $quizdetails['coursemodule']      = $quiz->coursemodule;
                $quizdetails['course']            = $quiz->course;
                $quizdetails['name']              = external_format_string($quiz->name, $context->id);

                if (has_capability('mod/quiz:view', $context)) {
                    // Format intro.
                    list($quizdetails['intro'], $quizdetails['introformat']) = external_format_text($quiz->intro,
                                                                    $quiz->introformat, $context->id, 'mod_quiz', 'intro', null);

                    $viewablefields = array('timeopen', 'timeclose', 'grademethod', 'section', 'visible', 'groupmode',
                                            'groupingid');

                    $timenow = time();
                    $quizobj = quiz::create($quiz->id, $USER->id);
                    $accessmanager = new quiz_access_manager($quizobj, $timenow, has_capability('mod/quiz:ignoretimelimits',
                                                                $context, null, false));

                    // Fields the user could see if have access to the quiz.
                    if (!$accessmanager->prevent_access()) {
                        // Some times this function returns just empty.
                        $hasfeedback = quiz_has_feedback($quiz);
                        $quizdetails['hasfeedback'] = (!empty($hasfeedback)) ? 1 : 0;
                        $quizdetails['hasquestions'] = (int) $quizobj->has_questions();
                        $quizdetails['autosaveperiod'] = get_config('quiz', 'autosaveperiod');

                        $additionalfields = array('timelimit', 'attempts', 'attemptonlast', 'grademethod', 'decimalpoints',
                                                    'questiondecimalpoints', 'reviewattempt', 'reviewcorrectness', 'reviewmarks',
                                                    'reviewspecificfeedback', 'reviewgeneralfeedback', 'reviewrightanswer',
                                                    'reviewoverallfeedback', 'questionsperpage', 'navmethod', 'sumgrades', 'grade',
                                                    'browsersecurity', 'delay1', 'delay2', 'showuserpicture', 'showblocks',
                                                    'completionattemptsexhausted', 'completionpass', 'overduehandling',
                                                    'graceperiod', 'preferredbehaviour', 'canredoquestions');
                        $viewablefields = array_merge($viewablefields, $additionalfields);
                    }

                    // Fields only for managers.
                    if (has_capability('moodle/course:manageactivities', $context)) {
                        $additionalfields = array('shuffleanswers', 'timecreated', 'timemodified', 'password', 'subnet');
                        $viewablefields = array_merge($viewablefields, $additionalfields);
                    }

                    foreach ($viewablefields as $field) {
                        $quizdetails[$field] = $quiz->{$field};
                    }
                }
                $returnedquizzes[] = $quizdetails;
            }
        }
        $result = array();
        $result['quizzes'] = $returnedquizzes;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_quizzes_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_quizzes_by_courses_returns() {
        return new external_single_structure(
            array(
                'quizzes' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Standard Moodle primary key.'),
                            'course' => new external_value(PARAM_INT, 'Foreign key reference to the course this quiz is part of.'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id.'),
                            'name' => new external_value(PARAM_RAW, 'Quiz name.'),
                            'intro' => new external_value(PARAM_RAW, 'Quiz introduction text.', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                            'timeopen' => new external_value(PARAM_INT, 'The time when this quiz opens. (0 = no restriction.)',
                                                                VALUE_OPTIONAL),
                            'timeclose' => new external_value(PARAM_INT, 'The time when this quiz closes. (0 = no restriction.)',
                                                                VALUE_OPTIONAL),
                            'timelimit' => new external_value(PARAM_INT, 'The time limit for quiz attempts, in seconds.',
                                                                VALUE_OPTIONAL),
                            'overduehandling' => new external_value(PARAM_ALPHA, 'The method used to handle overdue attempts.
                                                                    \'autosubmit\', \'graceperiod\' or \'autoabandon\'.',
                                                                    VALUE_OPTIONAL),
                            'graceperiod' => new external_value(PARAM_INT, 'The amount of time (in seconds) after the time limit
                                                                runs out during which attempts can still be submitted,
                                                                if overduehandling is set to allow it.', VALUE_OPTIONAL),
                            'preferredbehaviour' => new external_value(PARAM_ALPHANUMEXT, 'The behaviour to ask questions to use.',
                                                                        VALUE_OPTIONAL),
                            'canredoquestions' => new external_value(PARAM_INT, 'Allows students to redo any completed question
                                                                        within a quiz attempt.', VALUE_OPTIONAL),
                            'attempts' => new external_value(PARAM_INT, 'The maximum number of attempts a student is allowed.',
                                                                VALUE_OPTIONAL),
                            'attemptonlast' => new external_value(PARAM_INT, 'Whether subsequent attempts start from the answer
                                                                    to the previous attempt (1) or start blank (0).',
                                                                    VALUE_OPTIONAL),
                            'grademethod' => new external_value(PARAM_INT, 'One of the values QUIZ_GRADEHIGHEST, QUIZ_GRADEAVERAGE,
                                                                    QUIZ_ATTEMPTFIRST or QUIZ_ATTEMPTLAST.', VALUE_OPTIONAL),
                            'decimalpoints' => new external_value(PARAM_INT, 'Number of decimal points to use when displaying
                                                                    grades.', VALUE_OPTIONAL),
                            'questiondecimalpoints' => new external_value(PARAM_INT, 'Number of decimal points to use when
                                                                            displaying question grades.
                                                                            (-1 means use decimalpoints.)', VALUE_OPTIONAL),
                            'reviewattempt' => new external_value(PARAM_INT, 'Whether users are allowed to review their quiz
                                                                    attempts at various times. This is a bit field, decoded by the
                                                                    mod_quiz_display_options class. It is formed by ORing together
                                                                    the constants defined there.', VALUE_OPTIONAL),
                            'reviewcorrectness' => new external_value(PARAM_INT, 'Whether users are allowed to review their quiz
                                                                        attempts at various times.
                                                                        A bit field, like reviewattempt.', VALUE_OPTIONAL),
                            'reviewmarks' => new external_value(PARAM_INT, 'Whether users are allowed to review their quiz attempts
                                                                at various times. A bit field, like reviewattempt.',
                                                                VALUE_OPTIONAL),
                            'reviewspecificfeedback' => new external_value(PARAM_INT, 'Whether users are allowed to review their
                                                                            quiz attempts at various times. A bit field, like
                                                                            reviewattempt.', VALUE_OPTIONAL),
                            'reviewgeneralfeedback' => new external_value(PARAM_INT, 'Whether users are allowed to review their
                                                                            quiz attempts at various times. A bit field, like
                                                                            reviewattempt.', VALUE_OPTIONAL),
                            'reviewrightanswer' => new external_value(PARAM_INT, 'Whether users are allowed to review their quiz
                                                                        attempts at various times. A bit field, like
                                                                        reviewattempt.', VALUE_OPTIONAL),
                            'reviewoverallfeedback' => new external_value(PARAM_INT, 'Whether users are allowed to review their quiz
                                                                            attempts at various times. A bit field, like
                                                                            reviewattempt.', VALUE_OPTIONAL),
                            'questionsperpage' => new external_value(PARAM_INT, 'How often to insert a page break when editing
                                                                        the quiz, or when shuffling the question order.',
                                                                        VALUE_OPTIONAL),
                            'navmethod' => new external_value(PARAM_ALPHA, 'Any constraints on how the user is allowed to navigate
                                                                around the quiz. Currently recognised values are
                                                                \'free\' and \'seq\'.', VALUE_OPTIONAL),
                            'shuffleanswers' => new external_value(PARAM_INT, 'Whether the parts of the question should be shuffled,
                                                                    in those question types that support it.', VALUE_OPTIONAL),
                            'sumgrades' => new external_value(PARAM_FLOAT, 'The total of all the question instance maxmarks.',
                                                                VALUE_OPTIONAL),
                            'grade' => new external_value(PARAM_FLOAT, 'The total that the quiz overall grade is scaled to be
                                                            out of.', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'The time when the quiz was added to the course.',
                                                                VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Last modified time.',
                                                                    VALUE_OPTIONAL),
                            'password' => new external_value(PARAM_RAW, 'A password that the student must enter before starting or
                                                                continuing a quiz attempt.', VALUE_OPTIONAL),
                            'subnet' => new external_value(PARAM_RAW, 'Used to restrict the IP addresses from which this quiz can
                                                            be attempted. The format is as requried by the address_in_subnet
                                                            function.', VALUE_OPTIONAL),
                            'browsersecurity' => new external_value(PARAM_ALPHANUMEXT, 'Restriciton on the browser the student must
                                                                    use. E.g. \'securewindow\'.', VALUE_OPTIONAL),
                            'delay1' => new external_value(PARAM_INT, 'Delay that must be left between the first and second attempt,
                                                            in seconds.', VALUE_OPTIONAL),
                            'delay2' => new external_value(PARAM_INT, 'Delay that must be left between the second and subsequent
                                                            attempt, in seconds.', VALUE_OPTIONAL),
                            'showuserpicture' => new external_value(PARAM_INT, 'Option to show the user\'s picture during the
                                                                    attempt and on the review page.', VALUE_OPTIONAL),
                            'showblocks' => new external_value(PARAM_INT, 'Whether blocks should be shown on the attempt.php and
                                                                review.php pages.', VALUE_OPTIONAL),
                            'completionattemptsexhausted' => new external_value(PARAM_INT, 'Mark quiz complete when the student has
                                                                                exhausted the maximum number of attempts',
                                                                                VALUE_OPTIONAL),
                            'completionpass' => new external_value(PARAM_INT, 'Whether to require passing grade', VALUE_OPTIONAL),
                            'autosaveperiod' => new external_value(PARAM_INT, 'Auto-save delay', VALUE_OPTIONAL),
                            'hasfeedback' => new external_value(PARAM_INT, 'Whether the quiz has any non-blank feedback text',
                                                                VALUE_OPTIONAL),
                            'hasquestions' => new external_value(PARAM_INT, 'Whether the quiz has questions', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'Module visibility', VALUE_OPTIONAL),
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
     * Describes the parameters for view_quiz.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_quiz_parameters() {
        return new external_function_parameters (
            array(
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $quizid quiz instance id
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function view_quiz($quizid) {
        global $DB;

        $params = self::validate_parameters(self::view_quiz_parameters(), array('quizid' => $quizid));
        $warnings = array();

        // Request and permission validation.
        $quiz = $DB->get_record('quiz', array('id' => $params['quizid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($quiz, 'quiz');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Trigger course_module_viewed event and completion.
        quiz_view($quiz, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the view_quiz return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function view_quiz_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_attempts.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_user_attempts_parameters() {
        return new external_function_parameters (
            array(
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
                'userid' => new external_value(PARAM_INT, 'user id, empty for current user', VALUE_DEFAULT, 0),
                'status' => new external_value(PARAM_ALPHA, 'quiz status: all, finished or unfinished', VALUE_DEFAULT, 'finished'),
                'includepreviews' => new external_value(PARAM_BOOL, 'whether to include previews or not', VALUE_DEFAULT, false),

            )
        );
    }

    /**
     * Return a list of attempts for the given quiz and user.
     *
     * @param int $quizid quiz instance id
     * @param int $userid user id
     * @param string $status quiz status: all, finished or unfinished
     * @param bool $includepreviews whether to include previews or not
     * @return array of warnings and the list of attempts
     * @since Moodle 3.1
     * @throws invalid_parameter_exception
     */
    public static function get_user_attempts($quizid, $userid = 0, $status = 'finished', $includepreviews = false) {
        global $DB, $USER;

        $warnings = array();

        $params = array(
            'quizid' => $quizid,
            'userid' => $userid,
            'status' => $status,
            'includepreviews' => $includepreviews,
        );
        $params = self::validate_parameters(self::get_user_attempts_parameters(), $params);

        // Request and permission validation.
        $quiz = $DB->get_record('quiz', array('id' => $params['quizid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($quiz, 'quiz');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        if (!in_array($params['status'], array('all', 'finished', 'unfinished'))) {
            throw new invalid_parameter_exception('Invalid status value');
        }

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $user->id) {
            require_capability('mod/quiz:viewreports', $context);
        }

        $attempts = quiz_get_user_attempts($quiz->id, $user->id, $params['status'], $params['includepreviews']);

        $result = array();
        $result['attempts'] = $attempts;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes a single attempt structure.
     *
     * @return external_single_structure the attempt structure
     */
    private static function attempt_structure() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Attempt id.', VALUE_OPTIONAL),
                'quiz' => new external_value(PARAM_INT, 'Foreign key reference to the quiz that was attempted.',
                                                VALUE_OPTIONAL),
                'userid' => new external_value(PARAM_INT, 'Foreign key reference to the user whose attempt this is.',
                                                VALUE_OPTIONAL),
                'attempt' => new external_value(PARAM_INT, 'Sequentially numbers this students attempts at this quiz.',
                                                VALUE_OPTIONAL),
                'uniqueid' => new external_value(PARAM_INT, 'Foreign key reference to the question_usage that holds the
                                                    details of the the question_attempts that make up this quiz
                                                    attempt.', VALUE_OPTIONAL),
                'layout' => new external_value(PARAM_RAW, 'Attempt layout.', VALUE_OPTIONAL),
                'currentpage' => new external_value(PARAM_INT, 'Attempt current page.', VALUE_OPTIONAL),
                'preview' => new external_value(PARAM_INT, 'Whether is a preview attempt or not.', VALUE_OPTIONAL),
                'state' => new external_value(PARAM_ALPHA, 'The current state of the attempts. \'inprogress\',
                                                \'overdue\', \'finished\' or \'abandoned\'.', VALUE_OPTIONAL),
                'timestart' => new external_value(PARAM_INT, 'Time when the attempt was started.', VALUE_OPTIONAL),
                'timefinish' => new external_value(PARAM_INT, 'Time when the attempt was submitted.
                                                    0 if the attempt has not been submitted yet.', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Last modified time.', VALUE_OPTIONAL),
                'timecheckstate' => new external_value(PARAM_INT, 'Next time quiz cron should check attempt for
                                                        state changes.  NULL means never check.', VALUE_OPTIONAL),
                'sumgrades' => new external_value(PARAM_FLOAT, 'Total marks for this attempt.', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Describes the get_user_attempts return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_user_attempts_returns() {
        return new external_single_structure(
            array(
                'attempts' => new external_multiple_structure(self::attempt_structure()),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_user_best_grade.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_user_best_grade_parameters() {
        return new external_function_parameters (
            array(
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
                'userid' => new external_value(PARAM_INT, 'user id', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get the best current grade for the given user on a quiz.
     *
     * @param int $quizid quiz instance id
     * @param int $userid user id
     * @return array of warnings and the grade information
     * @since Moodle 3.1
     */
    public static function get_user_best_grade($quizid, $userid = 0) {
        global $DB, $USER;

        $warnings = array();

        $params = array(
            'quizid' => $quizid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_best_grade_parameters(), $params);

        // Request and permission validation.
        $quiz = $DB->get_record('quiz', array('id' => $params['quizid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($quiz, 'quiz');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $user->id) {
            require_capability('mod/quiz:viewreports', $context);
        }

        $result = array();
        $grade = quiz_get_best_grade($quiz, $user->id);

        if ($grade === null) {
            $result['hasgrade'] = false;
        } else {
            $result['hasgrade'] = true;
            $result['grade'] = $grade;
        }
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_user_best_grade return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_user_best_grade_returns() {
        return new external_single_structure(
            array(
                'hasgrade' => new external_value(PARAM_BOOL, 'Whether the user has a grade on the given quiz.'),
                'grade' => new external_value(PARAM_FLOAT, 'The grade (only if the user has a grade).', VALUE_OPTIONAL),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_combined_review_options.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_combined_review_options_parameters() {
        return new external_function_parameters (
            array(
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
                'userid' => new external_value(PARAM_INT, 'user id (empty for current user)', VALUE_DEFAULT, 0),

            )
        );
    }

    /**
     * Combines the review options from a number of different quiz attempts.
     *
     * @param int $quizid quiz instance id
     * @param int $userid user id (empty for current user)
     * @return array of warnings and the review options
     * @since Moodle 3.1
     */
    public static function get_combined_review_options($quizid, $userid = 0) {
        global $DB, $USER;

        $warnings = array();

        $params = array(
            'quizid' => $quizid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_combined_review_options_parameters(), $params);

        // Request and permission validation.
        $quiz = $DB->get_record('quiz', array('id' => $params['quizid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($quiz, 'quiz');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        // Extra checks so only users with permissions can view other users attempts.
        if ($USER->id != $user->id) {
            require_capability('mod/quiz:viewreports', $context);
        }

        $attempts = quiz_get_user_attempts($quiz->id, $user->id, 'all', true);

        $result = array();
        $result['someoptions'] = [];
        $result['alloptions'] = [];

        list($someoptions, $alloptions) = quiz_get_combined_reviewoptions($quiz, $attempts);

        foreach (array('someoptions', 'alloptions') as $typeofoption) {
            foreach ($$typeofoption as $key => $value) {
                $result[$typeofoption][] = array(
                    "name" => $key,
                    "value" => (!empty($value)) ? $value : 0
                );
            }
        }

        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_combined_review_options return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_combined_review_options_returns() {
        return new external_single_structure(
            array(
                'someoptions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'option name'),
                            'value' => new external_value(PARAM_INT, 'option value'),
                        )
                    )
                ),
                'alloptions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'option name'),
                            'value' => new external_value(PARAM_INT, 'option value'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for start_attempt.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function start_attempt_parameters() {
        return new external_function_parameters (
            array(
                'quizid' => new external_value(PARAM_INT, 'quiz instance id'),
                'preflightdata' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUMEXT, 'data name'),
                            'value' => new external_value(PARAM_RAW, 'data value'),
                        )
                    ), 'Preflight required data (like passwords)', VALUE_DEFAULT, array()
                ),
                'forcenew' => new external_value(PARAM_BOOL, 'Whether to force a new attempt or not.', VALUE_DEFAULT, false),

            )
        );
    }

    /**
     * Starts a new attempt at a quiz.
     *
     * @param int $quizid quiz instance id
     * @param array $preflightdata preflight required data (like passwords)
     * @param bool $forcenew Whether to force a new attempt or not.
     * @return array of warnings and the attempt basic data
     * @since Moodle 3.1
     * @throws moodle_quiz_exception
     */
    public static function start_attempt($quizid, $preflightdata = array(), $forcenew = false) {
        global $DB, $USER;

        $warnings = array();
        $attempt = array();

        $params = array(
            'quizid' => $quizid,
            'preflightdata' => $preflightdata,
            'forcenew' => $forcenew,
        );
        $params = self::validate_parameters(self::start_attempt_parameters(), $params);
        $forcenew = $params['forcenew'];

        // Request and permission validation.
        $quiz = $DB->get_record('quiz', array('id' => $params['quizid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($quiz, 'quiz');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $quizobj = quiz::create($cm->instance, $USER->id);

        // Check questions.
        if (!$quizobj->has_questions()) {
            throw new moodle_quiz_exception($quizobj, 'noquestionsfound');
        }

        // Create an object to manage all the other (non-roles) access rules.
        $timenow = time();
        $accessmanager = $quizobj->get_access_manager($timenow);

        // Validate permissions for creating a new attempt and start a new preview attempt if required.
        list($currentattemptid, $attemptnumber, $lastattempt, $messages, $page) =
            quiz_validate_new_attempt($quizobj, $accessmanager, $forcenew, -1, false);

        // Check access.
        if (!$quizobj->is_preview_user() && $messages) {
            // Create warnings with the exact messages.
            foreach ($messages as $message) {
                $warnings[] = array(
                    'item' => 'quiz',
                    'itemid' => $quiz->id,
                    'warningcode' => '1',
                    'message' => clean_text($message, PARAM_TEXT)
                );
            }
        } else {
            if ($accessmanager->is_preflight_check_required($currentattemptid)) {
                // Need to do some checks before allowing the user to continue.

                $provideddata = array();
                foreach ($params['preflightdata'] as $data) {
                    $provideddata[$data['name']] = $data['value'];
                }

                $errors = $accessmanager->validate_preflight_check($provideddata, [], $currentattemptid);

                if (!empty($errors)) {
                    throw new moodle_quiz_exception($quizobj, array_shift($errors));
                }

                // Pre-flight check passed.
                $accessmanager->notify_preflight_check_passed($currentattemptid);
            }

            if ($currentattemptid) {
                if ($lastattempt->state == quiz_attempt::OVERDUE) {
                    throw new moodle_quiz_exception($quizobj, 'stateoverdue');
                } else {
                    throw new moodle_quiz_exception($quizobj, 'attemptstillinprogress');
                }
            }
            $attempt = quiz_prepare_and_start_new_attempt($quizobj, $attemptnumber, $lastattempt);
        }

        $result = array();
        $result['attempt'] = $attempt;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the start_attempt return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function start_attempt_returns() {
        return new external_single_structure(
            array(
                'attempt' => self::attempt_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }

}
