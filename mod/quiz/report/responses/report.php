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
 * This file defines the quiz responses report class.
 *
 * @package    quiz
 * @subpackage responses
 * @copyright  2006 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/quiz/report/attemptsreport.php');
require_once($CFG->dirroot.'/mod/quiz/report/responses/responsessettings_form.php');
require_once($CFG->dirroot.'/mod/quiz/report/responses/responses_table.php');


/**
 * Quiz report subclass for the responses report.
 *
 * This report lists some combination of
 *  * what question each student saw (this makes sense if random questions were used).
 *  * the response they gave,
 *  * and what the right answer is.
 *
 * Like the overview report, there are options for showing students with/without
 * attempts, and for deleting selected attempts.
 *
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_responses_report extends quiz_attempt_report {

    public function display($quiz, $cm, $course) {
        global $CFG, $COURSE, $DB, $PAGE, $OUTPUT;

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $download = optional_param('download', '', PARAM_ALPHA);

        list($currentgroup, $students, $groupstudents, $allowed) =
                $this->load_relevant_students($cm, $course);

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'responses';

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);
        $qmsubselect = quiz_report_qm_filter_select($quiz);

        $mform = new mod_quiz_report_responses_settings($reporturl,
                array('qmsubselect' => $qmsubselect, 'quiz' => $quiz,
                'currentgroup' => $currentgroup, 'context' => $this->context));

        if ($fromform = $mform->get_data()) {
            $attemptsmode = $fromform->attemptsmode;
            if ($qmsubselect) {
                $qmfilter = $fromform->qmfilter;
            } else {
                $qmfilter = 0;
            }
            set_user_preference('quiz_report_responses_qtext', $fromform->qtext);
            set_user_preference('quiz_report_responses_resp', $fromform->resp);
            set_user_preference('quiz_report_responses_right', $fromform->right);
            set_user_preference('quiz_report_pagesize', $fromform->pagesize);
            $includeqtext = $fromform->qtext;
            $includeresp = $fromform->resp;
            $includeright = $fromform->right;
            $pagesize = $fromform->pagesize;

        } else {
            $attemptsmode = optional_param('attemptsmode', null, PARAM_INT);
            if ($qmsubselect) {
                $qmfilter = optional_param('qmfilter', 0, PARAM_INT);
            } else {
                $qmfilter = 0;
            }
            $includeqtext = get_user_preferences('quiz_report_responses_qtext', 0);
            $includeresp = get_user_preferences('quiz_report_responses_resp', 1);
            $includeright = get_user_preferences('quiz_report_responses_right', 0);
            $pagesize = get_user_preferences('quiz_report_pagesize', 0);
        }

        $this->validate_common_options($attemptsmode, $pagesize, $course, $currentgroup);
        if (!$includeqtext && !$includeresp && !$includeright) {
            $includeresp = 1;
            set_user_preference('quiz_report_responses_resp', 1);
        }

        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $includecheckboxes = has_capability('mod/quiz:deleteattempts', $this->context)
                && ($attemptsmode != QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO);

        $displayoptions = array();
        $displayoptions['attemptsmode'] = $attemptsmode;
        $displayoptions['qmfilter'] = $qmfilter;
        $displayoptions['qtext'] = $includeqtext;
        $displayoptions['resp'] = $includeresp;
        $displayoptions['right'] = $includeright;

        $mform->set_data($displayoptions + array('pagesize' => $pagesize));

        if ($attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL) {
            // This option is only available to users who can access all groups in
            // groups mode, so setting allowed to empty (which means all quiz attempts
            // are accessible, is not a security porblem.
            $allowed = array();
        }

        if (empty($currentgroup) || $groupstudents) {
            if (optional_param('delete', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
                    require_capability('mod/quiz:deleteattempts', $this->context);
                    $this->delete_selected_attempts($quiz, $cm, $attemptids, $allowed);
                    redirect($reporturl->out(false, $displayoptions));
                }
            }
        }

        // Load the required questions.
        $questions = quiz_report_get_significant_questions($quiz);

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

        $displaycoursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $displaycourseshortname = format_string($COURSE->shortname, true, array('context' => $displaycoursecontext));

        $table = new quiz_report_responses_table($quiz, $this->context, $qmsubselect,
                $qmfilter, $attemptsmode, $groupstudents, $students, $questions,
                $includecheckboxes, $reporturl, $displayoptions);
        $filename = quiz_report_download_filename(get_string('responsesfilename', 'quiz_responses'),
                $courseshortname, $quiz->name);
        $table->is_downloading($download, $filename,
                $displaycourseshortname . ' ' . format_string($quiz->name, true));
        if ($table->is_downloading()) {
            raise_memory_limit(MEMORY_EXTRA);
        }

        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data
            $this->print_header_and_tabs($cm, $course, $quiz, 'responses');
        }

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
            if (!$table->is_downloading()) {
                groups_print_activity_menu($cm, $reporturl->out(true, $displayoptions));
            }
        }

        // Print information on the number of existing attempts
        if (!$table->is_downloading()) { //do not print notices when downloading
            if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm, true, $currentgroup)) {
                echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
            }
        }

        $hasquestions = quiz_questions_in_quiz($quiz->questions);
        if (!$table->is_downloading()) {
            if (!$hasquestions) {
                echo quiz_no_questions_message($quiz, $cm, $this->context);
            } else if (!$students) {
                echo $OUTPUT->notification(get_string('nostudentsyet'));
            } else if ($currentgroup && !$groupstudents) {
                echo $OUTPUT->notification(get_string('nostudentsingroup'));
            }

            // Print display options
            $mform->display();
        }

        $hasstudents = $students && (!$currentgroup || $groupstudents);
        if ($hasquestions && ($hasstudents || $attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL)) {
            // Print information on the grading method and whether we are displaying
            if (!$table->is_downloading()) { //do not print notices when downloading
                if ($strattempthighlight = quiz_report_highlighting_grading_method(
                        $quiz, $qmsubselect, $qmfilter)) {
                    echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
                }
            }

            list($fields, $from, $where, $params) = $table->base_sql($allowed);

            $table->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);

            $table->set_sql($fields, $from, $where, $params);

            // Define table columns
            $columns = array();
            $headers = array();

            if (!$table->is_downloading() && $includecheckboxes) {
                $columns[] = 'checkbox';
                $headers[] = null;
            }

            $this->add_user_columns($table, $columns, $headers);

            if ($table->is_downloading()) {
                $this->add_time_columns($columns, $headers);
            }

            $this->add_grade_columns($quiz, $columns, $headers);

            foreach ($questions as $id => $question) {
                if ($displayoptions['qtext']) {
                    $columns[] = 'question' . $id;
                    $headers[] = get_string('questionx', 'question', $question->number);
                }
                if ($displayoptions['resp']) {
                    $columns[] = 'response' . $id;
                    $headers[] = get_string('responsex', 'quiz_responses', $question->number);
                }
                if ($displayoptions['right']) {
                    $columns[] = 'right' . $id;
                    $headers[] = get_string('rightanswerx', 'quiz_responses', $question->number);
                }
            }

            $table->define_columns($columns);
            $table->define_headers($headers);
            $table->sortable(true, 'uniqueid');

            // Set up the table
            $table->define_baseurl($reporturl->out(true, $displayoptions));

            $this->configure_user_columns($table);

            $table->no_sorting('feedbacktext');
            $table->column_class('sumgrades', 'bold');

            $table->set_attribute('id', 'attempts');

            $table->collapsible(true);

            $table->out($pagesize, true);
        }
        return true;
    }
}
