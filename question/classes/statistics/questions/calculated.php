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
 * Question statistics calculations class. Used in the quiz statistics report but also available for use elsewhere.
 *
 * @package    core
 * @subpackage questionbank
 * @copyright  2013 Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;
defined('MOODLE_INTERNAL') || die();

/**
 * This class is used to return the stats as calculated by {@link \core_question\statistics\questions\calculator}
 *
 * @copyright 2013 Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calculated {

    public $questionid;

    // These first fields are the final fields cached in the db and shown in reports.

    // See : http://docs.moodle.org/dev/Quiz_statistics_calculations#Position_statistics .

    public $slot = null;

    /**
     * @var bool is this a sub question.
     */
    public $subquestion = false;

    /**
     * @var int total attempts at this question.
     */
    public $s = 0;

    /**
     * @var float effective weight of this question.
     */
    public $effectiveweight;

    /**
     * @var bool is covariance of this questions mark with other question marks negative?
     */
    public $negcovar;

    /**
     * @var float
     */
    public $discriminationindex;

    /**
     * @var float
     */
    public $discriminativeefficiency;

    /**
     * @var float standard deviation
     */
    public $sd;

    /**
     * @var float
     */
    public $facility;

    /**
     * @var float max mark achievable for this question.
     */
    public $maxmark;

    /**
     * @var string comma separated list of the positions in which this question appears.
     */
    public $positions;

    /**
     * @var null|float The average score that students would have got by guessing randomly. Or null if not calculable.
     */
    public $randomguessscore = null;


    // End of fields in db.

    protected $fieldsindb = array('questionid', 'slot', 'subquestion', 's', 'effectiveweight', 'negcovar', 'discriminationindex',
        'discriminativeefficiency', 'sd', 'facility', 'subquestions', 'maxmark', 'positions', 'randomguessscore');

    // Fields used for intermediate calculations.

    public $totalmarks = 0;

    public $totalothermarks = 0;

    /**
     * @var float The total of marks achieved for all positions in all attempts where this item was seen.
     */
    public $totalsummarks = 0;

    public $markvariancesum = 0;

    public $othermarkvariancesum = 0;

    public $covariancesum = 0;

    public $covariancemaxsum = 0;

    public $subquestions = '';

    public $covariancewithoverallmarksum = 0;

    public $markarray = array();

    public $othermarksarray = array();

    public $markaverage;

    public $othermarkaverage;

    /**
     * @var float The average for all attempts, of the sum of the marks for all positions in which this item appeared.
     */
    public $summarksaverage;

    public $markvariance;
    public $othermarkvariance;
    public $covariance;
    public $covariancemax;
    public $covariancewithoverallmark;

    /**
     * @var object full question data
     */
    public $question;

    /**
     * Set if this record has been retrieved from cache. This is the time that the statistics were calculated.
     *
     * @var integer
     */
    public $timemodified;

    /**
     * Cache calculated stats stored in this object in 'question_statistics' table.
     *
     * @param \qubaid_condition $qubaids
     */
    public function cache($qubaids) {
        global $DB;
        $toinsert = new \stdClass();
        $toinsert->hashcode = $qubaids->get_hash_code();
        $toinsert->timemodified = time();
        foreach ($this->fieldsindb as $field) {
            $toinsert->{$field} = $this->{$field};
        }
        $DB->insert_record('question_statistics', $toinsert, false);
    }

    /**
     * @param object $record Given a record from 'question_statistics' copy stats from record to properties.
     */
    public function populate_from_record($record) {
        foreach ($this->fieldsindb as $field) {
            $this->$field = $record->$field;
        }
        $this->timemodified = $record->timemodified;
    }

}
