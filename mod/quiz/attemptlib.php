<?php
/**
 * This class handles loading all the information about a quiz attempt into memory,
 * and making it available for attemtp.php, summary.php and review.php.
 * Initially, it only loads a minimal amout of information about each attempt - loading
 * extra information only when necessary or when asked. The class tracks which questions
 * are loaded.
 *//** */

require_once("../../config.php");

/**
 * Class for quiz exceptions. Just saves a couple of arguments on the
 * constructor for a moodle_exception.
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
 * A base class for holding and accessing information about a quiz and its questions,
 * before details of a particular attempt are loaded.
 */
class quiz {
    // Fields initialised in the constructor.
    protected $course;
    protected $cm;
    protected $quiz;
    protected $context;
    
    // Fields set later if that data is needed.
    protected $accessmanager = null;
    protected $reviewoptions = null;
    protected $ispreviewuser = null;
    protected $questions = array();
    protected $questionsnumbered = false;

    // Constructor =========================================================================
    /**
     * Constructor, assuming we already have the necessary data loaded.
     *
     * @param object $quiz the row from the quiz table.
     * @param object $cm the course_module object for this quiz.
     * @param object $course the row from the course table for the course we belong to.
     */
    function __construct($quiz, $cm, $course) {
        $this->quiz = $quiz;
        $this->cm = $cm;
        $this->course = $course;
        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    // Functions for loading more data =====================================================
    public function load_questions_on_page($page) {
        $this->load_questions(quiz_questions_on_page($this->quiz->layout, $page));
    }

    /**
     * Load some or all of the queestions for this quiz.
     *
     * @param string $questionlist comma-separate list of question ids. Blank for all.
     */
    public function load_questions($questionlist = '') {
        if (!$questionlist) {
            $questionlist = quiz_questions_in_quiz($this->quiz->layout);
        }
        $newquestions = question_load_questions($questionlist, 'qqi.grade AS maxgrade, qqi.id AS instance',
                '{quiz_question_instances} qqi ON qqi.quiz = ' . $this->quiz->id . ' AND q.id = qqi.question');
        if (is_string($newquestions)) {
            throw new moodle_quiz_exception($this, 'loadingquestionsfailed', $newquestions);
        }
        $this->questions = $this->questions + $newquestions;
        $this->questionsnumbered = false;
    }

    // Simple getters ======================================================================
    /** @return integer the course id. */
    public function get_courseid() {
        return $this->course->id;
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
     * @param integer $id the question id.
     * @return object the question object with that id.
     */
    public function get_question($id) {
        $this->ensure_question_loaded($id);
        return $this->questions[$id];
    }

    /**
     * @param integer $timenow the current time as a unix timestamp.
     * @return object and instance of the quiz_access_manager class for this quiz at this time.
     */
    public function get_access_manager($timenow) {
        if (is_null($this->accessmanager)) {
            $this->accessmanager = new quiz_access_manager($this->quiz, $timenow,
                    has_capability('mod/quiz:ignoretimelimits', $this->context, NULL, false));
        }
        return $this->accessmanager;
    }

    // URLs related to this attempt ========================================================
    /**
     * @return string the URL of this quiz's view page.
     */
    public function view_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/view.php?id=' . $this->cm->id;
    }

    // Bits of content =====================================================================
    /**
     * @return string the HTML snipped that needs to be supplied to print_header_simple
     * as the $button parameter.
     */
    public function update_module_button() {
        if (has_capability('moodle/course:manageactivities',
                get_context_instance(CONTEXT_COURSE, $this->course->id))) {
            return update_module_button($this->cm->id, $this->course->id, get_string('modulename', 'quiz'));
        } else {
            return '';
        }
    }

    /**
     * @param string $title the name of this particular quiz page.
     * @return array the data that needs to be sent to print_header_simple as the $navigation
     * parameter.
     */
    public function navigation($title) {
        return build_navigation($title, $this->cm);
    }

    // Private methods =====================================================================
    // Check that the definition of a particular question is loaded, and if not throw an exception.
    private function ensure_question_loaded($id) {
        if (!array_key_exists($id, $this->questions)) {
            throw new moodle_quiz_exception($this, 'questionnotloaded', $id);
        }
    }
}

/**
 * This class extends the quiz class to hold data about the state of a particular attempt,
 * in addition to the data about the quiz.
 */
class quiz_attempt extends quiz {
    // Fields initialised in the constructor.
    protected $attempt;

    // Fields set later if that data is needed.
    protected $states = array();

    // Constructor =========================================================================
    /**
     * Constructor from just an attemptid.
     *
     * @param integer $attemptid the id of the attempt to load. We automatically load the
     * associated quiz, course, etc.
     */
    function __construct($attemptid) {
        global $DB;
        if (!$this->attempt = quiz_load_attempt($attemptid)) {
            throw new moodle_exception('invalidattemptid', 'quiz');
        }
        if (!$quiz = $DB->get_record('quiz', array('id' => $this->attempt->quiz))) {
            throw new moodle_exception('invalidquizid', 'quiz');
        }
        if (!$course = $DB->get_record('course', array('id' => $quiz->course))) {
            throw new moodle_exception('invalidcoursemodule');
        }
        if (!$cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id)) {
            throw new moodle_exception('invalidcoursemodule');
        }
        parent::__construct($quiz, $cm, $course);
    }

    // Functions for loading more data =====================================================
    public function load_questions_on_page($page) {
        $this->load_questions(quiz_questions_on_page($this->attempt->layout, $page));
    }

    /**
     * Load some or all of the queestions for this quiz.
     *
     * @param string $questionlist comma-separate list of question ids. Blank for all.
     */
    public function load_questions($questionlist = '') {
        if (!$questionlist) {
            $questionlist = quiz_questions_in_quiz($this->attempt->layout);
        }
        parent::load_questions($questionlist);
    }

