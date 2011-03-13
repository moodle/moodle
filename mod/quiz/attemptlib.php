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
 * Back-end code for handling data about quizzes and the current user's attempt.
 *
 * There are classes for loading all the information about a quiz and attempts,
 * and for displaying the navigation panel.
 *
 * @package quiz
 * @copyright 2008 onwards Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class for quiz exceptions. Just saves a couple of arguments on the
 * constructor for a moodle_exception.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class moodle_quiz_exception extends moodle_exception {
    function __construct($quizobj, $errorcode, $a = NULL, $link = '', $debuginfo = null) {
        if (!$link) {
            $link = $quizobj->view_url();
        }
        parent::__construct($errorcode, 'quiz', $link, $a, $debuginfo);
    }
}

/**
 * A class encapsulating a quiz and the questions it contains, and making the
 * information available to scripts like view.php.
 *
 * Initially, it only loads a minimal amout of information about each question - loading
 * extra information only when necessary or when asked. The class tracks which questions
 * are loaded.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class quiz {
    // Fields initialised in the constructor.
    protected $course;
    protected $cm;
    protected $quiz;
    protected $context;
    protected $questionids; // All question ids in order that they appear in the quiz.
    protected $pagequestionids; // array page no => array of questionids on the page in order.

    // Fields set later if that data is needed.
    protected $questions = null;
    protected $accessmanager = null;
    protected $ispreviewuser = null;

    // Constructor =========================================================================
    /**
     * Constructor, assuming we already have the necessary data loaded.
     *
     * @param object $quiz the row from the quiz table.
     * @param object $cm the course_module object for this quiz.
     * @param object $course the row from the course table for the course we belong to.
     * @param boolean $getcontext intended for testing - stops the constructor getting the context.
     */
    function __construct($quiz, $cm, $course, $getcontext = true) {
        $this->quiz = $quiz;
        $this->cm = $cm;
        $this->quiz->cmid = $this->cm->id;
        $this->course = $course;
        if ($getcontext && !empty($cm->id)) {
            $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
        }
        $this->determine_layout();
    }

    /**
     * Static function to create a new quiz object for a specific user.
     *
     * @param integer $quizid the the quiz id.
     * @param integer $userid the the userid.
     * @return quiz the new quiz object
     */
    static public function create($quizid, $userid) {
        global $DB;

        if (!$quiz = $DB->get_record('quiz', array('id' => $quizid))) {
            throw new moodle_exception('invalidquizid', 'quiz');
        }
        if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
            throw new moodle_exception('invalidcoursemodule');
        }
        if (!$cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id)) {
            throw new moodle_exception('invalidcoursemodule');
        }

        // Update quiz with override information
        $quiz = quiz_update_effective_access($quiz, $userid);

        return new quiz($quiz, $cm, $course);
    }

    // Functions for loading more data =====================================================
    /**
     * Convenience method. Calls {@link load_questions()} with the list of
     * question ids for a given page.
     *
     * @param integer $page a page number.
     */
    public function load_questions_on_page($page) {
        $this->load_questions($this->pagequestionids[$page]);
    }

    /**
     * Load just basic information about all the questions in this quiz.
     */
    public function preload_questions() {
        if (empty($this->questionids)) {
            throw new moodle_quiz_exception($this, 'noquestions', $this->edit_url());
        }
        $this->questions = question_preload_questions($this->questionids,
                'qqi.grade AS maxgrade, qqi.id AS instance',
                '{quiz_question_instances} qqi ON qqi.quiz = :quizid AND q.id = qqi.question',
                array('quizid' => $this->quiz->id));
        $this->number_questions();
    }

   /**
     * Fully load some or all of the questions for this quiz. You must call {@link preload_questions()} first.
     *
     * @param array $questionids question ids of the questions to load. null for all.
     */
    public function load_questions($questionids = null) {
        if (is_null($questionids)) {
            $questionids = $this->questionids;
        }
        $questionstoprocess = array();
        foreach ($questionids as $id) {
            $questionstoprocess[$id] = $this->questions[$id];
        }
        if (!get_question_options($questionstoprocess)) {
            throw new moodle_quiz_exception($this, 'loadingquestionsfailed', implode(', ', $questionids));
        }
    }

    // Simple getters ======================================================================
    /** @return integer the course id. */
    public function get_courseid() {
        return $this->course->id;
    }

    /** @return object the row of the course table. */
    public function get_course() {
        return $this->course;
    }

    /** @return integer the quiz id. */
    public function get_quizid() {
        return $this->quiz->id;
    }

    /** @return object the row of the quiz table. */
    public function get_quiz() {
        return $this->quiz;
    }

    /** @return string the name of this quiz. */
    public function get_quiz_name() {
        return $this->quiz->name;
    }

    /** @return integer the number of attempts allowed at this quiz (0 = infinite). */
    public function get_num_attempts_allowed() {
        return $this->quiz->attempts;
    }

    /** @return integer the course_module id. */
    public function get_cmid() {
        return $this->cm->id;
    }

    /** @return object the course_module object. */
    public function get_cm() {
        return $this->cm;
    }

    /**
     * @return boolean wether the current user is someone who previews the quiz,
     * rather than attempting it.
     */
    public function is_preview_user() {
        if (is_null($this->ispreviewuser)) {
            $this->ispreviewuser = has_capability('mod/quiz:preview', $this->context);
        }
        return $this->ispreviewuser;
    }

    /**
     * @return integer number fo pages in this quiz.
     */
    public function get_num_pages() {
        return count($this->pagequestionids);
    }


    /**
     * @param int $page page number
     * @return boolean true if this is the last page of the quiz.
     */
    public function is_last_page($page) {
        return $page == count($this->pagequestionids) - 1;
    }

    /**
     * @param integer $id the question id.
     * @return object the question object with that id.
     */
    public function get_question($id) {
        return $this->questions[$id];
    }

    /**
     * @param array $questionids question ids of the questions to load. null for all.
     */
    public function get_questions($questionids = null) {
        if (is_null($questionids)) {
            $questionids = $this->questionids;
        }
        $questions = array();
        foreach ($questionids as $id) {
            $questions[$id] = $this->questions[$id];
            $this->ensure_question_loaded($id);
        }
        return $questions;
    }

    /**
     * Return the list of question ids for either a given page of the quiz, or for the
     * whole quiz.
     *
     * @param mixed $page string 'all' or integer page number.
     * @return array the reqested list of question ids.
     */
    public function get_question_ids($page = 'all') {
        if ($page === 'all') {
            $list = $this->questionids;
        } else {
            $list = $this->pagequestionids[$page];
        }
        // Clone the array, so our private arrays cannot be modified.
        $result = array();
        foreach ($list as $id) {
            $result[] = $id;
        }
        return $result;
    }

    /**
     * @param integer $timenow the current time as a unix timestamp.
     * @return quiz_access_manager and instance of the quiz_access_manager class for this quiz at this time.
     */
    public function get_access_manager($timenow) {
        if (is_null($this->accessmanager)) {
            $this->accessmanager = new quiz_access_manager($this, $timenow,
                    has_capability('mod/quiz:ignoretimelimits', $this->context, NULL, false));
        }
        return $this->accessmanager;
    }

    public function get_overall_feedback($grade) {
        return quiz_feedback_for_grade($grade, $this->quiz, $this->context, $this->cm);
    }

    /**
     * Wrapper round the has_capability funciton that automatically passes in the quiz context.
     */
    public function has_capability($capability, $userid = NULL, $doanything = true) {
        return has_capability($capability, $this->context, $userid, $doanything);
    }

    /**
     * Wrapper round the require_capability funciton that automatically passes in the quiz context.
     */
    public function require_capability($capability, $userid = NULL, $doanything = true) {
        return require_capability($capability, $this->context, $userid, $doanything);
    }

    // URLs related to this attempt ========================================================
    /**
     * @return string the URL of this quiz's view page.
     */
    public function view_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/view.php?id=' . $this->cm->id;
    }

    /**
     * @return string the URL of this quiz's edit page.
     */
    public function edit_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $this->cm->id;
    }

    /**
     * @param integer $attemptid the id of an attempt.
     * @return string the URL of that attempt.
     */
    public function attempt_url($attemptid) {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/attempt.php?attempt=' . $attemptid;
    }

    /**
     * @return string the URL of this quiz's edit page. Needs to be POSTed to with a cmid parameter.
     */
    public function start_attempt_url() {
        return new moodle_url('/mod/quiz/startattempt.php',
                array('cmid' => $this->cm->id, 'sesskey' => sesskey()));
    }

    /**
     * @param integer $attemptid the id of an attempt.
     * @return string the URL of the review of that attempt.
     */
    public function review_url($attemptid) {
        return new moodle_url('/mod/quiz/review.php', array('attempt' => $attemptid));
    }

    // Bits of content =====================================================================

    /**
     * @param string $title the name of this particular quiz page.
     * @return array the data that needs to be sent to print_header_simple as the $navigation
     * parameter.
     */
    public function navigation($title) {
        global $PAGE;
        $PAGE->navbar->add($title);
        return '';
    }

    // Private methods =====================================================================
    /**
     *  Check that the definition of a particular question is loaded, and if not throw an exception.
     *  @param $id a questionid.
     */
    protected function ensure_question_loaded($id) {
        if (isset($this->questions[$id]->_partiallyloaded)) {
            throw new moodle_quiz_exception($this, 'questionnotloaded', $id);
        }
    }

    /**
     * Populate {@link $questionids} and {@link $pagequestionids} from the layout.
     */
    protected function determine_layout() {
        $this->questionids = array();
        $this->pagequestionids = array();

        // Get the appropriate layout string (from quiz or attempt).
        $layout = quiz_clean_layout($this->get_layout_string(), true);
        if (empty($layout)) {
            // Nothing to do.
            return;
        }

        // Break up the layout string into pages.
        $pagelayouts = explode(',0', $layout);

        // Strip off any empty last page (normally there is one).
        if (end($pagelayouts) == '') {
            array_pop($pagelayouts);
        }

        // File the ids into the arrays.
        $this->questionids = array();
        $this->pagequestionids = array();
        foreach ($pagelayouts as $page => $pagelayout) {
            $pagelayout = trim($pagelayout, ',');
            if ($pagelayout == '') continue;
            $this->pagequestionids[$page] = explode(',', $pagelayout);
            foreach ($this->pagequestionids[$page] as $id) {
                $this->questionids[] = $id;
            }
        }
    }

    /**
     * Number the questions, adding a _number field to each one.
     */
    private function number_questions() {
        $number = 1;
        foreach ($this->pagequestionids as $page => $questionids) {
            foreach ($questionids as $id) {
                if ($this->questions[$id]->length > 0) {
                    $this->questions[$id]->_number = $number;
                    $number += $this->questions[$id]->length;
                } else {
                    $this->questions[$id]->_number = get_string('infoshort', 'quiz');
                }
                $this->questions[$id]->_page = $page;
            }
        }
    }

    /**
     * @return string the layout of this quiz. Used by number_questions to
     * work out which questions are on which pages.
     */
    protected function get_layout_string() {
        return $this->quiz->questions;
    }
}

