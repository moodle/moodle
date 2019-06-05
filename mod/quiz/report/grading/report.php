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

    protected $viewoptions = array();
    protected $questions;
    protected $cm;
    protected $quiz;
    protected $context;

    /** @var renderer_base Renderer of Quiz Grading. */
    private $renderer;

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
        $pagesize = optional_param('pagesize', self::DEFAULT_PAGE_SIZE, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $order = optional_param('order', self::DEFAULT_ORDER, PARAM_ALPHA);

        // Assemble the options requried to reload this page.
        $optparams = array('includeauto', 'page');
        foreach ($optparams as $param) {
            if ($$param) {
                $this->viewoptions[$param] = $$param;
            }
        }
        if ($pagesize != self::DEFAULT_PAGE_SIZE) {
            $this->viewoptions['pagesize'] = $pagesize;
        }
        if ($order != self::DEFAULT_ORDER) {
            $this->viewoptions['order'] = $order;
        }

        // Check permissions.
        $this->context = context_module::instance($cm->id);
        require_capability('mod/quiz:grade', $this->context);
        $shownames = has_capability('quiz/grading:viewstudentnames', $this->context);
        $showidnumbers = has_capability('quiz/grading:viewidnumber', $this->context);

        // Validate order.
        if (!in_array($order, array('random', 'date', 'studentfirstname', 'studentlastname', 'idnumber'))) {
            $order = self::DEFAULT_ORDER;
        } else if (!$shownames && ($order == 'studentfirstname' || $order == 'studentlastname')) {
            $order = self::DEFAULT_ORDER;
        } else if (!$showidnumbers && $order == 'idnumber') {
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
            $this->process_submitted_data();

            redirect($this->grade_question_url($slot, $questionid, $grade, $page + 1));
        }

        // Get the group, and the list of significant users.
        $this->currentgroup = $this->get_current_group($cm, $course, $this->context);
        if ($this->currentgroup == self::NO_GROUPS_ALLOWED) {
            $this->userssql = array();
        } else {
            $this->userssql = get_enrolled_sql($this->context,
                    array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $this->currentgroup);
        }

        $hasquestions = quiz_has_questions($quiz->id);
        $counts = null;
        if ($slot && $hasquestions) {
            // Make sure there is something to do.
            $statecounts = $this->get_question_state_summary(array($slot));
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
        }

        // Start output.
        $this->print_header_and_tabs($cm, $course, $quiz, 'grading');

        // What sort of page to display?
        if (!$hasquestions) {
            echo $this->renderer->render_quiz_no_question_notification($quiz, $cm, $this->context);

        } else if (!$slot) {
            echo $this->display_index($includeauto);

        } else {
            echo $this->display_grading_interface($slot, $questionid, $grade,
                    $pagesize, $page, $shownames, $showidnumbers, $order, $counts);
        }
        return true;
    }

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

    protected function load_attempts_by_usage_ids($qubaids) {
        global $DB;

        list($asql, $params) = $DB->get_in_or_equal($qubaids);
        $params[] = quiz_attempt::FINISHED;
        $params[] = $this->quiz->id;

        $fields = 'quiza.*, u.idnumber, ';
        $fields .= get_all_user_name_fields(true, 'u');
        $attemptsbyid = $DB->get_records_sql("
                SELECT $fields
                FROM {quiz_attempts} quiza
                JOIN {user} u ON u.id = quiza.userid
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
     * @param $includeauto if not given, use the current setting, otherwise,
     *      force a paricular value of includeauto in the URL.
     * @return string the URL.
     */
    protected function base_url() {
        return new moodle_url('/mod/quiz/report.php',
                array('id' => $this->cm->id, 'mode' => 'grading'));
    }

    /**
     * Get the URL of the front page of the report that lists all the questions.
     * @param $includeauto if not given, use the current setting, otherwise,
     *      force a paricular value of includeauto in the URL.
     * @return string the URL.
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
     * @param int $slot
     * @param int $questionid
     * @param string $grade
     * @param mixed $page = true, link to current page. false = omit page.
     *      number = link to specific page.
     */
    protected function grade_question_url($slot, $questionid, $grade, $page = true) {
        $url = $this->base_url();
        $url->params(array('slot' => $slot, 'qid' => $questionid, 'grade' => $grade));
        $url->params($this->viewoptions);

        $options = $this->viewoptions;
        if (!$page) {
            $url->remove_params('page');
        } else if (is_integer($page)) {
            $url->param('page', $page);
        }

        return $url;
    }

    protected function format_count_for_table($counts, $type, $gradestring) {
        $result = $counts->$type;
        if ($counts->$type > 0) {
            $gradeurl = $this->grade_question_url($counts->slot, $counts->questionid, $type);
            $result .= $this->renderer->render_grade_link($counts, $type, $gradestring, $gradeurl);
        }
        return $result;
    }

    protected function display_index($includeauto) {
        global $PAGE;
        $output = '';

        if ($groupmode = groups_get_activity_groupmode($this->cm)) {
            // Groups is being used.
            groups_print_activity_menu($this->cm, $this->list_questions_url());
        }
        $statecounts = $this->get_question_state_summary(array_keys($this->questions));
        if ($includeauto) {
            $linktext = get_string('hideautomaticallygraded', 'quiz_grading');
        } else {
            $linktext = get_string('alsoshowautomaticallygraded', 'quiz_grading');
        }
        $output .= $this->renderer->render_display_index_heading($linktext, $this->list_questions_url(!$includeauto));
        $data = array();
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

            $row = array();

            $row[] = $this->questions[$counts->slot]->number;

            $row[] = $PAGE->get_renderer('question', 'bank')->qtype_icon($this->questions[$counts->slot]->type);

            $row[] = format_string($counts->name);

            $row[] = $this->format_count_for_table($counts, 'needsgrading', 'grade');

            $row[] = $this->format_count_for_table($counts, 'manuallygraded', 'updategrade');

            if ($includeauto) {
                $row[] = $this->format_count_for_table($counts, 'autograded', 'updategrade');
            }

            $row[] = $this->format_count_for_table($counts, 'all', 'gradeall');

            $data[] = $row;
        }
        $output .= $this->renderer->render_questions_table($includeauto, $data, $header);
        return $output;
    }

    protected function display_grading_interface($slot, $questionid, $grade,
            $pagesize, $page, $shownames, $showidnumbers, $order, $counts) {
        $output = '';

        if ($pagesize * $page >= $counts->$grade) {
            $page = 0;
        }

        list($qubaids, $count) = $this->get_usage_ids_where_question_in_state(
                $grade, $slot, $questionid, $order, $page, $pagesize);
        $attempts = $this->load_attempts_by_usage_ids($qubaids);

        // Prepare the form.
        $hidden = array(
            'id' => $this->cm->id,
            'mode' => 'grading',
            'slot' => $slot,
            'qid' => $questionid,
            'page' => $page,
        );
        if (array_key_exists('includeauto', $this->viewoptions)) {
            $hidden['includeauto'] = $this->viewoptions['includeauto'];
        }
        $mform = new quiz_grading_settings_form($hidden, $counts, $shownames, $showidnumbers);

        // Tell the form the current settings.
        $settings = new stdClass();
        $settings->grade = $grade;
        $settings->pagesize = $pagesize;
        $settings->order = $order;
        $mform->set_data($settings);

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

        $gradequestioncontent = '';
        foreach ($qubaids as $qubaid) {
            $attempt = $attempts[$qubaid];
            $quba = question_engine::load_questions_usage_by_activity($qubaid);
            $displayoptions = quiz_get_review_options($this->quiz, $attempt, $this->context);
            $displayoptions->hide_all_feedback();
            $displayoptions->rightanswer = question_display_options::VISIBLE;
            $displayoptions->history = question_display_options::HIDDEN;
            $displayoptions->manualcomment = question_display_options::EDITABLE;

            $gradequestioncontent .= $this->renderer->render_grade_question(
                    $quba,
                    $slot,
                    $displayoptions,
                    $this->questions[$slot]->number,
                    $this->get_question_heading($attempt, $shownames, $showidnumbers)
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

        $output .= $this->renderer->render_grading_interface(
                $questioninfo,
                $this->list_questions_url(),
                $mform,
                $paginginfo,
                $pagingbar,
                $this->grade_question_url($slot, $questionid, $grade, $page),
                $hiddeninputs,
                $gradequestioncontent
        );
        return $output;
    }

    protected function validate_submitted_marks() {

        $qubaids = optional_param('qubaids', null, PARAM_SEQUENCE);
        if (!$qubaids) {
            return false;
        }
        $qubaids = clean_param_array(explode(',', $qubaids), PARAM_INT);

        $slots = optional_param('slots', '', PARAM_SEQUENCE);
        if (!$slots) {
            $slots = array();
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

    protected function process_submitted_data() {
        global $DB;

        $qubaids = optional_param('qubaids', null, PARAM_SEQUENCE);
        $assumedslotforevents = optional_param('slot', null, PARAM_INT);

        if (!$qubaids) {
            return;
        }

        $qubaids = clean_param_array(explode(',', $qubaids), PARAM_INT);
        $attempts = $this->load_attempts_by_usage_ids($qubaids);
        $events = array();

        $transaction = $DB->start_delegated_transaction();
        foreach ($qubaids as $qubaid) {
            $attempt = $attempts[$qubaid];
            $attemptobj = new quiz_attempt($attempt, $this->quiz, $this->cm, $this->course);
            $attemptobj->process_submitted_actions(time());

            // Add the event we will trigger later.
            $params = array(
                'objectid' => $attemptobj->get_question_attempt($assumedslotforevents)->get_question()->id,
                'courseid' => $attemptobj->get_courseid(),
                'context' => context_module::instance($attemptobj->get_cmid()),
                'other' => array(
                    'quizid' => $attemptobj->get_quizid(),
                    'attemptid' => $attemptobj->get_attemptid(),
                    'slot' => $assumedslotforevents
                )
            );
            $events[] = \mod_quiz\event\question_manually_graded::create($params);
        }
        $transaction->allow_commit();

        // Trigger events for all the questions we manually marked.
        foreach ($events as $event) {
            $event->trigger();
        }
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
     */
    protected function get_usage_ids_where_question_in_state($summarystate, $slot,
            $questionid = null, $orderby = 'random', $page = 0, $pagesize = null) {
        global $CFG, $DB;
        $dm = new question_engine_data_mapper();

        if ($pagesize && $orderby != 'random') {
            $limitfrom = $page * $pagesize;
        } else {
            $limitfrom = 0;
        }

        $qubaids = $this->get_qubaids_condition();

        $params = array();
        if ($orderby == 'date') {
            list($statetest, $params) = $dm->in_summary_state_test(
                    'manuallygraded', false, 'mangrstate');
            $orderby = "(
                    SELECT MAX(sortqas.timecreated)
                    FROM {question_attempt_steps} sortqas
                    WHERE sortqas.questionattemptid = qa.id
                        AND sortqas.state $statetest
                    )";
        } else if ($orderby == 'studentfirstname' || $orderby == 'studentlastname' || $orderby == 'idnumber') {
            $qubaids->from .= " JOIN {user} u ON quiza.userid = u.id ";
            // For name sorting, map orderby form value to
            // actual column names; 'idnumber' maps naturally
            switch ($orderby) {
                case "studentlastname":
                    $orderby = "u.lastname, u.firstname";
                    break;
                case "studentfirstname":
                    $orderby = "u.firstname, u.lastname";
                    break;
                case "idnumber":
                    $orderby = "u.idnumber";
                    break;
            }
        }

        return $dm->load_questions_usages_where_question_in_state($qubaids, $summarystate,
                $slot, $questionid, $orderby, $params, $limitfrom, $pagesize);
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
     * @param object $attempt an instance of quiz_attempt.
     * @param bool $shownames True to show the question name.
     * @param bool $showidnumbers True to show the question id number.
     * @return string The string text for the question heading.
     * @throws coding_exception
     */
    protected function get_question_heading($attempt, $shownames, $showidnumbers) {
        $a = new stdClass();
        $a->attempt = $attempt->attempt;
        $a->fullname = fullname($attempt);
        $a->idnumber = $attempt->idnumber;

        $showidnumbers = $showidnumbers && !empty($attempt->idnumber);

        if ($shownames && $showidnumbers) {
            return get_string('gradingattemptwithidnumber', 'quiz_grading', $a);
        } else if ($shownames) {
            return get_string('gradingattempt', 'quiz_grading', $a);
        } else if ($showidnumbers) {
            $a->fullname = $attempt->idnumber;
            return get_string('gradingattempt', 'quiz_grading', $a);
        } else {
            return '';
        }
    }
}