    public function load_question_states() {
        $questionstodo = array_diff_key($this->questions, $this->states);
        if (!$newstates = get_question_states($questionstodo, $this->quiz, $this->attempt)) {
            throw new moodle_quiz_exception($this, 'cannotrestore');
        }
        $this->states = $this->states + $newstates;
    }

    /**
     * Number the loaded questions.
     * 
     * At the moment, this assumes for simplicity that the loaded questions are contiguous.
     */
    public function number_questions($page = 'all') {
        if ($this->questionsnumbered) {
            return;
        }
        if ($page != 'all') {
            $pagelist = quiz_questions_in_page($this->attempt->layout, $page);
            $number = quiz_first_questionnumber($this->attempt->layout, $pagelist);
        } else {
            $number = 1;
        }
        $questionids = $this->get_question_ids($page);
        foreach ($questionids as $id) {
            if ($this->questions[$id]->length > 0) {
                $this->questions[$id]->number = $number;
                $number += $this->questions[$id]->length;
            } else {
                $this->questions[$id]->number = get_string('infoshort', 'quiz');
            }
        }
    }

    // Simple getters ======================================================================
    /** @return integer the attempt id. */
    public function get_attemptid() {
        return $this->attempt->id;
    }

    /** @return object the row from the quiz_attempts table. */
    public function get_attempt() {
        return $this->attempt;
    }

    /** @return integer the id of the user this attempt belongs to. */
    public function get_userid() {
        return $this->attempt->userid;
    }

    /** @return boolean whether this attemp has been finished (true) or is still in progress (false). */
    public function is_finished() {
        return $this->attempt->timefinish != 0;
    }

    /**
     * Wrapper that calls quiz_get_reviewoptions with the appropriate arguments.
     *
     * @return object the review optoins for this user on this attempt.
     */
    public function get_review_options() {
        if (is_null($this->reviewoptions)) {
            $this->reviewoptions = quiz_get_reviewoptions($this->quiz, $this->attempt, $this->context);
        }
        return $this->reviewoptions;
    }

    /**
     * Return the list of question ids for either a given page of the quiz, or for the 
     * whole quiz.
     *
     * @param mixed $page string 'all' or integer page number.
     * @return array the reqested list of question ids.
     */
    public function get_question_ids($page = 'all') {
        if ($page == 'all') {
            $questionlist = quiz_questions_in_quiz($this->attempt->layout);
        } else {
            $questionlist = quiz_questions_in_page($this->attempt->layout, $page);
        }
        return explode(',', $questionlist);
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
        $this->ensure_state_loaded($questionid);
        $state = $this->states[$questionid];
        switch ($state->event) {
            case QUESTION_EVENTOPEN:
                return 'open';

            case QUESTION_EVENTSAVE:
            case QUESTION_EVENTGRADE:
                return 'saved';

            case QUESTION_EVENTCLOSEANDGRADE:
            case QUESTION_EVENTCLOSE:
            case QUESTION_EVENTMANUALGRADE:
                $options = quiz_get_renderoptions($this->quiz->review, $this->states[$questionid]);
                if ($options->scores) {
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
     * Return the grade obtained on a particular question, if the user is permitted to see it.
     * You must previously have called load_question_states to load the state data about this question.
     *
     * @param integer $questionid question id of a question that belongs to this quiz.
     * @return string the formatted grade, to the number of decimal places specified by the quiz.
     */
    public function get_question_score($questionid) {
        $this->ensure_state_loaded($questionid);
        $options = quiz_get_renderoptions($this->quiz->review, $this->states[$questionid]);
        if ($options->scores) {
            return round($this->states[$questionid]->last_graded->grade, $this->quiz->decimalpoints);
        } else {
            return '';
        }
    }

    // URLs related to this attempt ========================================================
    /**
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page.
     * @param integer $question a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page.
     * @return string the URL to continue this attempt.
     */
    public function attempt_url($page = 0, $question = false) {
        global $CFG;
        $fragment = '';
        if ($question) {
            $fragment = '#q' . $question;
        }
        return $CFG->wwwroot . '/mod/quiz/attempt.php?id=' .
                $this->cm->id . '$amp;page=' . $page . $fragment;
    }

    /**
     * @return string the URL of this quiz's summary page.
     */
    public function summary_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/summary.php?attempt=' . $this->attempt->id;
    }

    /**
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page.
     * @param integer $question a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page.
     * @param boolean $showall if true, the URL will be to review the entire attempt on one page,
     * and $page will be ignored.
     * @return string the URL to review this attempt.
     */
    public function review_url($page = 0, $question = false, $showall = false) {
        global $CFG;
        $fragment = '';
        if ($question) {
            $fragment = '#q' . $question;
        }
        $param = '';
        if ($showall) {
            $param = '$amp;showall=1';
        } else if ($page) {
            $param = '$amp;page=' . $page;
        }
        return $CFG->wwwroot . '/mod/quiz/review.php?attempt=' .
                $this->attempt->id . $param . $fragment;
    }


    // Private methods =====================================================================
    // Check that the state of a particular question is loaded, and if not throw an exception.
    private function ensure_state_loaded($id) {
        if (!array_key_exists($id, $this->states)) {
            throw new moodle_quiz_exception($this, 'statenotloaded', $id);
        }
    }
}

/**
 * A PHP Iterator for conviniently looping over the questions in a quiz. The keys are the question
 * numbers (with 'i' for descriptions) and the values are the question objects.
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
        $attemptobj->number_questions($page);
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
            return $this->attemptobj->get_question($id)->number;
        } else {
            return false;
        }
        return $this->attemptobj->get_question(current($this->questionids))->number;
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
?>