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

namespace mod_quiz;

use cm_info;
use coding_exception;
use context;
use context_module;
use core_question\local\bank\question_version_status;
use mod_quiz\question\bank\qbank_helper;
use mod_quiz\question\display_options;
use moodle_exception;
use moodle_url;
use question_bank;
use stdClass;

/**
 * A class encapsulating the settings for a quiz.
 *
 * When this class is initialised, it may have the settings adjusted to account
 * for the overrides for a particular user. See the create methods.
 *
 * Initially, it only loads a minimal amount of information about each question - loading
 * extra information only when necessary or when asked. The class tracks which questions
 * are loaded.
 *
 * @package   mod_quiz
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_settings {
    /** @var stdClass the course settings from the database. */
    protected $course;
    /** @var cm_info the course_module settings from the database. */
    protected $cm;
    /** @var stdClass the quiz settings from the database. */
    protected $quiz;
    /** @var context the quiz context. */
    protected $context;

    /**
     * @var stdClass[] of questions augmented with slot information. For non-random
     *     questions, the array key is question id. For random quesions it is 's' . $slotid.
     *     probalby best to use ->questionid field of the object instead.
     */
    protected $questions = null;
    /** @var stdClass[] of quiz_section rows. */
    protected $sections = null;
    /** @var access_manager the access manager for this quiz. */
    protected $accessmanager = null;
    /** @var bool whether the current user has capability mod/quiz:preview. */
    protected $ispreviewuser = null;

    /** @var grade_calculator|null grade calculator for this quiz. */
    protected ?grade_calculator $gradecalculator = null;

    // Constructor =============================================================.

    /**
     * Constructor, assuming we already have the necessary data loaded.
     *
     * @param stdClass $quiz the row from the quiz table.
     * @param stdClass $cm the course_module object for this quiz.
     * @param stdClass $course the row from the course table for the course we belong to.
     * @param bool $getcontext intended for testing - stops the constructor getting the context.
     */
    public function __construct($quiz, $cm, $course, $getcontext = true) {
        $this->quiz = $quiz;
        $this->cm = $cm;
        $this->quiz->cmid = $this->cm->id;
        $this->course = $course;
        if ($getcontext && !empty($cm->id)) {
            $this->context = context_module::instance($cm->id);
        }
    }

    /**
     * Helper used by the other factory methods.
     *
     * @param stdClass $quiz
     * @param cm_info $cm
     * @param stdClass $course
     * @param int|null $userid the the userid (optional). If passed, relevant overrides are applied.
     * @return quiz_settings the new quiz settings object.
     */
    protected static function create_helper(stdClass $quiz, cm_info $cm, stdClass $course, ?int $userid): self {
        // Update quiz with override information.
        if ($userid) {
            $quiz = quiz_update_effective_access($quiz, $userid);
        }

        return new quiz_settings($quiz, $cm, $course);
    }

    /**
     * Static function to create a new quiz settings object from a quiz id, for a specific user.
     *
     * @param int $quizid the quiz id.
     * @param int|null $userid the the userid (optional). If passed, relevant overrides are applied.
     * @return quiz_settings the new quiz settings object.
     */
    public static function create(int $quizid, ?int $userid = null): self {
        $quiz = access_manager::load_quiz_and_settings($quizid);
        [$course, $cm] = get_course_and_cm_from_instance($quiz, 'quiz');

        return self::create_helper($quiz, $cm, $course, $userid);
    }

    /**
     * Static function to create a new quiz settings object from a cmid, for a specific user.
     *
     * @param int $cmid the course-module id.
     * @param int|null $userid the the userid (optional). If passed, relevant overrides are applied.
     * @return quiz_settings the new quiz settings object.
     */
    public static function create_for_cmid(int $cmid, ?int $userid = null): self {
        [$course, $cm] = get_course_and_cm_from_cmid($cmid, 'quiz');
        $quiz = access_manager::load_quiz_and_settings($cm->instance);

        return self::create_helper($quiz, $cm, $course, $userid);
    }

    /**
     * Create a {@see quiz_attempt} for an attempt at this quiz.
     *
     * @param stdClass $attemptdata row from the quiz_attempts table.
     * @return quiz_attempt the new quiz_attempt object.
     */
    public function create_attempt_object($attemptdata) {
        return new quiz_attempt($attemptdata, $this->quiz, $this->cm, $this->course);
    }

    // Functions for loading more data =========================================.

    /**
     * Load just basic information about all the questions in this quiz.
     */
    public function preload_questions() {
        $slots = qbank_helper::get_question_structure($this->quiz->id, $this->context);
        $this->questions = [];
        foreach ($slots as $slot) {
            $this->questions[$slot->questionid] = $slot;
        }
    }

    /**
     * Fully load some or all of the questions for this quiz. You must call
     * {@see preload_questions()} first.
     *
     * @param array|null $deprecated no longer supported (it was not used).
     */
    public function load_questions($deprecated = null) {
        if ($deprecated !== null) {
            debugging('The argument to quiz::load_questions is no longer supported. ' .
                    'All questions are always loaded.', DEBUG_DEVELOPER);
        }
        if ($this->questions === null) {
            throw new coding_exception('You must call preload_questions before calling load_questions.');
        }

        $questionstoprocess = [];
        foreach ($this->questions as $question) {
            if (is_number($question->questionid)) {
                $question->id = $question->questionid;
                $questionstoprocess[$question->questionid] = $question;
            }
        }
        get_question_options($questionstoprocess);
    }

    /**
     * Get an instance of the {@see \mod_quiz\structure} class for this quiz.
     *
     * @return structure describes the questions in the quiz.
     */
    public function get_structure() {
        return structure::create_for_quiz($this);
    }

    // Simple getters ==========================================================.

    /**
     * Get the id of the course this quiz belongs to.
     *
     * @return int the course id.
     */
    public function get_courseid() {
        return $this->course->id;
    }

    /**
     * Get the course settings object that this quiz belongs to.
     *
     * @return stdClass the row of the course table.
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Get this quiz's id (in the quiz table).
     *
     * @return int the quiz id.
     */
    public function get_quizid() {
        return $this->quiz->id;
    }

    /**
     * Get the quiz settings object.
     *
     * @return stdClass the row of the quiz table.
     */
    public function get_quiz() {
        return $this->quiz;
    }

    /**
     * Get the quiz name.
     *
     * @return string the name of this quiz.
     */
    public function get_quiz_name() {
        return $this->quiz->name;
    }

    /**
     * Get the navigation method in use.
     *
     * @return int QUIZ_NAVMETHOD_FREE or QUIZ_NAVMETHOD_SEQ.
     */
    public function get_navigation_method() {
        return $this->quiz->navmethod;
    }

    /**
     * How many attepts is the user allowed at this quiz?
     *
     * @return int the number of attempts allowed at this quiz (0 = infinite).
     */
    public function get_num_attempts_allowed() {
        return $this->quiz->attempts;
    }

    /**
     * Get the course-module id for this quiz.
     *
     * @return int the course_module id.
     */
    public function get_cmid() {
        return $this->cm->id;
    }

    /**
     * Get the course-module object for this quiz.
     *
     * @return cm_info the course_module object.
     */
    public function get_cm() {
        return $this->cm;
    }

    /**
     * Get the quiz context.
     *
     * @return context_module the module context for this quiz.
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Is the current user is someone who previews the quiz, rather than attempting it?
     *
     * @return bool true user is a preview user. False, if they can do real attempts.
     */
    public function is_preview_user() {
        if (is_null($this->ispreviewuser)) {
            $this->ispreviewuser = has_capability('mod/quiz:preview', $this->context);
        }
        return $this->ispreviewuser;
    }

    /**
     * Checks user enrollment in the current course.
     *
     * @param int $userid the id of the user to check.
     * @return bool whether the user is enrolled.
     */
    public function is_participant($userid) {
        return is_enrolled($this->get_context(), $userid, 'mod/quiz:attempt', $this->show_only_active_users());
    }

    /**
     * Check is only active users in course should be shown.
     *
     * @return bool true if only active users should be shown.
     */
    public function show_only_active_users() {
        return !has_capability('moodle/course:viewsuspendedusers', $this->get_context());
    }

    /**
     * Have any questions been added to this quiz yet?
     *
     * @return bool whether any questions have been added to this quiz.
     */
    public function has_questions() {
        if ($this->questions === null) {
            $this->preload_questions();
        }
        return !empty($this->questions);
    }

    /**
     * Get a particular question in this quiz, by its id.
     *
     * @param int $id the question id.
     * @return stdClass the question object with that id.
     */
    public function get_question($id) {
        return $this->questions[$id];
    }

    /**
     * Get some of the question in this quiz.
     *
     * @param array|null $questionids question ids of the questions to load. null for all.
     * @param bool $requirequestionfullyloaded Whether to require that a particular question is fully loaded.
     * @return stdClass[] the question data objects.
     */
    public function get_questions(?array $questionids = null, bool $requirequestionfullyloaded = true) {
        if (is_null($questionids)) {
            $questionids = array_keys($this->questions);
        }
        $questions = [];
        foreach ($questionids as $id) {
            if (!array_key_exists($id, $this->questions)) {
                throw new moodle_exception('cannotstartmissingquestion', 'quiz', $this->view_url());
            }
            $questions[$id] = $this->questions[$id];
            if ($requirequestionfullyloaded) {
                $this->ensure_question_loaded($id);
            }
        }
        return $questions;
    }

    /**
     * Get all the sections in this quiz.
     *
     * @return array 0, 1, 2, ... => quiz_sections row from the database.
     */
    public function get_sections() {
        global $DB;
        if ($this->sections === null) {
            $this->sections = array_values($DB->get_records('quiz_sections',
                    ['quizid' => $this->get_quizid()], 'firstslot'));
        }
        return $this->sections;
    }

    /**
     * Return access_manager and instance of the access_manager class
     * for this quiz at this time.
     *
     * @param int $timenow the current time as a unix timestamp.
     * @return access_manager an instance of the access_manager class
     *      for this quiz at this time.
     */
    public function get_access_manager($timenow) {
        if (is_null($this->accessmanager)) {
            $this->accessmanager = new access_manager($this, $timenow,
                    has_capability('mod/quiz:ignoretimelimits', $this->context, null, false));
        }
        return $this->accessmanager;
    }

    /**
     * Return the grade_calculator object for this quiz.
     *
     * @return grade_calculator
     */
    public function get_grade_calculator(): grade_calculator {
        if ($this->gradecalculator === null) {
            $this->gradecalculator = grade_calculator::create($this);
        }

        return $this->gradecalculator;
    }

    /**
     * Wrapper round the has_capability funciton that automatically passes in the quiz context.
     *
     * @param string $capability the name of the capability to check. For example mod/quiz:view.
     * @param int|null $userid A user id. By default (null) checks the permissions of the current user.
     * @param bool $doanything If false, ignore effect of admin role assignment.
     * @return boolean true if the user has this capability. Otherwise false.
     */
    public function has_capability($capability, $userid = null, $doanything = true) {
        return has_capability($capability, $this->context, $userid, $doanything);
    }

    /**
     * Wrapper round the require_capability function that automatically passes in the quiz context.
     *
     * @param string $capability the name of the capability to check. For example mod/quiz:view.
     * @param int|null $userid A user id. By default (null) checks the permissions of the current user.
     * @param bool $doanything If false, ignore effect of admin role assignment.
     */
    public function require_capability($capability, $userid = null, $doanything = true) {
        require_capability($capability, $this->context, $userid, $doanything);
    }

    // URLs related to this attempt ============================================.

    /**
     * Get the URL of this quiz's view.php page.
     *
     * @return moodle_url the URL of this quiz's view page.
     */
    public function view_url() {
        return new moodle_url('/mod/quiz/view.php', ['id' => $this->cm->id]);
    }

    /**
     * Get the URL of this quiz's edit questions page.
     *
     * @return moodle_url the URL of this quiz's edit page.
     */
    public function edit_url() {
        return new moodle_url('/mod/quiz/edit.php', ['cmid' => $this->cm->id]);
    }

    /**
     * Get the URL of a particular page within an attempt.
     *
     * @param int $attemptid the id of an attempt.
     * @param int $page optional page number to go to in the attempt.
     * @return moodle_url the URL of that attempt.
     */
    public function attempt_url($attemptid, $page = 0) {
        $params = ['attempt' => $attemptid, 'cmid' => $this->get_cmid()];
        if ($page) {
            $params['page'] = $page;
        }
        return new moodle_url('/mod/quiz/attempt.php', $params);
    }

    /**
     * Get the URL to start/continue an attempt.
     *
     * @param int $page page in the attempt to start on (optional).
     * @return moodle_url the URL of this quiz's edit page. Needs to be POSTed to with a cmid parameter.
     */
    public function start_attempt_url($page = 0) {
        $params = ['cmid' => $this->cm->id, 'sesskey' => sesskey()];
        if ($page) {
            $params['page'] = $page;
        }
        return new moodle_url('/mod/quiz/startattempt.php', $params);
    }

    /**
     * Get the URL to review a particular quiz attempt.
     *
     * @param int $attemptid the id of an attempt.
     * @return string the URL of the review of that attempt.
     */
    public function review_url($attemptid) {
        return new moodle_url('/mod/quiz/review.php', ['attempt' => $attemptid, 'cmid' => $this->get_cmid()]);
    }

    /**
     * Get the URL for the summary page for a particular attempt.
     *
     * @param int $attemptid the id of an attempt.
     * @return string the URL of the review of that attempt.
     */
    public function summary_url($attemptid) {
        return new moodle_url('/mod/quiz/summary.php', ['attempt' => $attemptid, 'cmid' => $this->get_cmid()]);
    }

    // Bits of content =========================================================.

    /**
     * If $reviewoptions->attempt is false, meaning that students can't review this
     * attempt at the moment, return an appropriate string explaining why.
     *
     * @param int $when One of the display_options::DURING,
     *      IMMEDIATELY_AFTER, LATER_WHILE_OPEN or AFTER_CLOSE constants.
     * @param bool $short if true, return a shorter string.
     * @param int|null $attemptsubmittime time this attempt was submitted. (Optional, but should be given.)
     * @return string an appropraite message.
     */
    public function cannot_review_message($when, $short = false, ?int $attemptsubmittime = null) {

        if ($attemptsubmittime === null) {
            debugging('It is recommended that you pass $attemptsubmittime to cannot_review_message', DEBUG_DEVELOPER);
            $attemptsubmittime = time(); // This will be approximately right, which is enough for the one place were it is used.
        }

        if ($short) {
            $langstrsuffix = 'short';
            $dateformat = get_string('strftimedatetimeshort', 'langconfig');
        } else {
            $langstrsuffix = '';
            $dateformat = '';
        }

        $reviewfrom = 0;
        switch ($when) {
            case display_options::DURING:
                return '';

            case display_options::IMMEDIATELY_AFTER:
                if ($this->quiz->reviewattempt & display_options::LATER_WHILE_OPEN) {
                    $reviewfrom = $attemptsubmittime + quiz_attempt::IMMEDIATELY_AFTER_PERIOD;
                    break;
                }
                // Fall through.

            case display_options::LATER_WHILE_OPEN:
                if ($this->quiz->timeclose && ($this->quiz->reviewattempt & display_options::AFTER_CLOSE)) {
                    $reviewfrom = $this->quiz->timeclose;
                    break;
                }
        }

        if ($reviewfrom) {
            return get_string('noreviewuntil' . $langstrsuffix, 'quiz',
                    userdate($reviewfrom, $dateformat));
        } else {
            return get_string('noreview' . $langstrsuffix, 'quiz');
        }
    }

    /**
     * Probably not used any more, but left for backwards compatibility.
     *
     * @param string $title the name of this particular quiz page.
     * @return string always returns ''.
     */
    public function navigation($title) {
        global $PAGE;
        $PAGE->navbar->add($title);
        return '';
    }

    // Private methods =========================================================.

    /**
     * Check that the definition of a particular question is loaded, and if not throw an exception.
     *
     * @param int $id a question id.
     */
    protected function ensure_question_loaded($id) {
        if (isset($this->questions[$id]->_partiallyloaded)) {
            throw new moodle_exception('questionnotloaded', 'quiz', $this->view_url(), $id);
        }
    }

    /**
     * Return all the question types used in this quiz.
     *
     * @param boolean $includepotential if the quiz include random questions,
     *      setting this flag to true will make the function to return all the
     *      possible question types in the random questions category.
     * @return array a sorted array including the different question types.
     * @since  Moodle 3.1
     */
    public function get_all_question_types_used($includepotential = false) {
        $questiontypes = [];

        // To control if we need to look in categories for questions.
        $qcategories = [];

        foreach ($this->get_questions(null, false) as $questiondata) {
            if ($questiondata->status == question_version_status::QUESTION_STATUS_DRAFT) {
                // Skip questions where all versions are draft.
                continue;
            }
            if ($questiondata->qtype === 'random' && $includepotential) {
                $filtercondition = $questiondata->filtercondition;
                if (!empty($filtercondition)) {
                    $filter = $filtercondition['filter'];
                    if (isset($filter['category'])) {
                        foreach ($filter['category']['values'] as $catid) {
                            $qcategories[$catid] = $filter['category']['filteroptions']['includesubcategories'];
                        }
                    }
                }
            } else {
                if (!in_array($questiondata->qtype, $questiontypes)) {
                    $questiontypes[] = $questiondata->qtype;
                }
            }
        }

        if (!empty($qcategories)) {
            // We have to look for all the question types in these categories.
            $categoriestolook = [];
            foreach ($qcategories as $cat => $includesubcats) {
                if ($includesubcats) {
                    $categoriestolook = array_merge($categoriestolook, question_categorylist($cat));
                } else {
                    $categoriestolook[] = $cat;
                }
            }
            $questiontypesincategories = question_bank::get_all_question_types_in_categories($categoriestolook);
            $questiontypes = array_merge($questiontypes, $questiontypesincategories);
        }
        $questiontypes = array_unique($questiontypes);
        sort($questiontypes);

        return $questiontypes;
    }

    /**
     * Returns an override manager instance with context and quiz loaded.
     *
     * @return \mod_quiz\local\override_manager
     */
    public function get_override_manager(): \mod_quiz\local\override_manager {
        return new \mod_quiz\local\override_manager(
            quiz: $this->quiz,
            context: $this->context
        );
    }
}
