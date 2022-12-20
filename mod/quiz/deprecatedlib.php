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
 * List of deprecated mod_quiz functions.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\access_manager;
use mod_quiz\quiz_settings;
use mod_quiz\task\update_overdue_attempts;

/**
 * Internal function used in quiz_get_completion_state. Check passing grade (or no attempts left) requirement for completion.
 *
 * @deprecated since Moodle 3.11
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_quiz\completion\custom_completion
 * @param stdClass $course
 * @param cm_info|stdClass $cm
 * @param int $userid
 * @param stdClass $quiz
 * @return bool True if the passing grade (or no attempts left) requirement is disabled or met.
 * @throws coding_exception
 */
function quiz_completion_check_passing_grade_or_all_attempts($course, $cm, $userid, $quiz) {
    global $CFG;

    debugging('quiz_completion_check_passing_grade_or_all_attempts has been deprecated.', DEBUG_DEVELOPER);

    if (!$cm->completionpassgrade) {
        return true;
    }

    // Check for passing grade.
    require_once($CFG->libdir . '/gradelib.php');
    $item = grade_item::fetch(array('courseid' => $course->id, 'itemtype' => 'mod',
            'itemmodule' => 'quiz', 'iteminstance' => $cm->instance, 'outcomeid' => null));
    if ($item) {
        $grades = grade_grade::fetch_users_grades($item, array($userid), false);
        if (!empty($grades[$userid]) && $grades[$userid]->is_passed($item)) {
            return true;
        }
    }

    // If a passing grade is required and exhausting all available attempts is not accepted for completion,
    // then this quiz is not complete.
    if (!$quiz->completionattemptsexhausted) {
        return false;
    }

    // Check if all attempts are used up.
    $attempts = quiz_get_user_attempts($quiz->id, $userid, 'finished', true);
    if (!$attempts) {
        return false;
    }
    $lastfinishedattempt = end($attempts);
    $context = context_module::instance($cm->id);
    $quizobj = quiz_settings::create($quiz->id, $userid);
    $accessmanager = new access_manager($quizobj, time(),
            has_capability('mod/quiz:ignoretimelimits', $context, $userid, false));

    return $accessmanager->is_finished(count($attempts), $lastfinishedattempt);
}

/**
 * Internal function used in quiz_get_completion_state. Check minimum attempts requirement for completion.
 *
 * @deprecated since Moodle 3.11
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_quiz\completion\custom_completion
 * @param int $userid
 * @param stdClass $quiz
 * @return bool True if minimum attempts requirement is disabled or met.
 */
function quiz_completion_check_min_attempts($userid, $quiz) {

    debugging('quiz_completion_check_min_attempts has been deprecated.', DEBUG_DEVELOPER);

    if (empty($quiz->completionminattempts)) {
        return true;
    }

    // Check if the user has done enough attempts.
    $attempts = quiz_get_user_attempts($quiz->id, $userid, 'finished', true);
    return $quiz->completionminattempts <= count($attempts);
}

/**
 * Obtains the automatic completion state for this quiz on any conditions
 * in quiz settings, such as if all attempts are used or a certain grade is achieved.
 *
 * @deprecated since Moodle 3.11
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_quiz\completion\custom_completion
 * @param stdClass $course Course
 * @param cm_info|stdClass $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function quiz_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // No need to call debugging here. Deprecation debugging notice already being called in \completion_info::internal_get_state().

    $quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
    if (!$quiz->completionattemptsexhausted && !$cm->completionpassgrade && !$quiz->completionminattempts) {
        return $type;
    }

    if (!quiz_completion_check_passing_grade_or_all_attempts($course, $cm, $userid, $quiz)) {
        return false;
    }

    if (!quiz_completion_check_min_attempts($userid, $quiz)) {
        return false;
    }

    return true;
}

/**
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
class mod_quiz_overdue_attempt_updater {

    /**
     * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts. that was.
     */
    public function update_overdue_attempts($timenow, $processto) {
        debugging('mod_quiz_overdue_attempt_updater has been deprecated. The code wsa moved to ' .
                'mod_quiz\task\update_overdue_attempts.');
        return (new update_overdue_attempts())->update_all_overdue_attempts((int) $timenow, (int) $processto);
    }

    /**
     * @deprecated since Moodle 4.2. Code moved to mod_quiz\task\update_overdue_attempts.
     */
    public function get_list_of_overdue_attempts($processto) {
        debugging('mod_quiz_overdue_attempt_updater has been deprecated. The code wsa moved to ' .
                'mod_quiz\task\update_overdue_attempts.');
        return (new update_overdue_attempts())->get_list_of_overdue_attempts((int) $processto);
    }
}

/**
 * Class for quiz exceptions. Just saves a couple of arguments on the
 * constructor for a moodle_exception.
 *
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 * @deprecated since Moodle 4.2. Please just use moodle_exception.
 * @todo MDL-76612 Final deprecation in Moodle 4.6
 */
class moodle_quiz_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param quiz_settings $quizobj the quiz the error relates to.
     * @param string $errorcode The name of the string from error.php to print.
     * @param mixed $a Extra words and phrases that might be required in the error string.
     * @param string $link The url where the user will be prompted to continue.
     *      If no url is provided the user will be directed to the site index page.
     * @param string|null $debuginfo optional debugging information.
     * @deprecated since Moodle 4.2. Please just use moodle_exception.
     */
    public function __construct($quizobj, $errorcode, $a = null, $link = '', $debuginfo = null) {
        if (!$link) {
            $link = $quizobj->view_url();
        }
        parent::__construct($errorcode, 'quiz', $link, $a, $debuginfo);
    }
}
