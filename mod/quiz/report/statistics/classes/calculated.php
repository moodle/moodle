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

namespace quiz_statistics;

defined('MOODLE_INTERNAL') || die();

/**
 * The statistics calculator returns an instance of this class which contains the calculated statistics.
 *
 * These quiz statistics calculations are described here :
 *
 * http://docs.moodle.org/dev/Quiz_statistics_calculations#Test_statistics
 *
 * @package    quiz_statistics
 * @copyright  2013 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated {

    /**
     * @param  string $whichattempts which attempts to use, represented internally as one of the constants as used in
     *                                   $quiz->grademethod ie.
     *                                   QUIZ_GRADEAVERAGE, QUIZ_GRADEHIGHEST, QUIZ_ATTEMPTLAST or QUIZ_ATTEMPTFIRST
     *                                   we calculate stats based on which attempts would affect the grade for each student,
     *                                   the default null value is used when constructing an instance whose values will be
     *                                   populated from a db record.
     */
    public function __construct($whichattempts = null) {
        if ($whichattempts !== null) {
            $this->whichattempts = $whichattempts;
        }
    }

    /**
     * @var int which attempts we are calculating calculate stats from.
     */
    public $whichattempts;

    /* Following stats all described here : http://docs.moodle.org/dev/Quiz_statistics_calculations#Test_statistics  */

    public $firstattemptscount = 0;

    public $allattemptscount = 0;

    public $lastattemptscount = 0;

    public $highestattemptscount = 0;

    public $firstattemptsavg;

    public $allattemptsavg;

    public $lastattemptsavg;

    public $highestattemptsavg;

    public $median;

    public $standarddeviation;

    public $skewness;

    public $kurtosis;

    public $cic;

    public $errorratio;

    public $standarderror;

    /**
     * @var int time these stats where calculated and cached.
     */
    public $timemodified;

    /**
     * Count of attempts selected by $this->whichattempts
     *
     * @return int
     */
    public function s() {
        return $this->get_field('count');
    }

    /**
     * Average grade for the attempts selected by $this->whichattempts
     *
     * @return float
     */
    public function avg() {
        return $this->get_field('avg');
    }

    /**
     * Get the right field name to fetch a stat for these attempts that is calculated for more than one $whichattempts (count or
     * avg).
     *
     * @param string $field name of field
     * @return int|float
     */
    protected function get_field($field) {
        $fieldname = calculator::using_attempts_string_id($this->whichattempts).$field;
        return $this->{$fieldname};
    }

    /**
     * @param $course
     * @param $cm
     * @param $quiz
     * @return array to display in table or spreadsheet.
     */
    public function get_formatted_quiz_info_data($course, $cm, $quiz) {

        // You can edit this array to control which statistics are displayed.
        $todisplay = array('firstattemptscount' => 'number',
                           'allattemptscount' => 'number',
                           'firstattemptsavg' => 'summarks_as_percentage',
                           'allattemptsavg' => 'summarks_as_percentage',
                           'lastattemptsavg' => 'summarks_as_percentage',
                           'highestattemptsavg' => 'summarks_as_percentage',
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
            if (!isset($this->$property) || !$format) {
                continue;
            }
            $value = $this->$property;

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
                                 calculator::using_attempts_lang_string($this->whichattempts))] = $formattedvalue;
        }

        return $quizinfo;
    }

    /**
     * @var array of names of properties of this class that are cached in db record.
     */
    protected $fieldsindb = array('whichattempts', 'firstattemptscount', 'allattemptscount', 'firstattemptsavg', 'allattemptsavg',
                                    'lastattemptscount', 'highestattemptscount', 'lastattemptsavg', 'highestattemptsavg',
                                    'median', 'standarddeviation', 'skewness',
                                    'kurtosis', 'cic', 'errorratio', 'standarderror');

    /**
     * Cache the stats contained in this class.
     *
     * @param $qubaids \qubaid_condition
     */
    public function cache($qubaids) {
        global $DB;

        $toinsert = new \stdClass();

        foreach ($this->fieldsindb as $field) {
            $toinsert->{$field} = $this->{$field};
        }

        $toinsert->hashcode = $qubaids->get_hash_code();
        $toinsert->timemodified = time();

        // Fix up some dodgy data.
        if (isset($toinsert->errorratio) && is_nan($toinsert->errorratio)) {
            $toinsert->errorratio = null;
        }
        if (isset($toinsert->standarderror) && is_nan($toinsert->standarderror)) {
            $toinsert->standarderror = null;
        }

        // Delete older statistics before we save the new ones.
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('quiz_statistics', ['hashcode' => $qubaids->get_hash_code()]);

        // Store the data.
        $DB->insert_record('quiz_statistics', $toinsert);
        $transaction->allow_commit();
    }

    /**
     * Given a record from 'quiz_statistics' table load the data into the properties of this class.
     *
     * @param $record \stdClass from db.
     */
    public function populate_from_record($record) {
        foreach ($this->fieldsindb as $field) {
            $this->$field = $record->$field;
        }
        $this->timemodified = $record->timemodified;
    }
}
