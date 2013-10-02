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
 * This file defines interface of all grading evaluation classes
 *
 * @package    mod
 * @subpackage workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(__FILE__)) . '/lib.php');  // interface definition
require_once($CFG->libdir . '/gradelib.php');

/**
 * Defines all methods that grading evaluation subplugins has to implement
 *
 * @todo the final interface is not decided yet as we have only one implementation so far
 */
class workshop_calibrated_evaluation implements workshop_evaluation {

    /** @var workshop the parent workshop instance */
    protected $workshop;

    /** @var the recently used settings in this workshop */
    protected $settings;
	
	private $examples;
	
	public static $grading_curves = array(9 => 4.0, 8 => 3.0, 7 => 2.0, 6 => 1.5, 5 => 1.0, 4 => 0.666, 3 => 0.5, 2 => 0.333, 1 => 0.25, 0 => 0);
	
    public function __construct(workshop $workshop) {
        global $DB;
        $this->workshop = $workshop;
        $this->settings = $DB->get_record('workshopeval_calibrated', array('workshopid' => $this->workshop->id));
		$this->examples = array();
    }

    public function update_grading_grades(stdclass $settings, $restrict=null) {
		
		global $DB, $SESSION;
		
        // remember the recently used settings for this workshop
        if (empty($this->settings)) {
            $this->settings = new stdclass();
            $record = new stdclass();
            $record->workshopid = $this->workshop->id;
            $record->comparison = $settings->comparison;
			$record->consistency = $settings->consistency;
            $DB->insert_record('workshopeval_calibrated', $record);
        } elseif (($this->settings->comparison != $settings->comparison) || ($this->settings->consistency != $settings->consistency)) {
            $DB->set_field('workshopeval_calibrated', 'comparison', $settings->comparison,
                    array('workshopid' => $this->workshop->id));
            $DB->set_field('workshopeval_calibrated', 'consistency', $settings->consistency,
                    array('workshopid' => $this->workshop->id));
					
        }
		
        $grader = $this->workshop->grading_strategy_instance();
        
        $this->settings->comparison = $settings->comparison;
        $this->settings->consistency = $settings->consistency;

        // get the information about the assessment dimensions
        $diminfo = $grader->get_dimensions_info();

		// fetch the reference assessments
		$references = $this->get_reference_assessments();

        // fetch a recordset with all assessments to process
        $rs = $grader->get_assessments_recordset($restrict,true);
		$rrs = array(); //to go over the recordset again later...
        $users = array();
        
        $reference_assessments = array();
        foreach($references as $r) { 
            $reference_assessments[] = $r->assessmentid;
        }
        
        foreach ($rs as $r) {
            //skip the exemplar assessments
            if (in_array($r->assessmentid,$reference_assessments))
                continue;
            
            if (array_key_exists($r->submissionid,$references)) {
                $users[$r->reviewerid][$r->submissionid][$r->dimensionid] = $r->grade;
            }
			$rrs[] = $r;
        }
        $rs->close();
        
        $SESSION->workshop_calibration_no_competent_reviewers = array();
        
        foreach($users as $u => $assessments) {
            $calibration_scores[$u] = $this->calculate_calibration_score($assessments,$references,$diminfo) * 100;
        }
		
		foreach($rrs as $r) {
            $record = new stdclass();
            $record->id = $r->assessmentid;
            $record->gradinggrade = grade_floatval($calibration_scores[$r->reviewerid]);
			$DB->update_record('workshop_assessments', $record, false);  // bulk operations expected
		}
		
	}
    
    private function get_reference_assessments() {
        $grader = $this->workshop->grading_strategy_instance();
        $diminfo = $grader->get_dimensions_info();
        
		// cache the reference assessments
		$references = $this->workshop->get_examples_for_manager();
		$calibration_scores = array();
        
        //fetch grader recordset for examples
        $userkeys = array();
        foreach($references as $r) {
            $userkeys[$r->authorid] = $r->authorid;
        }

        $exemplars = $grader->get_assessments_recordset($userkeys,true);
        
        foreach($exemplars as $r) {
            if (array_key_exists($r->submissionid,$references)) {
                $references[$r->submissionid]->diminfo[$r->dimensionid] = $r->grade;
            }
        }
        
        return $references;
    }
    