/**
 * This class extends the quiz class to hold data about the state of a particular attempt,
 * in addition to the data about the quiz.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class quiz_attempt extends quiz {
    // Fields initialised in the constructor.
    protected $attempt;

    // Fields set later if that data is needed.
    protected $states = array();
    protected $reviewoptions = null;

    // Constructor =========================================================================
    /**
     * Constructor assuming we already have the necessary data loaded.
     *
     * @param object $attempt the row of the quiz_attempts table.
     * @param object $quiz the quiz object for this attempt and user.
     * @param object $cm the course_module object for this quiz.
     * @param object $course the row from the course table for the course we belong to.
     */
    function __construct($attempt, $quiz, $cm, $course) {
        $this->attempt = $attempt;
        parent::__construct($quiz, $cm, $course);
        $this->preload_questions();
        $this->preload_question_states();
    }

    /**
     * Used by {create()} and {create_from_usage_id()}.
     * @param array $conditions passed to $DB->get_record('quiz_attempts', $conditions).
     */
    static protected function create_helper($conditions) {
        global $DB;

        $attempt = $DB->get_record('quiz_attempts', $conditions, '*', MUST_EXIST);
        $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $quiz->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id, false, MUST_EXIST);

        // Update quiz with override information
        $quiz = quiz_update_effective_access($quiz, $attempt->userid);

        return new quiz_attempt($attempt, $quiz, $cm, $course);
    }

    /**
     * Static function to create a new quiz_attempt object given an attemptid.
     *
     * @param int $attemptid the attempt id.
     * @return quiz_attempt the new quiz_attempt object
     */
    static public function create($attemptid) {
        return self::create_helper(array('id' => $attemptid));
    }

    /**
     * Static function to create a new quiz_attempt object given a usage id.
     *
     * @param int $usageid the attempt usage id.
     * @return quiz_attempt the new quiz_attempt object
     */
    static public function create_from_unique_id($usageid) {
        return self::create_helper(array('uniqueid' => $usageid));
    }

    // Functions for loading more data =====================================================
    /**
     * Load the state of a number of questions that have already been loaded.
     *
     * @param array $questionids question ids to process. Blank = all.
     */
    public function load_question_states($questionids = null) {
        if (is_null($questionids)) {
            $questionids = $this->questionids;
        }
        $questionstoprocess = array();
        foreach ($questionids as $id) {
            $this->ensure_question_loaded($id);
            $questionstoprocess[$id] = $this->questions[$id];
        }
        if (!question_load_states($questionstoprocess, $this->states,
                $this->quiz, $this->attempt)) {
            throw new moodle_quiz_exception($this, 'cannotrestore');
        }
    }

    /**
     * Load basic information about the state of each question.
     *
     * This is enough to, for example, show the state of each question in the
     * navigation panel, but only takes one DB query.
     */
    public function preload_question_states() {
        if (empty($this->questionids)) {
            throw new moodle_quiz_exception($this, 'noquestions', $this->edit_url());
        }
        $this->states = question_preload_states($this->attempt->uniqueid);
        if (!$this->states) {
            $this->states = array();
        }
    }

    /**
     * Load a particular state of a particular question. Used by the reviewquestion.php
     * script to let the teacher walk through the entire sequence of a student's
     * interaction with a question.
     *
     * @param $questionid the question id
     * @param $stateid the id of the particular state to load.
     */
    public function load_specific_question_state($questionid, $stateid) {
        global $DB;
        $state = question_load_specific_state($this->questions[$questionid],
                $this->quiz, $this->attempt->uniqueid, $stateid);
        if ($state === false) {
            throw new moodle_quiz_exception($this, 'invalidstateid');
        }
        $this->states[$questionid] = $state;
    }

    // Simple getters ======================================================================
    /** @return integer the attempt id. */
    public function get_attemptid() {
        return $this->attempt->id;
    }

    /** @return integer the attempt unique id. */
    public function get_uniqueid() {
        return $this->attempt->uniqueid;
    }

    /** @return object the row from the quiz_attempts table. */
    public function get_attempt() {
        return $this->attempt;
    }

    /** @return integer the number of this attemp (is it this user's first, second, ... attempt). */
    public function get_attempt_number() {
        return $this->attempt->attempt;
    }

    /** @return integer the id of the user this attempt belongs to. */
    public function get_userid() {
        return $this->attempt->userid;
    }

    /** @return boolean whether this attempt has been finished (true) or is still in progress (false). */
    public function is_finished() {
        return $this->attempt->timefinish != 0;
    }

    /** @return boolean whether this attempt is a preview attempt. */
    public function is_preview() {
        return $this->attempt->preview;
    }

    /**
     * Is this a student dealing with their own attempt/teacher previewing,
     * or someone with 'mod/quiz:viewreports' reviewing someone elses attempt.
     *
     * @return boolean whether this situation should be treated as someone looking at their own
     * attempt. The distinction normally only matters when an attempt is being reviewed.
     */
    public function is_own_attempt() {
        global $USER;
        return $this->attempt->userid == $USER->id &&
                (!$this->is_preview_user() || $this->attempt->preview);
    }

    /**
     * Is the current user allowed to review this attempt. This applies when
     * {@link is_own_attempt()} returns false.
     * @return bool whether the review should be allowed.
     */
    public function is_review_allowed() {
        if (!$this->has_capability('mod/quiz:viewreports')) {
            return false;
        }

        $cm = $this->get_cm();
        if ($this->has_capability('moodle/site:accessallgroups') ||
                groups_get_activity_groupmode($cm) != SEPARATEGROUPS) {
            return true;
        }

        // Check the users have at least one group in common.
        $teachersgroups = groups_get_activity_allowed_groups($cm);
        $studentsgroups = groups_get_all_groups($cm->course, $this->attempt->userid, $cm->groupingid);
        return $teachersgroups && $studentsgroups &&
                array_intersect(array_keys($teachersgroups), array_keys($studentsgroups));
    }

    /**
     * Check the appropriate capability to see whether this user may review their own attempt.
     * If not, prints an error.
     */
    public function check_review_capability() {
        if (!$this->has_capability('mod/quiz:viewreports')) {
            if ($this->get_review_options()->quizstate == QUIZ_STATE_IMMEDIATELY) {
                $this->require_capability('mod/quiz:attempt');
            } else {
                $this->require_capability('mod/quiz:reviewmyattempts');
            }
        }
    }

    /**
     * Get the current state of a question in the attempt.
     *
     * @param $questionid a questionid.
     * @return object the state.
     */
    public function get_question_state($questionid) {
        return $this->states[$questionid];
    }

    /**
     * Wrapper that calls quiz_get_reviewoptions with the appropriate arguments.
     *
     * @return object the review options for this user on this attempt.
     */
    public function get_review_options() {
        if (is_null($this->reviewoptions)) {
            $this->reviewoptions = quiz_get_reviewoptions($this->quiz, $this->attempt, $this->context);
        }
        return $this->reviewoptions;
    }

    /**
     * Wrapper that calls get_render_options with the appropriate arguments.
     *
     * @param integer questionid the quetsion to get the render options for.
     * @return object the render options for this user on this attempt.
     */
    public function get_render_options($questionid) {
        return quiz_get_renderoptions($this->quiz, $this->attempt, $this->context,
                $this->get_question_state($questionid));
    }

    /**
     * Get a quiz_attempt_question_iterator for either a page of the quiz, or a whole quiz.
     * You must have called load_questions with an appropriate argument first.
     *
     * @param mixed $page as for the @see{get_question_ids} method.
     * @return quiz_attempt_question_iterator the requested iterator.
     */
    public function get_question_iterator($page = 'all') {
        return new quiz_attempt_question_iterator($this, $page);
    }

    /**
     * Return a summary of the current state of a question in this attempt. You must previously
     * have called load_question_states to load the state data about this question.
     *
     * @param integer $questionid question id of a question that belongs to this quiz.
     * @return string a brief string (that could be used as a CSS class name, for example)
     * that describes the current state of a question in this attempt. Possible results are:
     * open|saved|closed|correct|partiallycorrect|incorrect.
     */
    public function get_question_status($questionid) {
        $state = $this->states[$questionid];
        switch ($state->event) {
            case QUESTION_EVENTOPEN:
                return 'open';

            case QUESTION_EVENTSAVE:
            case QUESTION_EVENTGRADE:
            case QUESTION_EVENTSUBMIT:
                return 'answered';

            case QUESTION_EVENTCLOSEANDGRADE:
            case QUESTION_EVENTCLOSE:
            case QUESTION_EVENTMANUALGRADE:
                $options = $this->get_render_options($questionid);
                if ($options->scores && $this->questions[$questionid]->maxgrade > 0) {
                    return question_get_feedback_class($state->last_graded->raw_grade /
                            $this->questions[$questionid]->maxgrade);
                } else {
                    return 'closed';
                }

            default:
                $a = new stdClass;
                $a->event = $state->event;
                $a->questionid = $questionid;
                $a->attemptid = $this->attempt->id;
                throw new moodle_quiz_exception($this, 'errorunexpectedevent', $a);
        }
    }

    /**
     * @param integer $questionid question id of a question that belongs to this quiz.
     * @return boolean whether this question hss been flagged by the attempter.
     */
    public function is_question_flagged($questionid) {
        $state = $this->states[$questionid];
        return $state->flagged;
    }

    /**
     * Return the grade obtained on a particular question, if the user is permitted to see it.
     * You must previously have called load_question_states to load the state data about this question.
     *
     * @param integer $questionid question id of a question that belongs to this quiz.
     * @return string the formatted grade, to the number of decimal places specified by the quiz.
     */
    public function get_question_score($questionid) {
        $options = $this->get_render_options($questionid);
        if ($options->scores) {
            return quiz_format_question_grade($this->quiz, $this->states[$questionid]->last_graded->grade);
        } else {
            return '';
        }
    }

    // URLs related to this attempt ========================================================
    /**
     * @param integer $questionid a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page.
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page. If -1, deduce $page from $questionid.
     * @param integer $thispage if not -1, the current page. Will cause links to other things on
     * this page to be output as only a fragment.
     * @return string the URL to continue this attempt.
     */
    public function attempt_url($questionid = 0, $page = -1, $thispage = -1) {
        return $this->page_and_question_url('attempt', $questionid, $page, false, $thispage);
    }

    /**
     * @return string the URL of this quiz's summary page.
     */
    public function summary_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/summary.php?attempt=' . $this->attempt->id;
    }

    /**
     * @return string the URL of this quiz's summary page.
     */
    public function processattempt_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/processattempt.php';
    }

    /**
     * @param integer $questionid a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page. If -1, deduce $page from $questionid.
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page.
     * @param boolean $showall if true, the URL will be to review the entire attempt on one page,
     * and $page will be ignored.
     * @param integer $thispage if not -1, the current page. Will cause links to other things on
     * this page to be output as only a fragment.
     * @return string the URL to review this attempt.
     */
    public function review_url($questionid = 0, $page = -1, $showall = false, $thispage = -1) {
        return $this->page_and_question_url('review', $questionid, $page, $showall, $thispage);
    }

    // Bits of content =====================================================================
    /**
     * Initialise the JS etc. required all the questions on a page..
     * @param mixed $page a page number, or 'all'.
     */
    public function get_html_head_contributions($page = 'all') {
        global $PAGE;
        question_get_html_head_contributions($this->get_question_ids($page), $this->questions, $this->states);
    }

    /**
     * Initialise the JS etc. required by one question.
     * @param integer $questionid the question id.
     */
    public function get_question_html_head_contributions($questionid) {
        question_get_html_head_contributions(array($questionid), $this->questions, $this->states);
    }

    /**
     * Print the HTML for the start new preview button.
     */
    public function print_restart_preview_button() {
        global $CFG, $OUTPUT;
        echo $OUTPUT->container_start('controls');
        $url = new moodle_url($this->start_attempt_url(), array('forcenew' => true));
        echo $OUTPUT->single_button($url, get_string('startagain', 'quiz'));
        echo $OUTPUT->container_end();
    }

    /**
     * Return the HTML of the quiz timer.
     * @return string HTML content.
     */
    public function get_timer_html() {
        return '<div id="quiz-timer">' . get_string('timeleft', 'quiz') .
                ' <span id="quiz-time-left"></span></div>';
    }

    /**
     * Wrapper round print_question from lib/questionlib.php.
     *
     * @param integer $id the id of a question in this quiz attempt.
     * @param boolean $reviewing is the being printed on an attempt or a review page.
     * @param string $thispageurl the URL of the page this question is being printed on.
     */
    public function print_question($id, $reviewing, $thispageurl = '') {
        global $CFG;

        if ($reviewing) {
            $options = $this->get_review_options();
        } else {
            $options = $this->get_render_options($id);
        }
        if ($thispageurl instanceof moodle_url) {
            $thispageurl = $thispageurl->out(false);
        }
        if ($thispageurl) {
            $this->quiz->thispageurl = str_replace($CFG->wwwroot, '', $thispageurl);
        } else {
            unset($thispageurl);
        }
        print_question($this->questions[$id], $this->states[$id], $this->questions[$id]->_number,
                $this->quiz, $options);
    }

    public function check_file_access($questionid, $isreviewing, $contextid, $component,
            $filearea, $args, $forcedownload) {
        if ($isreviewing) {
            $options = $this->get_review_options();
        } else {
            $options = $this->get_render_options($questionid);
        }
        // XXX: mulitichoice type needs quiz id to get maxgrade
        $options->quizid = $this->attempt->quiz;
        return question_check_file_access($this->questions[$questionid],
                $this->get_question_state($questionid), $options, $contextid,
                $component, $filearea, $args, $forcedownload);
    }

    /**
     * Triggers the sending of the notification emails at the end of this attempt.
     */
    public function quiz_send_notification_emails() {
        quiz_send_notification_emails($this->course, $this->quiz, $this->attempt,
                $this->context, $this->cm);
    }

    /**
     * Get the navigation panel object for this attempt.
     *
     * @param $panelclass The type of panel, quiz_attempt_nav_panel or quiz_review_nav_panel
     * @param $page the current page number.
     * @param $showall whether we are showing the whole quiz on one page. (Used by review.php)
     * @return quiz_nav_panel_base the requested object.
     */
    public function get_navigation_panel($panelclass, $page, $showall = false) {
        $panel = new $panelclass($this, $this->get_review_options(), $page, $showall);
        return $panel->get_contents();
    }

    /**
     * Given a URL containing attempt={this attempt id}, return an array of variant URLs
     * @param $url a URL.
     * @return string HTML fragment. Comma-separated list of links to the other
     * attempts with the attempt number as the link text. The curent attempt is
     * included but is not a link.
     */
    public function links_to_other_attempts($url) {
        $search = '/\battempt=' . $this->attempt->id . '\b/';
        $attempts = quiz_get_user_attempts($this->quiz->id, $this->attempt->userid, 'all');
        if (count($attempts) <= 1) {
            return false;
        }
        $attemptlist = array();
        foreach ($attempts as $at) {
            if ($at->id == $this->attempt->id) {
                $attemptlist[] = '<strong>' . $at->attempt . '</strong>';
            } else {
                $changedurl = preg_replace($search, 'attempt=' . $at->id, $url);
                $attemptlist[] = '<a href="' . s($changedurl) . '">' . $at->attempt . '</a>';
            }
        }
        return implode(', ', $attemptlist);
    }

    // Methods for processing manual comments ==============================================
    /**
     * Process a manual comment for a question in this attempt.
     * @param $questionid
     * @param integer $questionid the question id
     * @param string $comment the new comment from the teacher.
     * @param mixed $grade the grade the teacher assigned, or '' to not change the grade.
     * @return mixed true on success, a string error message if a problem is detected
     *         (for example score out of range).
     */
    public function process_comment($questionid, $comment, $commentformat, $grade) {
        // I am not sure it is a good idea to have update methods here - this
        // class is only about getting data out of the question engine, and
        // helping to display it, apart from this.
        $this->ensure_question_loaded($questionid);
        $this->ensure_state_loaded($questionid);
        $state = $this->states[$questionid];

        $error = question_process_comment($this->questions[$questionid],
                $state, $this->attempt, $comment, $commentformat, $grade);

        // If the state was update (successfully), save the changes.
        if (!is_string($error) && $state->changed) {
            if (!save_question_session($this->questions[$questionid], $state)) {
                $error = get_string('errorudpatingquestionsession', 'quiz');
            }
            if (!quiz_save_best_grade($this->quiz, $this->attempt->userid)) {
                $error = get_string('errorudpatingbestgrade', 'quiz');
            }
        }
        return $error;
    }

    /**
     * Print the fields of the comment form for questions in this attempt.
     * @param $questionid a question id.
     * @param $prefix Prefix to add to all field names.
     */
    public function question_print_comment_fields($questionid, $prefix) {
        global $DB;

        $this->ensure_question_loaded($questionid);
        $this->ensure_state_loaded($questionid);

    /// Work out a nice title.
        $student = $DB->get_record('user', array('id' => $this->get_userid()));
        $a = new stdClass();
        $a->fullname = fullname($student, true);
        $a->attempt = $this->get_attempt_number();

        question_print_comment_fields($this->questions[$questionid],
                $this->states[$questionid], $prefix, $this->quiz, get_string('gradingattempt', 'quiz_grading', $a));
    }

    // Private methods =====================================================================
    /**
     * Check that the state of a particular question is loaded, and if not throw an exception.
     * @param integer $id a question id.
     */
    private function ensure_state_loaded($id) {
        if (!array_key_exists($id, $this->states) || isset($this->states[$id]->_partiallyloaded)) {
            throw new moodle_quiz_exception($this, 'statenotloaded', $id);
        }
    }

    /**
     * @return string the layout of this quiz. Used by number_questions to
     * work out which questions are on which pages.
     */
    protected function get_layout_string() {
        return $this->attempt->layout;
    }

    /**
     * Get a URL for a particular question on a particular page of the quiz.
     * Used by {@link attempt_url()} and {@link review_url()}.
     *
     * @param string $script. Used in the URL like /mod/quiz/$script.php
     * @param integer $questionid the id of a particular question on the page to jump to. 0 to just use the $page parameter.
     * @param integer $page -1 to look up the page number from the questionid, otherwise the page number to go to.
     * @param boolean $showall if true, return a URL with showall=1, and not page number
     * @param integer $thispage the page we are currently on. Links to questoins on this
     *      page will just be a fragment #q123. -1 to disable this.
     * @return The requested URL.
     */
    protected function page_and_question_url($script, $questionid, $page, $showall, $thispage) {
        global $CFG;

        // Fix up $page
        if ($page == -1) {
            if ($questionid && !$showall) {
                $page = $this->questions[$questionid]->_page;
            } else {
                $page = 0;
            }
        }
        if ($showall) {
            $page = 0;
        }

        // Work out the correct start to the URL.
        if ($thispage == $page) {
            $url = '';
        } else {
            $url = $CFG->wwwroot . '/mod/quiz/' . $script . '.php?attempt=' . $this->attempt->id;
            if ($showall) {
                $url .= '&showall=1';
            } else if ($page > 0) {
                $url .= '&page=' . $page;
            }
        }

        // Add a fragment to scroll down to the question.
        if ($questionid) {
            if ($questionid == reset($this->pagequestionids[$page])) {
                // First question on page, go to top.
                $url .= '#';
            } else {
                $url .= '#q' . $questionid;
            }
        }

        return $url;
    }
}

