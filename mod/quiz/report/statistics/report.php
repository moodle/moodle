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
 * Quiz statistics report class.
 *
 * @package   quiz_statistics
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/statistics/statistics_form.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statistics_table.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statistics_question_table.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/statisticslib.php');
/**
 * The quiz statistics report provides summary information about each question in
 * a quiz, compared to the whole quiz. It also provides a drill-down to more
 * detailed information about each question.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_report extends quiz_default_report {

    /**
     * @var context_module
     */
    protected $context;

    /** @var quiz_statistics_table instance of table class used for main questions stats table. */
    protected $table;

    /**
     * Display the report.
     */
    public function display($quiz, $cm, $course) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $this->context = context_module::instance($cm->id);

        if (!quiz_questions_in_quiz($quiz->questions)) {
            $this->print_header_and_tabs($cm, $course, $quiz, 'statistics');
            echo quiz_no_questions_message($quiz, $cm, $this->context);
            return true;
        }

        // Work out the display options.
        $download = optional_param('download', '', PARAM_ALPHA);
        $everything = optional_param('everything', 0, PARAM_BOOL);
        $recalculate = optional_param('recalculate', 0, PARAM_BOOL);
        // A qid paramter indicates we should display the detailed analysis of a sub question.
        $qid = optional_param('qid', 0, PARAM_INT);
        $slot = optional_param('slot', 0, PARAM_INT);
        $whichattempts = optional_param('whichattempts', $quiz->grademethod, PARAM_INT);

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'statistics';

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);

        $mform = new quiz_statistics_settings_form($reporturl);

        $mform->set_data(array('whichattempts' => $whichattempts));

        if ($fromform = $mform->get_data()) {
            $whichattempts = $fromform->whichattempts;
        }

        if ($whichattempts != $quiz->grademethod) {
            $reporturl->param('whichattempts', $whichattempts);
        }

        // Find out current groups mode.
        $currentgroup = $this->get_current_group($cm, $course, $this->context);
        $nostudentsingroup = false; // True if a group is selected and there is no one in it.
        if (empty($currentgroup)) {
            $currentgroup = 0;
            $groupstudents = array();

        } else if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            $groupstudents = array();
            $nostudentsingroup = true;

        } else {
            // All users who can attempt quizzes and who are in the currently selected group.
            $groupstudents = get_users_by_capability($this->context,
                    array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),
                    '', '', '', '', $currentgroup, '', false);
            if (!$groupstudents) {
                $nostudentsingroup = true;
            }
        }

        $qubaids = quiz_statistics_qubaids_condition($quiz->id, $groupstudents, $whichattempts);

        // If recalculate was requested, handle that.
        if ($recalculate && confirm_sesskey()) {
            $this->clear_cached_data($qubaids);
            redirect($reporturl);
        }

        // Set up the main table.
        $this->table = new quiz_statistics_table();
        if ($everything) {
            $report = get_string('completestatsfilename', 'quiz_statistics');
        } else {
            $report = get_string('questionstatsfilename', 'quiz_statistics');
        }
        $courseshortname = format_string($course->shortname, true,
                array('context' => context_course::instance($course->id)));
        $filename = quiz_report_download_filename($report, $courseshortname, $quiz->name);
        $this->table->is_downloading($download, $filename,
                get_string('quizstructureanalysis', 'quiz_statistics'));
        $questions = $this->load_and_initialise_questions_for_calculations($quiz);

        if (!$nostudentsingroup) {
            // Get the data to be displayed.
            list($quizstats, $questionstats, $subquestionstats) =
                $this->get_quiz_and_questions_stats($quiz, $whichattempts, $groupstudents, $questions);
        } else {
            // Or create empty stats containers.
            $quizstats = new quiz_statistics_calculated($whichattempts);
            $questionstats = array();
            $subquestionstats = array();
        }

        // Set up the table, if there is data.
        if ($quizstats->s()) {
            $this->table->statistics_setup($quiz, $cm->id, $reporturl, $quizstats->s());
        }

        // Print the page header stuff (if not downloading.
        if (!$this->table->is_downloading()) {
            $this->print_header_and_tabs($cm, $course, $quiz, 'statistics');

            if (groups_get_activity_groupmode($cm)) {
                groups_print_activity_menu($cm, $reporturl->out());
                if ($currentgroup && !$groupstudents) {
                    $OUTPUT->notification(get_string('nostudentsingroup', 'quiz_statistics'));
                }
            }

            if (!$this->table->is_downloading() && $quizstats->s() == 0) {
                echo $OUTPUT->notification(get_string('noattempts', 'quiz'));
            }

            // Print display options form.
            $mform->display();
        }

        if ($everything) { // Implies is downloading.
            // Overall report, then the analysis of each question.
            $quizinfo = $quizstats->get_formatted_quiz_info_data($course, $cm, $quiz);
            $this->download_quiz_info_table($quizinfo);

            if ($quizstats->s()) {
                $this->output_quiz_structure_analysis_table($quizstats->s(), $questionstats, $subquestionstats);

                if ($this->table->is_downloading() == 'xhtml' && $quizstats->s() != 0) {
                    $this->output_statistics_graph($quiz->id, $currentgroup, $whichattempts);
                }

                foreach ($questions as $slot => $question) {
                    if (question_bank::get_qtype(
                            $question->qtype, false)->can_analyse_responses()) {
                        $this->output_individual_question_response_analysis(
                                $question, $questionstats[$slot]->s, $reporturl, $qubaids);

                    } else if (!empty($questionstats[$slot]->subquestions)) {
                        $subitemstodisplay = explode(',', $questionstats[$slot]->subquestions);
                        foreach ($subitemstodisplay as $subitemid) {
                            $this->output_individual_question_response_analysis(
                                $subquestionstats[$subitemid]->question, $subquestionstats[$subitemid]->s, $reporturl, $qubaids);
                        }
                    }
                }
            }

            $this->table->export_class_instance()->finish_document();

        } else if ($slot) {
            // Report on an individual question indexed by position.
            if (!isset($questions[$slot])) {
                print_error('questiondoesnotexist', 'question');
            }

            $this->output_individual_question_data($quiz, $questionstats[$slot]);
            $this->output_individual_question_response_analysis($questions[$slot], $questionstats[$slot]->s, $reporturl, $qubaids);

            // Back to overview link.
            echo $OUTPUT->box('<a href="' . $reporturl->out() . '">' .
                    get_string('backtoquizreport', 'quiz_statistics') . '</a>',
                    'backtomainstats boxaligncenter generalbox boxwidthnormal mdl-align');

        } else if ($qid) {
            // Report on an individual sub-question indexed questionid.
            if (!isset($subquestionstats[$qid])) {
                print_error('questiondoesnotexist', 'question');
            }

            $this->output_individual_question_data($quiz, $subquestionstats[$qid]);
            $this->output_individual_question_response_analysis($subquestionstats[$qid]->question,
                                                                $subquestionstats[$qid]->s, $reporturl, $qubaids);

            // Back to overview link.
            echo $OUTPUT->box('<a href="' . $reporturl->out() . '">' .
                    get_string('backtoquizreport', 'quiz_statistics') . '</a>',
                    'boxaligncenter generalbox boxwidthnormal mdl-align');

        } else if ($this->table->is_downloading()) {
            // Downloading overview report.
            $quizinfo = $quizstats->get_formatted_quiz_info_data($course, $cm, $quiz);
            $this->download_quiz_info_table($quizinfo);
            $this->output_quiz_structure_analysis_table($quizstats->s(), $questionstats, $subquestionstats);
            $this->table->finish_output();

        } else {
            // On-screen display of overview report.
            echo $OUTPUT->heading(get_string('quizinformation', 'quiz_statistics'), 3);
            echo $this->output_caching_info($quizstats, $quiz->id, $groupstudents, $whichattempts, $reporturl);
            echo $this->everything_download_options();
            $quizinfo = $quizstats->get_formatted_quiz_info_data($course, $cm, $quiz);
            echo $this->output_quiz_info_table($quizinfo);
            if ($quizstats->s()) {
                echo $OUTPUT->heading(get_string('quizstructureanalysis', 'quiz_statistics'), 3);
                $this->output_quiz_structure_analysis_table($quizstats->s(), $questionstats, $subquestionstats);
                $this->output_statistics_graph($quiz->id, $currentgroup, $whichattempts);
            }
        }

        return true;
    }

    /**
     * Display the statistical and introductory information about a question.
     * Only called when not downloading.
     * @param object                                         $quiz         the quiz settings.
     * @param \core_question\statistics\questions\calculated $questionstat the question to report on.
     */
    protected function output_individual_question_data($quiz, $questionstat) {
        global $OUTPUT;

        // On-screen display. Show a summary of the question's place in the quiz,
        // and the question statistics.
        $datumfromtable = $this->table->format_row($questionstat);

        // Set up the question info table.
        $questioninfotable = new html_table();
        $questioninfotable->align = array('center', 'center');
        $questioninfotable->width = '60%';
        $questioninfotable->attributes['class'] = 'generaltable titlesleft';

        $questioninfotable->data = array();
        $questioninfotable->data[] = array(get_string('modulename', 'quiz'), $quiz->name);
        $questioninfotable->data[] = array(get_string('questionname', 'quiz_statistics'),
                $questionstat->question->name.'&nbsp;'.$datumfromtable['actions']);
        $questioninfotable->data[] = array(get_string('questiontype', 'quiz_statistics'),
                $datumfromtable['icon'] . '&nbsp;' .
                question_bank::get_qtype($questionstat->question->qtype, false)->menu_name() . '&nbsp;' .
                $datumfromtable['icon']);
        $questioninfotable->data[] = array(get_string('positions', 'quiz_statistics'),
                $questionstat->positions);

        // Set up the question statistics table.
        $questionstatstable = new html_table();
        $questionstatstable->align = array('center', 'center');
        $questionstatstable->width = '60%';
        $questionstatstable->attributes['class'] = 'generaltable titlesleft';

        unset($datumfromtable['number']);
        unset($datumfromtable['icon']);
        $actions = $datumfromtable['actions'];
        unset($datumfromtable['actions']);
        unset($datumfromtable['name']);
        $labels = array(
            's' => get_string('attempts', 'quiz_statistics'),
            'facility' => get_string('facility', 'quiz_statistics'),
            'sd' => get_string('standarddeviationq', 'quiz_statistics'),
            'random_guess_score' => get_string('random_guess_score', 'quiz_statistics'),
            'intended_weight' => get_string('intended_weight', 'quiz_statistics'),
            'effective_weight' => get_string('effective_weight', 'quiz_statistics'),
            'discrimination_index' => get_string('discrimination_index', 'quiz_statistics'),
            'discriminative_efficiency' =>
                                get_string('discriminative_efficiency', 'quiz_statistics')
        );
        foreach ($datumfromtable as $item => $value) {
            $questionstatstable->data[] = array($labels[$item], $value);
        }

        // Display the various bits.
        echo $OUTPUT->heading(get_string('questioninformation', 'quiz_statistics'), 3);
        echo html_writer::table($questioninfotable);
        echo $this->render_question_text($questionstat->question);
        echo $OUTPUT->heading(get_string('questionstatistics', 'quiz_statistics'), 3);
        echo html_writer::table($questionstatstable);
    }

    /**
     * @param object $question question data.
     * @return string HTML of question text, ready for display.
     */
    protected function render_question_text($question) {
        global $OUTPUT;

        $text = question_rewrite_question_preview_urls($question->questiontext, $question->id,
                $question->contextid, 'question', 'questiontext', $question->id,
                $this->context->id, 'quiz_statistics');

        return $OUTPUT->box(format_text($text, $question->questiontextformat,
                array('noclean' => true, 'para' => false, 'overflowdiv' => true)),
                'questiontext boxaligncenter generalbox boxwidthnormal mdl-align');
    }

    /**
     * Display the response analysis for a question.
     * @param object           $question  the question to report on.
     * @param int              $s
     * @param moodle_url       $reporturl the URL to redisplay this report.
     * @param qubaid_condition $qubaids
     */
    protected function output_individual_question_response_analysis($question, $s, $reporturl, $qubaids) {
        global $OUTPUT;

        if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
            return;
        }

        $qtable = new quiz_statistics_question_table($question->id);
        $exportclass = $this->table->export_class_instance();
        $qtable->export_class_instance($exportclass);
        if (!$this->table->is_downloading()) {
            // Output an appropriate title.
            echo $OUTPUT->heading(get_string('analysisofresponses', 'quiz_statistics'), 3);

        } else {
            // Work out an appropriate title.
            $questiontabletitle = '"' . $question->name . '"';
            if (!empty($question->number)) {
                $questiontabletitle = '(' . $question->number . ') ' . $questiontabletitle;
            }
            if ($this->table->is_downloading() == 'xhtml') {
                $questiontabletitle = get_string('analysisofresponsesfor', 'quiz_statistics', $questiontabletitle);
            }

            // Set up the table.
            $exportclass->start_table($questiontabletitle);

            if ($this->table->is_downloading() == 'xhtml') {
                echo $this->render_question_text($question);
            }
        }

        $responesanalyser = new \core_question\statistics\responses\analyser($question);
        $responseanalysis = $responesanalyser->load_cached($qubaids);

        $qtable->question_setup($reporturl, $question, $s, $responseanalysis);
        if ($this->table->is_downloading()) {
            $exportclass->output_headers($qtable->headers);
        }
        foreach ($responseanalysis->get_subpart_ids() as $partid) {
            $subpart = $responseanalysis->get_subpart($partid);
            foreach ($subpart->get_response_class_ids() as $responseclassid) {
                $responseclass = $subpart->get_response_class($responseclassid);
                $tabledata = $responseclass->data_for_question_response_table($subpart->has_multiple_response_classes(), $partid);
                foreach ($tabledata as $row) {
                    $qtable->add_data_keyed($qtable->format_row($row));
                }
            }
        }

        $qtable->finish_output(!$this->table->is_downloading());
    }

    /**
     * Output the table that lists all the questions in the quiz with their statistics.
     * @param int $s number of attempts.
     * @param \core_question\statistics\questions\calculated[] $questionstats the stats for the main questions in the quiz.
     * @param \core_question\statistics\questions\calculated_for_subquestion[] $subquestionstats the stats of any random questions.
     */
    protected function output_quiz_structure_analysis_table($s, $questionstats, $subquestionstats) {
        if (!$s) {
            return;
        }

        foreach ($questionstats as $questionstat) {
            // Output the data for these question statistics.
            $this->table->add_data_keyed($this->table->format_row($questionstat));

            if (empty($questionstat->subquestions)) {
                continue;
            }

            // And its subquestions, if it has any.
            $subitemstodisplay = explode(',', $questionstat->subquestions);
            foreach ($subitemstodisplay as $subitemid) {
                $subquestionstats[$subitemid]->maxmark = $questionstat->maxmark;
                $this->table->add_data_keyed($this->table->format_row($subquestionstats[$subitemid]));
            }
        }

        $this->table->finish_output(!$this->table->is_downloading());
    }

    /**
     * Output the table of overall quiz statistics.
     * @param array $quizinfo as returned by {@link get_formatted_quiz_info_data()}.
     * @return string the HTML.
     */
    protected function output_quiz_info_table($quizinfo) {

        $quizinfotable = new html_table();
        $quizinfotable->align = array('center', 'center');
        $quizinfotable->width = '60%';
        $quizinfotable->attributes['class'] = 'generaltable titlesleft';
        $quizinfotable->data = array();

        foreach ($quizinfo as $heading => $value) {
             $quizinfotable->data[] = array($heading, $value);
        }

        return html_writer::table($quizinfotable);
    }

    /**
     * Download the table of overall quiz statistics.
     * @param array $quizinfo as returned by {@link get_formatted_quiz_info_data()}.
     */
    protected function download_quiz_info_table($quizinfo) {
        global $OUTPUT;

        // XHTML download is a special case.
        if ($this->table->is_downloading() == 'xhtml') {
            echo $OUTPUT->heading(get_string('quizinformation', 'quiz_statistics'), 3);
            echo $this->output_quiz_info_table($quizinfo);
            return;
        }

        // Reformat the data ready for output.
        $headers = array();
        $row = array();
        foreach ($quizinfo as $heading => $value) {
            $headers[] = $heading;
            $row[] = $value;
        }

        // Do the output.
        $exportclass = $this->table->export_class_instance();
        $exportclass->start_table(get_string('quizinformation', 'quiz_statistics'));
        $exportclass->output_headers($headers);
        $exportclass->add_data($row);
        $exportclass->finish_table();
    }

    /**
     * Output the HTML needed to show the statistics graph.
     * @param $quizid
     * @param $currentgroup
     * @param $whichattempts
     */
    protected function output_statistics_graph($quizid, $currentgroup, $whichattempts) {
        global $PAGE;

        $output = $PAGE->get_renderer('mod_quiz');
        $imageurl = new moodle_url('/mod/quiz/report/statistics/statistics_graph.php',
                                    compact('quizid', 'currentgroup', 'whichattempts'));
        $graphname = get_string('statisticsreportgraph', 'quiz_statistics');
        echo $output->graph($imageurl, $graphname);
    }

    /**
     * Get the quiz and question statistics, either by loading the cached results,
     * or by recomputing them.
     *
     * @param object $quiz the quiz settings.
     * @param string $whichattempts which attempts to use, represented internally as one of the constants as used in
     *                                   $quiz->grademethod ie.
     *                                   QUIZ_GRADEAVERAGE, QUIZ_GRADEHIGHEST, QUIZ_ATTEMPTLAST or QUIZ_ATTEMPTFIRST
     *                                   we calculate stats based on which attempts would affect the grade for each student.
     * @param array $groupstudents students in this group.
     * @param array $questions full question data.
     * @return array with 4 elements:
     *     - $quizstats The statistics for overall attempt scores.
     *     - $questionstats array of \core_question\statistics\questions\calculated objects keyed by slot.
     *     - $subquestionstats array of \core_question\statistics\questions\calculated_for_subquestion objects keyed by question id.
     */
    public function get_quiz_and_questions_stats($quiz, $whichattempts, $groupstudents, $questions) {

        $qubaids = quiz_statistics_qubaids_condition($quiz->id, $groupstudents, $whichattempts);

        $qcalc = new \core_question\statistics\questions\calculator($questions);

        $quizcalc = new quiz_statistics_calculator();

        if ($quizcalc->get_last_calculated_time($qubaids) === false) {
            // Recalculate now.
            list($questionstats, $subquestionstats) = $qcalc->calculate($qubaids);

            $quizstats = $quizcalc->calculate($quiz->id, $whichattempts, $groupstudents, count($questions),
                                              $qcalc->get_sum_of_mark_variance());

            if ($quizstats->s()) {
                $this->analyse_responses_for_all_questions_and_subquestions($qubaids, $questions, $subquestionstats);
            }
        } else {
            $quizstats = $quizcalc->get_cached($qubaids);
            list($questionstats, $subquestionstats) = $qcalc->get_cached($qubaids);
        }

        return array($quizstats, $questionstats, $subquestionstats);
    }

    protected function analyse_responses_for_all_questions_and_subquestions($qubaids, $questions, $subquestionstats) {

        $done = array();
        foreach ($questions as $question) {
            if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
                continue;
            }
            $done[$question->id] = 1;

            $responesstats = new \core_question\statistics\responses\analyser($question);
            $responesstats->calculate($qubaids);
        }

        foreach ($subquestionstats as $subquestionstat) {
            if (!question_bank::get_qtype($subquestionstat->question->qtype, false)->can_analyse_responses() ||
                    isset($done[$subquestionstat->question->id])) {
                continue;
            }
            $done[$subquestionstat->question->id] = 1;

            $responesstats = new \core_question\statistics\responses\analyser($subquestionstat->question);
            $responesstats->calculate($qubaids);
        }
    }

    /**
     * @return string HTML snipped for the Download full report as UI.
     */
    protected function everything_download_options() {
        $downloadoptions = $this->table->get_download_menu();

        $downloadelements = new stdClass();
        $downloadelements->formatsmenu = html_writer::select($downloadoptions, 'download',
                $this->table->defaultdownloadformat, false);
        $downloadelements->downloadbutton = '<input type="submit" value="' .
                get_string('download') . '"/>';

        $output = '<form action="'. $this->table->baseurl .'" method="post">';
        $output .= '<div class="mdl-align">';
        $output .= '<input type="hidden" name="everything" value="1"/>';
        $output .= html_writer::tag('label', get_string('downloadeverything', 'quiz_statistics', $downloadelements));
        $output .= '</div></form>';

        return $output;
    }

    /**
     * Generate the snipped of HTML that says when the stats were last caculated,
     * with a recalcuate now button.
     * @param object $quizstats      the overall quiz statistics.
     * @param int    $quizid         the quiz id.
     * @param array  $groupstudents  ids of students in the group or empty array if groups not used.
     * @param string $whichattempts which attempts to use, represented internally as one of the constants as used in
     *                                   $quiz->grademethod ie.
     *                                   QUIZ_GRADEAVERAGE, QUIZ_GRADEHIGHEST, QUIZ_ATTEMPTLAST or QUIZ_ATTEMPTFIRST
     *                                   we calculate stats based on which attempts would affect the grade for each student.
     * @param moodle_url $reporturl url for this report
     * @return string a HTML snipped saying when the stats were last computed,
     *      or blank if that is not appropriate.
     */
    protected function output_caching_info($quizstats, $quizid, $groupstudents, $whichattempts, $reporturl) {
        global $DB, $OUTPUT;

        if (empty($quizstats->timemodified)) {
            return '';
        }

        // Find the number of attempts since the cached statistics were computed.
        list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql($quizid, $groupstudents, $whichattempts, true);
        $count = $DB->count_records_sql("
                SELECT COUNT(1)
                FROM $fromqa
                WHERE $whereqa
                AND quiza.timefinish > {$quizstats->timemodified}", $qaparams);

        if (!$count) {
            $count = 0;
        }

        // Generate the output.
        $a = new stdClass();
        $a->lastcalculated = format_time(time() - $quizstats->timemodified);
        $a->count = $count;

        $recalcualteurl = new moodle_url($reporturl,
                array('recalculate' => 1, 'sesskey' => sesskey()));
        $output = '';
        $output .= $OUTPUT->box_start(
                'boxaligncenter generalbox boxwidthnormal mdl-align', 'cachingnotice');
        $output .= get_string('lastcalculated', 'quiz_statistics', $a);
        $output .= $OUTPUT->single_button($recalcualteurl,
                get_string('recalculatenow', 'quiz_statistics'));
        $output .= $OUTPUT->box_end(true);

        return $output;
    }

    /**
     * Clear the cached data for a particular report configuration. This will
     * trigger a re-computation the next time the report is displayed.
     * @param $qubaids qubaid_condition
     */
    protected function clear_cached_data($qubaids) {
        global $DB;
        $DB->delete_records('quiz_statistics', array('hashcode' => $qubaids->get_hash_code()));
        $DB->delete_records('question_statistics', array('hashcode' => $qubaids->get_hash_code()));
        $DB->delete_records('question_response_analysis', array('hashcode' => $qubaids->get_hash_code()));
    }

    /**
     * @param object $quiz the quiz.
     * @return array of questions for this quiz.
     */
    public function load_and_initialise_questions_for_calculations($quiz) {
        // Load the questions.
        $questions = quiz_report_get_significant_questions($quiz);
        $questionids = array();
        foreach ($questions as $question) {
            $questionids[] = $question->id;
        }
        $fullquestions = question_load_questions($questionids);
        foreach ($questions as $qno => $question) {
            $q = $fullquestions[$question->id];
            $q->maxmark = $question->maxmark;
            $q->slot = $qno;
            $q->number = $question->number;
            $questions[$qno] = $q;
        }
        return $questions;
    }
}

