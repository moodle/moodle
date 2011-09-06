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
 * This file defines the quiz overview report class.
 *
 * @package    quiz
 * @subpackage overview
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/quiz/report/attemptsreport.php');
require_once($CFG->dirroot.'/mod/quiz/report/overview/overviewsettings_form.php');
require_once($CFG->dirroot.'/mod/quiz/report/overview/overview_table.php');


/**
 * Quiz report subclass for the overview (grades) report.
 *
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_overview_report extends quiz_attempt_report {

    public function display($quiz, $cm, $course) {
        global $CFG, $COURSE, $DB, $OUTPUT;

        $this->context = get_context_instance(CONTEXT_MODULE, $cm->id);

        $download = optional_param('download', '', PARAM_ALPHA);

        list($currentgroup, $students, $groupstudents, $allowed) =
                $this->load_relevant_students($cm);

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'overview';

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);
        $qmsubselect = quiz_report_qm_filter_select($quiz);

        $mform = new mod_quiz_report_overview_settings($reporturl,
                array('qmsubselect' => $qmsubselect, 'quiz' => $quiz,
                'currentgroup' => $currentgroup, 'context' => $this->context));

        if ($fromform = $mform->get_data()) {
            $regradeall = false;
            $regradealldry = false;
            $regradealldrydo = false;
            $attemptsmode = $fromform->attemptsmode;
            if ($qmsubselect) {
                $qmfilter = $fromform->qmfilter;
            } else {
                $qmfilter = 0;
            }
            $regradefilter = !empty($fromform->regradefilter);
            set_user_preference('quiz_report_overview_detailedmarks', $fromform->detailedmarks);
            set_user_preference('quiz_report_pagesize', $fromform->pagesize);
            $detailedmarks = $fromform->detailedmarks;
            $pagesize = $fromform->pagesize;

        } else {
            $regradeall  = optional_param('regradeall', 0, PARAM_BOOL);
            $regradealldry  = optional_param('regradealldry', 0, PARAM_BOOL);
            $regradealldrydo  = optional_param('regradealldrydo', 0, PARAM_BOOL);
            $attemptsmode = optional_param('attemptsmode', null, PARAM_INT);
            if ($qmsubselect) {
                $qmfilter = optional_param('qmfilter', 0, PARAM_INT);
            } else {
                $qmfilter = 0;
            }
            $regradefilter = optional_param('regradefilter', 0, PARAM_INT);
            $detailedmarks = get_user_preferences('quiz_report_overview_detailedmarks', 1);
            $pagesize = get_user_preferences('quiz_report_pagesize', 0);
        }

        $this->validate_common_options($attemptsmode, $pagesize, $course, $currentgroup);
        $displayoptions = array();
        $displayoptions['attemptsmode'] = $attemptsmode;
        $displayoptions['qmfilter'] = $qmfilter;
        $displayoptions['regradefilter'] = $regradefilter;

        $mform->set_data($displayoptions +
                array('detailedmarks' => $detailedmarks, 'pagesize' => $pagesize));

        if (!$this->should_show_grades($quiz)) {
            $detailedmarks = 0;
        }

        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $candelete = has_capability('mod/quiz:deleteattempts', $this->context)
                && ($attemptsmode != QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO);

        if ($attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL) {
            // This option is only available to users who can access all groups in
            // groups mode, so setting allowed to empty (which means all quiz attempts
            // are accessible, is not a security porblem.
            $allowed = array();
        }

        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

        $displaycoursecontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        $displaycourseshortname = format_string($COURSE->shortname, true, array('context' => $displaycoursecontext));

        // Load the required questions.
        $questions = quiz_report_get_significant_questions($quiz);

        $table = new quiz_report_overview_table($quiz, $this->context, $qmsubselect,
                $groupstudents, $students, $detailedmarks, $questions, $candelete,
                $reporturl, $displayoptions);
        $filename = quiz_report_download_filename(get_string('overviewfilename', 'quiz_overview'),
                $courseshortname, $quiz->name);
        $table->is_downloading($download, $filename,
                $displaycourseshortname . ' ' . format_string($quiz->name, true));
        if ($table->is_downloading()) {
            raise_memory_limit(MEMORY_EXTRA);
        }

        // Process actions.
        if (empty($currentgroup) || $groupstudents) {
            if (optional_param('delete', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
                    require_capability('mod/quiz:deleteattempts', $this->context);
                    $this->delete_selected_attempts($quiz, $cm, $attemptids, $allowed);
                    redirect($reporturl->out(false, $displayoptions));
                }

            } else if (optional_param('regrade', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param('attemptid', array(), PARAM_INT)) {
                    require_capability('mod/quiz:regrade', $this->context);
                    $this->regrade_attempts($quiz, false, $groupstudents, $attemptids);
                    redirect($reporturl->out(false, $displayoptions));
                }
            }
        }

        if ($regradeall && confirm_sesskey()) {
            require_capability('mod/quiz:regrade', $this->context);
            $this->regrade_attempts($quiz, false, $groupstudents);
            redirect($reporturl->out(false, $displayoptions), '', 5);

        } else if ($regradealldry && confirm_sesskey()) {
            require_capability('mod/quiz:regrade', $this->context);
            $this->regrade_attempts($quiz, true, $groupstudents);
            redirect($reporturl->out(false, $displayoptions), '', 5);

        } else if ($regradealldrydo && confirm_sesskey()) {
            require_capability('mod/quiz:regrade', $this->context);
            $this->regrade_attempts_needing_it($quiz, $groupstudents);
            redirect($reporturl->out(false, $displayoptions), '', 5);
        }

        // Start output.
        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data
            $this->print_header_and_tabs($cm, $course, $quiz, 'overview');
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
        if ($hasquestions && ($hasstudents || ($attemptsmode == QUIZ_REPORT_ATTEMPTS_ALL))) {
            // Construct the SQL
            $fields = $DB->sql_concat('u.id', "'#'", 'COALESCE(quiza.attempt, 0)') .
                    ' AS uniqueid, ';
            if ($qmsubselect) {
                $fields .=
                    "(CASE " .
                    "   WHEN $qmsubselect THEN 1" .
                    "   ELSE 0 " .
                    "END) AS gradedattempt, ";
            }

            list($fields, $from, $where, $params) =
                    $this->base_sql($quiz, $qmsubselect, $qmfilter, $attemptsmode, $allowed);

            $table->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);

            // Test to see if there are any regraded attempts to be listed.
            $fields .= ", COALESCE((
                                SELECT MAX(qqr.regraded)
                                  FROM {quiz_overview_regrades} qqr
                                 WHERE qqr.questionusageid = quiza.uniqueid
                          ), -1) AS regraded";
            if ($regradefilter) {
                $where .= " AND COALESCE((
                                    SELECT MAX(qqr.regraded)
                                      FROM {quiz_overview_regrades} qqr
                                     WHERE qqr.questionusageid = quiza.uniqueid
                                ), -1) <> -1";
            }
            $table->set_sql($fields, $from, $where, $params);

            if (!$table->is_downloading()) {
                // Regrade buttons
                if (has_capability('mod/quiz:regrade', $this->context)) {
                    $regradesneeded = $this->count_question_attempts_needing_regrade(
                            $quiz, $groupstudents);
                    if ($currentgroup) {
                        $a= new stdClass();
                        $a->groupname = groups_get_group_name($currentgroup);
                        $a->coursestudents = get_string('participants');
                        $a->countregradeneeded = $regradesneeded;
                        $regradealldrydolabel =
                                get_string('regradealldrydogroup', 'quiz_overview', $a);
                        $regradealldrylabel =
                                get_string('regradealldrygroup', 'quiz_overview', $a);
                        $regradealllabel =
                                get_string('regradeallgroup', 'quiz_overview', $a);
                    } else {
                        $regradealldrydolabel =
                                get_string('regradealldrydo', 'quiz_overview', $regradesneeded);
                        $regradealldrylabel =
                                get_string('regradealldry', 'quiz_overview');
                        $regradealllabel =
                                get_string('regradeall', 'quiz_overview');
                    }
                    $displayurl = new moodle_url($reporturl,
                            $displayoptions + array('sesskey' => sesskey()));
                    echo '<div class="mdl-align">';
                    echo '<form action="'.$displayurl->out_omit_querystring().'">';
                    echo '<div>';
                    echo html_writer::input_hidden_params($displayurl);
                    echo '<input type="submit" name="regradeall" value="'.$regradealllabel.'"/>';
                    echo '<input type="submit" name="regradealldry" value="' .
                            $regradealldrylabel . '"/>';
                    if ($regradesneeded) {
                        echo '<input type="submit" name="regradealldrydo" value="' .
                                $regradealldrydolabel . '"/>';
                    }
                    echo '</div>';
                    echo '</form>';
                    echo '</div>';
                }
                // Print information on the grading method
                if ($strattempthighlight = quiz_report_highlighting_grading_method(
                        $quiz, $qmsubselect, $qmfilter)) {
                    echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
                }
            }

            // Define table columns
            $columns = array();
            $headers = array();

            if (!$table->is_downloading() && $candelete) {
                $columns[] = 'checkbox';
                $headers[] = null;
            }

            $this->add_user_columns($table, $columns, $headers);

            $this->add_time_columns($columns, $headers);

            if ($detailedmarks) {
                foreach ($questions as $slot => $question) {
                    // Ignore questions of zero length
                    $columns[] = 'qsgrade' . $slot;
                    $header = get_string('qbrief', 'quiz', $question->number);
                    if (!$table->is_downloading()) {
                        $header .= '<br />';
                    } else {
                        $header .= ' ';
                    }
                    $header .= '/' . quiz_rescale_grade($question->maxmark, $quiz, 'question');
                    $headers[] = $header;
                }
            }

            if (!$table->is_downloading() && has_capability('mod/quiz:regrade', $this->context) &&
                    $this->has_regraded_questions($from, $where, $params)) {
                $columns[] = 'regraded';
                $headers[] = get_string('regrade', 'quiz_overview');
            }

            $this->add_grade_columns($quiz, $columns, $headers);

            $this->set_up_table_columns(
                    $table, $columns, $headers, $reporturl, $displayoptions, false);
            $table->set_attribute('class', 'generaltable generalbox grades');

            $table->out($pagesize, true);
        }

        if (!$table->is_downloading() && $this->should_show_grades($quiz)) {
            if ($currentgroup && $groupstudents) {
                list($usql, $params) = $DB->get_in_or_equal($groupstudents);
                $params[] = $quiz->id;
                if ($DB->record_exists_select('quiz_grades', "userid $usql AND quiz = ?",
                        $params)) {
                     $imageurl = new moodle_url('/mod/quiz/report/overview/overviewgraph.php',
                            array('id' => $quiz->id, 'groupid' => $currentgroup));
                     $graphname = get_string('overviewreportgraphgroup', 'quiz_overview',
                            groups_get_group_name($currentgroup));
                     echo $OUTPUT->heading($graphname);
                     echo html_writer::tag('div', html_writer::empty_tag('img',
                            array('src' => $imageurl, 'alt' => $graphname)),
                            array('class' => 'graph'));
                }
            }

            if ($DB->record_exists('quiz_grades', array('quiz'=> $quiz->id))) {
                 $graphname = get_string('overviewreportgraph', 'quiz_overview');
                 $imageurl = new moodle_url('/mod/quiz/report/overview/overviewgraph.php',
                        array('id' => $quiz->id));
                 echo $OUTPUT->heading($graphname);
                 echo html_writer::tag('div', html_writer::empty_tag('img',
                        array('src' => $imageurl, 'alt' => $graphname)),
                        array('class' => 'graph'));
            }
        }
        return true;
    }

    /**
     * Regrade a particular quiz attempt. Either for real ($dryrun = false), or
     * as a pretend regrade to see which fractions would change. The outcome is
     * stored in the quiz_overview_regrades table.
     *
     * Note, $attempt is not upgraded in the database. The caller needs to do that.
     * However, $attempt->sumgrades is updated, if this is not a dry run.
     *
     * @param object $attempt the quiz attempt to regrade.
     * @param bool $dryrun if true, do a pretend regrade, otherwise do it for real.
     * @param array $slots if null, regrade all questions, otherwise, just regrade
     *      the quetsions with those slots.
     */
    protected function regrade_attempt($attempt, $dryrun = false, $slots = null) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $quba = question_engine::load_questions_usage_by_activity($attempt->uniqueid);

        if (is_null($slots)) {
            $slots = $quba->get_slots();
        }

        $finished = $attempt->timefinish > 0;
        foreach ($slots as $slot) {
            $qqr = new stdClass();
            $qqr->oldfraction = $quba->get_question_fraction($slot);

            $quba->regrade_question($slot, $finished);

            $qqr->newfraction = $quba->get_question_fraction($slot);

            if (abs($qqr->oldfraction - $qqr->newfraction) > 1e-7) {
                $qqr->questionusageid = $quba->get_id();
                $qqr->slot = $slot;
                $qqr->regraded = empty($dryrun);
                $qqr->timemodified = time();
                $DB->insert_record('quiz_overview_regrades', $qqr, false);
            }
        }

        if (!$dryrun) {
            question_engine::save_questions_usage_by_activity($quba);
        }

        $transaction->allow_commit();
    }

    /**
     * Regrade attempts for this quiz, exactly which attempts are regraded is
     * controlled by the parameters.
     * @param object $quiz the quiz settings.
     * @param bool $dryrun if true, do a pretend regrade, otherwise do it for real.
     * @param array $groupstudents blank for all attempts, otherwise regrade attempts
     * for these users.
     * @param array $attemptids blank for all attempts, otherwise only regrade
     * attempts whose id is in this list.
     */
    protected function regrade_attempts($quiz, $dryrun = false,
            $groupstudents = array(), $attemptids = array()) {
        global $DB;

        $where = "quiz = ? AND preview = 0";
        $params = array($quiz->id);

        if ($groupstudents) {
            list($usql, $uparams) = $DB->get_in_or_equal($groupstudents);
            $where .= " AND userid $usql";
            $params = array_merge($params, $uparams);
        }

        if ($attemptids) {
            list($asql, $aparams) = $DB->get_in_or_equal($attemptids);
            $where .= " AND id $asql";
            $params = array_merge($params, $aparams);
        }

        $attempts = $DB->get_records_select('quiz_attempts', $where, $params);
        if (!$attempts) {
            return;
        }

        $this->clear_regrade_table($quiz, $groupstudents);

        foreach ($attempts as $attempt) {
            set_time_limit(30);
            $this->regrade_attempt($attempt, $dryrun);
        }

        if (!$dryrun) {
            $this->update_overall_grades($quiz);
        }
    }

    /**
     * Regrade those questions in those attempts that are marked as needing regrading
     * in the quiz_overview_regrades table.
     * @param object $quiz the quiz settings.
     * @param array $groupstudents blank for all attempts, otherwise regrade attempts
     * for these users.
     */
    protected function regrade_attempts_needing_it($quiz, $groupstudents) {
        global $DB;

        $where = "quiza.quiz = ? AND quiza.preview = 0 AND qqr.regraded = 0";
        $params = array($quiz->id);

        // Fetch all attempts that need regrading
        if ($groupstudents) {
            list($usql, $uparams) = $DB->get_in_or_equal($groupstudents);
            $where .= " AND quiza.userid $usql";
            $params += $uparams;
        }

        $toregrade = $DB->get_records_sql("
                SELECT quiza.uniqueid, qqr.slot
                FROM {quiz_attempts} quiza
                JOIN {quiz_overview_regrades} qqr ON qqr.questionusageid = quiza.uniqueid
                WHERE $where", $params);

        if (!$toregrade) {
            return;
        }

        $attemptquestions = array();
        foreach ($toregrade as $row) {
            $attemptquestions[$row->uniqueid][] = $row->slot;
        }
        $attempts = $DB->get_records_list('quiz_attempts', 'uniqueid',
                array_keys($attemptquestions));

        $this->clear_regrade_table($quiz, $groupstudents);

        foreach ($attempts as $attempt) {
            set_time_limit(30);
            $this->regrade_attempt($attempt, false, $attemptquestions[$attempt->uniqueid]);
        }

        $this->update_overall_grades($quiz);
    }

    /**
     * Count the number of attempts in need of a regrade.
     * @param object $quiz the quiz settings.
     * @param array $groupstudents user ids. If this is given, only data relating
     * to these users is cleared.
     */
    protected function count_question_attempts_needing_regrade($quiz, $groupstudents) {
        global $DB;

        $usertest = '';
        $params = array();
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $usertest = "quiza.userid $usql AND ";
        }

        $params[] = $quiz->id;
        $sql = "SELECT COUNT(DISTINCT quiza.id)
                FROM {quiz_attempts} quiza
                JOIN {quiz_overview_regrades} qqr ON quiza.uniqueid = qqr.questionusageid
                WHERE
                    $usertest
                    quiza.quiz = ? AND
                    quiza.preview = 0 AND
                    qqr.regraded = 0";
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Are there any pending regrades in the table we are going to show?
     * @param string $from tables used by the main query.
     * @param string $where where clause used by the main query.
     * @param array $params required by the SQL.
     * @return bool whether there are pending regrades.
     */
    protected function has_regraded_questions($from, $where, $params) {
        global $DB;
        $qubaids = new qubaid_join($from, 'uniqueid', $where, $params);
        return $DB->record_exists_select('quiz_overview_regrades',
                'questionusageid ' . $qubaids->usage_id_in(),
                $qubaids->usage_id_in_params());
    }

    /**
     * Remove all information about pending/complete regrades from the database.
     * @param object $quiz the quiz settings.
     * @param array $groupstudents user ids. If this is given, only data relating
     * to these users is cleared.
     */
    protected function clear_regrade_table($quiz, $groupstudents) {
        global $DB;

        // Fetch all attempts that need regrading
        $where = '';
        $params = array();
        if ($groupstudents) {
            list($usql, $params) = $DB->get_in_or_equal($groupstudents);
            $where = "userid $usql AND ";
        }

        $params[] = $quiz->id;
        $DB->delete_records_select('quiz_overview_regrades',
                "questionusageid IN (
                    SELECT uniqueid
                    FROM {quiz_attempts}
                    WHERE $where quiz = ?
                )", $params);
    }

    /**
     * Update the final grades for all attempts. This method is used following
     * a regrade.
     * @param object $quiz the quiz settings.
     * @param array $userids only update scores for these userids.
     * @param array $attemptids attemptids only update scores for these attempt ids.
     */
    protected function update_overall_grades($quiz) {
        quiz_update_all_attempt_sumgrades($quiz);
        quiz_update_all_final_grades($quiz);
        quiz_update_grades($quiz);
    }
}