/**
 * A PHP Iterator for conviniently looping over the questions in a quiz. The keys are the question
 * numbers (with 'i' for descriptions) and the values are the question objects.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class quiz_attempt_question_iterator implements Iterator {
    private $attemptobj; // Reference to the quiz_attempt object we provide access to.
    private $questionids; // Array of the question ids within that attempt we are iterating over.

    /**
     * Constructor. Normally, you don't want to call this directly. Instead call
     * quiz_attempt::get_question_iterator
     *
     * @param quiz_attempt $attemptobj the quiz_attempt object we will be providing access to.
     * @param mixed $page as for @see{quiz_attempt::get_question_iterator}.
     */
    public function __construct(quiz_attempt $attemptobj, $page = 'all') {
        $this->attemptobj = $attemptobj;
        $this->questionids = $attemptobj->get_question_ids($page);
    }

    // Implementation of the Iterator interface ============================================
    public function rewind() {
        reset($this->questionids);
    }

    public function current() {
        $id = current($this->questionids);
        if ($id) {
            return $this->attemptobj->get_question($id);
        } else {
            return false;
        }
    }

    public function key() {
        $id = current($this->questionids);
        if ($id) {
            return $this->attemptobj->get_question($id)->_number;
        } else {
            return false;
        }
    }

    public function next() {
        $id = next($this->questionids);
        if ($id) {
            return $this->attemptobj->get_question($id);
        } else {
            return false;
        }
    }

    public function valid() {
        return $this->current() !== false;
    }
}

