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
 * Contains logic class and interface for the grading evaluation plugin "Comparison
 * with the best assessment".
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');  // interface definition
require_once($CFG->libdir . '/gradelib.php');

/**
 * Defines the computation login of the grading evaluation subplugin
 */
class workshop_best_evaluation implements workshop_evaluation {

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /**
     * Constructor
     *
     * @param workshop $workshop The workshop api instance
     * @return void
     */
    public function __construct(workshop $workshop) {
        $this->workshop         = $workshop;
    }

    /**
     * TODO
     *
     * @param null|int|array $restrict If null, update all reviewers, otherwise update just grades for the given reviewers(s)
     *
     * @return void
     */
    public function update_grading_grades($restrict=null) {
        global $DB;

        $grader = $this->workshop->grading_strategy_instance();
        if (! $grader->supports_evaluation($this)) {
            throw new coding_exception('The currently selected grading strategy plugin does not
                support this method of grading evaluation.');
        }

        // fetch a recordset with all assessments to process
        $rs         = $grader->get_assessments_recordset($restrict);
        $batch      = array();    // will contain a set of all assessments of a single submission
        $previous   = null;       // a previous record in the recordset
        foreach ($rs as $current) {
            if (is_null($previous)) {
                // we are processing the very first record in the recordset
                $previous = $current;
            }
            if ($current->submissionid == $previous->submissionid) {
                $batch[] = $current;
            } else {
                // process all the assessments of a sigle submission
                $this->process_assessments($batch);
                // start with a new batch to be processed
                $batch = array($current);
                $previous = $current;
            }
        }
        // do not forget to process the last batch!
        $this->process_assessments($batch);
        $rs->close();
    }

////////////////////////////////////////////////////////////////////////////////
// Internal methods                                                           //
////////////////////////////////////////////////////////////////////////////////

    /**
     * Given a list of all assessments of a single submission, updates the grading grades in database
     *
     * @param array $assessments of stdClass Object(->assessmentid ->assessmentweight ->reviewerid ->submissionid
     *                                              ->dimensionid ->grade ->dimensionweight)
     * @return void
     */
    protected function process_assessments(array $assessments) {
        global $DB;

        $grades = $this->evaluate_assessments($assessments);
        foreach ($grades as $assessmentid => $grade) {
            $record = new stdClass();
            $record->id = $assessmentid;
            $record->gradinggrade = grade_floatval($grade);
            $DB->update_record('workshop_assessments', $record, true);
        }
    }

    /**
     * Given a list of all assessments of a single submission, calculates the grading grades for them
     *
     * @param array $assessments same structure as for {@link self::process_assessments()}
     * @return array [(int)assessmentid => (float)gradinggrade] to be saved into {workshop_assessments}
     */
    protected function evaluate_assessments(array $assessments) {
        $gradinggrades = array();
        foreach ($assessments as $assessment) {
            $gradinggrades[$assessment->assessmentid] = grade_floatval(rand(0, 100));   // todo
        }
        return $gradinggrades;
    }

}
