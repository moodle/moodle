<?php

/**
 * This class handles loading all the information about a quiz attempt into memory,
 * and making it available for attemtp.php, summary.php and review.php.
 * Initially, it only loads a minimal amout of information about each attempt - loading
 * extra information only when necessary or when asked. The class tracks which questions
 * are loaded.
 */ 

require_once("../../config.php");

/**
 * Class for quiz exceptions.
 *
 */
class moodle_quiz_exception extends moodle_exception {
    function __construct($quizobj, $errorcode, $a = NULL, $link = '', $debuginfo = null) {
        if (!$link) {
            $link = $quizobj->view_url();
        }
        parent::__construct($errorcode, 'quiz', $link, $a, $debuginfo);
    }
}

class quiz {
    // Fields initialised in the constructor.
    protected $course;
    protected $cm;
    protected $quiz;
    protected $context;
    
    // Fields set later if that data is needed.
    protected $accessmanager = null;
    protected $reviewoptions = null;
    protected $questions = array();
    protected $questionsnumbered = false;

    // Constructor =========================================================================
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
    public function get_courseid() {
        return $this->course->id;
    }

    public function get_quizid() {
        return $this->quiz->id;
    }

    public function get_quiz() {
        return $this->quiz;
    }

    public function get_quiz_name() {
        return $this->quiz->name;
    }

    public function get_cmid() {
        return $this->cm->id;
    }

    public function get_cm() {
        return $this->cm;
    }

    public function get_question($id) {
        $this->ensure_question_loaded($id);
        return $this->questions[$id];
    }

    public function get_access_manager($timenow) {
        if (is_null($this->accessmanager)) {
            $this->accessmanager = new quiz_access_manager($this->quiz, $timenow,
                    has_capability('mod/quiz:ignoretimelimits', $this->context, NULL, false));
        }
        return $this->accessmanager;
    }

    // URLs related to this attempt ========================================================
    public function view_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/view.php?id=' . $this->cm->id;
    }

    // Bits of content =====================================================================
    public function update_module_button() {
        if (has_capability('moodle/course:manageactivities',
                get_context_instance(CONTEXT_COURSE, $this->course->id))) {
            return update_module_button($this->cm->id, $this->course->id, get_string('modulename', 'quiz'));
        } else {
            return '';
        }
    }

    public function navigation($title) {
        return build_navigation($title, $this->cm);
    }

    // Private methods =====================================================================
    private function ensure_question_loaded($id) {
        if (!array_key_exists($id, $this->questions)) {
            throw new moodle_quiz_exception($this, 'questionnotloaded', $id);
        }
    }
}

class quiz_attempt extends quiz {
    // Fields initialised in the constructor.
    protected $attempt;

    // Fields set later if that data is needed.
    protected $ispreview = null;
    protected $states = array();

    // Constructor =========================================================================
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
     * Number the loaded quetsions.
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
    public function get_attemptid() {
        return $this->attempt->id;
    }

    public function get_attempt() {
        return $this->attempt;
    }

    public function get_userid() {
        return $this->attempt->userid;
    }

    public function is_finished() {
        return $this->attempt->timefinish != 0;
    }

    public function is_preview() {
        if (is_null($this->ispreview)) {
            $this->ispreview = has_capability('mod/quiz:preview', $this->context);
        }
        return $this->ispreview;
    }

    public function get_review_options() {
        if (is_null($this->reviewoptions)) {
            $this->reviewoptions = quiz_get_reviewoptions($this->quiz, $this->attempt, $this->context);
        }
        return $this->reviewoptions;
    }

    public function get_question_ids($page = 'all') {
        if ($page == 'all') {
            $questionlist = quiz_questions_in_quiz($this->attempt->layout);
        } else {
            $questionlist = quiz_questions_in_page($this->attempt->layout, $page);
        }
        return explode(',', $questionlist);
    }

    public function get_question_iterator($page = 'all') {
        return new quiz_attempt_question_iterator($this, $page);
    }

    public function get_question_status($questionid) {
        //TODO
        return 'FROG';
    }

    /**
     * Return the grade obtained on a particular question, if the user ispermitted to see it.
     *
     * @param integer $questionid
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
    public function attempt_url($page = 0, $question = false) {
        global $CFG;
        $fragment = '';
        if ($question) {
            $fragment = '#q' . $question;
        }
        return $CFG->wwwroot . '/mod/quiz/attempt.php?id=' .
                $this->cm->id . '$amp;page=' . $page . $fragment;
    }

    public function summary_url() {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/summary.php?attempt=' . $this->attempt->id;
    }

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
    private function ensure_state_loaded($id) {
        if (!array_key_exists($id, $this->states)) {
            throw new moodle_quiz_exception($this, 'statenotloaded', $id);
        }
    }
}

class quiz_attempt_question_iterator implements Iterator {
    private $attemptobj; 
    private $questionids; 
    public function __construct(quiz_attempt $attemptobj, $page = 'all') {
        $this->attemptobj = $attemptobj;
        $attemptobj->number_questions($page);
        $this->questionids = $attemptobj->get_question_ids($page);
    }

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