/**
 * Represents the navigation panel, and builds a {@link block_contents} to allow
 * it to be output.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
abstract class quiz_nav_panel_base {
    /** @var quiz_attempt */
    protected $attemptobj;
    /** @var question_display_options */
    protected $options;
    /** @var integer */
    protected $page;
    /** @var boolean */
    protected $showall;

    public function __construct(quiz_attempt $attemptobj, $options, $page, $showall) {
        $this->attemptobj = $attemptobj;
        $this->options = $options;
        $this->page = $page;
        $this->showall = $showall;
    }

    protected function get_question_buttons() {
        $html = '<div class="qn_buttons">' . "\n";
        foreach ($this->attemptobj->get_question_iterator() as $number => $question) {
            $html .= $this->get_question_button($number, $question) . "\n";
        }
        $html .= "</div>\n";
        return $html;
    }

    protected function get_question_button($number, $question) {
        $strstate = get_string($this->attemptobj->get_question_status($question->id), 'quiz');
        $flagstate = '';
        if ($this->attemptobj->is_question_flagged($question->id)) {
            $flagstate = get_string('flagged', 'question');
        }
        return '<a href="' . s($this->get_question_url($question)) .
                '" class="qnbutton ' . $this->get_question_state_classes($question) .
                '" id="quiznavbutton' . $question->id . '" title="' . $strstate . '">' .
                $number . ' <span class="accesshide"> (' . $strstate . '
                    <span class="flagstate">' . $flagstate . '</span>)</span></a>';
    }

    protected function get_before_button_bits() {
        return '';
    }

    abstract protected function get_end_bits();

    abstract protected function get_question_url($question);

    protected function get_user_picture() {
        global $DB, $OUTPUT;
        $user = $DB->get_record('user', array('id' => $this->attemptobj->get_userid()));
        $output = '';
        $output .= '<div id="user-picture" class="clearfix">';
        $output .= $OUTPUT->user_picture($user, array('courseid'=>$this->attemptobj->get_courseid()));
        $output .= ' ' . fullname($user);
        $output .= '</div>';
        return $output;
    }

    protected function get_question_state_classes($question) {
        // The current status of the question.
        $classes = $this->attemptobj->get_question_status($question->id);

        // Plus a marker for the current page.
        if ($this->showall || $question->_page == $this->page) {
            $classes .= ' thispage';
        }

        // Plus a marker for flagged questions.
        if ($this->attemptobj->is_question_flagged($question->id)) {
            $classes .= ' flagged';
        }
        return $classes;
    }

    public function get_contents() {
        global $PAGE;
        $PAGE->requires->js_init_call('M.mod_quiz.nav.init', null, false, quiz_get_js_module());

        $content = '';
        if ($this->attemptobj->get_quiz()->showuserpicture) {
            $content .= $this->get_user_picture() . "\n";
        }
        $content .= $this->get_before_button_bits();
        $content .= $this->get_question_buttons() . "\n";
        $content .= '<div class="othernav">' . "\n" . $this->get_end_bits() . "\n</div>\n";

        $bc = new block_contents();
        $bc->id = 'quiznavigation';
        $bc->title = get_string('quiznavigation', 'quiz');
        $bc->content = $content;
        return $bc;
    }
}

