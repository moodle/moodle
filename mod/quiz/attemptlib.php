<?php
/**
 * This class handles loading all the information about a quiz attempt into memory,
 * and making it available for attemtp.php, summary.php and review.php.
 * Initially, it only loads a minimal amout of information about each attempt - loading
 * extra information only when necessary or when asked. The class tracks which questions
 * are loaded.
 *//** */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

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

    // Functions for loading more data =====================================================
    public function load_questions_on_page($page) {
        $this->load_questions($this->pagequestionids[$page]);
    }

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
     * Load some or all of the questions for this quiz.
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
     * @return object and instance of the quiz_access_manager class for this quiz at this time.
     */
    public function get_access_manager($timenow) {
        if (is_null($this->accessmanager)) {
            $this->accessmanager = new quiz_access_manager($this, $timenow,
                    has_capability('mod/quiz:ignoretimelimits', $this->context, NULL, false));
        }
        return $this->accessmanager;
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
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/startattempt.php';
    }

    /**
     * @param integer $attemptid the id of an attempt.
     * @return string the URL of the review of that attempt.
     */
    public function review_url($attemptid) {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/review.php?attempt=' . $attemptid;
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
    protected function ensure_question_loaded($id) {
        if (isset($this->questions[$id]->_partiallyloaded)) {
            throw new moodle_quiz_exception($this, 'questionnotloaded', $id);
        }
    }

    private function determine_layout() {
        $this->questionids = array();
        $this->pagequestionids = array();

        // Get the appropriate layout string (from quiz or attempt).
        $layout = $this->get_layout_string();
        if (empty($layout)) {
            // Nothing to do.
            return;
        }

        // Break up the layout string into pages.
        $pagelayouts = explode(',0', quiz_clean_layout($layout, true));

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

    // Number the questions.
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
 */
class quiz_attempt extends quiz {
    // Fields initialised in the constructor.
    protected $attempt;

    // Fields set later if that data is needed.
    protected $states = array();
    protected $reviewoptions = null;

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
        $this->preload_questions();
        $this->preload_question_states();
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

    
    public function preload_question_states() {
        if (empty($this->questionids)) {
            throw new moodle_quiz_exception($this, 'noquestions', $this->edit_url());
        }
        $this->states = question_preload_states($this->attempt->uniqueid);
        if (!$this->states) {
            $this->states = array();
        }
    }

    public function load_specific_question_state($questionid, $stateid) {
        global $DB;
        $state = question_load_specific_state($this->questions[$questionid],
                $this->quiz, $this->attempt, $stateid);
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

    /** @return boolean whether this attemp is a preview attempt. */
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

    public function get_question_state($questionid) {
        $this->ensure_state_loaded($questionid);
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
     * @return object the render options for this user on this attempt.
     */
    public function get_render_options($state) {
        return quiz_get_renderoptions($this->quiz, $this->attempt, $this->context, $state);
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
                $options = $this->get_render_options($this->states[$questionid]);
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
        $options = $this->get_render_options($this->states[$questionid]);
        if ($options->scores) {
            return quiz_format_question_grade($this->quiz, $this->states[$questionid]->last_graded->grade);
        } else {
            return '';
        }
    }

    // URLs related to this attempt ========================================================
    /**
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page.
     * @param integer $questionid a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page.
     * @return string the URL to continue this attempt.
     */
    public function attempt_url($questionid = 0, $page = -1) {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/attempt.php?attempt=' . $this->attempt->id .
                $this->page_and_question_fragment($questionid, $page);
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
     * @param integer $page if specified, the URL of this particular page of the attempt, otherwise
     * the URL will go to the first page.
     * @param integer $questionid a question id. If set, will add a fragment to the URL
     * to jump to a particuar question on the page.
     * @param boolean $showall if true, the URL will be to review the entire attempt on one page,
     * and $page will be ignored.
     * @param $otherattemptid if given, link to another attempt, instead of the one we represent.
     * @return string the URL to review this attempt.
     */
    public function review_url($questionid = 0, $page = -1, $showall = false) {
        global $CFG;
        return $CFG->wwwroot . '/mod/quiz/review.php?attempt=' . $this->attempt->id .
                $this->page_and_question_fragment($questionid, $page, $showall);
    }

    // Bits of content =====================================================================
    public function get_html_head_contributions($page = 'all') {
        return get_html_head_contributions($this->get_question_ids($page),
                $this->questions, $this->states);
    }

    public function get_question_html_head_contributions($questionid) {
        return get_html_head_contributions(array($questionid),
                $this->questions, $this->states);
    }

    public function print_restart_preview_button() {
        global $CFG;
        echo '<div class="controls">';
        print_single_button($this->start_attempt_url(), array('cmid' => $this->cm->id,
                'forcenew' => true, 'sesskey' => sesskey()), get_string('startagain', 'quiz'), 'post');
        echo '</div>';
    }

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
        if ($reviewing) {
            $options = $this->get_review_options();
        } else {
            $options = $this->get_render_options($this->states[$id]);
        }
        if ($thispageurl) {
            $this->quiz->thispageurl = $thispageurl;
        } else {
            unset($thispageurl);
        }
        print_question($this->questions[$id], $this->states[$id], $this->questions[$id]->_number,
                $this->quiz, $options);
    }

    public function quiz_send_notification_emails() {
        quiz_send_notification_emails($this->course, $this->quiz, $this->attempt,
                $this->context, $this->cm);
    }

    public function get_navigation_panel($panelclass, $page) {
        $panel = new $panelclass($this, $this->get_review_options(), $page);
        return $panel->get_contents();
    }

    /// List of all this user's attempts for people who can see reports.
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
    // I am not sure it is a good idea to have update methods here - this class is only
    // about getting data out of the question engine, and helping to display it, apart from
    // this.
    public function process_comment($questionid, $comment, $grade) {
        $this->ensure_question_loaded($questionid);
        $this->ensure_state_loaded($questionid);
        $state = $this->states[$questionid];

        $error = question_process_comment($this->questions[$questionid],
                $state, $this->attempt, $comment, $grade);

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

    public function question_print_comment_fields($questionid, $prefix) {
        global $DB;

        $this->ensure_question_loaded($questionid);
        $this->ensure_state_loaded($questionid);

    /// Work out a nice title.
        $student = $DB->get_record('user', array('id' => $this->get_userid()));
        $a = new object();
        $a->fullname = fullname($student, true);
        $a->attempt = $this->get_attempt_number();

        question_print_comment_fields($this->questions[$questionid],
                $this->states[$questionid], $prefix, $this->quiz, get_string('gradingattempt', 'quiz_grading', $a));
    }

    // Private methods =====================================================================
    // Check that the state of a particular question is loaded, and if not throw an exception.
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
     * Enter description here...
     *
     * @param unknown_type $questionid the id of a particular question on the page to jump to.
     * @param integer $page -1 to look up the page number from the questionid, otherwise the page number to use.
     * @param boolean $showall
     * @return string bit to add to the end of a URL.
     */
    private function page_and_question_fragment($questionid, $page, $showall = false) {
        if ($page == -1) {
            if ($questionid) {
                $page = $this->questions[$questionid]->_page;
            } else {
                $page = 0;
            }
        }
        if ($showall) {
            $page = 0;
        }
        $fragment = '';
        if ($questionid) {
            if ($questionid == reset($this->pagequestionids[$page])) {
                // First question on page, go to top.
                $fragment = '#';
            } else {
                $fragment = '#q' . $questionid;
            }
        }
        $param = '';
        if ($showall) {
            $param = '&showall=1';
        } else if ($page > 0) {
            $param = '&page=' . $page;
        }
        return $param . $fragment;
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

abstract class quiz_nav_panel_base {
    protected $attemptobj;
    protected $options;
    protected $page;

    protected function __construct(quiz_attempt $attemptobj, $options, $page) {
          $this->attemptobj = $attemptobj;
          $this->options = $options;
          $this->page = $page;
    }

    protected function get_question_buttons() {
        global $PAGE;
        $html = '<div class="qn_buttons">' . "\n";
        foreach ($this->attemptobj->get_question_iterator() as $number => $question) {
            $html .= $this->get_question_button($number, $question) . "\n";
            $PAGE->requires->js_function_call('quiz_init_nav_button',
                    array($this->get_button_id($question), $question->id));
        }
        $html .= "</div>\n";
        return $html;
    }

    protected function get_button_id($question) {
        // The id to put on the button element in the HTML.
        return 'quiznavbutton' . $question->id;
    }

    abstract protected function get_question_button($number, $question);

    abstract protected function get_end_bits();

    protected function get_user_picture() {
        global $DB;
        $user = $DB->get_record('user', array('id' => $this->attemptobj->get_userid()));
        $output = '';
        $output .= '<div id="user-picture" class="clearfix">';
        $output .= print_user_picture($user, $this->attemptobj->get_courseid(), NULL, 0, true, false);
        $output .= ' ' . fullname($user);
        $output .= '</div>';
        return $output;
    }

    protected function get_question_state_classes($question) {
        // The current status of the question.
        $classes = $this->attemptobj->get_question_status($question->id);

        // Plus a marker for the current page.
        if ($question->_page == $this->page) {
            $classes .= ' thispage';
        }

        // Plus a marker for flagged questions.
        if ($this->attemptobj->is_question_flagged($question->id)) {
            $classes .= ' flagged';
        }
        return $classes;
    }

    public function get_contents() {
        $content = '';
        if ($this->attemptobj->get_quiz()->showuserpicture) {
            $content .= $this->get_user_picture() . "\n";
        }
        $content .= $this->get_question_buttons() . "\n";
        $content .= '<div class="othernav">' . "\n" . $this->get_end_bits() . "\n</div>\n";

        $bc = new block_contents();
        $bc->id = 'quiznavigation';
        $bc->title = get_string('quiznavigation', 'quiz');
        $bc->content = $content;
        return $bc;
    }
}

class quiz_attempt_nav_panel extends quiz_nav_panel_base {
    public function __construct(quiz_attempt $attemptobj, $options, $page) {
        parent::__construct($attemptobj, $options, $page);
    }

    protected function get_question_button($number, $question) {
        $questionsonpage = $this->attemptobj->get_question_ids($question->_page);
        $onclick = '';
        if ($question->id != reset($questionsonpage)) {
            $onclick = ' onclick="form.action = form.action + \'#q' . $question->id .
                '\'; return true;"';
        }
        return '<input type="submit" name="gotopage' . $question->_page .
                '" value="' . $number . '" class="qnbutton ' .
                $this->get_question_state_classes($question) . '" id="' .
                $this->get_button_id($question) . '" ' . $onclick . '/>';
    }

    protected function get_end_bits() {
        $output = '';
        $output .= '<input type="submit" name="gotosummary" value="' .
                get_string('endtest', 'quiz') . '" class="endtestlink" />';
        $output .= $this->attemptobj->get_timer_html();
        return $output;
    }
}

class quiz_review_nav_panel extends quiz_nav_panel_base {
    public function __construct(quiz_attempt $attemptobj, $options, $page) {
        parent::__construct($attemptobj, $options, $page);
    }

    protected function get_question_button($number, $question) {
        $strstate = get_string($this->attemptobj->get_question_status($question->id), 'quiz');
        return '<a href="' . $this->attemptobj->review_url($question->id) .
                '" class="qnbutton ' . $this->get_question_state_classes($question) . '" id="' .
                $this->get_button_id($question) . '" title="' . $strstate . '">' . $number . '<span class="accesshide">(' . $strstate . '</span></a>';
    }

    protected function get_end_bits() {
        $accessmanager = $this->attemptobj->get_access_manager(time());
        $html = '<a href="' . $this->attemptobj->review_url(0, 0, true) . '">' .
                get_string('showall', 'quiz') . '</a>';
        $html .= $accessmanager->print_finish_review_link($this->attemptobj->is_preview_user(), true);
        return $html;
    }
}
?>