    public function update_submission_grades(stdclass $settings) {
    
	
		global $DB;

		//fetch all the assessments for all the submissions in this 
		$sql = "SELECT a.id, a.submissionid, a.weight, a.grade, a.gradinggrade, a.gradinggradeover, s.title
				FROM {workshop_submissions} s, {workshop_assessments} a
				WHERE s.workshopid = {$this->workshop->id}
					AND s.example = 0
					AND a.submissionid = s.id
				ORDER BY a.submissionid";
		
		$records = $DB->get_recordset_sql($sql);
		
		$weighted_grades = array();
		$total_weight = 0;
		$current_submission = null;
		foreach($records as $v) {
			
			//this is actually "last": if the submissionid has changed, then we're on to a new submission.
			//it's kind of a stupid way of doing it but unfortunately there's no seeking in moodle recordsets, so
			//we can't get the submissionid of the next record to check if this is the last one
			if (($current_submission == null) or ($v->submissionid != $current_submission->submissionid)) {

				if ($current_submission != null)
					$this->update_submission_grade($current_submission, $weighted_grades, $total_weight);
				
				//reset our vital statistics
				$weighted_grades = array();
				$total_weight = 0;
				$current_submission = $v;
				
			}
			
			//just add the submission to the queue. we do all the work in the above if statement.
			$gradinggrade = is_null($v->gradinggradeover) ? $v->gradinggrade : $v->gradinggradeover;
			$weighted_grade = $v->grade * $v->weight * $gradinggrade;
			$weighted_grades[] = $weighted_grade;
			$total_weight += $v->gradinggrade * $v->weight;
		}
		
		//do it for the last one
		$this->update_submission_grade($current_submission, $weighted_grades, $total_weight);
        
		$records->close();
    }
	
	private function update_submission_grade($submission, $weighted_grades, $total_weight) {
		
		global $DB, $SESSION;
		
		//perform weighted average
        if ($total_weight > 0) {
    		$weighted_avg = array_sum($weighted_grades) / $total_weight;
        } else {
            $weighted_avg = null;
            $SESSION->workshop_calibration_no_competent_reviewers[$submission->submissionid] = $submission->title;
        }
			
		$DB->set_field('workshop_submissions','grade',$weighted_avg,array("id" => $submission->submissionid));
		
	}
	
	public function get_settings_form(moodle_url $actionurl=null) {
        global $CFG;    // needed because the included files use it
        global $DB;
        require_once(dirname(__FILE__) . '/settings_form.php');

        $customdata['workshop'] = $this->workshop;
        $customdata['current'] = $this->settings;
		$customdata['methodname'] = 'calibrated';
        $attributes = array('class' => 'evalsettingsform calibrated');

        return new workshop_calibrated_evaluation_settings_form($actionurl, $customdata, 'post', '', $attributes);
	}
	

    
    public function has_messages() {
        global $SESSION;
        if (isset($SESSION->workshop_calibration_no_competent_reviewers) && count($SESSION->workshop_calibration_no_competent_reviewers)) {
            return true;
        }
        return false;
    }
    
    public function display_messages() {
        //is this a hilariously incorrect way to do this?
        global $output, $PAGE, $SESSION;
        echo $output->box_start('no-competent-reviewers');

        echo get_string('nocompetentreviewers','workshopeval_calibrated');

        echo html_writer::start_tag('ul');
        foreach ($SESSION->workshop_calibration_no_competent_reviewers as $k => $v) {
            echo html_writer::start_tag('li');
            $url = new moodle_url('/mod/workshop/submission.php',
                                  array('cmid' => $PAGE->context->instanceid, 'id' => $k));
            echo html_writer::link($url, $v, array('class'=>'title'));
            echo html_writer::end_tag('li');
        }
        echo html_writer::end_tag('ul');
        
        echo $output->box_end();
    }
	
