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
 * Quiz statistics report, table for showing statistics of each question in the quiz.
 *
 * @package   quiz_statistics
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use \core_question\statistics\questions\calculated_question_summary;

/**
 * This table has one row for each question in the quiz, with sub-rows when
 * random questions and variants appear.
 *
 * There are columns for the various item and position statistics.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_statistics_table extends flexible_table {
    /** @var object the quiz settings. */
    protected $quiz;

    /** @var integer the quiz course_module id. */
    protected $cmid;

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('mod-quiz-report-statistics-report');
    }

    /**
     * Set up the columns and headers and other properties of the table and then
     * call flexible_table::setup() method.
     *
     * @param object $quiz the quiz settings
     * @param int $cmid the quiz course_module id
     * @param moodle_url $reporturl the URL to redisplay this report.
     * @param int $s number of attempts included in the statistics.
     */
    public function statistics_setup($quiz, $cmid, $reporturl, $s) {
        $this->quiz = $quiz;
        $this->cmid = $cmid;

        // Define the table columns.
        $columns = array();
        $headers = array();

        $columns[] = 'number';
        $headers[] = get_string('questionnumber', 'quiz_statistics');

        if (!$this->is_downloading()) {
            $columns[] = 'icon';
            $headers[] = '';
            $columns[] = 'actions';
            $headers[] = '';
        } else {
            $columns[] = 'qtype';
            $headers[] = get_string('questiontype', 'quiz_statistics');
        }

        $columns[] = 'name';
        $headers[] = get_string('questionname', 'quiz');

        $columns[] = 's';
        $headers[] = get_string('attempts', 'quiz_statistics');

        if ($s > 1) {
            $columns[] = 'facility';
            $headers[] = get_string('facility', 'quiz_statistics');

            $columns[] = 'sd';
            $headers[] = get_string('standarddeviationq', 'quiz_statistics');
        }

        $columns[] = 'random_guess_score';
        $headers[] = get_string('random_guess_score', 'quiz_statistics');

        $columns[] = 'intended_weight';
        $headers[] = get_string('intended_weight', 'quiz_statistics');

        $columns[] = 'effective_weight';
        $headers[] = get_string('effective_weight', 'quiz_statistics');

        $columns[] = 'discrimination_index';
        $headers[] = get_string('discrimination_index', 'quiz_statistics');

        $columns[] = 'discriminative_efficiency';
        $headers[] = get_string('discriminative_efficiency', 'quiz_statistics');

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(false);

        $this->column_class('s', 'numcol');
        $this->column_class('facility', 'numcol');
        $this->column_class('sd', 'numcol');
        $this->column_class('random_guess_score', 'numcol');
        $this->column_class('intended_weight', 'numcol');
        $this->column_class('effective_weight', 'numcol');
        $this->column_class('discrimination_index', 'numcol');
        $this->column_class('discriminative_efficiency', 'numcol');

        // Set up the table.
        $this->define_baseurl($reporturl->out());

        $this->collapsible(true);

        $this->set_attribute('id', 'questionstatistics');
        $this->set_attribute('class', 'generaltable generalbox boxaligncenter');

        parent::setup();
    }

    /**
     * Open a div tag to wrap statistics table.
     */
    public function  wrap_html_start() {
        // Horrible Moodle 2.0 wide-content work-around.
        if (!$this->is_downloading()) {
            echo html_writer::start_tag('div', array('id' => 'tablecontainer',
                    'class' => 'statistics-tablecontainer'));
        }
    }

    /**
     * Close a statistics table div.
     */
    public function wrap_html_finish() {
        if (!$this->is_downloading()) {
            echo html_writer::end_tag('div');
        }
    }

    /**
     * The question number.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_number($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            return '';
        }
        if (!isset($questionstat->question->number)) {
            return '';
        }
        $number = $questionstat->question->number;

        if (isset($questionstat->subqdisplayorder)) {
            $number = $number . '.'.$questionstat->subqdisplayorder;
        }

        if ($questionstat->question->qtype != 'random' && !is_null($questionstat->variant)) {
            $number = $number . '.'.$questionstat->variant;
        }

        return $number;
    }

    /**
     * The question type icon.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_icon($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            return '';
        } else {
            return print_question_icon($questionstat->question, true);
        }
    }

    /**
     * Actions that can be performed on the question by this user (e.g. edit or preview).
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_actions($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            return '';
        } else {
            return quiz_question_action_icons($this->quiz, $this->cmid,
                    $questionstat->question, $this->baseurl, $questionstat->variant);
        }
    }

    /**
     * The question type name.
     *
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_qtype($questionstat) {
        return question_bank::get_qtype_name($questionstat->question->qtype);
    }

    /**
     * The question name.
     *
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_name($questionstat) {
        $name = $questionstat->question->name;

        if (!is_null($questionstat->variant)) {
            $a = new stdClass();
            $a->name = $name;
            $a->variant = $questionstat->variant;
            $name = get_string('nameforvariant', 'quiz_statistics', $a);
        }

        if ($this->is_downloading()) {
            return $name;
        }

        $baseurl = new moodle_url($this->baseurl);
        if (!is_null($questionstat->variant)) {
            if ($questionstat->subquestion) {
                // Variant of a sub-question.
                $url = new moodle_url($baseurl, array('qid' => $questionstat->questionid, 'variant' => $questionstat->variant));
                $name = html_writer::link($url, $name, array('title' => get_string('detailedanalysisforvariant',
                                                                                   'quiz_statistics',
                                                                                   $questionstat->variant)));
            } else if ($questionstat->slot) {
                // Variant of a question in a slot.
                $url = new moodle_url($baseurl, array('slot' => $questionstat->slot, 'variant' => $questionstat->variant));
                $name = html_writer::link($url, $name, array('title' => get_string('detailedanalysisforvariant',
                                                                                   'quiz_statistics',
                                                                                   $questionstat->variant)));
            }
        } else {
            if ($questionstat->subquestion && !$questionstat->get_variants()) {
                // Sub question without variants.
                $url = new moodle_url($baseurl, array('qid' => $questionstat->questionid));
                $name = html_writer::link($url, $name, array('title' => get_string('detailedanalysis', 'quiz_statistics')));
            } else if ($baseurl->param('slot') === null && $questionstat->slot) {
                // Question in a slot, we are not on a page showing structural analysis of one slot,
                // we don't want linking on those pages.
                $number = $questionstat->question->number;
                $israndomquestion = $questionstat->question->qtype == 'random';
                $url = new moodle_url($baseurl, array('slot' => $questionstat->slot));

                if ($this->is_calculated_question_summary($questionstat)) {
                    // Only make the random question summary row name link to the slot structure
                    // analysis page with specific text to clearly indicate the link to the user.
                    // Random and variant question rows will render the name without a link to improve clarity
                    // in the UI.
                    $name = html_writer::div(get_string('rangeofvalues', 'quiz_statistics'));
                } else if (!$israndomquestion && !$questionstat->get_variants() && !$questionstat->get_sub_question_ids()) {
                    // Question cannot be broken down into sub-questions or variants. Link will show response analysis page.
                    $name = html_writer::link($url,
                                              $name,
                                              array('title' => get_string('detailedanalysis', 'quiz_statistics')));
                }
            }
        }


        if ($this->is_dubious_question($questionstat)) {
            $name = html_writer::tag('div', $name, array('class' => 'dubious'));
        }

        if ($this->is_calculated_question_summary($questionstat)) {
            $name .= html_writer::link($url, get_string('viewanalysis', 'quiz_statistics'));
        } else if (!empty($questionstat->minmedianmaxnotice)) {
            $name = get_string($questionstat->minmedianmaxnotice, 'quiz_statistics') . '<br />' . $name;
        }

        return $name;
    }

    /**
     * The number of attempts at this question.
     *
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_s($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('s');
            $min = $min ?: 0;
            $max = $max ?: 0;
            return $this->format_range($min, $max);
        } else if (!isset($questionstat->s)) {
            return 0;
        } else {
            return $questionstat->s;
        }
    }

    /**
     * The facility index (average fraction).
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_facility($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('facility');
            return $this->format_percentage_range($min, $max);
        } else if (is_null($questionstat->facility)) {
            return '';
        } else {
            return $this->format_percentage($questionstat->facility);
        }
    }

    /**
     * The standard deviation of the fractions.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_sd($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('sd');
            return $this->format_percentage_range($min, $max);
        } else if (is_null($questionstat->sd) || $questionstat->maxmark == 0) {
            return '';
        } else {
            return $this->format_percentage($questionstat->sd / $questionstat->maxmark);
        }
    }

    /**
     * An estimate of the fraction a student would get by guessing randomly.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_random_guess_score($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('randomguessscore');
            return $this->format_percentage_range($min, $max);
        } else if (is_null($questionstat->randomguessscore)) {
            return '';
        } else {
            return $this->format_percentage($questionstat->randomguessscore);
        }
    }

    /**
     * The intended question weight. Maximum mark for the question as a percentage
     * of maximum mark for the quiz. That is, the indended influence this question
     * on the student's overall mark.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_intended_weight($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('maxmark');

            if (is_null($min) && is_null($max)) {
                return '';
            } else {
                $min = quiz_report_scale_summarks_as_percentage($min, $this->quiz);
                $max = quiz_report_scale_summarks_as_percentage($max, $this->quiz);
                return $this->format_range($min, $max);
            }
        } else {
            return quiz_report_scale_summarks_as_percentage($questionstat->maxmark, $this->quiz);
        }
    }

    /**
     * The effective question weight. That is, an estimate of the actual
     * influence this question has on the student's overall mark.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_effective_weight($questionstat) {
        global $OUTPUT;

        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('effectiveweight');

            if (is_null($min) && is_null($max)) {
                return '';
            } else {
                list( , $negcovar) = $questionstat->get_min_max_of('negcovar');
                if ($negcovar) {
                    $min = get_string('negcovar', 'quiz_statistics');
                }

                return $this->format_range($min, $max);
            }
        } else if (is_null($questionstat->effectiveweight)) {
            return '';
        } else if ($questionstat->negcovar) {
            $negcovar = get_string('negcovar', 'quiz_statistics');

            if (!$this->is_downloading()) {
                $negcovar = html_writer::tag('div',
                        $negcovar . $OUTPUT->help_icon('negcovar', 'quiz_statistics'),
                        array('class' => 'negcovar'));
            }

            return $negcovar;
        } else {
            return $this->format_percentage($questionstat->effectiveweight, false);
        }
    }

    /**
     * Discrimination index. This is the product moment correlation coefficient
     * between the fraction for this question, and the average fraction for the
     * other questions in this quiz.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_discrimination_index($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('discriminationindex');

            if (isset($max)) {
                $min = $min ?: 0;
            }

            if (is_numeric($min)) {
                $min = $this->format_percentage($min, false);
            }
            if (is_numeric($max)) {
                $max = $this->format_percentage($max, false);
            }

            return $this->format_range($min, $max);
        } else if (!is_numeric($questionstat->discriminationindex)) {
            return $questionstat->discriminationindex;
        } else {
            return $this->format_percentage($questionstat->discriminationindex, false);
        }
    }

    /**
     * Discrimination efficiency, similar to, but different from, the Discrimination index.
     *
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return string contents of this table cell.
     */
    protected function col_discriminative_efficiency($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            list($min, $max) = $questionstat->get_min_max_of('discriminativeefficiency');

            if (!is_numeric($min) && !is_numeric($max)) {
                return '';
            } else {
                return $this->format_percentage_range($min, $max, false);
            }
        } else if (!is_numeric($questionstat->discriminativeefficiency)) {
            return '';
        } else {
            return $this->format_percentage($questionstat->discriminativeefficiency);
        }
    }

    /**
     * This method encapsulates the test for wheter a question should be considered dubious.
     * @param \core_question\statistics\questions\calculated $questionstat stats for the question.
     * @return bool is this question possibly not pulling it's weight?
     */
    protected function is_dubious_question($questionstat) {
        if ($this->is_calculated_question_summary($questionstat)) {
            // We only care about the minimum value here.
            // If the minimum value is less than the threshold, then we know that there is at least one value below the threshold.
            list($discriminativeefficiency) = $questionstat->get_min_max_of('discriminativeefficiency');
        } else {
            $discriminativeefficiency = $questionstat->discriminativeefficiency;
        }

        if (!is_numeric($discriminativeefficiency)) {
            return false;
        }

        return $discriminativeefficiency < 15;
    }

    /**
     * Check if the given stats object is an instance of calculated_question_summary.
     *
     * @param  \core_question\statistics\questions\calculated $questionstat Stats object
     * @return bool
     */
    protected function is_calculated_question_summary($questionstat) {
        return $questionstat instanceof calculated_question_summary;
    }

    /**
     * Format inputs to represent a range between $min and $max.
     * This function does not check if $min is less than $max or not.
     * If both $min and $max are equal to null, this function returns an empty string.
     *
     * @param string|null $min The minimum value in the range
     * @param string|null $max The maximum value in the range
     * @return string
     */
    protected function format_range(string $min = null, string $max = null) {
        if (is_null($min) && is_null($max)) {
            return '';
        } else {
            $a = new stdClass();
            $a->min = $min;
            $a->max = $max;

            return get_string('rangebetween', 'quiz_statistics', $a);
        }
    }

    /**
     * Format a number to a localised percentage with specified decimal points.
     *
     * @param float $number The number being formatted
     * @param bool $fraction An indicator for whether the number is a fraction or is already multiplied by 100
     * @param int $decimals Sets the number of decimal points
     * @return string
     */
    protected function format_percentage(float $number, bool $fraction = true, int $decimals = 2) {
        $coefficient = $fraction ? 100 : 1;
        return get_string('percents', 'moodle', format_float($number * $coefficient, $decimals));
    }

    /**
     * Format $min and $max to localised percentages and form a string that represents a range between them.
     * This function does not check if $min is less than $max or not.
     * If both $min and $max are equal to null, this function returns an empty string.
     *
     * @param float|null $min The minimum value of the range
     * @param float|null $max The maximum value of the range
     * @param bool $fraction An indicator for whether min and max are a fractions or are already multiplied by 100
     * @param int $decimals Sets the number of decimal points
     * @return string A formatted string that represents a range between $min to $max.
     */
    protected function format_percentage_range(float $min = null, float $max = null, bool $fraction = true, int $decimals = 2) {
        if (is_null($min) && is_null($max)) {
            return '';
        } else {
            $min = $min ?: 0;
            $max = $max ?: 0;
            return $this->format_range(
                    $this->format_percentage($min, $fraction, $decimals),
                    $this->format_percentage($max, $fraction, $decimals)
            );
        }
    }
}
