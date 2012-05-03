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
 * The file defines a base class that can be used to build a report like the
 * overview or responses report, that has one row per attempt.
 *
 * @package   mod_quiz
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');


/**
 * Base class for quiz reports that are basically a table with one row for each attempt.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class quiz_attempts_report extends quiz_default_report {
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

    /** @var object the quiz context. */
    protected $context;

    /** @var mod_quiz_attempts_report_form The settings form to use. */
    protected $form;

    /** @var string SQL fragment for selecting the attempt that gave the final grade,
     * if applicable. */
    protected $qmsubselect;

    /** @var boolean caches the results of {@link should_show_grades()}. */
    protected $showgrades = null;

    /**
     *  Initialise various aspects of this report.
     *
     * @param string $mode
     * @param string $formclass
     * @param object $quiz
     * @param object $cm
     * @param object $course
     */
    protected function init($mode, $formclass, $quiz, $cm, $course) {
        $this->mode = $mode;

        $this->context = context_module::instance($cm->id);

        list($currentgroup, $students, $groupstudents, $allowed) =
                $this->load_relevant_students($cm, $course);

        $this->qmsubselect = quiz_report_qm_filter_select($quiz);

        $this->form = new $formclass($this->get_base_url(),
                array('qmsubselect' => $this->qmsubselect, 'quiz' => $quiz,
                'currentgroup' => $currentgroup, 'context' => $this->context));

        return array($currentgroup, $students, $groupstudents, $allowed);
    }

    /**
     * Get the base URL for this report.
     * @return moodle_url the URL.
     */
    protected function get_base_url() {
        return new moodle_url('/mod/quiz/report.php',
                array('id' => $this->context->instanceid, 'mode' => $this->mode));
    }

    /**
     * Get information about which students to show in the report.
     * @param object $cm the coures module.
     * @param object $course the course settings.
     * @return an array with four elements:
     *      0 => integer the current group id (0 for none).
     *      1 => array ids of all the students in this course.
     *      2 => array ids of all the students in the current group.
     *      3 => array ids of all the students to show in the report. Will be the
     *              same as either element 1 or 2.
     */
    protected function load_relevant_students($cm, $course = null) {
        $currentgroup = $this->get_current_group($cm, $course, $this->context);

        if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            return array($currentgroup, array(), array(), array());
        }

        if (!$students = get_users_by_capability($this->context,
                array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),
                'u.id, 1', '', '', '', '', '', false)) {
            $students = array();
        } else {
            $students = array_keys($students);
        }

        if (empty($currentgroup)) {
            return array($currentgroup, $students, array(), $students);
        }

        // We have a currently selected group.
        if (!$groupstudents = get_users_by_capability($this->context,
                array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),
                'u.id, 1', '', '', '', $currentgroup, '', false)) {
            $groupstudents = array();
        } else {
            $groupstudents = array_keys($groupstudents);
        }

        return array($currentgroup, $students, $groupstudents, $groupstudents);
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

        // When downloading, some extra fields are always displayed (because
        // there's no space constraint) so do not include in extra-field list.
        $extrafields = get_extra_user_fields($this->context,
                $table->is_downloading() ? array('institution', 'department', 'email') : array());
        foreach ($extrafields as $field) {
            $columns[] = $field;
            $headers[] = get_user_field_name($field);
        }

        if ($table->is_downloading()) {
            $columns[] = 'institution';
            $headers[] = get_string('institution');

            $columns[] = 'department';
            $headers[] = get_string('department');

            $columns[] = 'email';
            $headers[] = get_string('email');
        }
    }

    /**
     * Set the display options for the user-related columns in the table.
     * @param table_sql $table the table being constructed.
     */
    protected function configure_user_columns($table) {
        $table->column_suppress('picture');
        $table->column_suppress('fullname');
        $table->column_suppress('idnumber');

        $table->column_class('picture', 'picture');
        $table->column_class('lastname', 'bold');
        $table->column_class('firstname', 'bold');
        $table->column_class('fullname', 'bold');
    }

    /**
     * Add the state column to the $columns and $headers arrays.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_state_column(&$columns, &$headers) {
        $columns[] = 'state';
        $headers[] = get_string('attemptstate', 'quiz');
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
     * Add all the grade and feedback columns, if applicable, to the $columns
     * and $headers arrays.
     * @param object $quiz the quiz settings.
     * @param bool $usercanseegrades whether the user is allowed to see grades for this quiz.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     * @param bool $includefeedback whether to include the feedbacktext columns
     */
    protected function add_grade_columns($quiz, $usercanseegrades, &$columns, &$headers, $includefeedback = true) {
        if ($usercanseegrades) {
            $columns[] = 'sumgrades';
            $headers[] = get_string('grade', 'quiz') . '/' .
                    quiz_format_grade($quiz, $quiz->grade);
        }

        if ($includefeedback && quiz_has_feedback($quiz)) {
            $columns[] = 'feedbacktext';
            $headers[] = get_string('feedback', 'quiz');
        }
    }

    /**
     * Set up the table.
     * @param table_sql $table the table being constructed.
     * @param array $columns the list of columns.
     * @param array $headers the columns headings.
     * @param moodle_url $reporturl the URL of this report.
     * @param mod_quiz_attempts_report_options $options the display options.
     * @param bool $collapsible whether to allow columns in the report to be collapsed.
     */
    protected function set_up_table_columns($table, $columns, $headers, $reporturl,
            mod_quiz_attempts_report_options $options, $collapsible) {
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
     * @param object $quiz the quiz settings.
     * @param object $cm the cm object for the quiz.
     * @param int $currentgroup the currently selected group.
     * @param array $groupstudents the students in the current group.
     * @param array $allowed the users whose attempt this user is allowed to modify.
     * @param moodle_url $redirecturl where to redircet to after a successful action.
     */
    protected function process_actions($quiz, $cm, $currentgroup, $groupstudents, $allowed, $redirecturl) {
        if (empty($currentgroup) || $groupstudents) {
            if (optional_param('delete', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param_array('attemptid', array(), PARAM_INT)) {
                    require_capability('mod/quiz:deleteattempts', $this->context);
                    $this->delete_selected_attempts($quiz, $cm, $attemptids, $allowed);
                    redirect($redirecturl);
                }
            }
        }
    }

    /**
     * Delete the quiz attempts
     * @param object $quiz the quiz settings. Attempts that don't belong to
     * this quiz are not deleted.
     * @param object $cm the course_module object.
     * @param array $attemptids the list of attempt ids to delete.
     * @param array $allowed This list of userids that are visible in the report.
     *      Users can only delete attempts that they are allowed to see in the report.
     *      Empty means all users.
     */
    protected function delete_selected_attempts($quiz, $cm, $attemptids, $allowed) {
        global $DB;

        foreach ($attemptids as $attemptid) {
            $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
            if (!$attempt || $attempt->quiz != $quiz->id || $attempt->preview != 0) {
                // Ensure the attempt exists, and belongs to this quiz. If not skip.
                continue;
            }
            if ($allowed && !in_array($attempt->userid, $allowed)) {
                // Ensure the attempt belongs to a student included in the report. If not skip.
                continue;
            }
            add_to_log($quiz->course, 'quiz', 'delete attempt', 'report.php?id=' . $cm->id,
                    $attemptid, $cm->id);
            quiz_delete_attempt($attempt, $quiz);
        }
    }
}
