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

namespace mod_quiz\local\reports;

use coding_exception;
use context_module;
use mod_quiz\quiz_settings;
use moodle_url;
use stdClass;
use table_sql;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');


/**
 * Base class for quiz reports that are basically a table with one row for each attempt.
 *
 * @package   mod_quiz
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class attempts_report extends report_base {
    /** @var int default page size for reports. */
    const DEFAULT_PAGE_SIZE = 30;

    /** @var string constant used for the options, means all users with attempts. */
    const ALL_WITH = 'all_with';
    /** @var string constant used for the options, means only enrolled users with attempts. */
    const ENROLLED_WITH = 'enrolled_with';
    /** @var string constant used for the options, means only enrolled users without attempts. */
    const ENROLLED_WITHOUT = 'enrolled_without';
    /** @var string constant used for the options, means all enrolled users. */
    const ENROLLED_ALL = 'enrolled_any';

    /** @var string the mode this report is. */
    protected $mode;

    /** @var context_module the quiz context. */
    protected $context;

    /** @var attempts_report_options_form The settings form to use. */
    protected $form;

    /** @var string SQL fragment for selecting the attempt that gave the final grade,
     * if applicable. */
    protected $qmsubselect;

    /** @var boolean caches the results of {@see should_show_grades()}. */
    protected $showgrades = null;

    /** @var quiz_settings|null quiz settings object. Set in {@see init()}. */
    protected $quizobj = null;

    /**
     * Can be used in subclasses to cache this information, but it will only get set if you set it.
     * @example an example use in quiz_overview_report.
     *
     * @var bool
     */
    protected $hasgroupstudents;

    /**
     *  Initialise various aspects of this report.
     *
     * @param string $mode
     * @param string $formclass
     * @param stdClass $quiz
     * @param stdClass $cm
     * @param stdClass $course
     * @return array with four elements:
     *      0 => integer the current group id (0 for none).
     *      1 => \core\dml\sql_join Contains joins, wheres, params for all the students in this course.
     *      2 => \core\dml\sql_join Contains joins, wheres, params for all the students in the current group.
     *      3 => \core\dml\sql_join Contains joins, wheres, params for all the students to show in the report.
     *              Will be the same as either element 1 or 2.
     */
    public function init($mode, $formclass, $quiz, $cm, $course): array {
        $this->mode = $mode;
        $this->quizobj = new quiz_settings($quiz, $cm, $course);
        $this->context = $this->quizobj->get_context();

        [$currentgroup, $studentsjoins, $groupstudentsjoins, $allowedjoins] = $this->get_students_joins(
                $cm, $course);

        $this->qmsubselect = quiz_report_qm_filter_select($quiz);

        $this->form = new $formclass($this->get_base_url(),
                ['quiz' => $quiz, 'currentgroup' => $currentgroup, 'context' => $this->context]);

        return [$currentgroup, $studentsjoins, $groupstudentsjoins, $allowedjoins];
    }

    /**
     * Get the base URL for this report.
     * @return moodle_url the URL.
     */
    protected function get_base_url() {
        return new moodle_url('/mod/quiz/report.php',
                ['id' => $this->context->instanceid, 'mode' => $this->mode]);
    }

    /**
     * Get sql fragments (joins) which can be used to build queries that
     * will select an appropriate set of students to show in the reports.
     *
     * @param stdClass $cm the course module.
     * @param stdClass $course the course settings.
     * @return array with four elements:
     *      0 => integer the current group id (0 for none).
     *      1 => \core\dml\sql_join Contains joins, wheres, params for all the students in this course.
     *      2 => \core\dml\sql_join Contains joins, wheres, params for all the students in the current group.
     *      3 => \core\dml\sql_join Contains joins, wheres, params for all the students to show in the report.
     *              Will be the same as either element 1 or 2.
     */
    protected function get_students_joins($cm, $course = null) {
        $currentgroup = $this->get_current_group($cm, $course, $this->context);

        $empty = new \core\dml\sql_join();
        if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            return [$currentgroup, $empty, $empty, $empty];
        }

        $studentsjoins = get_enrolled_with_capabilities_join($this->context, '',
                ['mod/quiz:attempt', 'mod/quiz:reviewmyattempts']);

        if (empty($currentgroup)) {
            return [$currentgroup, $studentsjoins, $empty, $studentsjoins];
        }

        // We have a currently selected group.
        $groupstudentsjoins = get_enrolled_with_capabilities_join($this->context, '',
                ['mod/quiz:attempt', 'mod/quiz:reviewmyattempts'], $currentgroup);

        return [$currentgroup, $studentsjoins, $groupstudentsjoins, $groupstudentsjoins];
    }

    /**
     * Outputs the things you commonly want at the top of a quiz report.
     *
     * Calls through to {@see print_header_and_tabs()} and then
     * outputs the standard group selector, number of attempts summary,
     * and messages to cover common cases when the report can't be shown.
     *
     * @param stdClass $cm the course_module information.
     * @param stdClass $course the course settings.
     * @param stdClass $quiz the quiz settings.
     * @param attempts_report_options $options the current report settings.
     * @param int $currentgroup the current group.
     * @param bool $hasquestions whether there are any questions in the quiz.
     * @param bool $hasstudents whether there are any relevant students.
     */
    protected function print_standard_header_and_messages($cm, $course, $quiz,
            $options, $currentgroup, $hasquestions, $hasstudents) {
        global $OUTPUT;

        $this->print_header_and_tabs($cm, $course, $quiz, $this->mode);

        if (groups_get_activity_groupmode($cm)) {
            // Groups are being used, so output the group selector if we are not downloading.
            groups_print_activity_menu($cm, $options->get_url());
        }

        // Print information on the number of existing attempts.
        if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm, true, $currentgroup)) {
            echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
        }

        if (!$hasquestions) {
            echo quiz_no_questions_message($quiz, $cm, $this->context);
        } else if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            echo $OUTPUT->notification(get_string('notingroup'));
        } else if (!$hasstudents) {
            echo $OUTPUT->notification(get_string('nostudentsyet'));
        } else if ($currentgroup && !$this->hasgroupstudents) {
            echo $OUTPUT->notification(get_string('nostudentsingroup'));
        }
    }

    /**
     * Add all the user-related columns to the $columns and $headers arrays.
     * @param table_sql $table the table being constructed.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_user_columns($table, &$columns, &$headers) {
        global $CFG;
        if (!$table->is_downloading() && $CFG->grade_report_showuserimage) {
            $columns[] = 'picture';
            $headers[] = '';
        }
        if (!$table->is_downloading()) {
            $columns[] = 'fullname';
            $headers[] = get_string('name');
        } else {
            $columns[] = 'lastname';
            $headers[] = get_string('lastname');
            $columns[] = 'firstname';
            $headers[] = get_string('firstname');
        }

        $extrafields = \core_user\fields::get_identity_fields($this->context);
        foreach ($extrafields as $field) {
            $columns[] = $field;
            $headers[] = \core_user\fields::get_display_name($field);
        }
    }

    /**
     * Set the display options for the user-related columns in the table.
     * @param table_sql $table the table being constructed.
     */
    protected function configure_user_columns($table) {
        $table->column_suppress('picture');
        $table->column_suppress('fullname');

        $extrafields = \core_user\fields::get_identity_fields($this->context);
        foreach ($extrafields as $field) {
            $table->column_suppress($field);
        }

        $table->column_class('picture', 'picture');
        $table->column_class('lastname', 'bold');
        $table->column_class('firstname', 'bold');
        $table->column_class('fullname', 'bold');

        $table->column_sticky('fullname');
    }

    /**
     * Add the state column to the $columns and $headers arrays.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_state_column(&$columns, &$headers) {
        global $PAGE;
        $columns[] = 'state';
        $headers[] = get_string('attemptstate', 'quiz');
        $PAGE->requires->js_call_amd('mod_quiz/reopen_attempt_ui', 'init');
    }

    /**
     * Add all the time-related columns to the $columns and $headers arrays.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_time_columns(&$columns, &$headers) {
        $columns[] = 'timestart';
        $headers[] = get_string('startedon', 'quiz');

        $columns[] = 'timefinish';
        $headers[] = get_string('timecompleted', 'quiz');

        $columns[] = 'duration';
        $headers[] = get_string('attemptduration', 'quiz');
    }

    /**
     * Add a column for the overall quiz grade and the overall feedback, if applicable.
     *
     * @param stdClass $quiz the quiz settings.
     * @param bool $usercanseegrades whether the user is allowed to see grades for this quiz.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     * @param bool $includefeedback whether to include the feedbacktext columns
     */
    protected function add_grade_columns($quiz, $usercanseegrades, &$columns, &$headers, $includefeedback = true) {
        if ($usercanseegrades) {
            $columns[] = 'sumgrades';
            $headers[] = get_string('gradenoun') . '/' .
                    quiz_format_grade($quiz, $quiz->grade);
        }

        if ($includefeedback && quiz_has_feedback($quiz)) {
            $columns[] = 'feedbacktext';
            $headers[] = get_string('feedback', 'quiz');
        }
    }

    /**
     * Add columns for any extra quiz grade items, if applicable.
     *
     * @param bool $usercanseegrades whether the user is allowed to see grades for this quiz.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_grade_item_columns(bool $usercanseegrades, array &$columns, array &$headers) {
        if (!$usercanseegrades) {
            return;
        }

        $gradeitems = $this->quizobj->get_grade_calculator()->get_grade_items();
        if (!$gradeitems) {
            return;
        }

        foreach ($gradeitems as $gradeitem) {
            $columns[] = 'marks' . $gradeitem->id;
            $headers[] = format_string($gradeitem->name) . '/' .
                quiz_format_grade($this->quizobj->get_quiz(), $gradeitem->maxmark);
        }
    }

    /**
     * Set up the table.
     * @param attempts_report_table $table the table being constructed.
     * @param array $columns the list of columns.
     * @param array $headers the columns headings.
     * @param moodle_url $reporturl the URL of this report.
     * @param attempts_report_options $options the display options.
     * @param bool $collapsible whether to allow columns in the report to be collapsed.
     */
    protected function set_up_table_columns($table, $columns, $headers, $reporturl,
            attempts_report_options $options, $collapsible) {
        $table->set_quiz_setting($this->quizobj);
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->sortable(true, 'uniqueid');

        $table->define_baseurl($options->get_url());

        $this->configure_user_columns($table);

        $table->no_sorting('feedbacktext');
        $table->column_class('sumgrades', 'bold');

        $table->set_attribute('id', 'attempts');

        $table->collapsible($collapsible);
    }

    /**
     * Process any submitted actions.
     * @param stdClass $quiz the quiz settings.
     * @param stdClass $cm the cm object for the quiz.
     * @param int $currentgroup the currently selected group.
     * @param \core\dml\sql_join $groupstudentsjoins (joins, wheres, params) the students in the current group.
     * @param \core\dml\sql_join $allowedjoins (joins, wheres, params) the users whose attempt this user is allowed to modify.
     * @param moodle_url $redirecturl where to redircet to after a successful action.
     */
    protected function process_actions($quiz, $cm, $currentgroup, \core\dml\sql_join $groupstudentsjoins,
            \core\dml\sql_join $allowedjoins, $redirecturl) {
        if (empty($currentgroup) || $this->hasgroupstudents) {
            if (optional_param('delete', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param_array('attemptid', [], PARAM_INT)) {
                    require_capability('mod/quiz:deleteattempts', $this->context);
                    $this->delete_selected_attempts($quiz, $cm, $attemptids, $allowedjoins);
                    redirect($redirecturl);
                }
            }
        }
    }

    /**
     * Delete the quiz attempts
     * @param stdClass $quiz the quiz settings. Attempts that don't belong to
     * this quiz are not deleted.
     * @param stdClass $cm the course_module object.
     * @param array $attemptids the list of attempt ids to delete.
     * @param \core\dml\sql_join $allowedjoins (joins, wheres, params) This list of userids that are visible in the report.
     *      Users can only delete attempts that they are allowed to see in the report.
     *      Empty means all users.
     */
    protected function delete_selected_attempts($quiz, $cm, $attemptids, \core\dml\sql_join $allowedjoins) {
        global $DB;

        foreach ($attemptids as $attemptid) {
            if (empty($allowedjoins->joins)) {
                $sql = "SELECT quiza.*
                          FROM {quiz_attempts} quiza
                          JOIN {user} u ON u.id = quiza.userid
                         WHERE quiza.id = :attemptid";
            } else {
                $sql = "SELECT quiza.*
                          FROM {quiz_attempts} quiza
                          JOIN {user} u ON u.id = quiza.userid
                        {$allowedjoins->joins}
                         WHERE {$allowedjoins->wheres} AND quiza.id = :attemptid";
            }
            $params = $allowedjoins->params + ['attemptid' => $attemptid];
            $attempt = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE);
            if (!$attempt || $attempt->quiz != $quiz->id || $attempt->preview != 0) {
                // Ensure the attempt exists, belongs to this quiz and belongs to
                // a student included in the report. If not skip.
                continue;
            }

            // Set the course module id before calling quiz_delete_attempt().
            $quiz->cmid = $cm->id;
            quiz_delete_attempt($attempt, $quiz);
        }
    }
}
