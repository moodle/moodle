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

use action_link;
use block_contents;
use cm_info;
use coding_exception;
use context_module;
use Exception;
use html_writer;
use mod_quiz\output\links_to_other_attempts;
use mod_quiz\output\renderer;
use mod_quiz\question\bank\qbank_helper;
use mod_quiz\question\display_options;
use moodle_exception;
use moodle_url;
use popup_action;
use qtype_description_question;
use question_attempt;
use question_bank;
use question_display_options;
use question_engine;
use question_out_of_sequence_exception;
use question_state;
use question_usage_by_activity;
use stdClass;

/**
 * This class represents one user's attempt at a particular quiz.
 *
 * @package   mod_quiz
 * @copyright 2008 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_attempt {

    /** @var string to identify the in progress state. */
    const IN_PROGRESS = 'inprogress';
    /** @var string to identify the overdue state. */
    const OVERDUE     = 'overdue';
    /** @var string to identify the finished state. */
    const FINISHED    = 'finished';
    /** @var string to identify the abandoned state. */
    const ABANDONED   = 'abandoned';

    /** @var int maximum number of slots in the quiz for the review page to default to show all. */
    const MAX_SLOTS_FOR_DEFAULT_REVIEW_SHOW_ALL = 50;

    /** @var quiz_settings object containing the quiz settings. */
    protected $quizobj;

    /** @var stdClass the quiz_attempts row. */
    protected $attempt;

    /** @var question_usage_by_activity the question usage for this quiz attempt. */
    protected $quba;

    /**
     * @var array of slot information. These objects contain ->slot (int),
     *      ->requireprevious (bool), ->questionids (int) the original question for random questions,
     *      ->firstinsection (bool), ->section (stdClass from $this->sections).
     *      This does not contain page - get that from {@see get_question_page()} -
     *      or maxmark - get that from $this->quba.
     */
    protected $slots;

    /** @var array of quiz_sections rows, with a ->lastslot field added. */
    protected $sections;

    /** @var array page no => array of slot numbers on the page in order. */
    protected $pagelayout;

    /** @var array slot => displayed question number for this slot. (E.g. 1, 2, 3 or 'i'.) */
    protected $questionnumbers;

    /** @var array slot => page number for this slot. */
    protected $questionpages;

    /** @var display_options cache for the appropriate review options. */
    protected $reviewoptions = null;

    // Constructor =============================================================.
    /**
     * Constructor assuming we already have the necessary data loaded.
     *
     * @param stdClass $attempt the row of the quiz_attempts table.
     * @param stdClass $quiz the quiz object for this attempt and user.
     * @param stdClass|cm_info $cm the course_module object for this quiz.
     * @param stdClass $course the row from the course table for the course we belong to.
     * @param bool $loadquestions (optional) if true, the default, load all the details
     *      of the state of each question. Else just set up the basic details of the attempt.
     */
    public function __construct($attempt, $quiz, $cm, $course, $loadquestions = true) {
        $this->attempt = $attempt;
        $this->quizobj = new quiz_settings($quiz, $cm, $course);

        if ($loadquestions) {
            $this->load_questions();
        }
    }

    /**
     * Used by {create()} and {create_from_usage_id()}.
     *
     * @param array $conditions passed to $DB->get_record('quiz_attempts', $conditions).
     * @return quiz_attempt the desired instance of this class.
     */
    protected static function create_helper($conditions) {
        global $DB;

        $attempt = $DB->get_record('quiz_attempts', $conditions, '*', MUST_EXIST);
        $quiz = access_manager::load_quiz_and_settings($attempt->quiz);
        $course = get_course($quiz->course);
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id, false, MUST_EXIST);

        // Update quiz with override information.
        $quiz = quiz_update_effective_access($quiz, $attempt->userid);

        return new quiz_attempt($attempt, $quiz, $cm, $course);
    }

    /**
     * Static function to create a new quiz_attempt object given an attemptid.
     *
     * @param int $attemptid the attempt id.
     * @return quiz_attempt the new quiz_attempt object
     */
    public static function create($attemptid) {
        return self::create_helper(['id' => $attemptid]);
    }

    /**
     * Static function to create a new quiz_attempt object given a usage id.
     *
     * @param int $usageid the attempt usage id.
     * @return quiz_attempt the new quiz_attempt object
     */
    public static function create_from_usage_id($usageid) {
        return self::create_helper(['uniqueid' => $usageid]);
    }

    /**
     * Get a human-readable name for one of the quiz attempt states.
     *
     * @param string $state one of the state constants like IN_PROGRESS.
     * @return string the human-readable state name.
     */
    public static function state_name($state) {
        return quiz_attempt_state_name($state);
    }

    /**
     * This method can be called later if the object was constructed with $loadquestions = false.
     */
    public function load_questions() {
        global $DB;

        if (isset($this->quba)) {
            throw new coding_exception('This quiz attempt has already had the questions loaded.');
        }

        $this->quba = question_engine::load_questions_usage_by_activity($this->attempt->uniqueid);
        $this->slots = $DB->get_records('quiz_slots',
                ['quizid' => $this->get_quizid()], 'slot', 'slot, id, requireprevious, displaynumber');
        $this->sections = array_values($DB->get_records('quiz_sections',
                ['quizid' => $this->get_quizid()], 'firstslot'));

        $this->link_sections_and_slots();
        $this->determine_layout();
        $this->number_questions();
    }

    /**
     * Preload all attempt step users to show in Response history.
     */
    public function preload_all_attempt_step_users(): void {
        $this->quba->preload_all_step_users();
    }

    /**
     * Let each slot know which section it is part of.
     */
    protected function link_sections_and_slots() {
        foreach ($this->sections as $i => $section) {
            if (isset($this->sections[$i + 1])) {
                $section->lastslot = $this->sections[$i + 1]->firstslot - 1;
            } else {
                $section->lastslot = count($this->slots);
            }
            for ($slot = $section->firstslot; $slot <= $section->lastslot; $slot += 1) {
                $this->slots[$slot]->section = $section;
            }
        }
    }

    /**
     * Parse attempt->layout to populate the other arrays that represent the layout.
     */
    protected function determine_layout() {

        // Break up the layout string into pages.
        $pagelayouts = explode(',0', $this->attempt->layout);

        // Strip off any empty last page (normally there is one).
        if (end($pagelayouts) == '') {
            array_pop($pagelayouts);
        }

        // File the ids into the arrays.
        // Tracking which is the first slot in each section in this attempt is
        // trickier than you might guess, since the slots in this section
        // may be shuffled, so $section->firstslot (the lowest numbered slot in
        // the section) may not be the first one.
        $unseensections = $this->sections;
        $this->pagelayout = [];
        foreach ($pagelayouts as $page => $pagelayout) {
            $pagelayout = trim($pagelayout, ',');
            if ($pagelayout == '') {
                continue;
            }
            $this->pagelayout[$page] = explode(',', $pagelayout);
            foreach ($this->pagelayout[$page] as $slot) {
                $sectionkey = array_search($this->slots[$slot]->section, $unseensections);
                if ($sectionkey !== false) {
                    $this->slots[$slot]->firstinsection = true;
                    unset($unseensections[$sectionkey]);
                } else {
                    $this->slots[$slot]->firstinsection = false;
                }
            }
        }
    }

    /**
     * Work out the number to display for each question/slot.
     */
    protected function number_questions() {
        $number = 1;
        foreach ($this->pagelayout as $page => $slots) {
            foreach ($slots as $slot) {
                if ($length = $this->is_real_question($slot)) {
                    // Whether question numbering is customised or is numeric and automatically incremented.
                    if ($this->slots[$slot]->displaynumber !== null && $this->slots[$slot]->displaynumber !== '' &&
                            !$this->slots[$slot]->section->shufflequestions) {
                        $this->questionnumbers[$slot] = $this->slots[$slot]->displaynumber;
                    } else {
                        $this->questionnumbers[$slot] = (string) $number;
                    }
                    $number += $length;
                } else {
                    $this->questionnumbers[$slot] = get_string('infoshort', 'quiz');
                }
                $this->questionpages[$slot] = $page;
            }
        }
    }

    /**
     * If the given page number is out of range (before the first page, or after
     * the last page, change it to be within range).
     *
     * @param int $page the requested page number.
     * @return int a safe page number to use.
     */
    public function force_page_number_into_range($page) {
        return min(max($page, 0), count($this->pagelayout) - 1);
    }

    // Simple getters ==========================================================.

    /**
     * Get the raw quiz settings object.
     *
     * @return stdClass
     */
    public function get_quiz() {
        return $this->quizobj->get_quiz();
    }

    /**
     * Get the {@see seb_quiz_settings} object for this quiz.
     *
     * @return quiz_settings
     */
    public function get_quizobj() {
        return $this->quizobj;
    }

    /**
     * Git the id of the course this quiz belongs to.
     *
     * @return int the course id.
     */
    public function get_courseid() {
        return $this->quizobj->get_courseid();
    }

    /**
     * Get the course settings object.
     *
     * @return stdClass the course settings object.
     */
    public function get_course() {
        return $this->quizobj->get_course();
    }

    /**
     * Get the quiz id.
     *
     * @return int the quiz id.
     */
    public function get_quizid() {
        return $this->quizobj->get_quizid();
    }

    /**
     * Get the name of this quiz.
     *
     * @return string Quiz name, directly from the database (format_string must be called before output).
     */
    public function get_quiz_name() {
        return $this->quizobj->get_quiz_name();
    }

    /**
     * Get the quiz navigation method.
     *
     * @return int QUIZ_NAVMETHOD_FREE or QUIZ_NAVMETHOD_SEQ.
     */
    public function get_navigation_method() {
        return $this->quizobj->get_navigation_method();
    }

    /**
     * Get the course_module for this quiz.
     *
     * @return stdClass|cm_info the course_module object.
     */
    public function get_cm() {
        return $this->quizobj->get_cm();
    }

    /**
     * Get the course-module id.
     *
     * @return int the course_module id.
     */
    public function get_cmid() {
        return $this->quizobj->get_cmid();
    }

    /**
     * Get the quiz context.
     *
     * @return context_module the context of the quiz this attempt belongs to.
     */
    public function get_context(): context_module {
        return $this->quizobj->get_context();
    }

    /**
     * Is the current user is someone who previews the quiz, rather than attempting it?
     *
     * @return bool true user is a preview user. False, if they can do real attempts.
     */
    public function is_preview_user() {
        return $this->quizobj->is_preview_user();
    }

    /**
     * Get the number of attempts the user is allowed at this quiz.
     *
     * @return int the number of attempts allowed at this quiz (0 = infinite).
     */
    public function get_num_attempts_allowed() {
        return $this->quizobj->get_num_attempts_allowed();
    }

    /**
     * Get the number of quizzes in the quiz attempt.
     *
     * @return int number pages.
     */
    public function get_num_pages() {
        return count($this->pagelayout);
    }

    /**
     * Get the access_manager for this quiz attempt.
     *
     * @param int $timenow the current time as a unix timestamp.
     * @return access_manager and instance of the access_manager class
     *      for this quiz at this time.
     */
    public function get_access_manager($timenow) {
        return $this->quizobj->get_access_manager($timenow);
    }

    /**
     * Get the id of this attempt.
     *
     * @return int the attempt id.
     */
    public function get_attemptid() {
        return $this->attempt->id;
    }

    /**
     * Get the question-usage id corresponding to this quiz attempt.
     *
     * @return int the attempt unique id.
     */
    public function get_uniqueid() {
        return $this->attempt->uniqueid;
    }

    /**
     * Get the raw quiz attempt object.
     *
     * @return stdClass the row from the quiz_attempts table.
     */
    public function get_attempt() {
        return $this->attempt;
    }

    /**
     * Get the attempt number.
     *
     * @return int the number of this attempt (is it this user's first, second, ... attempt).
     */
    public function get_attempt_number() {
        return $this->attempt->attempt;
    }

    /**
     * Get the state of this attempt.
     *
     * @return string {@see IN_PROGRESS}, {@see FINISHED}, {@see OVERDUE} or {@see ABANDONED}.
     */
    public function get_state() {
        return $this->attempt->state;
    }

    /**
     * Get the id of the user this attempt belongs to.
     * @return int user id.
     */
    public function get_userid() {
        return $this->attempt->userid;
    }

    /**
     * Get the current page of the attempt
     * @return int page number.
     */
    public function get_currentpage() {
        return $this->attempt->currentpage;
    }

    /**
     * Get the total number of marks that the user had scored on all the questions.
     *
     * @return float
     */
    public function get_sum_marks() {
        return $this->attempt->sumgrades;
    }

    /**
     * Has this attempt been finished?
     *
     * States {@see FINISHED} and {@see ABANDONED} are both considered finished in this state.
     * Other states are not.
     *
     * @return bool
     */
    public function is_finished() {
        return $this->attempt->state == self::FINISHED || $this->attempt->state == self::ABANDONED;
    }

    /**
     * Is this attempt a preview?
     *
     * @return bool true if it is.
     */
    public function is_preview() {
        return $this->attempt->preview;
    }

    /**
     * Does this attempt belong to the current user?
     *
     * @return bool true => own attempt/preview. false => reviewing someone else's.
     */
    public function is_own_attempt() {
        global $USER;
        return $this->attempt->userid == $USER->id;
    }

    /**
     * Is this attempt is a preview belonging to the current user.
     *
     * @return bool true if it is.
     */
    public function is_own_preview() {
        return $this->is_own_attempt() &&
                $this->is_preview_user() && $this->attempt->preview;
    }

    /**
     * Is the current user allowed to review this attempt. This applies when
     * {@see is_own_attempt()} returns false.
     *
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
        $studentsgroups = groups_get_all_groups(
                $cm->course, $this->attempt->userid, $cm->groupingid);
        return $teachersgroups && $studentsgroups &&
                array_intersect(array_keys($teachersgroups), array_keys($studentsgroups));
    }

    /**
     * Has the student, in this attempt, engaged with the quiz in a non-trivial way?
     *
     * That is, is there any question worth a non-zero number of marks, where
     * the student has made some response that we have saved?
     *
     * @return bool true if we have saved a response for at least one graded question.
     */
    public function has_response_to_at_least_one_graded_question() {
        foreach ($this->quba->get_attempt_iterator() as $qa) {
            if ($qa->get_max_mark() == 0) {
                continue;
            }
            if ($qa->get_num_steps() > 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Do any questions in this attempt need to be graded manually?
     *
     * @return bool True if we have at least one question still needs manual grading.
     */
    public function requires_manual_grading(): bool {
        return $this->quba->get_total_mark() === null;
    }

    /**
     * Get extra summary information about this attempt.
     *
     * Some behaviours may be able to provide interesting summary information
     * about the attempt as a whole, and this method provides access to that data.
     * To see how this works, try setting a quiz to one of the CBM behaviours,
     * and then look at the extra information displayed at the top of the quiz
     * review page once you have submitted an attempt.
     *
     * In the return value, the array keys are identifiers of the form
     * qbehaviour_behaviourname_meaningfullkey. For qbehaviour_deferredcbm_highsummary.
     * The values are arrays with two items, title and content. Each of these
     * will be either a string, or a renderable.
     *
     * @param question_display_options $options the display options for this quiz attempt at this time.
     * @return array as described above.
     */
    public function get_additional_summary_data(question_display_options $options) {
        return $this->quba->get_summary_information($options);
    }

    /**
     * Get the overall feedback corresponding to a particular mark.
     *
     * @param number $grade a particular grade.
     * @return string the feedback.
     */
    public function get_overall_feedback($grade) {
        return quiz_feedback_for_grade($grade, $this->get_quiz(),
                $this->quizobj->get_context());
    }

    /**
     * Wrapper round the has_capability function that automatically passes in the quiz context.
     *
     * @param string $capability the name of the capability to check. For example mod/forum:view.
     * @param int|null $userid A user id. If null checks the permissions of the current user.
     * @param bool $doanything If false, ignore effect of admin role assignment.
     * @return boolean true if the user has this capability, otherwise false.
     */
    public function has_capability($capability, $userid = null, $doanything = true) {
        return $this->quizobj->has_capability($capability, $userid, $doanything);
    }

    /**
     * Wrapper round the require_capability function that automatically passes in the quiz context.
     *
     * @param string $capability the name of the capability to check. For example mod/forum:view.
     * @param int|null $userid A user id. If null checks the permissions of the current user.
     * @param bool $doanything If false, ignore effect of admin role assignment.
     */
    public function require_capability($capability, $userid = null, $doanything = true) {
        $this->quizobj->require_capability($capability, $userid, $doanything);
    }

    /**
     * Check the appropriate capability to see whether this user may review their own attempt.
     * If not, prints an error.
     */
    public function check_review_capability() {
        if ($this->get_attempt_state() == display_options::IMMEDIATELY_AFTER) {
            $capability = 'mod/quiz:attempt';
        } else {
            $capability = 'mod/quiz:reviewmyattempts';
        }

        // These next tests are in a slightly funny order. The point is that the
        // common and most performance-critical case is students attempting a quiz,
        // so we want to check that permission first.

        if ($this->has_capability($capability)) {
            // User has the permission that lets you do the quiz as a student. Fine.
            return;
        }

        if ($this->has_capability('mod/quiz:viewreports') ||
                $this->has_capability('mod/quiz:preview')) {
            // User has the permission that lets teachers review. Fine.
            return;
        }

        // They should not be here. Trigger the standard no-permission error
        // but using the name of the student capability.
        // We know this will fail. We just want the standard exception thrown.
        $this->require_capability($capability);
    }

    /**
     * Checks whether a user may navigate to a particular slot.
     *
     * @param int $slot the target slot (currently does not affect the answer).
     * @return bool true if the navigation should be allowed.
     */
    public function can_navigate_to($slot) {
        if ($this->attempt->state == self::OVERDUE) {
            // When the attempt is overdue, students can only see the
            // attempt summary page and cannot navigate anywhere else.
            return false;
        }

        return $this->get_navigation_method() == QUIZ_NAVMETHOD_FREE;
    }

    /**
     * Get where we are time-wise in relation to this attempt and the quiz settings.
     *
     * @return int one of {@see display_options::DURING}, {@see display_options::IMMEDIATELY_AFTER},
     *      {@see display_options::LATER_WHILE_OPEN} or {@see display_options::AFTER_CLOSE}.
     */
    public function get_attempt_state() {
        return quiz_attempt_state($this->get_quiz(), $this->attempt);
    }

    /**
     * Wrapper that the correct display_options for this quiz at the
     * moment.
     *
     * @param bool $reviewing true for options when reviewing, false for when attempting.
     * @return question_display_options the render options for this user on this attempt.
     */
    public function get_display_options($reviewing) {
        if ($reviewing) {
            if (is_null($this->reviewoptions)) {
                $this->reviewoptions = quiz_get_review_options($this->get_quiz(),
                        $this->attempt, $this->quizobj->get_context());
                if ($this->is_own_preview()) {
                    // It should  always be possible for a teacher to review their
                    // own preview irrespective of the review options settings.
                    $this->reviewoptions->attempt = true;
                }
            }
            return $this->reviewoptions;

        } else {
            $options = display_options::make_from_quiz($this->get_quiz(),
                    display_options::DURING);
            $options->flags = quiz_get_flag_option($this->attempt, $this->quizobj->get_context());
            return $options;
        }
    }

    /**
     * Wrapper that the correct display_options for this quiz at the
     * moment.
     *
     * @param bool $reviewing true for review page, else attempt page.
     * @param int $slot which question is being displayed.
     * @param moodle_url $thispageurl to return to after the editing form is
     *      submitted or cancelled. If null, no edit link will be generated.
     *
     * @return question_display_options the render options for this user on this
     *      attempt, with extra info to generate an edit link, if applicable.
     */
    public function get_display_options_with_edit_link($reviewing, $slot, $thispageurl) {
        $options = clone($this->get_display_options($reviewing));

        if (!$thispageurl) {
            return $options;
        }

        if (!($reviewing || $this->is_preview())) {
            return $options;
        }

        $question = $this->quba->get_question($slot, false);
        if (!question_has_capability_on($question, 'edit', $question->category)) {
            return $options;
        }

        $options->editquestionparams['cmid'] = $this->get_cmid();
        $options->editquestionparams['returnurl'] = $thispageurl;

        return $options;
    }

    /**
     * Is a particular page the last one in the quiz?
     *
     * @param int $page a page number
     * @return bool true if that is the last page of the quiz.
     */
    public function is_last_page($page) {
        return $page == count($this->pagelayout) - 1;
    }

    /**
     * Return the list of slot numbers for either a given page of the quiz, or for the
     * whole quiz.
     *
     * @param mixed $page string 'all' or integer page number.
     * @return array the requested list of slot numbers.
     */
    public function get_slots($page = 'all') {
        if ($page === 'all') {
            $numbers = [];
            foreach ($this->pagelayout as $numbersonpage) {
                $numbers = array_merge($numbers, $numbersonpage);
            }
            return $numbers;
        } else {
            return $this->pagelayout[$page];
        }
    }

    /**
     * Return the list of slot numbers for either a given page of the quiz, or for the
     * whole quiz.
     *
     * @param mixed $page string 'all' or integer page number.
     * @return array the requested list of slot numbers.
     */
    public function get_active_slots($page = 'all') {
        $activeslots = [];
        foreach ($this->get_slots($page) as $slot) {
            if (!$this->is_blocked_by_previous_question($slot)) {
                $activeslots[] = $slot;
            }
        }
        return $activeslots;
    }

    /**
     * Helper method for unit tests. Get the underlying question usage object.
     *
     * @return question_usage_by_activity the usage.
     */
    public function get_question_usage() {
        if (!(PHPUNIT_TEST || defined('BEHAT_TEST'))) {
            throw new coding_exception('get_question_usage is only for use in unit tests. ' .
                    'For other operations, use the quiz_attempt api, or extend it properly.');
        }
        return $this->quba;
    }

    /**
     * Get the question_attempt object for a particular question in this attempt.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return question_attempt the requested question_attempt.
     */
    public function get_question_attempt($slot) {
        return $this->quba->get_question_attempt($slot);
    }

    /**
     * Get all the question_attempt objects that have ever appeared in a given slot.
     *
     * This relates to the 'Try another question like this one' feature.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return question_attempt[] the attempts.
     */
    public function all_question_attempts_originally_in_slot($slot) {
        $qas = [];
        foreach ($this->quba->get_attempt_iterator() as $qa) {
            if ($qa->get_metadata('originalslot') == $slot) {
                $qas[] = $qa;
            }
        }
        $qas[] = $this->quba->get_question_attempt($slot);
        return $qas;
    }

    /**
     * Is a particular question in this attempt a real question, or something like a description.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return int whether that question is a real question. Actually returns the
     *     question length, which could theoretically be greater than one.
     */
    public function is_real_question($slot) {
        return $this->quba->get_question($slot, false)->length;
    }

    /**
     * Is a particular question in this attempt a real question, or something like a description.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return bool whether that question is a real question.
     */
    public function is_question_flagged($slot) {
        return $this->quba->get_question_attempt($slot)->is_flagged();
    }

    /**
     * Checks whether the question in this slot requires the previous
     * question to have been completed.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return bool whether the previous question must have been completed before
     *      this one can be seen.
     */
    public function is_blocked_by_previous_question($slot) {
        return $slot > 1 && isset($this->slots[$slot]) && $this->slots[$slot]->requireprevious &&
            !$this->slots[$slot]->section->shufflequestions &&
            !$this->slots[$slot - 1]->section->shufflequestions &&
            $this->get_navigation_method() != QUIZ_NAVMETHOD_SEQ &&
            !$this->get_question_state($slot - 1)->is_finished() &&
            $this->quba->can_question_finish_during_attempt($slot - 1);
    }

    /**
     * Is it possible for this question to be re-started within this attempt?
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return bool whether the student should be given the option to restart this question now.
     */
    public function can_question_be_redone_now($slot) {
        return $this->get_quiz()->canredoquestions && !$this->is_finished() &&
                $this->get_question_state($slot)->is_finished();
    }

    /**
     * Given a slot in this attempt, which may or not be a redone question, return the original slot.
     *
     * @param int $slot identifies a particular question in this attempt.
     * @return int the slot where this question was originally.
     */
    public function get_original_slot($slot) {
        $originalslot = $this->quba->get_question_attempt_metadata($slot, 'originalslot');
        if ($originalslot) {
            return $originalslot;
        } else {
            return $slot;
        }
    }

    /**
     * Get the displayed question number for a slot.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return string the displayed question number for the question in this slot.
     *      For example '1', '2', '3' or 'i'.
     */
    public function get_question_number($slot): string {
        return $this->questionnumbers[$slot];
    }

    /**
     * If the section heading, if any, that should come just before this slot.
     *
     * @param int $slot identifies a particular question in this attempt.
     * @return string|null the required heading, or null if there is not one here.
     */
    public function get_heading_before_slot($slot) {
        if ($this->slots[$slot]->firstinsection) {
            return $this->slots[$slot]->section->heading;
        } else {
            return null;
        }
    }

    /**
     * Return the page of the quiz where this question appears.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return int the page of the quiz this question appears on.
     */
    public function get_question_page($slot) {
        return $this->questionpages[$slot];
    }

    /**
     * Return the grade obtained on a particular question, if the user is permitted
     * to see it. You must previously have called load_question_states to load the
     * state data about this question.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return string the formatted grade, to the number of decimal places specified
     *      by the quiz.
     */
    public function get_question_name($slot) {
        return $this->quba->get_question($slot, false)->name;
    }

    /**
     * Return the {@see question_state} that this question is in.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return question_state the state this question is in.
     */
    public function get_question_state($slot) {
        return $this->quba->get_question_state($slot);
    }

    /**
     * Return the grade obtained on a particular question, if the user is permitted
     * to see it. You must previously have called load_question_states to load the
     * state data about this question.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @param bool $showcorrectness Whether right/partial/wrong states should
     *      be distinguished.
     * @return string the formatted grade, to the number of decimal places specified
     *      by the quiz.
     */
    public function get_question_status($slot, $showcorrectness) {
        return $this->quba->get_question_state_string($slot, $showcorrectness);
    }

    /**
     * Return the grade obtained on a particular question, if the user is permitted
     * to see it. You must previously have called load_question_states to load the
     * state data about this question.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @param bool $showcorrectness Whether right/partial/wrong states should
     *      be distinguished.
     * @return string class name for this state.
     */
    public function get_question_state_class($slot, $showcorrectness) {
        return $this->quba->get_question_state_class($slot, $showcorrectness);
    }

    /**
     * Return the grade obtained on a particular question.
     *
     * You must previously have called load_question_states to load the state
     * data about this question.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return string the formatted grade, to the number of decimal places specified by the quiz.
     */
    public function get_question_mark($slot) {
        return quiz_format_question_grade($this->get_quiz(), $this->quba->get_question_mark($slot));
    }

    /**
     * Get the time of the most recent action performed on a question.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @return int timestamp.
     */
    public function get_question_action_time($slot) {
        return $this->quba->get_question_action_time($slot);
    }

    /**
     * Return the question type name for a given slot within the current attempt.
     *
     * @param int $slot the number used to identify this question within this attempt.
     * @return string the question type name.
     */
    public function get_question_type_name($slot) {
        return $this->quba->get_question($slot, false)->get_type_name();
    }

    /**
     * Get the time remaining for an in-progress attempt, if the time is short
     * enough that it would be worth showing a timer.
     *
     * @param int $timenow the time to consider as 'now'.
     * @return int|false the number of seconds remaining for this attempt.
     *      False if there is no limit.
     */
    public function get_time_left_display($timenow) {
        if ($this->attempt->state != self::IN_PROGRESS) {
            return false;
        }
        return $this->get_access_manager($timenow)->get_time_left_display($this->attempt, $timenow);
    }


    /**
     * Get the time when this attempt was submitted.
     *
     * @return int timestamp, or 0 if it has not been submitted yet.
     */
    public function get_submitted_date() {
        return $this->attempt->timefinish;
    }

    /**
     * If the attempt is in an applicable state, work out the time by which the
     * student should next do something.
     *
     * @return int timestamp by which the student needs to do something.
     */
    public function get_due_date() {
        $deadlines = [];
        if ($this->quizobj->get_quiz()->timelimit) {
            $deadlines[] = $this->attempt->timestart + $this->quizobj->get_quiz()->timelimit;
        }
        if ($this->quizobj->get_quiz()->timeclose) {
            $deadlines[] = $this->quizobj->get_quiz()->timeclose;
        }
        if ($deadlines) {
            $duedate = min($deadlines);
        } else {
            return false;
        }

        switch ($this->attempt->state) {
            case self::IN_PROGRESS:
                return $duedate;

            case self::OVERDUE:
                return $duedate + $this->quizobj->get_quiz()->graceperiod;

            default:
                throw new coding_exception('Unexpected state: ' . $this->attempt->state);
        }
    }

    // URLs related to this attempt ============================================.

    /**
     * Get the URL of this quiz's view.php page.
     *
     * @return moodle_url quiz view url.
     */
    public function view_url() {
        return $this->quizobj->view_url();
    }

    /**
     * Get the URL to start or continue an attempt.
     *
     * @param int|null $slot which question in the attempt to go to after starting (optional).
     * @param int $page which page in the attempt to go to after starting.
     * @return moodle_url the URL of this quiz's edit page. Needs to be POSTed to with a cmid parameter.
     */
    public function start_attempt_url($slot = null, $page = -1) {
        if ($page == -1 && !is_null($slot)) {
            $page = $this->get_question_page($slot);
        } else {
            $page = 0;
        }
        return $this->quizobj->start_attempt_url($page);
    }

    /**
     * Generates the title of the attempt page.
     *
     * @param int $page the page number (starting with 0) in the attempt.
     * @return string attempt page title.
     */
    public function attempt_page_title(int $page) : string {
        if ($this->get_num_pages() > 1) {
            $a = new stdClass();
            $a->name = $this->get_quiz_name();
            $a->currentpage = $page + 1;
            $a->totalpages = $this->get_num_pages();
            $title = get_string('attempttitlepaged', 'quiz', $a);
        } else {
            $title = get_string('attempttitle', 'quiz', $this->get_quiz_name());
        }

        return $title;
    }

    /**
     * Get the URL of a particular page within this attempt.
     *
     * @param int|null $slot if specified, the slot number of a specific question to link to.
     * @param int $page if specified, a particular page to link to. If not given deduced
     *      from $slot, or goes to the first page.
     * @param int $thispage if not -1, the current page. Will cause links to other things on
     *      this page to be output as only a fragment.
     * @return moodle_url the URL to continue this attempt.
     */
    public function attempt_url($slot = null, $page = -1, $thispage = -1) {
        return $this->page_and_question_url('attempt', $slot, $page, false, $thispage);
    }

    /**
     * Generates the title of the summary page.
     *
     * @return string summary page title.
     */
    public function summary_page_title() : string {
        return get_string('attemptsummarytitle', 'quiz', $this->get_quiz_name());
    }

    /**
     * Get the URL of the summary page of this attempt.
     *
     * @return moodle_url the URL of this quiz's summary page.
     */
    public function summary_url() {
        return new moodle_url('/mod/quiz/summary.php', ['attempt' => $this->attempt->id, 'cmid' => $this->get_cmid()]);
    }

    /**
     * Get the URL to which the attempt data should be submitted.
     *
     * @return moodle_url the URL of this quiz's summary page.
     */
    public function processattempt_url() {
        return new moodle_url('/mod/quiz/processattempt.php');
    }

    /**
     * Generates the title of the review page.
     *
     * @param int $page the page number (starting with 0) in the attempt.
     * @param bool $showall whether the review page contains the entire attempt on one page.
     * @return string title of the review page.
     */
    public function review_page_title(int $page, bool $showall = false) : string {
        if (!$showall && $this->get_num_pages() > 1) {
            $a = new stdClass();
            $a->name = $this->get_quiz_name();
            $a->currentpage = $page + 1;
            $a->totalpages = $this->get_num_pages();
            $title = get_string('attemptreviewtitlepaged', 'quiz', $a);
        } else {
            $title = get_string('attemptreviewtitle', 'quiz', $this->get_quiz_name());
        }

        return $title;
    }

    /**
     * Get the URL of a particular page in the review of this attempt.
     *
     * @param int|null $slot indicates which question to link to.
     * @param int $page if specified, the URL of this particular page of the attempt, otherwise
     *      the URL will go to the first page.  If -1, deduce $page from $slot.
     * @param bool|null $showall if true, the URL will be to review the entire attempt on one page,
     *      and $page will be ignored. If null, a sensible default will be chosen.
     * @param int $thispage if not -1, the current page. Will cause links to other things on
     *      this page to be output as only a fragment.
     * @return moodle_url the URL to review this attempt.
     */
    public function review_url($slot = null, $page = -1, $showall = null, $thispage = -1) {
        return $this->page_and_question_url('review', $slot, $page, $showall, $thispage);
    }

    /**
     * By default, should this script show all questions on one page for this attempt?
     *
     * @param string $script the script name, e.g. 'attempt', 'summary', 'review'.
     * @return bool whether show all on one page should be on by default.
     */
    public function get_default_show_all($script) {
        return $script === 'review' && count($this->questionpages) < self::MAX_SLOTS_FOR_DEFAULT_REVIEW_SHOW_ALL;
    }

    // Bits of content =========================================================.

    /**
     * If $reviewoptions->attempt is false, meaning that students can't review this
     * attempt at the moment, return an appropriate string explaining why.
     *
     * @param bool $short if true, return a shorter string.
     * @return string an appropriate message.
     */
    public function cannot_review_message($short = false) {
        return $this->quizobj->cannot_review_message(
                $this->get_attempt_state(), $short);
    }

    /**
     * Initialise the JS etc. required all the questions on a page.
     *
     * @param int|string $page a page number, or 'all'.
     * @param bool $showall if true, forces page number to all.
     * @return string HTML to output - mostly obsolete, will probably be an empty string.
     */
    public function get_html_head_contributions($page = 'all', $showall = false) {
        if ($showall) {
            $page = 'all';
        }
        $result = '';
        foreach ($this->get_slots($page) as $slot) {
            $result .= $this->quba->render_question_head_html($slot);
        }
        $result .= question_engine::initialise_js();
        return $result;
    }

    /**
     * Initialise the JS etc. required by one question.
     *
     * @param int $slot the question slot number.
     * @return string HTML to output - but this is mostly obsolete. Will probably be an empty string.
     */
    public function get_question_html_head_contributions($slot) {
        return $this->quba->render_question_head_html($slot) .
                question_engine::initialise_js();
    }

    /**
     * Print the HTML for the start new preview button, if the current user
     * is allowed to see one.
     *
     * @return string HTML for the button.
     */
    public function restart_preview_button() {
        global $OUTPUT;
        if ($this->is_preview() && $this->is_preview_user()) {
            return $OUTPUT->single_button(new moodle_url(
                    $this->start_attempt_url(), ['forcenew' => true]),
                    get_string('startnewpreview', 'quiz'));
        } else {
            return '';
        }
    }

    /**
     * Generate the HTML that displays the question in its current state, with
     * the appropriate display options.
     *
     * @param int $slot identifies the question in the attempt.
     * @param bool $reviewing is the being printed on an attempt or a review page.
     * @param renderer $renderer the quiz renderer.
     * @param moodle_url $thispageurl the URL of the page this question is being printed on.
     * @return string HTML for the question in its current state.
     */
    public function render_question($slot, $reviewing, renderer $renderer, $thispageurl = null) {
        if ($this->is_blocked_by_previous_question($slot)) {
            $placeholderqa = $this->make_blocked_question_placeholder($slot);

            $displayoptions = $this->get_display_options($reviewing);
            $displayoptions->manualcomment = question_display_options::HIDDEN;
            $displayoptions->history = question_display_options::HIDDEN;
            $displayoptions->readonly = true;

            return html_writer::div($placeholderqa->render($displayoptions,
                    $this->get_question_number($this->get_original_slot($slot))),
                    'mod_quiz-blocked_question_warning');
        }

        return $this->render_question_helper($slot, $reviewing, $thispageurl, $renderer, null);
    }

    /**
     * Helper used by {@see render_question()} and {@see render_question_at_step()}.
     *
     * @param int $slot identifies the question in the attempt.
     * @param bool $reviewing is the being printed on an attempt or a review page.
     * @param moodle_url $thispageurl the URL of the page this question is being printed on.
     * @param renderer $renderer the quiz renderer.
     * @param int|null $seq the seq number of the past state to display.
     * @return string HTML fragment.
     */
    protected function render_question_helper($slot, $reviewing, $thispageurl,
            renderer $renderer, $seq) {
        $originalslot = $this->get_original_slot($slot);
        $number = $this->get_question_number($originalslot);
        $displayoptions = $this->get_display_options_with_edit_link($reviewing, $slot, $thispageurl);

        if ($slot != $originalslot) {
            $originalmaxmark = $this->get_question_attempt($slot)->get_max_mark();
            $this->get_question_attempt($slot)->set_max_mark($this->get_question_attempt($originalslot)->get_max_mark());
        }

        if ($this->can_question_be_redone_now($slot)) {
            $displayoptions->extrainfocontent = $renderer->redo_question_button(
                    $slot, $displayoptions->readonly);
        }

        if ($displayoptions->history && $displayoptions->questionreviewlink) {
            $links = $this->links_to_other_redos($slot, $displayoptions->questionreviewlink);
            if ($links) {
                $displayoptions->extrahistorycontent = html_writer::tag('p',
                        get_string('redoesofthisquestion', 'quiz', $renderer->render($links)));
            }
        }

        if ($seq === null) {
            $output = $this->quba->render_question($slot, $displayoptions, $number);
        } else {
            $output = $this->quba->render_question_at_step($slot, $seq, $displayoptions, $number);
        }

        if ($slot != $originalslot) {
            $this->get_question_attempt($slot)->set_max_mark($originalmaxmark);
        }

        return $output;
    }

    /**
     * Create a fake question to be displayed in place of a question that is blocked
     * until the previous question has been answered.
     *
     * @param int $slot int slot number of the question to replace.
     * @return question_attempt the placeholder question attempt.
     */
    protected function make_blocked_question_placeholder($slot) {
        $replacedquestion = $this->get_question_attempt($slot)->get_question(false);

        question_bank::load_question_definition_classes('description');
        $question = new qtype_description_question();
        $question->id = $replacedquestion->id;
        $question->category = null;
        $question->parent = 0;
        $question->qtype = question_bank::get_qtype('description');
        $question->name = '';
        $question->questiontext = get_string('questiondependsonprevious', 'quiz');
        $question->questiontextformat = FORMAT_HTML;
        $question->generalfeedback = '';
        $question->defaultmark = $this->quba->get_question_max_mark($slot);
        $question->length = $replacedquestion->length;
        $question->penalty = 0;
        $question->stamp = '';
        $question->status = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $question->timecreated = null;
        $question->timemodified = null;
        $question->createdby = null;
        $question->modifiedby = null;

        $placeholderqa = new question_attempt($question, $this->quba->get_id(),
                null, $this->quba->get_question_max_mark($slot));
        $placeholderqa->set_slot($slot);
        $placeholderqa->start($this->get_quiz()->preferredbehaviour, 1);
        $placeholderqa->set_flagged($this->is_question_flagged($slot));
        return $placeholderqa;
    }

    /**
     * Like {@see render_question()} but displays the question at the past step
     * indicated by $seq, rather than showing the latest step.
     *
     * @param int $slot the slot number of a question in this quiz attempt.
     * @param int $seq the seq number of the past state to display.
     * @param bool $reviewing is the being printed on an attempt or a review page.
     * @param renderer $renderer the quiz renderer.
     * @param moodle_url $thispageurl the URL of the page this question is being printed on.
     * @return string HTML for the question in its current state.
     */
    public function render_question_at_step($slot, $seq, $reviewing,
            renderer $renderer, $thispageurl = null) {
        return $this->render_question_helper($slot, $reviewing, $thispageurl, $renderer, $seq);
    }

    /**
     * Wrapper round print_question from lib/questionlib.php.
     *
     * @param int $slot the id of a question in this quiz attempt.
     * @return string HTML of the question.
     */
    public function render_question_for_commenting($slot) {
        $options = $this->get_display_options(true);
        $options->generalfeedback = question_display_options::HIDDEN;
        $options->manualcomment = question_display_options::EDITABLE;
        return $this->quba->render_question($slot, $options,
                $this->get_question_number($slot));
    }

    /**
     * Check whether access should be allowed to a particular file.
     *
     * @param int $slot the slot of a question in this quiz attempt.
     * @param bool $reviewing is the being printed on an attempt or a review page.
     * @param int $contextid the file context id from the request.
     * @param string $component the file component from the request.
     * @param string $filearea the file area from the request.
     * @param array $args extra part components from the request.
     * @param bool $forcedownload whether to force download.
     * @return bool true if the file can be accessed.
     */
    public function check_file_access($slot, $reviewing, $contextid, $component,
            $filearea, $args, $forcedownload) {
        $options = $this->get_display_options($reviewing);

        // Check permissions - warning there is similar code in review.php and
        // reviewquestion.php. If you change on, change them all.
        if ($reviewing && $this->is_own_attempt() && !$options->attempt) {
            return false;
        }

        if ($reviewing && !$this->is_own_attempt() && !$this->is_review_allowed()) {
            return false;
        }

        return $this->quba->check_file_access($slot, $options,
                $component, $filearea, $args, $forcedownload);
    }

    /**
     * Get the navigation panel object for this attempt.
     *
     * @param renderer $output the quiz renderer to use to output things.
     * @param string $panelclass The type of panel, navigation_panel_attempt::class or navigation_panel_review::class
     * @param int $page the current page number.
     * @param bool $showall whether we are showing the whole quiz on one page. (Used by review.php.)
     * @return block_contents the requested object.
     */
    public function get_navigation_panel(renderer $output,
             $panelclass, $page, $showall = false) {
        $panel = new $panelclass($this, $this->get_display_options(true), $page, $showall);

        $bc = new block_contents();
        $bc->attributes['id'] = 'mod_quiz_navblock';
        $bc->attributes['role'] = 'navigation';
        $bc->title = get_string('quiznavigation', 'quiz');
        $bc->content = $output->navigation_panel($panel);
        return $bc;
    }

    /**
     * Return an array of variant URLs to other attempts at this quiz.
     *
     * The $url passed in must contain an attempt parameter.
     *
     * The {@see links_to_other_attempts} object returned contains an
     * array with keys that are the attempt number, 1, 2, 3.
     * The array values are either a {@see moodle_url} with the attempt parameter
     * updated to point to the attempt id of the other attempt, or null corresponding
     * to the current attempt number.
     *
     * @param moodle_url $url a URL.
     * @return links_to_other_attempts|bool containing array int => null|moodle_url.
     *      False if none.
     */
    public function links_to_other_attempts(moodle_url $url) {
        $attempts = quiz_get_user_attempts($this->get_quiz()->id, $this->attempt->userid, 'all');
        if (count($attempts) <= 1) {
            return false;
        }

        $links = new links_to_other_attempts();
        foreach ($attempts as $at) {
            if ($at->id == $this->attempt->id) {
                $links->links[$at->attempt] = null;
            } else {
                $links->links[$at->attempt] = new moodle_url($url, ['attempt' => $at->id]);
            }
        }
        return $links;
    }

    /**
     * Return an array of variant URLs to other redos of the question in a particular slot.
     *
     * The $url passed in must contain a slot parameter.
     *
     * The {@see links_to_other_attempts} object returned contains an
     * array with keys that are the redo number, 1, 2, 3.
     * The array values are either a {@see moodle_url} with the slot parameter
     * updated to point to the slot that has that redo of this question; or null
     * corresponding to the redo identified by $slot.
     *
     * @param int $slot identifies a question in this attempt.
     * @param moodle_url $baseurl the base URL to modify to generate each link.
     * @return links_to_other_attempts|null containing array int => null|moodle_url,
     *      or null if the question in this slot has not been redone.
     */
    public function links_to_other_redos($slot, moodle_url $baseurl) {
        $originalslot = $this->get_original_slot($slot);

        $qas = $this->all_question_attempts_originally_in_slot($originalslot);
        if (count($qas) <= 1) {
            return null;
        }

        $links = new links_to_other_attempts();
        $index = 1;
        foreach ($qas as $qa) {
            if ($qa->get_slot() == $slot) {
                $links->links[$index] = null;
            } else {
                $url = new moodle_url($baseurl, ['slot' => $qa->get_slot()]);
                $links->links[$index] = new action_link($url, $index,
                        new popup_action('click', $url, 'reviewquestion',
                                ['width' => 450, 'height' => 650]),
                        ['title' => get_string('reviewresponse', 'question')]);
            }
            $index++;
        }
        return $links;
    }

    // Methods for processing ==================================================.

    /**
     * Check this attempt, to see if there are any state transitions that should
     * happen automatically. This function will update the attempt checkstatetime.
     * @param int $timestamp the timestamp that should be stored as the modified
     * @param bool $studentisonline is the student currently interacting with Moodle?
     */
    public function handle_if_time_expired($timestamp, $studentisonline) {

        $timeclose = $this->get_access_manager($timestamp)->get_end_time($this->attempt);

        if ($timeclose === false || $this->is_preview()) {
            $this->update_timecheckstate(null);
            return; // No time limit.
        }
        if ($timestamp < $timeclose) {
            $this->update_timecheckstate($timeclose);
            return; // Time has not yet expired.
        }

        // If the attempt is already overdue, look to see if it should be abandoned ...
        if ($this->attempt->state == self::OVERDUE) {
            $timeoverdue = $timestamp - $timeclose;
            $graceperiod = $this->quizobj->get_quiz()->graceperiod;
            if ($timeoverdue >= $graceperiod) {
                $this->process_abandon($timestamp, $studentisonline);
            } else {
                // Overdue time has not yet expired.
                $this->update_timecheckstate($timeclose + $graceperiod);
            }
            return; // ... and we are done.
        }

        if ($this->attempt->state != self::IN_PROGRESS) {
            $this->update_timecheckstate(null);
            return; // Attempt is already in a final state.
        }

        // Otherwise, we were in quiz_attempt::IN_PROGRESS, and time has now expired.
        // Transition to the appropriate state.
        switch ($this->quizobj->get_quiz()->overduehandling) {
            case 'autosubmit':
                $this->process_finish($timestamp, false, $studentisonline ? $timestamp : $timeclose, $studentisonline);
                return;

            case 'graceperiod':
                $this->process_going_overdue($timestamp, $studentisonline);
                return;

            case 'autoabandon':
                $this->process_abandon($timestamp, $studentisonline);
                return;
        }

        // This is an overdue attempt with no overdue handling defined, so just abandon.
        $this->process_abandon($timestamp, $studentisonline);
    }

    /**
     * Process all the actions that were submitted as part of the current request.
     *
     * @param int $timestamp the timestamp that should be stored as the modified.
     *      time in the database for these actions. If null, will use the current time.
     * @param bool $becomingoverdue
     * @param array|null $simulatedresponses If not null, then we are testing, and this is an array of simulated data.
     *      There are two formats supported here, for historical reasons. The newer approach is to pass an array created by
     *      {@see core_question_generator::get_simulated_post_data_for_questions_in_usage()}.
     *      the second is to pass an array slot no => contains arrays representing student
     *      responses which will be passed to {@see question_definition::prepare_simulated_post_data()}.
     *      This second method will probably get deprecated one day.
     */
    public function process_submitted_actions($timestamp, $becomingoverdue = false, $simulatedresponses = null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        if ($simulatedresponses !== null) {
            if (is_int(key($simulatedresponses))) {
                // Legacy approach. Should be removed one day.
                $simulatedpostdata = $this->quba->prepare_simulated_post_data($simulatedresponses);
            } else {
                $simulatedpostdata = $simulatedresponses;
            }
        } else {
            $simulatedpostdata = null;
        }

        $this->quba->process_all_actions($timestamp, $simulatedpostdata);
        question_engine::save_questions_usage_by_activity($this->quba);

        $this->attempt->timemodified = $timestamp;
        if ($this->attempt->state == self::FINISHED) {
            $this->attempt->sumgrades = $this->quba->get_total_mark();
        }
        if ($becomingoverdue) {
            $this->process_going_overdue($timestamp, true);
        } else {
            $DB->update_record('quiz_attempts', $this->attempt);
        }

        if (!$this->is_preview() && $this->attempt->state == self::FINISHED) {
            $this->recompute_final_grade();
        }

        $transaction->allow_commit();
    }

    /**
     * Replace a question in an attempt with a new attempt at the same question.
     *
     * Well, for randomised questions, it won't be the same question, it will be
     * a different randomly selected pick from the available question.
     *
     * @param int $slot the question to restart.
     * @param int $timestamp the timestamp to record for this action.
     */
    public function process_redo_question($slot, $timestamp) {
        global $DB;

        if (!$this->can_question_be_redone_now($slot)) {
            throw new coding_exception('Attempt to restart the question in slot ' . $slot .
                    ' when it is not in a state to be restarted.');
        }

        $qubaids = new \mod_quiz\question\qubaids_for_users_attempts(
                $this->get_quizid(), $this->get_userid(), 'all', true);

        $transaction = $DB->start_delegated_transaction();

        // Add the question to the usage. It is important we do this before we choose a variant.
        $newquestionid = qbank_helper::choose_question_for_redo($this->get_quizid(),
                    $this->get_quizobj()->get_context(), $this->slots[$slot]->id, $qubaids);
        $newquestion = question_bank::load_question($newquestionid, $this->get_quiz()->shuffleanswers);
        $newslot = $this->quba->add_question_in_place_of_other($slot, $newquestion);

        // Choose the variant.
        if ($newquestion->get_num_variants() == 1) {
            $variant = 1;
        } else {
            $variantstrategy = new \core_question\engine\variants\least_used_strategy(
                    $this->quba, $qubaids);
            $variant = $variantstrategy->choose_variant($newquestion->get_num_variants(),
                    $newquestion->get_variants_selection_seed());
        }

        // Start the question.
        $this->quba->start_question($slot, $variant);
        $this->quba->set_max_mark($newslot, 0);
        $this->quba->set_question_attempt_metadata($newslot, 'originalslot', $slot);
        question_engine::save_questions_usage_by_activity($this->quba);
        $this->fire_attempt_question_restarted_event($slot, $newquestion->id);

        $transaction->allow_commit();
    }

    /**
     * Process all the autosaved data that was part of the current request.
     *
     * @param int $timestamp the timestamp that should be stored as the modified.
     * time in the database for these actions. If null, will use the current time.
     */
    public function process_auto_save($timestamp) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $this->quba->process_all_autosaves($timestamp);
        question_engine::save_questions_usage_by_activity($this->quba);
        $this->fire_attempt_autosaved_event();

        $transaction->allow_commit();
    }

    /**
     * Update the flagged state for all question_attempts in this usage, if their
     * flagged state was changed in the request.
     */
    public function save_question_flags() {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $this->quba->update_question_flags();
        question_engine::save_questions_usage_by_activity($this->quba);
        $transaction->allow_commit();
    }

    /**
     * Submit the attempt.
     *
     * The separate $timefinish argument should be used when the quiz attempt
     * is being processed asynchronously (for example when cron is submitting
     * attempts where the time has expired).
     *
     * @param int $timestamp the time to record as last modified time.
     * @param bool $processsubmitted if true, and question responses in the current
     *      POST request are stored to be graded, before the attempt is finished.
     * @param ?int $timefinish if set, use this as the finish time for the attempt.
     *      (otherwise use $timestamp as the finish time as well).
     * @param bool $studentisonline is the student currently interacting with Moodle?
     */
    public function process_finish($timestamp, $processsubmitted, $timefinish = null, $studentisonline = false) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        if ($processsubmitted) {
            $this->quba->process_all_actions($timestamp);
        }
        $this->quba->finish_all_questions($timestamp);

        question_engine::save_questions_usage_by_activity($this->quba);

        $this->attempt->timemodified = $timestamp;
        $this->attempt->timefinish = $timefinish ?? $timestamp;
        $this->attempt->sumgrades = $this->quba->get_total_mark();
        $this->attempt->state = self::FINISHED;
        $this->attempt->timecheckstate = null;
        $this->attempt->gradednotificationsenttime = null;

        if (!$this->requires_manual_grading() ||
                !has_capability('mod/quiz:emailnotifyattemptgraded', $this->get_quizobj()->get_context(),
                        $this->get_userid())) {
            $this->attempt->gradednotificationsenttime = $this->attempt->timefinish;
        }

        $DB->update_record('quiz_attempts', $this->attempt);

        if (!$this->is_preview()) {
            $this->recompute_final_grade();

            // Trigger event.
            $this->fire_state_transition_event('\mod_quiz\event\attempt_submitted', $timestamp, $studentisonline);

            // Tell any access rules that care that the attempt is over.
            $this->get_access_manager($timestamp)->current_attempt_finished();
        }

        $transaction->allow_commit();
    }

    /**
     * Update this attempt timecheckstate if necessary.
     *
     * @param int|null $time the timestamp to set.
     */
    public function update_timecheckstate($time) {
        global $DB;
        if ($this->attempt->timecheckstate !== $time) {
            $this->attempt->timecheckstate = $time;
            $DB->set_field('quiz_attempts', 'timecheckstate', $time, ['id' => $this->attempt->id]);
        }
    }

    /**
     * Needs to be called after this attempt's grade is changed, to update the overall quiz grade.
     */
    protected function recompute_final_grade(): void {
        $this->quizobj->get_grade_calculator()->recompute_final_grade($this->get_userid());
    }

    /**
     * Mark this attempt as now overdue.
     *
     * @param int $timestamp the time to deem as now.
     * @param bool $studentisonline is the student currently interacting with Moodle?
     */
    public function process_going_overdue($timestamp, $studentisonline) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $this->attempt->timemodified = $timestamp;
        $this->attempt->state = self::OVERDUE;
        // If we knew the attempt close time, we could compute when the graceperiod ends.
        // Instead, we'll just fix it up through cron.
        $this->attempt->timecheckstate = $timestamp;
        $DB->update_record('quiz_attempts', $this->attempt);

        $this->fire_state_transition_event('\mod_quiz\event\attempt_becameoverdue', $timestamp, $studentisonline);

        $transaction->allow_commit();

        quiz_send_overdue_message($this);
    }

    /**
     * Mark this attempt as abandoned.
     *
     * @param int $timestamp the time to deem as now.
     * @param bool $studentisonline is the student currently interacting with Moodle?
     */
    public function process_abandon($timestamp, $studentisonline) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        $this->attempt->timemodified = $timestamp;
        $this->attempt->state = self::ABANDONED;
        $this->attempt->timecheckstate = null;
        $DB->update_record('quiz_attempts', $this->attempt);

        $this->fire_state_transition_event('\mod_quiz\event\attempt_abandoned', $timestamp, $studentisonline);

        $transaction->allow_commit();
    }

    /**
     * This method takes an attempt in the 'Never submitted' state, and reopens it.
     *
     * If, for this student, time has not expired (perhaps, because an override has
     * been added, then the attempt is left open. Otherwise, it is immediately submitted
     * for grading.
     *
     * @param int $timestamp the time to deem as now.
     */
    public function process_reopen_abandoned($timestamp) {
        global $DB;

        // Verify that things are as we expect.
        if ($this->get_state() != self::ABANDONED) {
            throw new coding_exception('Can only reopen an attempt that was never submitted.');
        }

        $transaction = $DB->start_delegated_transaction();
        $this->attempt->timemodified = $timestamp;
        $this->attempt->state = self::IN_PROGRESS;
        $this->attempt->timecheckstate = null;
        $DB->update_record('quiz_attempts', $this->attempt);

        $this->fire_state_transition_event('\mod_quiz\event\attempt_reopened', $timestamp, false);

        $timeclose = $this->get_access_manager($timestamp)->get_end_time($this->attempt);
        if ($timeclose && $timestamp > $timeclose) {
            $this->process_finish($timestamp, false, $timeclose);
        }

        $transaction->allow_commit();
    }

    /**
     * Fire a state transition event.
     *
     * @param string $eventclass the event class name.
     * @param int $timestamp the timestamp to include in the event.
     * @param bool $studentisonline is the student currently interacting with Moodle?
     */
    protected function fire_state_transition_event($eventclass, $timestamp, $studentisonline) {
        global $USER;
        $quizrecord = $this->get_quiz();
        $params = [
            'context' => $this->get_quizobj()->get_context(),
            'courseid' => $this->get_courseid(),
            'objectid' => $this->attempt->id,
            'relateduserid' => $this->attempt->userid,
            'other' => [
                'submitterid' => CLI_SCRIPT ? null : $USER->id,
                'quizid' => $quizrecord->id,
                'studentisonline' => $studentisonline
            ]
        ];
        $event = $eventclass::create($params);
        $event->add_record_snapshot('quiz', $this->get_quiz());
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    // Private methods =========================================================.

    /**
     * Get a URL for a particular question on a particular page of the quiz.
     * Used by {@see attempt_url()} and {@see review_url()}.
     *
     * @param string $script e.g. 'attempt' or 'review'. Used in the URL like /mod/quiz/$script.php.
     * @param int $slot identifies the specific question on the page to jump to.
     *      0 to just use the $page parameter.
     * @param int $page -1 to look up the page number from the slot, otherwise
     *      the page number to go to.
     * @param bool|null $showall if true, return a URL with showall=1, and not page number.
     *      if null, then an intelligent default will be chosen.
     * @param int $thispage the page we are currently on. Links to questions on this
     *      page will just be a fragment #q123. -1 to disable this.
     * @return moodle_url The requested URL.
     */
    protected function page_and_question_url($script, $slot, $page, $showall, $thispage) {

        $defaultshowall = $this->get_default_show_all($script);
        if ($showall === null && ($page == 0 || $page == -1)) {
            $showall = $defaultshowall;
        }

        // Fix up $page.
        if ($page == -1) {
            if ($slot !== null && !$showall) {
                $page = $this->get_question_page($slot);
            } else {
                $page = 0;
            }
        }

        if ($showall) {
            $page = 0;
        }

        // Add a fragment to scroll down to the question.
        $fragment = '';
        if ($slot !== null) {
            if ($slot == reset($this->pagelayout[$page]) && $thispage != $page) {
                // Changing the page, go to top.
                $fragment = '#';
            } else {
                // Link to the question container.
                $qa = $this->get_question_attempt($slot);
                $fragment = '#' . $qa->get_outer_question_div_unique_id();
            }
        }

        // Work out the correct start to the URL.
        if ($thispage == $page) {
            return new moodle_url($fragment);

        } else {
            $url = new moodle_url('/mod/quiz/' . $script . '.php' . $fragment,
                    ['attempt' => $this->attempt->id, 'cmid' => $this->get_cmid()]);
            if ($page == 0 && $showall != $defaultshowall) {
                $url->param('showall', (int) $showall);
            } else if ($page > 0) {
                $url->param('page', $page);
            }
            return $url;
        }
    }

    /**
     * Process responses during an attempt at a quiz.
     *
     * @param  int $timenow time when the processing started.
     * @param  bool $finishattempt whether to finish the attempt or not.
     * @param  bool $timeup true if form was submitted by timer.
     * @param  int $thispage current page number.
     * @return string the attempt state once the data has been processed.
     * @since  Moodle 3.1
     */
    public function process_attempt($timenow, $finishattempt, $timeup, $thispage) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        // Get key times.
        $accessmanager = $this->get_access_manager($timenow);
        $timeclose = $accessmanager->get_end_time($this->get_attempt());
        $graceperiodmin = get_config('quiz', 'graceperiodmin');

        // Don't enforce timeclose for previews.
        if ($this->is_preview()) {
            $timeclose = false;
        }

        // Check where we are in relation to the end time, if there is one.
        $toolate = false;
        if ($timeclose !== false) {
            if ($timenow > $timeclose - QUIZ_MIN_TIME_TO_CONTINUE) {
                // If there is only a very small amount of time left, there is no point trying
                // to show the student another page of the quiz. Just finish now.
                $timeup = true;
                if ($timenow > $timeclose + $graceperiodmin) {
                    $toolate = true;
                }
            } else {
                // If time is not close to expiring, then ignore the client-side timer's opinion
                // about whether time has expired. This can happen if the time limit has changed
                // since the student's previous interaction.
                $timeup = false;
            }
        }

        // If time is running out, trigger the appropriate action.
        $becomingoverdue = false;
        $becomingabandoned = false;
        if ($timeup) {
            if ($this->get_quiz()->overduehandling === 'graceperiod') {
                if ($timenow > $timeclose + $this->get_quiz()->graceperiod + $graceperiodmin) {
                    // Grace period has run out.
                    $finishattempt = true;
                    $becomingabandoned = true;
                } else {
                    $becomingoverdue = true;
                }
            } else {
                $finishattempt = true;
            }
        }

        if (!$finishattempt) {
            // Just process the responses for this page and go to the next page.
            if (!$toolate) {
                try {
                    $this->process_submitted_actions($timenow, $becomingoverdue);
                    $this->fire_attempt_updated_event();
                } catch (question_out_of_sequence_exception $e) {
                    throw new moodle_exception('submissionoutofsequencefriendlymessage', 'question',
                            $this->attempt_url(null, $thispage));

                } catch (Exception $e) {
                    // This sucks, if we display our own custom error message, there is no way
                    // to display the original stack trace.
                    $debuginfo = '';
                    if (!empty($e->debuginfo)) {
                        $debuginfo = $e->debuginfo;
                    }
                    throw new moodle_exception('errorprocessingresponses', 'question',
                            $this->attempt_url(null, $thispage), $e->getMessage(), $debuginfo);
                }

                if (!$becomingoverdue) {
                    foreach ($this->get_slots() as $slot) {
                        if (optional_param('redoslot' . $slot, false, PARAM_BOOL)) {
                            $this->process_redo_question($slot, $timenow);
                        }
                    }
                }

            } else {
                // The student is too late.
                $this->process_going_overdue($timenow, true);
            }

            $transaction->allow_commit();

            return $becomingoverdue ? self::OVERDUE : self::IN_PROGRESS;
        }

        // Update the quiz attempt record.
        try {
            if ($becomingabandoned) {
                $this->process_abandon($timenow, true);
            } else {
                if (!$toolate || $this->get_quiz()->overduehandling === 'graceperiod') {
                    // Normally, we record the accurate finish time when the student is online.
                    $finishtime = $timenow;
                } else {
                    // But, if there is no grade period, and the final responses were too
                    // late to be processed, record the close time, to reduce confusion.
                    $finishtime = $timeclose;
                }
                $this->process_finish($timenow, !$toolate, $finishtime, true);
            }

        } catch (question_out_of_sequence_exception $e) {
            throw new moodle_exception('submissionoutofsequencefriendlymessage', 'question',
                    $this->attempt_url(null, $thispage));

        } catch (Exception $e) {
            // This sucks, if we display our own custom error message, there is no way
            // to display the original stack trace.
            $debuginfo = '';
            if (!empty($e->debuginfo)) {
                $debuginfo = $e->debuginfo;
            }
            throw new moodle_exception('errorprocessingresponses', 'question',
                    $this->attempt_url(null, $thispage), $e->getMessage(), $debuginfo);
        }

        // Send the user to the review page.
        $transaction->allow_commit();

        return $becomingabandoned ? self::ABANDONED : self::FINISHED;
    }

    /**
     * Check a page read access to see if is an out of sequence access.
     *
     * If allownext is set then we also check whether access to the page
     * after the current one should be permitted.
     *
     * @param int $page page number.
     * @param bool $allownext in case of a sequential navigation, can we go to next page ?
     * @return boolean false is an out of sequence access, true otherwise.
     * @since Moodle 3.1
     */
    public function check_page_access(int $page, bool $allownext = true): bool {
        if ($this->get_navigation_method() != QUIZ_NAVMETHOD_SEQ) {
            return true;
        }
        // Sequential access: allow access to the summary, current page or next page.
        // Or if the user review his/her attempt, see MDLQA-1523.
        return $page == -1
            || $page == $this->get_currentpage()
            || $allownext && ($page == $this->get_currentpage() + 1);
    }

    /**
     * Update attempt page.
     *
     * @param  int $page page number.
     * @return boolean true if everything was ok, false otherwise (out of sequence access).
     * @since Moodle 3.1
     */
    public function set_currentpage($page) {
        global $DB;

        if ($this->check_page_access($page)) {
            $DB->set_field('quiz_attempts', 'currentpage', $page, ['id' => $this->get_attemptid()]);
            return true;
        }
        return false;
    }

    /**
     * Trigger the attempt_viewed event.
     *
     * @since Moodle 3.1
     */
    public function fire_attempt_viewed_event() {
        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid(),
                'page' => $this->get_currentpage()
            ]
        ];
        $event = \mod_quiz\event\attempt_viewed::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt_updated event.
     *
     * @return void
     */
    public function fire_attempt_updated_event(): void {
        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid(),
                'page' => $this->get_currentpage()
            ]
        ];
        $event = \mod_quiz\event\attempt_updated::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt_autosaved event.
     *
     * @return void
     */
    public function fire_attempt_autosaved_event(): void {
        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid(),
                'page' => $this->get_currentpage()
            ]
        ];
        $event = \mod_quiz\event\attempt_autosaved::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt_question_restarted event.
     *
     * @param int $slot Slot number
     * @param int $newquestionid New question id.
     * @return void
     */
    public function fire_attempt_question_restarted_event(int $slot, int $newquestionid): void {
        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid(),
                'page' => $this->get_currentpage(),
                'slot' => $slot,
                'newquestionid' => $newquestionid
            ]
        ];
        $event = \mod_quiz\event\attempt_question_restarted::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt_summary_viewed event.
     *
     * @since Moodle 3.1
     */
    public function fire_attempt_summary_viewed_event() {

        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid()
            ]
        ];
        $event = \mod_quiz\event\attempt_summary_viewed::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt_reviewed event.
     *
     * @since Moodle 3.1
     */
    public function fire_attempt_reviewed_event() {

        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid()
            ]
        ];
        $event = \mod_quiz\event\attempt_reviewed::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Trigger the attempt manual grading completed event.
     */
    public function fire_attempt_manual_grading_completed_event() {
        $params = [
            'objectid' => $this->get_attemptid(),
            'relateduserid' => $this->get_userid(),
            'courseid' => $this->get_courseid(),
            'context' => $this->get_context(),
            'other' => [
                'quizid' => $this->get_quizid()
            ]
        ];

        $event = \mod_quiz\event\attempt_manual_grading_completed::create($params);
        $event->add_record_snapshot('quiz_attempts', $this->get_attempt());
        $event->trigger();
    }

    /**
     * Update the timemodifiedoffline attempt field.
     *
     * This function should be used only when web services are being used.
     *
     * @param int $time time stamp.
     * @return boolean false if the field is not updated because web services aren't being used.
     * @since Moodle 3.2
     */
    public function set_offline_modified_time($time) {
        // Update the timemodifiedoffline field only if web services are being used.
        if (WS_SERVER) {
            $this->attempt->timemodifiedoffline = $time;
            return true;
        }
        return false;
    }

    /**
     * Get the total number of unanswered questions in the attempt.
     *
     * @return int
     */
    public function get_number_of_unanswered_questions(): int {
        $totalunanswered = 0;
        foreach ($this->get_slots() as $slot) {
            if (!$this->is_real_question($slot)) {
                continue;
            }
            $questionstate = $this->get_question_state($slot);
            if ($questionstate == question_state::$todo || $questionstate == question_state::$invalid) {
                $totalunanswered++;
            }
        }
        return $totalunanswered;
    }
}