    public static function delete_instance($workshopid) {
		//TODO
	}
	
	//Private functions
    
    /*
    Thinking through this calculation:
    
    T is the set of all absdev of student's scores v exemplar scores
    
    x is the sum of T

    you're aiming for LOWER x. Exemplar's x is 0. If x < 0.02, round to 0.
    
    Invert this and normalise it to 0..1. Exemplar's x is now 1. The worst possible x is 0.
    
    Scale this according to the accuracy curves.
    
    y is the mean absolute deviation of T. Once again you want small y. y falls in the range 0..50
    
    We invert and normalise y to 0..1. Exemplar's y is now 1. The worst possible y is 0.
    
    Plug y into the consistency curves. Multiply x by the result and you have your calibration score!
    
    */
	
	private function calculate_calibration_score($assessments, $references, $diminfo) {
        
        //before we even get started, make sure the user has completed enough assessments to be calibrated
        $required_number_of_assessments = $this->workshop->numexamples or count($references);
        if ( count($assessments) < $required_number_of_assessments ) {
            return 0;
        }
        
        //now that we've made sure of that, we need to get our set of deviations
        
        $absolute_deviations = array(); // the set of all absdev of student's scores v exemplar scores (T)
        
        foreach($references as $k => $a) {

            foreach($a->diminfo as $dimid => $mydimval) {
                if (!empty($assessments[$k])) {
                    $theirdimval = $assessments[$k][$dimid];
                    $dim = $diminfo[$dimid];

                    $diff = abs( $mydimval - $theirdimval ); 
                    $absolute_deviations[] = $this->normalize_grade($dim, $diff);
                }
            }
        }
        
        $x = array_sum($absolute_deviations) / count($absolute_deviations);
        
        $x /= 100;
        if ($x < 0.01) $x = 0; //round 99% up to 100%
        $x = 1 - $x; //invert $x. 1 is now the best possible score.
        
        $grading_curve = $this::$grading_curves[$this->settings->comparison];
        
        if ($grading_curve >= 1) {
            $x = 1 - pow(1-$x, $grading_curve);
        } else {
            $x = pow($x, 1 / $grading_curve);
        }
        
        //now let's adjust for consistency
        
        //let's get the mean absolute deviation of T
        
        $mean = array_sum($absolute_deviations) / count($absolute_deviations);
        $numerator = 0; //top half of the MAD fraction
        foreach($absolute_deviations as $z) {
            $numerator += abs($z - $mean);
        }
        $y = $numerator / count($absolute_deviations);
        
        $y /= 100;
        if ($y < 0.01) $y = 0;
        if ($y > 1) $y = 1; //this *shouldn't* happen, but I'm not ruling it out
        $y = 1 - $y; //invert y. 1 is now the best possible score.
        
        $consistency_curve = $this::$grading_curves[9 - $this->settings->consistency]; //the consistency curves are actually around the other way - 0 means no consistency check while 8 is strictest. so we subtract the consistency setting from nine.
        
        //y = ax - a + 1
        $consistency_multiplier = $consistency_curve * $y - $consistency_curve + 1;
        
        $x *= $consistency_multiplier;
        
        // restrict $x to 0..1
        if ($x < 0) $x = 0;
        if ($x > 1) $x = 1;
        
        return $x;
		
	}
    
    private function normalize_grade($dim,$grade) {
        //todo: weight? is weight a factor here? probably should be...
        $dimmin = $dim->min;
        $dimmax = $dim->max;
        if ($dimmin == $dimmax) {
            return grade_floatval($dimmax);
        } else {
            return grade_floatval(($grade - $dimmin) / ($dimmax - $dimmin) * 100);
        }
    }
	
	private function get_example_assessments() {
		
	}
    
