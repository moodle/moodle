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
 * Update a grade.
 *
 * @package local_o365
 * @author  2012 Paul Charsley, modified slightly 2017 James McQuillan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2012 Paul Charsley
 */

namespace local_o365\webservices;

use \local_o365\webservices\exception as exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/course/modlib.php');
require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/user/externallib.php');
require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Update a grade.
 */
class update_grade extends \external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters The parameters object for this webservice method.
     */
    public static function grade_update_parameters() {
        global $CFG;
        require_once("$CFG->dirroot/grade/grading/lib.php");
        $instance = new \assign(null, null, null);
        $pluginfeedbackparams = array();

        foreach ($instance->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginparams = $plugin->get_external_parameters();
                if (!empty($pluginparams)) {
                    $pluginfeedbackparams = array_merge($pluginfeedbackparams, $pluginparams);
                }
            }
        }

        $advancedgradingdata = array();
        $methods = array_keys(\grading_manager::available_methods(false));
        foreach ($methods as $method) {
            require_once($CFG->dirroot.'/grade/grading/form/'.$method.'/lib.php');
            $details  = call_user_func('gradingform_'.$method.'_controller::get_external_instance_filling_details');
            if (!empty($details)) {
                $items = array();
                foreach ($details as $key => $value) {
                    $value->required = VALUE_OPTIONAL;
                    unset($value->content->keys['id']);
                    $items[$key] = new \external_multiple_structure (new \external_single_structure(
                        array(
                            'criterionid' => new \external_value(PARAM_INT, 'criterion id'),
                            'fillings' => $value
                        )
                    ));
                }
                $advancedgradingdata[$method] = new \external_single_structure($items, 'items', VALUE_OPTIONAL);
            }
        }

        return new \external_function_parameters(
            array(
                'assignmentid' => new \external_value(PARAM_INT, 'The assignment id to operate on'),
                'userid' => new \external_value(PARAM_INT, 'The student id to operate on'),
                'grade' => new \external_value(PARAM_FLOAT, 'The new grade for this user. Ignored if advanced grading used'),
                'attemptnumber' => new \external_value(PARAM_INT, 'The attempt number (-1 means latest attempt)'),
                'addattempt' => new \external_value(PARAM_BOOL, 'Allow another attempt if the attempt reopen method is manual'),
                'workflowstate' => new \external_value(PARAM_ALPHA, 'The next marking workflow state'),
                'applytoall' => new \external_value(PARAM_BOOL, 'If true, this grade will be applied ' .
                                                               'to all members ' .
                                                               'of the group (for group assignments).'),
                'plugindata' => new \external_single_structure($pluginfeedbackparams, 'plugin data', VALUE_DEFAULT, array()),
                'advancedgradingdata' => new \external_single_structure($advancedgradingdata, 'advanced grading data',
                                                                       VALUE_DEFAULT, array())
            )
        );
    }

    /**
     * Save a student grade for a single assignment.
     *
     * @param int $assignmentid The id of the assignment
     * @param int $userid The id of the user
     * @param float $grade The grade (ignored if the assignment uses advanced grading)
     * @param int $attemptnumber The attempt number
     * @param bool $addattempt Allow another attempt
     * @param string $workflowstate New workflow state
     * @param bool $applytoall Apply the grade to all members of the group
     * @param array $plugindata Custom data used by plugins
     * @param array $advancedgradingdata Advanced grading data
     * @return null
     * @since Moodle 2.6
     */
    public static function grade_update($assignmentid,
                                      $userid,
                                      $grade,
                                      $attemptnumber,
                                      $addattempt,
                                      $workflowstate,
                                      $applytoall,
                                      $plugindata = array(),
                                      $advancedgradingdata = array()) {
        global $CFG, $USER, $DB;

        $params = self::validate_parameters(self::grade_update_parameters(),
                                            array('assignmentid' => $assignmentid,
                                                  'userid' => $userid,
                                                  'grade' => $grade,
                                                  'attemptnumber' => $attemptnumber,
                                                  'workflowstate' => $workflowstate,
                                                  'addattempt' => $addattempt,
                                                  'applytoall' => $applytoall,
                                                  'plugindata' => $plugindata,
                                                  'advancedgradingdata' => $advancedgradingdata));

        $cm = get_coursemodule_from_instance('assign', $params['assignmentid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);

        $assignment = new \assign($context, $cm, null);

        $gradedata = (object)$params['plugindata'];

        $gradedata->addattempt = $params['addattempt'];
        $gradedata->attemptnumber = $params['attemptnumber'];
        $gradedata->workflowstate = $params['workflowstate'];
        $gradedata->applytoall = $params['applytoall'];
        $gradedata->grade = $params['grade'];

        if (!empty($params['advancedgradingdata'])) {
            $advancedgrading = array();
            $criteria = reset($params['advancedgradingdata']);
            foreach ($criteria as $key => $criterion) {
                $details = array();
                foreach ($criterion as $value) {
                    foreach ($value['fillings'] as $filling) {
                        $details[$value['criterionid']] = $filling;
                    }
                }
                $advancedgrading[$key] = $details;
            }
            $gradedata->advancedgrading = $advancedgrading;
        }

        $assignment->save_grade($params['userid'], $gradedata);

        // Get grade_grades ID.
        $gradeitem = $assignment->get_grade_item();
        $sqlfilters = [
            'itemid' => $gradeitem->id,
            'userid' => $params['userid'],
        ];

        $graderec = $DB->get_record('grade_grades', $sqlfilters);
        if (empty($graderec)) {
            throw new exception\couldnotsavegrade();
        }

        return [
            'id' => $graderec->id,
            'itemid' => $graderec->itemid
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure Object describing return parameters for this webservice method.
     */
    public static function grade_update_returns() {
        return new \external_single_structure([
            'id' => new \external_value(PARAM_INT, 'id of grade'),
            'itemid' => new \external_value(PARAM_INT, 'id of grade item'),
        ]);
    }
}
