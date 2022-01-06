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
 * @package    workshopeval
 * @subpackage best
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');  // interface definition
require_once($CFG->libdir . '/gradelib.php');

/**
 * Defines the computation login of the grading evaluation subplugin
 */
class workshop_best_evaluation extends workshop_evaluation {

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /** @var the recently used settings in this workshop */
    protected $settings;

    /**
     * Constructor
     *
     * @param workshop $workshop The workshop api instance
     * @return void
     */
    public function __construct(workshop $workshop) {
        global $DB;
        $this->workshop = $workshop;
        $this->settings = $DB->get_record('workshopeval_best_settings', array('workshopid' => $this->workshop->id));
    }

    /**
     * Calculates the grades for assessment and updates 'gradinggrade' fields in 'workshop_assessments' table
     *
     * This function relies on the grading strategy subplugin providing get_assessments_recordset() method.
     * {@see self::process_assessments()} for the required structure of the recordset.
     *
     * @param stdClass $settings       The settings for this round of evaluation
     * @param null|int|array $restrict If null, update all reviewers, otherwise update just grades for the given reviewers(s)
     *
     * @return void
     */
    public function update_grading_grades(stdclass $settings, $restrict=null) {
        global $DB;

        // Remember the recently used settings for this workshop.
        if (empty($this->settings)) {
            $record = new stdclass();
            $record->workshopid = $this->workshop->id;
            $record->comparison = $settings->comparison;
            $DB->insert_record('workshopeval_best_settings', $record);
        } elseif ($this->settings->comparison != $settings->comparison) {
            $DB->set_field('workshopeval_best_settings', 'comparison', $settings->comparison,
                    array('workshopid' => $this->workshop->id));
        }

        // Get the grading strategy instance.
        $grader = $this->workshop->grading_strategy_instance();

        // get the information about the assessment dimensions
        $diminfo = $grader->get_dimensions_info();

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
                // process all the assessments of a single submission
                $this->process_assessments($batch, $diminfo, $settings);
                // start with a new batch to be processed
                $batch = array($current);
                $previous = $current;
            }
        }
        // do not forget to process the last batch!
        $this->process_assessments($batch, $diminfo, $settings);
        $rs->close();
    }

    /**
     * Returns an instance of the form to provide evaluation settings.
     *
     * @return workshop_best_evaluation_settings_form
     */
    public function get_settings_form(moodle_url $actionurl=null) {

        $customdata['workshop'] = $this->workshop;
        $customdata['current'] = $this->settings;
        $attributes = array('class' => 'evalsettingsform best');

        return new workshop_best_evaluation_settings_form($actionurl, $customdata, 'post', '', $attributes);
    }

    /**
     * Delete all data related to a given workshop module instance
     *
     * @see workshop_delete_instance()
     * @param int $workshopid id of the workshop module instance being deleted
     * @return void
     */
    public static function delete_instance($workshopid) {
        global $DB;

        $DB->delete_records('workshopeval_best_settings', array('workshopid' => $workshopid));
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Internal methods                                                           //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Given a list of all assessments of a single submission, updates the grading grades in database
     *
     * @param array $assessments of stdclass (->assessmentid ->assessmentweight ->reviewerid ->gradinggrade ->submissionid ->dimensionid ->grade)
     * @param array $diminfo of stdclass (->id ->weight ->max ->min)
     * @param stdClass grading evaluation settings
     * @return void
     */
    protected function process_assessments(array $assessments, array $diminfo, stdclass $settings) {
        global $DB;

        if (empty($assessments)) {
            return;
        }

        // reindex the passed flat structure to be indexed by assessmentid
        $assessments = $this->prepare_data_from_recordset($assessments);

        // normalize the dimension grades to the interval 0 - 100
        $assessments = $this->normalize_grades($assessments, $diminfo);

        // get a hypothetical average assessment
        $average = $this->average_assessment($assessments);

        // if unable to calculate the average assessment, set the grading grades to null
        if (is_null($average)) {
            foreach ($assessments as $asid => $assessment) {
                if (!is_null($assessment->gradinggrade)) {
                    $DB->set_field('workshop_assessments', 'gradinggrade', null, array('id' => $asid));
                }
            }
            return;
        }

        // calculate variance of dimension grades
        $variances = $this->weighted_variance($assessments);
        foreach ($variances as $dimid => $variance) {
            $diminfo[$dimid]->variance = $variance;
        }

        // for every assessment, calculate its distance from the average one
        $distances = array();
        foreach ($assessments as $asid => $assessment) {
            $distances[$asid] = $this->assessments_distance($assessment, $average, $diminfo, $settings);
        }

        // identify the best assessments - that is those with the shortest distance from the best assessment
        $bestids = array_keys($distances, min($distances));

        // for every assessment, calculate its distance from the nearest best assessment
        $distances = array();
        foreach ($bestids as $bestid) {
            $best = $assessments[$bestid];
            foreach ($assessments as $asid => $assessment) {
                $d = $this->assessments_distance($assessment, $best, $diminfo, $settings);
                if (!is_null($d) and (!isset($distances[$asid]) or $d < $distances[$asid])) {
                    $distances[$asid] = $d;
                }
            }
        }

        // calculate the grading grade
        foreach ($distances as $asid => $distance) {
            $gradinggrade = (100 - $distance);
            if ($gradinggrade < 0) {
                $gradinggrade = 0;
            }
            if ($gradinggrade > 100) {
                $gradinggrade = 100;
            }
            $grades[$asid] = grade_floatval($gradinggrade);
        }

        // if the new grading grade differs from the one stored in database, update it
        // we do not use set_field() here because we want to pass $bulk param
        foreach ($grades as $assessmentid => $grade) {
            if (grade_floats_different($grade, $assessments[$assessmentid]->gradinggrade)) {
                // the value has changed
                $record = new stdclass();
                $record->id = $assessmentid;
                $record->gradinggrade = grade_floatval($grade);
                // do not set timemodified here, it contains the timestamp of when the form was
                // saved by the peer reviewer, not when it was aggregated
                $DB->update_record('workshop_assessments', $record, true);  // bulk operations expected
            }
        }

        // done. easy, heh? ;-)
    }

    /**
     * Prepares a structure of assessments and given grades
     *
     * @param array $assessments batch of recordset items as returned by the grading strategy
     * @return array
     */
    protected function prepare_data_from_recordset($assessments) {
        $data = array();    // to be returned
        foreach ($assessments as $a) {
            $id = $a->assessmentid; // just an abbreviation
            if (!isset($data[$id])) {
                $data[$id] = new stdclass();
                $data[$id]->assessmentid = $a->assessmentid;
                $data[$id]->weight       = $a->assessmentweight;
                $data[$id]->reviewerid   = $a->reviewerid;
                $data[$id]->gradinggrade = $a->gradinggrade;
                $data[$id]->submissionid = $a->submissionid;
                $data[$id]->dimgrades    = array();
            }
            $data[$id]->dimgrades[$a->dimensionid] = $a->grade;
        }
        return $data;
    }

    /**
     * Normalizes the dimension grades to the interval 0.00000 - 100.00000
     *
     * Note: this heavily relies on PHP5 way of handling references in array of stdclasses. Hopefully
     * it will not change again soon.
     *
     * @param array $assessments of stdclass as returned by {@see self::prepare_data_from_recordset()}
     * @param array $diminfo of stdclass
     * @return array of stdclass with the same structure as $assessments
     */
    protected function normalize_grades(array $assessments, array $diminfo) {
        foreach ($assessments as $asid => $assessment) {
            foreach ($assessment->dimgrades as $dimid => $dimgrade) {
                $dimmin = $diminfo[$dimid]->min;
                $dimmax = $diminfo[$dimid]->max;
                if ($dimmin == $dimmax) {
                    $assessment->dimgrades[$dimid] = grade_floatval($dimmax);
                } else {
                    $assessment->dimgrades[$dimid] = grade_floatval(($dimgrade - $dimmin) / ($dimmax - $dimmin) * 100);
                }
            }
        }
        return $assessments;
    }

    /**
     * Given a set of a submission's assessments, returns a hypothetical average assessment
     *
     * The passed structure must be array of assessments objects with ->weight and ->dimgrades properties.
     * Returns null if all passed assessments have zero weight as there is nothing to choose
     * from then.
     *
     * @param array $assessments as prepared by {@link self::prepare_data_from_recordset()}
     * @return null|stdClass
     */
    protected function average_assessment(array $assessments) {
        $sumdimgrades = array();
        foreach ($assessments as $a) {
            foreach ($a->dimgrades as $dimid => $dimgrade) {
                if (!isset($sumdimgrades[$dimid])) {
                    $sumdimgrades[$dimid] = 0;
                }
                $sumdimgrades[$dimid] += $dimgrade * $a->weight;
            }
        }

        $sumweights = 0;
        foreach ($assessments as $a) {
            $sumweights += $a->weight;
        }
        if ($sumweights == 0) {
            // unable to calculate average assessment
            return null;
        }

        $average = new stdclass();
        $average->dimgrades = array();
        foreach ($sumdimgrades as $dimid => $sumdimgrade) {
            $average->dimgrades[$dimid] = grade_floatval($sumdimgrade / $sumweights);
        }
        return $average;
    }

    /**
     * Given a set of a submission's assessments, returns standard deviations of all their dimensions
     *
     * The passed structure must be array of assessments objects with at least ->weight
     * and ->dimgrades properties. This implementation uses weighted incremental algorithm as
     * suggested in "D. H. D. West (1979). Communications of the ACM, 22, 9, 532-535:
     * Updating Mean and Variance Estimates: An Improved Method"
     * {@link http://en.wikipedia.org/wiki/Algorithms_for_calculating_variance#Weighted_incremental_algorithm}
     *
     * @param array $assessments as prepared by {@link self::prepare_data_from_recordset()}
     * @return null|array indexed by dimension id
     */
    protected function weighted_variance(array $assessments) {
        $first = reset($assessments);
        if (empty($first)) {
            return null;
        }
        $dimids = array_keys($first->dimgrades);
        $asids  = array_keys($assessments);
        $vars   = array();  // to be returned
        foreach ($dimids as $dimid) {
            $n = 0;
            $s = 0;
            $sumweight = 0;
            foreach ($asids as $asid) {
                $x = $assessments[$asid]->dimgrades[$dimid];    // value (data point)
                $weight = $assessments[$asid]->weight;          // the values' weight
                if ($weight == 0) {
                    continue;
                }
                if ($n == 0) {
                    $n = 1;
                    $mean = $x;
                    $s = 0;
                    $sumweight = $weight;
                } else {
                    $n++;
                    $temp = $weight + $sumweight;
                    $q = $x - $mean;
                    $r = $q * $weight / $temp;
                    $s = $s + $sumweight * $q * $r;
                    $mean = $mean + $r;
                    $sumweight = $temp;
                }
            }
            if ($sumweight > 0 and $n > 1) {
                // for the sample: $vars[$dimid] = ($s * $n) / (($n - 1) * $sumweight);
                // for the population:
                $vars[$dimid] = $s / $sumweight;
            } else {
                $vars[$dimid] = null;
            }
        }
        return $vars;
    }

    /**
     * Measures the distance of the assessment from a referential one
     *
     * The passed data structures must contain ->dimgrades property. The referential
     * assessment is supposed to be close to the average assessment. All dimension grades are supposed to be
     * normalized to the interval 0 - 100.
     * Returned value is rounded to 4 valid decimals to prevent some rounding issues - see the unit test
     * for an example.
     *
     * @param stdClass $assessment the assessment being measured
     * @param stdClass $referential assessment
     * @param array $diminfo of stdclass(->weight ->min ->max ->variance) indexed by dimension id
     * @param stdClass $settings
     * @return float|null rounded to 4 valid decimals
     */
    protected function assessments_distance(stdclass $assessment, stdclass $referential, array $diminfo, stdclass $settings) {
        $distance = 0;
        $n = 0;
        foreach (array_keys($assessment->dimgrades) as $dimid) {
            $agrade = $assessment->dimgrades[$dimid];
            $rgrade = $referential->dimgrades[$dimid];
            $var    = $diminfo[$dimid]->variance;
            $weight = $diminfo[$dimid]->weight;
            $n     += $weight;

            // variations very close to zero are too sensitive to a small change of data values
            $var = max($var, 0.01);

            if ($agrade != $rgrade) {
                $absdelta   = abs($agrade - $rgrade);
                $reldelta   = pow($agrade - $rgrade, 2) / ($settings->comparison * $var);
                $distance  += $absdelta * $reldelta * $weight;
            }
        }
        if ($n > 0) {
            // average distance across all dimensions
            return round($distance / $n, 4);
        } else {
            return null;
        }
    }
}


/**
 * Represents the settings form for this plugin.
 */
class workshop_best_evaluation_settings_form extends workshop_evaluation_settings_form {

    /**
     * Defines specific fields for this evaluation method.
     */
    protected function definition_sub() {
        $mform = $this->_form;

        $plugindefaults = get_config('workshopeval_best');
        $current = $this->_customdata['current'];

        $options = array();
        for ($i = 9; $i >= 1; $i = $i-2) {
            $options[$i] = get_string('comparisonlevel' . $i, 'workshopeval_best');
        }
        $mform->addElement('select', 'comparison', get_string('comparison', 'workshopeval_best'), $options);
        $mform->addHelpButton('comparison', 'comparison', 'workshopeval_best');
        $mform->setDefault('comparison', $plugindefaults->comparison);

        $this->set_data($current);
    }
}