    public function prepare_explanation_for_assessor($userid) {
        $grader = $this->workshop->grading_strategy_instance();
        $diminfo = $grader->get_dimensions_info();
        $exxx = $grader->get_assessments_recordset(array($userid),true);
        $references = $this->get_reference_assessments();
        $options = array(
            'gradedecimals' => $this->workshop->gradedecimals,
            'accuracy' => $this->settings->comparison,
            'consistency' => $this->settings->consistency,
            'finalscoreoutof' => $this->workshop->gradinggrade
        );
        
        return new workshop_calibrated_evaluation_explanation($userid, $exxx, $references, $diminfo, $options);
    }
}

class workshop_calibrated_evaluation_explanation implements renderable {
    
    public function __construct($user, $examples, $references, $diminfo, $options = array()) {
        $this->user = $user;
        $this->examples = array();
        foreach($examples as $k => $v) {
            $this->examples[$v->submissionid][$v->dimensionid] = $v;
        }
        $this->references = array();
        foreach($references as $k => $v) {
            $this->references[$k] = $v;
        }
        $this->diminfo = $diminfo;
        foreach(array('gradedecimals' => 0, 'accuracy' => 5, 'consistency' => 5, 'finalscoreoutof' => null) as $k => $default) {
            $this->$k = empty($options[$k]) ? $default : $options[$k];
        }
        
        //make the table
        
        $reference_values = array();
        
        foreach($this->references as $k => $a) {
            
            foreach($a->diminfo as $dimid => $mydimval) {
                if (!empty($this->examples[$k])) {
                    $theirdimval = $this->examples[$k][$dimid];
                    $dim = $diminfo[$dimid];
                
                    $diff = abs( $mydimval - $theirdimval->grade ); 
                    $reference_values[$k][$dimid] = array($diminfo[$dimid], $theirdimval->grade, $mydimval, $diff);
                }
            }
        }
        
        $this->reference_values = $reference_values;
        
        //calculate the needed values
        
        $abs_devs = array();
        foreach($this->reference_values as $k => $v) {
            foreach($v as $i => $a) {
                $dim = $a[0];
                $diff = $a[3];
                $abs_devs[] = $this->normalize_grade($dim,$diff);
            }
        }
        
        $this->raw_average = array_sum($abs_devs) / count($abs_devs);
        $x = 100 - $this->raw_average;
        
        $grading_curve = workshop_calibrated_evaluation::$grading_curves[$this->accuracy];
        
        
        $x /= 100;
        if ($grading_curve >= 1) {
            $scaled_average = 1 - pow(1-$x, $grading_curve);
        } else {
            $scaled_average = pow($x, 1 / $grading_curve);
        }
        $scaled_average *= 100;
        
        $this->scaled_average = $scaled_average;
        
        $mean = array_sum($abs_devs) / count($abs_devs);
        $numerator = 0; //top half of the MAD fraction
        foreach($abs_devs as $z) {
            $numerator += abs($z - $mean);
        }
        $mad = $numerator / count($abs_devs);
        
        // $mad /= 50;
        // if ($mad < 0.01) $mad = 0;
        // if ($mad > 1) $mad = 1;
        // $mad = 1 - $mad;
        
        $this->mad = $mad;
        
        $consistency_curve = workshop_calibrated_evaluation::$grading_curves[9 - $this->consistency];
        
        $consistency_multiplier = $consistency_curve * (1 - ($mad / 100)) - $consistency_curve + 1;
        
        $this->consistency_multiplier = $consistency_multiplier;
        
        $this->final_score = $consistency_multiplier * $this->scaled_average;
        
        if ($this->final_score > 100) $this->final_score = 100;
        if ($this->final_score < 0) $this->final_score = 0;
    }
    
    public function normalize_grade($dim,$grade) {
        //todo: weight? is weight a factor here? probably should be...
        $dimmin = $dim->min;
        $dimmax = $dim->max;
        if ($dimmin == $dimmax) {
            return grade_floatval($dimmax);
        } else {
            return grade_floatval(($grade - $dimmin) / ($dimmax - $dimmin) * 100);
        }
    }
    
}
