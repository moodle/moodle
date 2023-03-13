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
 * This file defines the quiz manual grading report class.
 *
 * @package   quiz_grading
 * @copyright 2006 Gustav Delius
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/grading/gradingsettings_form.php');


/**
 * Quiz report to help teachers manually grade questions that need it.
 *
 * This report basically provides two screens:
 * - List question that might need manual grading (or optionally all questions).
 * - Provide an efficient UI to grade all attempts at a particular question.
 *
 * @copyright 2006 Gustav Delius
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_grading_report extends quiz_default_report {
    const DEFAULT_PAGE_SIZE = 5;
    const DEFAULT_ORDER = 'random';

    /** @var string Positive integer regular expression. */
    const REGEX_POSITIVE_INT = '/^[1-9]\d*$/';

    /** @var array URL parameters for what is being displayed when grading. */
    protected $viewoptions = [];

    /** @var int the current group, 0 if none, or NO_GROUPS_ALLOWED. */
    protected $currentgroup;

    /** @var array from quiz_report_get_significant_questions. */
    protected $questions;

    /** @var stdClass the course settings. */
    protected $course;

    /** @var stdClass the course_module settings. */
    protected $cm;

    /** @var stdClass the quiz settings. */
    protected $quiz;

    /** @var context the quiz context. */
    protected $context;

    /** @var quiz_grading_renderer Renderer of Quiz Grading. */
    protected $renderer;

    /** @var string fragment of SQL code to restrict to the relevant users. */
    protected $userssql;

    /** @var array extra user fields. */
    protected $extrauserfields = [];

    public function display($quiz, $cm, $course) {

        $this->quiz = $quiz;
        $this->cm = $cm;
        $this->course = $course;

        // Get the URL options.
        $slot = optional_param('slot', null, PARAM_INT);
        $questionid = optional_param('qid', null, PARAM_INT);
        $grade = optional_param('grade', null, PARAM_ALPHA);

        $includeauto = optional_param('includeauto', false, PARAM_BOOL);
        if (!in_array($grade, array('all', 'needsgrading', 'autograded', 'manuallygraded'))) {
            $grade = null;
        }
        $pagesize = optional_param('pagesize',
                get_user_preferences('quiz_grading_pagesize', self::DEFAULT_PAGE_SIZE),
                PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $order = optional_param('order',
                get_user_preferences('quiz_grading_order', self::DEFAULT_ORDER),
                PARAM_ALPHAEXT);

        // Assemble the options required to reload this page.
        $optparams = array('includeauto', 'page');
        foreach ($optparams as $param) {
            if ($$param) {
                $this->viewoptions[$param] = $$param;
            }
        }
        if (!data_submitted() && !preg_match(self::REGEX_POSITIVE_INT, $pagesize)) {
            // We only validate if the user accesses the page via a cleaned-up GET URL here.
            throw new moodle_exception('invalidpagesize');
        }
        if ($pagesize != self::DEFAULT_PAGE_SIZE) {
            $this->viewoptions['pagesize'] = $pagesize;
        }
        if ($order != self::DEFAULT_ORDER) {
            $this->viewoptions['order'] = $order;
        }

        // Check permissions.
        $this->context = context_module::instance($this->cm->id);
        require_capability('mod/quiz:grade', $this->context);
        $shownames = has_capability('quiz/grading:viewstudentnames', $this->context);
        // Whether the current user can see custom user fields.
        $showcustomfields = has_capability('quiz/grading:viewidnumber', $this->context);
        $userfieldsapi = \core_user\fields::for_identity($this->context)->with_name();
        $customfields = [];
        foreach ($userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $field) {
            $customfields[] = $field;
        }
        // Validate order.
        $orderoptions = array_merge(['random', 'date', 'studentfirstname', 'studentlastname'], $customfields);
        if (!in_array($order, $orderoptions)) {
            $order = self::DEFAULT_ORDER;
        } else if (!$shownames && ($order == 'studentfirstname' || $order == 'studentlastname')) {
            $order = self::DEFAULT_ORDER;
        } else if (!$showcustomfields && in_array($order, $customfields)) {
            $order = self::DEFAULT_ORDER;
        }
        if ($order == 'random') {
            $page = 0;
        }

        // Get the list of questions in this quiz.
        $this->questions = quiz_report_get_significant_questions($quiz);
        if ($slot && !array_key_exists($slot, $this->questions)) {
            throw new moodle_exception('unknownquestion', 'quiz_grading');
        }

        // Process any submitted data.
        if ($data = data_submitted() && confirm_sesskey() && $this->validate_submitted_marks()) {
            // Changes done to handle attempts being missed from grading due to redirecting to new page.
            $attemptsgraded = $this->process_submitted_data();

            $nextpagenumber = $page + 1;
            // If attempts need grading and one or more have now been graded, then page number should remain the same.
            if ($grade == 'needsgrading' && $attemptsgraded) {
                $nextpagenumber = $page;
            }

            redirect($this->grade_question_url($slot, $questionid, $grade, $nextpagenumber));
        }

        // Get the group, and the list of significant users.
        $this->currentgroup = $this->get_current_group($cm, $course, $this->context);
        if ($this->currentgroup == self::NO_GROUPS_ALLOWED) {
            $this->userssql = array();
        } else {
            $this->userssql = get_enrolled_sql($this->context,
                    array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $this->currentgroup);
        }

        $hasquestions = quiz_has_questions($this->quiz->id);
        if (!$hasquestions) {
            $this->print_header_and_tabs($cm, $course, $quiz, 'grading');
            echo $this->renderer->render_quiz_no_question_notification($quiz, $cm, $this->context);
            return true;
        }

        if (!$slot) {
            $this->display_index($includeauto);
            return true;
        }

        // Display the grading UI for one question.

        // Make sure there is something to do.
        $counts = null;
        $statecounts = $this->get_question_state_summary([$slot]);
        foreach ($statecounts as $record) {
            if ($record->questionid == $questionid) {
                $counts = $record;
                break;
            }
        }

        // If not, redirect back to the list.
        if (!$counts || $counts->$grade == 0) {
            redirect($this->list_questions_url(), get_string('alldoneredirecting', 'quiz_grading'));
        }

        $this->display_grading_interface($slot, $questionid, $grade,
                $pagesize, $page, $shownames, $showcustomfields, $order, $counts);
        return true;
    }

    /**
     * Get the JOIN conditions needed so we only show attempts by relevant users.
     *
     * @return qubaid_join
     */
    protected function get_qubaids_condition() {

        $where = "quiza.quiz = :mangrquizid AND
                quiza.preview = 0 AND
                quiza.state = :statefinished";
        $params = array('mangrquizid' => $this->cm->instance, 'statefinished' => quiz_attempt::FINISHED);

        $usersjoin = '';
        $currentgroup = groups_get_activity_group($this->cm, true);
        $enrolleduserscount = count_enrolled_users($this->context,
                array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $currentgroup);
        if ($currentgroup) {
            $userssql = get_enrolled_sql($this->context,
                    array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $currentgroup);
            if ($enrolleduserscount < 1) {
                $where .= ' AND quiza.userid = 0';
            } else {
                $usersjoin = "JOIN ({$userssql[0]}) AS enr ON quiza.userid = enr.id";
                $params += $userssql[1];
            }
        }

        return new qubaid_join("{quiz_attempts} quiza $usersjoin ", 'quiza.uniqueid', $where, $params);
    }

    /**
     * Load the quiz_attempts rows corresponding to a list of question_usage ids.
     *
     * @param int[] $qubaids the question_usage ids of the quiz_attempts to load.
     * @return array quiz attempts, with added user name fields.
     */
    protected function load_attempts_by_usage_ids($qubaids) {
        global $DB;

        list($asql, $params) = $DB->get_in_or_equal($qubaids);
        $params[] = quiz_attempt::FINISHED;
        $params[] = $this->quiz->id;

        $fields = 'quiza.*, ';
        $userfieldsapi = \core_user\fields::for_identity($this->context)->with_name();
        $userfieldssql = $userfieldsapi->get_sql('u', false, '', 'userid', false);
        $fields .= $userfieldssql->selects;
        foreach ($userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $userfield) {
            $this->extrauserfields[] = s($userfield);
        }
        $params = array_merge($userfieldssql->params, $params);
        $attemptsbyid = $DB->get_records_sql("
                SELECT $fields
                FROM {quiz_attempts} quiza
                JOIN {user} u ON u.id = quiza.userid
                {$userfieldssql->joins}
                WHERE quiza.uniqueid $asql AND quiza.state = ? AND quiza.quiz = ?",
                $params);

        $attempts = array();
        foreach ($attemptsbyid as $attempt) {
            $attempts[$attempt->uniqueid] = $attempt;
        }
        return $attempts;
    }

    /**
     * Get the URL of the front page of the report that lists all the questions.
     *
     * @return moodle_url the URL.
     */
    protected function base_url() {
        return new moodle_url('/mod/quiz/report.php',
                ['id' => $this->cm->id, 'mode' => 'grading']);
    }

    /**
     * Get the URL of the front page of the report that lists all the questions.
     *
     * @param bool $includeauto if not given, use the current setting, otherwise,
     *      force a particular value of includeauto in the URL.
     * @return moodle_url the URL.
     */
    protected function list_questions_url($includeauto = null) {
        $url = $this->base_url();

        $url->params($this->viewoptions);

        if (!is_null($includeauto)) {
            $url->param('includeauto', $includeauto);
        }

        return $url;
    }

    /**
     * Get the URL to grade a batch of question attempts.
     *
     * @param int $slot
     * @param int $questionid
     * @param string $grade
     * @param int|bool $page = true, link to current page. false = omit page.
     *      number = link to specific page.
     * @return moodle_url
     */
    protected function grade_question_url($slot, $questionid, $grade, $page = true) {
        $url = $this->base_url();
        $url->params(['slot' => $slot, 'qid' => $questionid, 'grade' => $grade]);
        $url->params($this->viewoptions);

        if (!$page) {
            $url->remove_params('page');
        } else if (is_integer($page)) {
            $url->param('page', $page);
        }

        return $url;
    }

    /**
     * Renders the contents of one cell of the table on the index view.
     *
     * @param stdClass $counts counts of different types of attempt for this slot.
     * @param string $type the type of count to format.
     * @param string $gradestring get_string identifier for the grading link text, if required.
     * @return string HTML.
     */
    protected function format_count_for_table($counts, $type, $gradestring) {
        $result = $counts->$type;
        if ($counts->$type > 0) {
            $gradeurl = $this->grade_question_url($counts->slot, $counts->questionid, $type);
            $result .= $this->renderer->render_grade_link($counts, $type, $gradestring, $gradeurl);
        }
        return $result;
    }

    /**
     * Display the report front page which summarises the number of attempts to grade.
     *
     * @param bool $includeauto whether to show automatically-graded questions.
     */
    protected function display_index($includeauto) {
        global $PAGE, $OUTPUT;

        $this->print_header_and_tabs($this->cm, $this->course, $this->quiz, 'grading');

        if ($groupmode = groups_get_activity_groupmode($this->cm)) {
            // Groups is being used.
            groups_print_activity_menu($this->cm, $this->list_questions_url());
        }
        // Get the current group for the user looking at the report.
        $currentgroup = $this->get_current_group($this->cm, $this->course, $this->context);
        if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            echo $OUTPUT->notification(get_string('notingroup'));
            return;
        }
        $statecounts = $this->get_question_state_summary(array_keys($this->questions));
        if ($includeauto) {
            $linktext = get_string('hideautomaticallygraded', 'quiz_grading');
        } else {
            $linktext = get_string('alsoshowautomaticallygraded', 'quiz_grading');
        }
        echo $this->renderer->render_display_index_heading($linktext, $this->list_questions_url(!$includeauto));
        $data = [];
        $header = [];

        $header[] = get_string('qno', 'quiz_grading');
        $header[] = get_string('qtypeveryshort', 'question');
        $header[] = get_string('questionname', 'quiz_grading');
        $header[] = get_string('tograde', 'quiz_grading');
        $header[] = get_string('alreadygraded', 'quiz_grading');
        if ($includeauto) {
            $header[] = get_string('automaticallygraded', 'quiz_grading');
        }
        $header[] = get_string('total', 'quiz_grading');

        foreach ($statecounts as $counts) {
            if ($counts->all == 0) {
                continue;
            }
            if (!$includeauto && $counts->needsgrading == 0 && $counts->manuallygraded == 0) {
                continue;
            }

            $row = [];

            $row[] = $this->questions[$counts->slot]->number;

            $row[] = $PAGE->get_renderer('question', 'bank')->qtype_icon($this->questions[$counts->slot]->qtype);

            $row[] = format_string($counts->name);

            $row[] = $this->format_count_for_table($counts, 'needsgrading', 'grade');

            $row[] = $this->format_count_for_table($counts, 'manuallygraded', 'updategrade');

            if ($includeauto) {
                $row[] = $this->format_count_for_table($counts, 'autograded', 'updategrade');
            }

            $row[] = $this->format_count_for_table($counts, 'all', 'gradeall');

            $data[] = $row;
        }
        echo $this->renderer->render_questions_table($includeauto, $data, $header);
    }

    /**
     * Display the UI for grading attempts at one question.
     *
     * @param int $slot identifies which question to grade.
     * @param int $questionid identifies which question to grade.
     * @param string $grade type of attempts to grade.
     * @param int $pagesize number of questions to show per page.
     * @param int $page current page number.
     * @param bool $shownames whether student names should be shown.
     * @param bool $showcustomfields whether custom field values should be shown.
     * @param string $order preferred order of attempts.
     * @param stdClass $counts object that stores the number of each type of attempt.
     */
    protected function display_grading_interface($slot, $questionid, $grade,
            $pagesize, $page, $shownames, $showcustomfields, $order, $counts) {

        if ($pagesize * $page >= $counts->$grade) {
            $page = 0;
        }

        // Prepare the options form.
        $hidden = [
            'id' => $this->cm->id,
            'mode' => 'grading',
            'slot' => $slot,
            'qid' => $questionid,
            'page' => $page,
        ];
        if (array_key_exists('includeauto', $this->viewoptions)) {
            $hidden['includeauto'] = $this->viewoptions['includeauto'];
        }
        $mform = new quiz_grading_settings_form($hidden, $counts, $shownames, $showcustomfields, $this->context);

        // Tell the form the current settings.
        $settings = new stdClass();
        $settings->grade = $grade;
        $settings->pagesize = $pagesize;
        $settings->order = $order;
        $mform->set_data($settings);

        if ($mform->is_submitted()) {
            if ($mform->is_validated()) {
                // If the form was submitted and validated, save the user preferences, and
                // redirect to a cleaned-up GET URL.
                set_user_preference('quiz_grading_pagesize', $pagesize);
                set_user_preference('quiz_grading_order', $order);
                redirect($this->grade_question_url($slot, $questionid, $grade, $page));
            } else {
                // Set the pagesize back to the previous value, so the report page can continue the render
                // and the form can show the validation.
                $pagesize = get_user_preferences('quiz_grading_pagesize', self::DEFAULT_PAGE_SIZE);
            }
        }

        list($qubaids, $count) = $this->get_usage_ids_where_question_in_state(
                $grade, $slot, $questionid, $order, $page, $pagesize);
        $attempts = $this->load_attempts_by_usage_ids($qubaids);

        // Question info.
        $questioninfo = new stdClass();
        $questioninfo->number = $this->questions[$slot]->number;
        $questioninfo->questionname = format_string($counts->name);

        // Paging info.
        $paginginfo = new stdClass();
        $paginginfo->from = $page * $pagesize + 1;
        $paginginfo->to = min(($page + 1) * $pagesize, $count);
        $paginginfo->of = $count;
        $qubaidlist = implode(',', $qubaids);

        $this->print_header_and_tabs($this->cm, $this->course, $this->quiz, 'grading');

        $gradequestioncontent = '';
        foreach ($qubaids as $qubaid) {
            $attempt = $attempts[$qubaid];
            $quba = question_engine::load_questions_usage_by_activity($qubaid);
            $displayoptions = quiz_get_review_options($this->quiz, $attempt, $this->context);
            $displayoptions->generalfeedback = question_display_options::HIDDEN;
            $displayoptions->history = question_display_options::HIDDEN;
            $displayoptions->manualcomment = question_display_options::EDITABLE;

            $gradequestioncontent .= $this->renderer->render_grade_question(
                    $quba,
                    $slot,
                    $displayoptions,
                    $this->questions[$slot]->number,
                    $this->get_question_heading($attempt, $shownames, $showcustomfields)
            );
        }

        $pagingbar = new stdClass();
        $pagingbar->count = $count;
        $pagingbar->page = $page;
        $pagingbar->pagesize = $pagesize;
        $pagingbar->pagesize = $pagesize;
        $pagingbar->order = $order;
        $pagingbar->pagingurl = $this->grade_question_url($slot, $questionid, $grade, false);

        $hiddeninputs = [
                'qubaids' => $qubaidlist,
                'slots' => $slot,
                'sesskey' => sesskey()
        ];

        echo $this->renderer->render_grading_interface(
                $questioninfo,
                $this->list_questions_url(),
                $mform,
                $paginginfo,
                $pagingbar,
                $this->grade_question_url($slot, $questionid, $grade, $page),
                $hiddeninputs,
                $gradequestioncontent
        );
    }

    /**
     * When saving a grading page, are all the submitted marks valid?
     *
     * @return bool true if all valid, else false.
     */
    protected function validate_submitted_marks() {

        $qubaids = optional_param('qubaids', null, PARAM_SEQUENCE);
        if (!$qubaids) {
            return false;
        }
        $qubaids = clean_param_array(explode(',', $qubaids), PARAM_INT);

        $slots = optional_param('slots', '', PARAM_SEQUENCE);
        if (!$slots) {
            $slots = [];
        } else {
            $slots = explode(',', $slots);
        }

        foreach ($qubaids as $qubaid) {
            foreach ($slots as $slot) {
                if (!question_engine::is_manual_grade_in_range($qubaid, $slot)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Save all submitted marks to the database.
     *
     * @return bool returns true if some attempts or all are graded. False, if none of the attempts are graded.
     */
    protected function process_submitted_data(): bool {
        global $DB;

        $qubaids = optional_param('qubaids', null, PARAM_SEQUENCE);
        $assumedslotforevents = optional_param('slot', null, PARAM_INT);

        if (!$qubaids) {
            return false;
        }

        $qubaids = clean_param_array(explode(',', $qubaids), PARAM_INT);
        $attempts = $this->load_attempts_by_usage_ids($qubaids);
        $events = [];

        $transaction = $DB->start_delegated_transaction();
        $attemptsgraded = false;
        foreach ($qubaids as $qubaid) {
            $attempt = $attempts[$qubaid];
            $attemptobj = new quiz_attempt($attempt, $this->quiz, $this->cm, $this->course);

            // State of the attempt before grades are changed.
            $attemptoldtstate = $attemptobj->get_question_state($assumedslotforevents);

            $attemptobj->process_submitted_actions(time());

            // Get attempt state after grades are changed.
            $attemptnewtstate = $attemptobj->get_question_state($assumedslotforevents);

            // Check if any attempts are graded.
            if (!$attemptsgraded && $attemptoldtstate->is_graded() != $attemptnewtstate->is_graded()) {
                $attemptsgraded = true;
            }

            // Add the event we will trigger later.
            $params = [
                'objectid' => $attemptobj->get_question_attempt($assumedslotforevents)->get_question_id(),
                'courseid' => $attemptobj->get_courseid(),
                'context' => context_module::instance($attemptobj->get_cmid()),
                'other' => [
                    'quizid' => $attemptobj->get_quizid(),
                    'attemptid' => $attemptobj->get_attemptid(),
                    'slot' => $assumedslotforevents,
                ],
            ];
            $events[] = \mod_quiz\event\question_manually_graded::create($params);
        }
        $transaction->allow_commit();

        // Trigger events for all the questions we manually marked.
        foreach ($events as $event) {
            $event->trigger();
        }

        return $attemptsgraded;
    }

    /**
     * Load information about the number of attempts at various questions in each
     * summarystate.
     *
     * The results are returned as an two dimensional array $qubaid => $slot => $dataobject
     *
     * @param array $slots A list of slots for the questions you want to konw about.
     * @return array The array keys are slot,qestionid. The values are objects with
     * fields $slot, $questionid, $inprogress, $name, $needsgrading, $autograded,
     * $manuallygraded and $all.
     */
    protected function get_question_state_summary($slots) {
        $dm = new question_engine_data_mapper();
        return $dm->load_questions_usages_question_state_summary(
                $this->get_qubaids_condition(), $slots);
    }

    /**
     * Get a list of usage ids where the question with slot $slot, and optionally
     * also with question id $questionid, is in summary state $summarystate. Also
     * return the total count of such states.
     *
     * Only a subset of the ids can be returned by using $orderby, $limitfrom and
     * $limitnum. A special value 'random' can be passed as $orderby, in which case
     * $limitfrom is ignored.
     *
     * @param int $slot The slot for the questions you want to konw about.
     * @param int $questionid (optional) Only return attempts that were of this specific question.
     * @param string $summarystate 'all', 'needsgrading', 'autograded' or 'manuallygraded'.
     * @param string $orderby 'random', 'date', 'student' or 'idnumber'.
     * @param int $page implements paging of the results.
     *      Ignored if $orderby = random or $pagesize is null.
     * @param int $pagesize implements paging of the results. null = all.
     * @return array with two elements, an array of usage ids, and a count of the total number.
     */
    protected function get_usage_ids_where_question_in_state($summarystate, $slot,
            $questionid = null, $orderby = 'random', $page = 0, $pagesize = null) {
        $dm = new question_engine_data_mapper();
        $extraselect = '';
        if ($pagesize && $orderby != 'random') {
            $limitfrom = $page * $pagesize;
        } else {
            $limitfrom = 0;
        }

        $qubaids = $this->get_qubaids_condition();

        $params = [];
        $userfieldsapi = \core_user\fields::for_identity($this->context)->with_name();
        $userfieldssql = $userfieldsapi->get_sql('u', true, '', 'userid', true);
        $params = array_merge($params, $userfieldssql->params);
        $customfields = [];
        foreach ($userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $field) {
            $customfields[] = $field;
        }
        if ($orderby === 'date') {
            list($statetest, $params) = $dm->in_summary_state_test(
                    'manuallygraded', false, 'mangrstate');
            $extraselect = "(
                    SELECT MAX(sortqas.timecreated)
                    FROM {question_attempt_steps} sortqas
                    WHERE sortqas.questionattemptid = qa.id
                        AND sortqas.state $statetest
                    ) as tcreated";
            $orderby = "tcreated";
        } else if ($orderby === 'studentfirstname' || $orderby === 'studentlastname' || in_array($orderby, $customfields)) {
            $qubaids->from .= " JOIN {user} u ON quiza.userid = u.id {$userfieldssql->joins}";
            // For name sorting, map orderby form value to
            // actual column names; 'idnumber' maps naturally.
            if ($orderby === "studentlastname") {
                $orderby = "u.lastname, u.firstname";
            } else if ($orderby === "studentfirstname") {
                $orderby = "u.firstname, u.lastname";
            } else if (in_array($orderby, $customfields)) { // Sort order by current custom user field.
                $orderby = $userfieldssql->mappings[$orderby];
            }
        }

        return $dm->load_questions_usages_where_question_in_state($qubaids, $summarystate,
                $slot, $questionid, $orderby, $params, $limitfrom, $pagesize, $extraselect);
    }

    /**
     * Initialise some parts of $PAGE and start output.
     *
     * @param object $cm the course_module information.
     * @param object $course the course settings.
     * @param object $quiz the quiz settings.
     * @param string $reportmode the report name.
     */
    public function print_header_and_tabs($cm, $course, $quiz, $reportmode = 'overview') {
        global $PAGE;
        $this->renderer = $PAGE->get_renderer('quiz_grading');
        parent::print_header_and_tabs($cm, $course, $quiz, $reportmode);
    }

    /**
     * Get question heading.
     *
     * @param stdClass $attempt An instance of quiz_attempt.
     * @param bool $shownames True to show the student first/lastnames.
     * @param bool $showcustomfields Whether custom field values should be shown.
     * @return string The string text for the question heading.
     */
    protected function get_question_heading(stdClass $attempt, bool $shownames, bool $showcustomfields): string {
        global $DB;
        $a = new stdClass();
        $a->attempt = $attempt->attempt;
        $a->fullname = fullname($attempt);
        $customfields = [];
        foreach ($this->extrauserfields as $field) {
            if ($attempt->{s($field)}) {
                $customfields[] = $attempt->{s($field)};
            }
        }
        $a->customfields = trim(implode(', ', (array)$customfields), ' ,');

        if ($shownames && $showcustomfields) {
            return get_string('gradingattemptwithcustomfields', 'quiz_grading', $a);
        } else if ($shownames) {
            return get_string('gradingattempt', 'quiz_grading', $a);
        } else if ($showcustomfields) {
            $a->fullname = $a->customfields;
            return get_string('gradingattempt', 'quiz_grading', $a);
        } else {
            return '';
        }
    }
}
