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
require_once($CFG->dirroot . '/mod/quiz/report/statistics/qstats.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/responseanalysis.php');


/**
 * The quiz statistics report provides summary information about each question in
 * a quiz, compared to the whole quiz. It also provides a drill-down to more
 * detailed information about each question.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_report extends quiz_default_report {
    /** @var integer Time after which statistics are automatically recomputed. */
    const TIME_TO_CACHE_STATS = 900; // 15 minutes.

    /** @var object instance of table class used for main questions stats table. */
    protected $table;

    /**
     * Display the report.
     */
    public function display($quiz, $cm, $course) {
        global $CFG, $DB, $OUTPUT, $PAGE;

        $this->context = context_module::instance($cm->id);

        // Work out the display options.
        $download = optional_param('download', '', PARAM_ALPHA);
        $everything = optional_param('everything', 0, PARAM_BOOL);
        $recalculate = optional_param('recalculate', 0, PARAM_BOOL);
        // A qid paramter indicates we should display the detailed analysis of a question.
        $qid = optional_param('qid', 0, PARAM_INT);
        $slot = optional_param('slot', 0, PARAM_INT);

        $pageoptions = array();
        $pageoptions['id'] = $cm->id;
        $pageoptions['mode'] = 'statistics';

        $reporturl = new moodle_url('/mod/quiz/report.php', $pageoptions);

        $mform = new quiz_statistics_settings_form($reporturl);
        if ($fromform = $mform->get_data()) {
            $useallattempts = $fromform->useallattempts;
            if ($fromform->useallattempts) {
                set_user_preference('quiz_report_statistics_useallattempts',
                        $fromform->useallattempts);
            } else {
                unset_user_preference('quiz_report_statistics_useallattempts');
            }

        } else {
            $useallattempts = get_user_preferences('quiz_report_statistics_useallattempts', 0);
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

        // If recalculate was requested, handle that.
        if ($recalculate && confirm_sesskey()) {
            $this->clear_cached_data($quiz->id, $currentgroup, $useallattempts);
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

        // Get the data to be displayed.
        list($quizstats, $questions, $subquestions, $s) =
                $this->get_quiz_and_questions_stats($quiz, $currentgroup,
                        $nostudentsingroup, $useallattempts, $groupstudents, $questions);
        $quizinfo = $this->get_formatted_quiz_info_data($course, $cm, $quiz, $quizstats);

        // Set up the table, if there is data.
        if ($s) {
            $this->table->statistics_setup($quiz, $cm->id, $reporturl, $s);
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

            if (!quiz_questions_in_quiz($quiz->questions)) {
                echo quiz_no_questions_message($quiz, $cm, $this->context);
            } else if (!$this->table->is_downloading() && $s == 0) {
                echo $OUTPUT->notification(get_string('noattempts', 'quiz'));
            }

            // Print display options form.
            $mform->set_data(array('useallattempts' => $useallattempts));
            $mform->display();
        }

        if ($everything) { // Implies is downloading.
            // Overall report, then the analysis of each question.
            $this->download_quiz_info_table($quizinfo);

            if ($s) {
                $this->output_quiz_structure_analysis_table($s, $questions, $subquestions);

                if ($this->table->is_downloading() == 'xhtml') {
                    $this->output_statistics_graph($quizstats->id, $s);
                }

                foreach ($questions as $question) {
                    if (question_bank::get_qtype(
                            $question->qtype, false)->can_analyse_responses()) {
                        $this->output_individual_question_response_analysis(
                                $question, $reporturl, $quizstats);

                    } else if (!empty($question->_stats->subquestions)) {
                        $subitemstodisplay = explode(',', $question->_stats->subquestions);
                        foreach ($subitemstodisplay as $subitemid) {
                            $this->output_individual_question_response_analysis(
                                    $subquestions[$subitemid], $reporturl, $quizstats);
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

            $this->output_individual_question_data($quiz, $questions[$slot]);
            $this->output_individual_question_response_analysis(
                    $questions[$slot], $reporturl, $quizstats);

            // Back to overview link.
            echo $OUTPUT->box('<a href="' . $reporturl->out() . '">' .
                    get_string('backtoquizreport', 'quiz_statistics') . '</a>',
                    'backtomainstats boxaligncenter generalbox boxwidthnormal mdl-align');

        } else if ($qid) {
            // Report on an individual sub-question indexed questionid.
            if (!isset($subquestions[$qid])) {
                print_error('questiondoesnotexist', 'question');
            }

            $this->output_individual_question_data($quiz, $subquestions[$qid]);
            $this->output_individual_question_response_analysis(
                    $subquestions[$qid], $reporturl, $quizstats);

            // Back to overview link.
            echo $OUTPUT->box('<a href="' . $reporturl->out() . '">' .
                    get_string('backtoquizreport', 'quiz_statistics') . '</a>',
                    'boxaligncenter generalbox boxwidthnormal mdl-align');

        } else if ($this->table->is_downloading()) {
            // Downloading overview report.
            $this->download_quiz_info_table($quizinfo);
            $this->output_quiz_structure_analysis_table($s, $questions, $subquestions);
            $this->table->finish_output();

        } else {
            // On-screen display of overview report.
            echo $OUTPUT->heading(get_string('quizinformation', 'quiz_statistics'));
            echo $this->output_caching_info($quizstats, $quiz->id, $currentgroup,
                    $groupstudents, $useallattempts, $reporturl);
            echo $this->everything_download_options();
            echo $this->output_quiz_info_table($quizinfo);
            if ($s) {
                echo $OUTPUT->heading(get_string('quizstructureanalysis', 'quiz_statistics'));
                $this->output_quiz_structure_analysis_table($s, $questions, $subquestions);
                $this->output_statistics_graph($quizstats->id, $s);
            }
        }

        return true;
    }

    /**
     * Display the statistical and introductory information about a question.
     * Only called when not downloading.
     * @param object $quiz the quiz settings.
     * @param object $question the question to report on.
     * @param moodle_url $reporturl the URL to resisplay this report.
     * @param object $quizstats Holds the quiz statistics.
     */
    protected function output_individual_question_data($quiz, $question) {
        global $OUTPUT;

        // On-screen display. Show a summary of the question's place in the quiz,
        // and the question statistics.
        $datumfromtable = $this->table->format_row($question);

        // Set up the question info table.
        $questioninfotable = new html_table();
        $questioninfotable->align = array('center', 'center');
        $questioninfotable->width = '60%';
        $questioninfotable->attributes['class'] = 'generaltable titlesleft';

        $questioninfotable->data = array();
        $questioninfotable->data[] = array(get_string('modulename', 'quiz'), $quiz->name);
        $questioninfotable->data[] = array(get_string('questionname', 'quiz_statistics'),
                $question->name.'&nbsp;'.$datumfromtable['actions']);
        $questioninfotable->data[] = array(get_string('questiontype', 'quiz_statistics'),
                $datumfromtable['icon'] . '&nbsp;' .
                question_bank::get_qtype($question->qtype, false)->menu_name() . '&nbsp;' .
                $datumfromtable['icon']);
        $questioninfotable->data[] = array(get_string('positions', 'quiz_statistics'),
                $question->_stats->positions);

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
        echo $OUTPUT->heading(get_string('questioninformation', 'quiz_statistics'));
        echo html_writer::table($questioninfotable);
        echo $this->render_question_text($question);
        echo $OUTPUT->heading(get_string('questionstatistics', 'quiz_statistics'));
        echo html_writer::table($questionstatstable);
    }

    /**
     * @param object $question question data.
     * @return string HTML of question text, ready for display.
     */
    protected function render_question_text($question) {
        global $OUTPUT;

        $text = question_rewrite_questiontext_preview_urls($question->questiontext,
                $this->context->id, 'quiz_statistics', $question->id);

        return $OUTPUT->box(format_text($text, $question->questiontextformat,
                array('noclean' => true, 'para' => false, 'overflowdiv' => true)),
                'questiontext boxaligncenter generalbox boxwidthnormal mdl-align');
    }

    /**
     * Display the response analysis for a question.
     * @param object $question the question to report on.
     * @param moodle_url $reporturl the URL to resisplay this report.
     * @param object $quizstats Holds the quiz statistics.
     */
    protected function output_individual_question_response_analysis($question,
            $reporturl, $quizstats) {
        global $OUTPUT;

        if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
            return;
        }

        $qtable = new quiz_statistics_question_table($question->id);
        $exportclass = $this->table->export_class_instance();
        $qtable->export_class_instance($exportclass);
        if (!$this->table->is_downloading()) {
            // Output an appropriate title.
            echo $OUTPUT->heading(get_string('analysisofresponses', 'quiz_statistics'));

        } else {
            // Work out an appropriate title.
            $questiontabletitle = '"' . $question->name . '"';
            if (!empty($question->number)) {
                $questiontabletitle = '(' . $question->number . ') ' . $questiontabletitle;
            }
            if ($this->table->is_downloading() == 'xhtml') {
                $questiontabletitle = get_string('analysisofresponsesfor',
                        'quiz_statistics', $questiontabletitle);
            }

            // Set up the table.
            $exportclass->start_table($questiontabletitle);

            if ($this->table->is_downloading() == 'xhtml') {
                echo $this->render_question_text($question);
            }
        }

        $responesstats = new quiz_statistics_response_analyser($question);
        $responesstats->load_cached($quizstats->id);

        $qtable->question_setup($reporturl, $question, $responesstats);
        if ($this->table->is_downloading()) {
            $exportclass->output_headers($qtable->headers);
        }

        foreach ($responesstats->responseclasses as $partid => $partclasses) {
            $rowdata = new stdClass();
            $rowdata->part = $partid;
            foreach ($partclasses as $responseclassid => $responseclass) {
                $rowdata->responseclass = $responseclass->responseclass;

                $responsesdata = $responesstats->responses[$partid][$responseclassid];
                if (empty($responsesdata)) {
                    if (!array_key_exists('responseclass', $qtable->columns)) {
                        $rowdata->response = $responseclass->responseclass;
                    } else {
                        $rowdata->response = '';
                    }
                    $rowdata->fraction = $responseclass->fraction;
                    $rowdata->count = 0;
                    $qtable->add_data_keyed($qtable->format_row($rowdata));
                    continue;
                }

                foreach ($responsesdata as $response => $data) {
                    $rowdata->response = $response;
                    $rowdata->fraction = $data->fraction;
                    $rowdata->count = $data->count;
                    $qtable->add_data_keyed($qtable->format_row($rowdata));
                }
            }
        }

        $qtable->finish_output(!$this->table->is_downloading());
    }

    /**
     * Output the table that lists all the questions in the quiz with their statistics.
     * @param int $s number of attempts.
     * @param array $questions the questions in the quiz.
     * @param array $subquestions the subquestions of any random questions.
     */
    protected function output_quiz_structure_analysis_table($s, $questions, $subquestions) {
        if (!$s) {
            return;
        }

        foreach ($questions as $question) {
            // Output the data for this questions.
            $this->table->add_data_keyed($this->table->format_row($question));

            if (empty($question->_stats->subquestions)) {
                continue;
            }

            // And its subquestions, if it has any.
            $subitemstodisplay = explode(',', $question->_stats->subquestions);
            foreach ($subitemstodisplay as $subitemid) {
                $subquestions[$subitemid]->maxmark = $question->maxmark;
                $this->table->add_data_keyed($this->table->format_row($subquestions[$subitemid]));
            }
        }

        $this->table->finish_output(!$this->table->is_downloading());
    }

    protected function get_formatted_quiz_info_data($course, $cm, $quiz, $quizstats) {

        // You can edit this array to control which statistics are displayed.
        $todisplay = array('firstattemptscount' => 'number',
                    'allattemptscount' => 'number',
                    'firstattemptsavg' => 'summarks_as_percentage',
                    'allattemptsavg' => 'summarks_as_percentage',
                    'median' => 'summarks_as_percentage',
                    'standarddeviation' => 'summarks_as_percentage',
                    'skewness' => 'number_format',
                    'kurtosis' => 'number_format',
                    'cic' => 'number_format_percent',
                    'errorratio' => 'number_format_percent',
                    'standarderror' => 'summarks_as_percentage');

        // General information about the quiz.
        $quizinfo = array();
        $quizinfo[get_string('quizname', 'quiz_statistics')] = format_string($quiz->name);
        $quizinfo[get_string('coursename', 'quiz_statistics')] = format_string($course->fullname);
        if ($cm->idnumber) {
            $quizinfo[get_string('idnumbermod')] = $cm->idnumber;
        }
        if ($quiz->timeopen) {
            $quizinfo[get_string('quizopen', 'quiz')] = userdate($quiz->timeopen);
        }
        if ($quiz->timeclose) {
            $quizinfo[get_string('quizclose', 'quiz')] = userdate($quiz->timeclose);
        }
        if ($quiz->timeopen && $quiz->timeclose) {
            $quizinfo[get_string('duration', 'quiz_statistics')] =
                    format_time($quiz->timeclose - $quiz->timeopen);
        }

        // The statistics.
        foreach ($todisplay as $property => $format) {
            if (!isset($quizstats->$property) || !$format) {
                continue;
            }
            $value = $quizstats->$property;

            switch ($format) {
                case 'summarks_as_percentage':
                    $formattedvalue = quiz_report_scale_summarks_as_percentage($value, $quiz);
                    break;
                case 'number_format_percent':
                    $formattedvalue = quiz_format_grade($quiz, $value) . '%';
                    break;
                case 'number_format':
                    // 2 extra decimal places, since not a percentage,
                    // and we want the same number of sig figs.
                    $formattedvalue = format_float($value, $quiz->decimalpoints + 2);
                    break;
                case 'number':
                    $formattedvalue = $value + 0;
                    break;
                default:
                    $formattedvalue = $value;
            }

            $quizinfo[get_string($property, 'quiz_statistics',
                    $this->using_attempts_string(!empty($quizstats->allattempts)))] =
                    $formattedvalue;
        }

        return $quizinfo;
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
            echo $OUTPUT->heading(get_string('quizinformation', 'quiz_statistics'));
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
     * @param int $quizstatsid the id of the statistics to show in the graph.
     */
    protected function output_statistics_graph($quizstatsid, $s) {
        global $PAGE;

        if ($s == 0) {
            return;
        }

        $output = $PAGE->get_renderer('mod_quiz');
        $imageurl = new moodle_url('/mod/quiz/report/statistics/statistics_graph.php',
                array('id' => $quizstatsid));
        $graphname = get_string('statisticsreportgraph', 'quiz_statistics');
        echo $output->graph($imageurl, $graphname);
    }

    /**
     * Return the stats data for when there are no stats to show.
     *
     * @param array $questions question definitions.
     * @param int $firstattemptscount number of first attempts (optional).
     * @param int $firstattemptscount total number of attempts (optional).
     * @return array with three elements:
     *      - integer $s Number of attempts included in the stats (0).
     *      - array $quizstats The statistics for overall attempt scores.
     *      - array $qstats The statistics for each question.
     */
    protected function get_emtpy_stats($questions, $firstattemptscount = 0,
            $allattemptscount = 0) {
        $quizstats = new stdClass();
        $quizstats->firstattemptscount = $firstattemptscount;
        $quizstats->allattemptscount = $allattemptscount;

        $qstats = new stdClass();
        $qstats->questions = $questions;
        $qstats->subquestions = array();
        $qstats->responses = array();

        return array(0, $quizstats, false);
    }

    /**
     * Compute the quiz statistics.
     *
     * @param object $quizid the quiz id.
     * @param int $currentgroup the current group. 0 for none.
     * @param bool $nostudentsingroup true if there a no students.
     * @param bool $useallattempts use all attempts, or just first attempts.
     * @param array $groupstudents students in this group.
     * @param array $questions question definitions.
     * @return array with three elements:
     *      - integer $s Number of attempts included in the stats.
     *      - array $quizstats The statistics for overall attempt scores.
     *      - array $qstats The statistics for each question.
     */
    protected function compute_stats($quizid, $currentgroup, $nostudentsingroup,
            $useallattempts, $groupstudents, $questions) {
        global $DB;

        // Calculating MEAN of marks for all attempts by students
        // http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise
        //     #Calculating_MEAN_of_grades_for_all_attempts_by_students.
        if ($nostudentsingroup) {
            return $this->get_emtpy_stats($questions);
        }

        list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql(
                $quizid, $currentgroup, $groupstudents, true);

        $attempttotals = $DB->get_records_sql("
                SELECT
                    CASE WHEN attempt = 1 THEN 1 ELSE 0 END AS isfirst,
                    COUNT(1) AS countrecs,
                    SUM(sumgrades) AS total
                FROM $fromqa
                WHERE $whereqa
                GROUP BY CASE WHEN attempt = 1 THEN 1 ELSE 0 END", $qaparams);

        if (!$attempttotals) {
            return $this->get_emtpy_stats($questions);
        }

        if (isset($attempttotals[1])) {
            $firstattempts = $attempttotals[1];
            $firstattempts->average = $firstattempts->total / $firstattempts->countrecs;
        } else {
            $firstattempts = new stdClass();
            $firstattempts->countrecs = 0;
            $firstattempts->total = 0;
            $firstattempts->average = null;
        }

        $allattempts = new stdClass();
        if (isset($attempttotals[0])) {
            $allattempts->countrecs = $firstattempts->countrecs + $attempttotals[0]->countrecs;
            $allattempts->total = $firstattempts->total + $attempttotals[0]->total;
        } else {
            $allattempts->countrecs = $firstattempts->countrecs;
            $allattempts->total = $firstattempts->total;
        }

        if ($useallattempts) {
            $usingattempts = $allattempts;
            $usingattempts->sql = '';
        } else {
            $usingattempts = $firstattempts;
            $usingattempts->sql = 'AND quiza.attempt = 1 ';
        }

        $s = $usingattempts->countrecs;
        if ($s == 0) {
            return $this->get_emtpy_stats($questions, $firstattempts->countrecs,
                    $allattempts->countrecs);
        }
        $summarksavg = $usingattempts->total / $usingattempts->countrecs;

        $quizstats = new stdClass();
        $quizstats->allattempts = $useallattempts;
        $quizstats->firstattemptscount = $firstattempts->countrecs;
        $quizstats->allattemptscount = $allattempts->countrecs;
        $quizstats->firstattemptsavg = $firstattempts->average;
        $quizstats->allattemptsavg = $allattempts->total / $allattempts->countrecs;

        // Recalculate sql again this time possibly including test for first attempt.
        list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql(
                $quizid, $currentgroup, $groupstudents, $useallattempts);

        // Median ...
        if ($s % 2 == 0) {
            // An even number of attempts.
            $limitoffset = $s/2 - 1;
            $limit = 2;
        } else {
            $limitoffset = floor($s/2);
            $limit = 1;
        }
        $sql = "SELECT id, sumgrades
                FROM $fromqa
                WHERE $whereqa
                ORDER BY sumgrades";

        $medianmarks = $DB->get_records_sql_menu($sql, $qaparams, $limitoffset, $limit);

        $quizstats->median = array_sum($medianmarks) / count($medianmarks);
        if ($s > 1) {
            // Fetch the sum of squared, cubed and power 4d
            // differences between marks and mean mark.
            $mean = $usingattempts->total / $s;
            $sql = "SELECT
                    SUM(POWER((quiza.sumgrades - $mean), 2)) AS power2,
                    SUM(POWER((quiza.sumgrades - $mean), 3)) AS power3,
                    SUM(POWER((quiza.sumgrades - $mean), 4)) AS power4
                    FROM $fromqa
                    WHERE $whereqa";
            $params = array('mean1' => $mean, 'mean2' => $mean, 'mean3' => $mean)+$qaparams;

            $powers = $DB->get_record_sql($sql, $params, MUST_EXIST);

            // Standard_Deviation:
            // see http://docs.moodle.org/dev/Quiz_item_analysis_calculations_in_practise
            //         #Standard_Deviation.

            $quizstats->standarddeviation = sqrt($powers->power2 / ($s - 1));

            // Skewness.
            if ($s > 2) {
                // See http://docs.moodle.org/dev/
                //      Quiz_item_analysis_calculations_in_practise#Skewness_and_Kurtosis.
                $m2= $powers->power2 / $s;
                $m3= $powers->power3 / $s;
                $m4= $powers->power4 / $s;

                $k2= $s*$m2/($s-1);
                $k3= $s*$s*$m3/(($s-1)*($s-2));
                if ($k2) {
                    $quizstats->skewness = $k3 / (pow($k2, 3/2));
                }
            }

            // Kurtosis.
            if ($s > 3) {
                $k4= $s*$s*((($s+1)*$m4)-(3*($s-1)*$m2*$m2))/(($s-1)*($s-2)*($s-3));
                if ($k2) {
                    $quizstats->kurtosis = $k4 / ($k2*$k2);
                }
            }
        }

        $qstats = new quiz_statistics_question_stats($questions, $s, $summarksavg);
        $qstats->load_step_data($quizid, $currentgroup, $groupstudents, $useallattempts);
        $qstats->compute_statistics();

        if ($s > 1) {
            $p = count($qstats->questions); // Number of positions.
            if ($p > 1 && isset($k2)) {
                $quizstats->cic = (100 * $p / ($p -1)) *
                        (1 - ($qstats->get_sum_of_mark_variance()) / $k2);
                $quizstats->errorratio = 100 * sqrt(1 - ($quizstats->cic / 100));
                $quizstats->standarderror = $quizstats->errorratio *
                        $quizstats->standarddeviation / 100;
            }
        }

        return array($s, $quizstats, $qstats);
    }

    /**
     * Load the cached statistics from the database.
     *
     * @param object $quiz the quiz settings
     * @param int $currentgroup the current group. 0 for none.
     * @param bool $nostudentsingroup true if there a no students.
     * @param bool $useallattempts use all attempts, or just first attempts.
     * @param array $groupstudents students in this group.
     * @param array $questions question definitions.
     * @return array with 4 elements:
     *     - $quizstats The statistics for overall attempt scores.
     *     - $questions The questions, with an additional _stats field.
     *     - $subquestions The subquestions, if any, with an additional _stats field.
     *     - $s Number of attempts included in the stats.
     * If there is no cached data in the database, returns an array of four nulls.
     */
    protected function try_loading_cached_stats($quiz, $currentgroup,
            $nostudentsingroup, $useallattempts, $groupstudents, $questions) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE_STATS;
        $quizstats = $DB->get_record_select('quiz_statistics',
                'quizid = ? AND groupid = ? AND allattempts = ? AND timemodified > ?',
                array($quiz->id, $currentgroup, $useallattempts, $timemodified));

        if (!$quizstats) {
            // No cached data found.
            return array(null, $questions, null, null);
        }

        if ($useallattempts) {
            $s = $quizstats->allattemptscount;
        } else {
            $s = $quizstats->firstattemptscount;
        }

        $subquestions = array();
        $questionstats = $DB->get_records('quiz_question_statistics',
                array('quizstatisticsid' => $quizstats->id));

        $subquestionstats = array();
        foreach ($questionstats as $stat) {
            if ($stat->slot) {
                $questions[$stat->slot]->_stats = $stat;
            } else {
                $subquestionstats[$stat->questionid] = $stat;
            }
        }

        if (!empty($subquestionstats)) {
            $subqstofetch = array_keys($subquestionstats);
            $subquestions = question_load_questions($subqstofetch);
            foreach ($subquestions as $subqid => $subq) {
                $subquestions[$subqid]->_stats = $subquestionstats[$subqid];
                $subquestions[$subqid]->maxmark = $subq->defaultmark;
            }
        }

        return array($quizstats, $questions, $subquestions, $s);
    }

    /**
     * Store the statistics in the cache tables in the database.
     *
     * @param object $quizid the quiz id.
     * @param int $currentgroup the current group. 0 for none.
     * @param bool $useallattempts use all attempts, or just first attempts.
     * @param object $quizstats The statistics for overall attempt scores.
     * @param array $questions The questions, with an additional _stats field.
     * @param array $subquestions The subquestions, if any, with an additional _stats field.
     */
    protected function cache_stats($quizid, $currentgroup,
            $quizstats, $questions, $subquestions) {
        global $DB;

        $toinsert = clone($quizstats);
        $toinsert->quizid = $quizid;
        $toinsert->groupid = $currentgroup;
        $toinsert->timemodified = time();

        // Fix up some dodgy data.
        if (isset($toinsert->errorratio) && is_nan($toinsert->errorratio)) {
            $toinsert->errorratio = null;
        }
        if (isset($toinsert->standarderror) && is_nan($toinsert->standarderror)) {
            $toinsert->standarderror = null;
        }

        // Store the data.
        $quizstats->id = $DB->insert_record('quiz_statistics', $toinsert);

        foreach ($questions as $question) {
            $question->_stats->quizstatisticsid = $quizstats->id;
            $DB->insert_record('quiz_question_statistics', $question->_stats, false);
        }

        foreach ($subquestions as $subquestion) {
            $subquestion->_stats->quizstatisticsid = $quizstats->id;
            $DB->insert_record('quiz_question_statistics', $subquestion->_stats, false);
        }

        return $quizstats->id;
    }

    /**
     * Get the quiz and question statistics, either by loading the cached results,
     * or by recomputing them.
     *
     * @param object $quiz the quiz settings.
     * @param int $currentgroup the current group. 0 for none.
     * @param bool $nostudentsingroup true if there a no students.
     * @param bool $useallattempts use all attempts, or just first attempts.
     * @param array $groupstudents students in this group.
     * @param array $questions question definitions.
     * @return array with 4 elements:
     *     - $quizstats The statistics for overall attempt scores.
     *     - $questions The questions, with an additional _stats field.
     *     - $subquestions The subquestions, if any, with an additional _stats field.
     *     - $s Number of attempts included in the stats.
     */
    protected function get_quiz_and_questions_stats($quiz, $currentgroup,
            $nostudentsingroup, $useallattempts, $groupstudents, $questions) {

        list($quizstats, $questions, $subquestions, $s) =
                $this->try_loading_cached_stats($quiz, $currentgroup, $nostudentsingroup,
                        $useallattempts, $groupstudents, $questions);

        if (is_null($quizstats)) {
            list($s, $quizstats, $qstats) = $this->compute_stats($quiz->id,
                    $currentgroup, $nostudentsingroup, $useallattempts, $groupstudents, $questions);

            if ($s) {
                $questions = $qstats->questions;
                $subquestions = $qstats->subquestions;

                $quizstatisticsid = $this->cache_stats($quiz->id, $currentgroup,
                        $quizstats, $questions, $subquestions);

                $this->analyse_responses($quizstatisticsid, $quiz->id, $currentgroup,
                        $nostudentsingroup, $useallattempts, $groupstudents,
                        $questions, $subquestions);
            }
        }

        return array($quizstats, $questions, $subquestions, $s);
    }

    protected function analyse_responses($quizstatisticsid, $quizid, $currentgroup,
            $nostudentsingroup, $useallattempts, $groupstudents, $questions, $subquestions) {

        $qubaids = quiz_statistics_qubaids_condition(
                $quizid, $currentgroup, $groupstudents, $useallattempts);

        $done = array();
        foreach ($questions as $question) {
            if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses()) {
                continue;
            }
            $done[$question->id] = 1;

            $responesstats = new quiz_statistics_response_analyser($question);
            $responesstats->analyse($qubaids);
            $responesstats->store_cached($quizstatisticsid);
        }

        foreach ($subquestions as $question) {
            if (!question_bank::get_qtype($question->qtype, false)->can_analyse_responses() ||
                    isset($done[$question->id])) {
                continue;
            }
            $done[$question->id] = 1;

            $responesstats = new quiz_statistics_response_analyser($question);
            $responesstats->analyse($qubaids);
            $responesstats->store_cached($quizstatisticsid);
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
     * @param object $quizstats the overall quiz statistics.
     * @param int $quizid the quiz id.
     * @param int $currentgroup the id of the currently selected group, or 0.
     * @param array $groupstudents ids of students in the group.
     * @param bool $useallattempts whether to use all attempts, instead of just
     *      first attempts.
     * @return string a HTML snipped saying when the stats were last computed,
     *      or blank if that is not appropriate.
     */
    protected function output_caching_info($quizstats, $quizid, $currentgroup,
            $groupstudents, $useallattempts, $reporturl) {
        global $DB, $OUTPUT;

        if (empty($quizstats->timemodified)) {
            return '';
        }

        // Find the number of attempts since the cached statistics were computed.
        list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql(
                $quizid, $currentgroup, $groupstudents, $useallattempts, true);
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
     * @param int $quizid the quiz id.
     * @param int $currentgroup a group id, or 0.
     * @param bool $useallattempts whether all attempts, or just first attempts are included.
     */
    protected function clear_cached_data($quizid, $currentgroup, $useallattempts) {
        global $DB;

        $todelete = $DB->get_records_menu('quiz_statistics', array('quizid' => $quizid,
                'groupid' => $currentgroup, 'allattempts' => $useallattempts), '', 'id, 1');

        if (!$todelete) {
            return;
        }

        list($todeletesql, $todeleteparams) = $DB->get_in_or_equal(array_keys($todelete));

        $DB->delete_records_select('quiz_question_statistics',
                'quizstatisticsid ' . $todeletesql, $todeleteparams);
        $DB->delete_records_select('quiz_question_response_stats',
                'quizstatisticsid ' . $todeletesql, $todeleteparams);
        $DB->delete_records_select('quiz_statistics',
                'id ' . $todeletesql, $todeleteparams);
    }

    /**
     * @param bool $useallattempts whether we are using all attempts.
     * @return the appropriate lang string to describe this option.
     */
    protected function using_attempts_string($useallattempts) {
        if ($useallattempts) {
            return get_string('allattempts', 'quiz_statistics');
        } else {
            return get_string('firstattempts', 'quiz_statistics');
        }
    }
}

function quiz_statistics_attempts_sql($quizid, $currentgroup, $groupstudents,
        $allattempts = true, $includeungraded = false) {
    global $DB;

    $fromqa = '{quiz_attempts} quiza ';

    $whereqa = 'quiza.quiz = :quizid AND quiza.preview = 0 AND quiza.state = :quizstatefinished';
    $qaparams = array('quizid' => $quizid, 'quizstatefinished' => quiz_attempt::FINISHED);

    if (!empty($currentgroup) && $groupstudents) {
        list($grpsql, $grpparams) = $DB->get_in_or_equal(array_keys($groupstudents),
                SQL_PARAMS_NAMED, 'u');
        $whereqa .= " AND quiza.userid $grpsql";
        $qaparams += $grpparams;
    }

    if (!$allattempts) {
        $whereqa .= ' AND quiza.attempt = 1';
    }

    if (!$includeungraded) {
        $whereqa .= ' AND quiza.sumgrades IS NOT NULL';
    }

    return array($fromqa, $whereqa, $qaparams);
}

/**
 * Return a {@link qubaid_condition} from the values returned by
 * {@link quiz_statistics_attempts_sql}
 * @param string $fromqa from quiz_statistics_attempts_sql.
 * @param string $whereqa from quiz_statistics_attempts_sql.
 */
function quiz_statistics_qubaids_condition($quizid, $currentgroup, $groupstudents,
        $allattempts = true, $includeungraded = false) {
    list($fromqa, $whereqa, $qaparams) = quiz_statistics_attempts_sql($quizid, $currentgroup,
            $groupstudents, $allattempts, $includeungraded);
    return new qubaid_join($fromqa, 'quiza.uniqueid', $whereqa, $qaparams);
}