/**
 * Specialisation of {@link quiz_nav_panel_base} for the attempt quiz page.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class quiz_attempt_nav_panel extends quiz_nav_panel_base {
    protected function get_question_url($question) {
        return $this->attemptobj->attempt_url($question->id, -1, $this->page);
    }

    protected function get_before_button_bits() {
        return '<div id="quiznojswarning">' . get_string('navnojswarning', 'quiz') . "</div>\n";
    }

    protected function get_end_bits() {
        global $PAGE;
        $output = '';
        $output .= '<a href="' . s($this->attemptobj->summary_url()) . '" class="endtestlink">' . get_string('finishattemptdots', 'quiz') . '</a>';
        $output .= $this->attemptobj->get_timer_html();
        return $output;
    }
}

/**
 * Specialisation of {@link quiz_nav_panel_base} for the review quiz page.
 *
 * @copyright 2008 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class quiz_review_nav_panel extends quiz_nav_panel_base {
    protected function get_question_url($question) {
        return $this->attemptobj->review_url($question->id, -1, $this->showall, $this->page);
    }

    protected function get_end_bits() {
        $html = '';
        if ($this->attemptobj->get_num_pages() > 1) {
            if ($this->showall) {
                $html .= '<a href="' . s($this->attemptobj->review_url(0, 0, false)) . '">' . get_string('showeachpage', 'quiz') . '</a>';
            } else {
                $html .= '<a href="' . s($this->attemptobj->review_url(0, 0, true)) . '">' . get_string('showall', 'quiz') . '</a>';
            }
        }
        $accessmanager = $this->attemptobj->get_access_manager(time());
        $html .= $accessmanager->print_finish_review_link($this->attemptobj->is_preview_user(), true);
        return $html;
    }
}
