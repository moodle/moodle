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
 * Web services relating to fetching of a rubric for the grading panel.
 *
 * @package    gradingform_rubric
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace gradingform_rubric\grades\grader\gradingpanel\external;

global $CFG;

use coding_exception;
use context;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use stdClass;
use moodle_exception;
require_once($CFG->dirroot.'/grade/grading/form/rubric/lib.php');

/**
 * Web services relating to fetching of a rubric for the grading panel.
 *
 * @package    gradingform_rubric
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch extends external_api {

    /**
     * Describes the parameters for fetching the grading panel for a simple grade.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'component' => new external_value(
                PARAM_ALPHANUMEXT,
                'The name of the component',
                VALUE_REQUIRED
            ),
            'contextid' => new external_value(
                PARAM_INT,
                'The ID of the context being graded',
                VALUE_REQUIRED
            ),
            'itemname' => new external_value(
                PARAM_ALPHANUM,
                'The grade item itemname being graded',
                VALUE_REQUIRED
            ),
            'gradeduserid' => new external_value(
                PARAM_INT,
                'The ID of the user show',
                VALUE_REQUIRED
            ),
        ]);
    }

    /**
     * Fetch the data required to build a grading panel for a simple grade.
     *
     * @param string $component
     * @param int $contextid
     * @param string $itemname
     * @param int $gradeduserid
     * @return array
     * @since Moodle 3.8
     */
    public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
        global $CFG, $USER;
        require_once("{$CFG->libdir}/gradelib.php");
        [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ]);

        // Validate the context.
        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // Validate that the supplied itemname is a gradable item.
        if (!component_gradeitems::is_valid_itemname($component, $itemname)) {
            throw new coding_exception("The '{$itemname}' item is not valid for the '{$component}' component");
        }

        // Fetch the gradeitem instance.
        $gradeitem = gradeitem::instance($component, $context, $itemname);

        if (RUBRIC !== $gradeitem->get_advanced_grading_method()) {
            throw new moodle_exception(
                "The {$itemname} item in {$component}/{$contextid} is not configured for advanced grading with a rubric"
            );
        }

        // Fetch the actual data.
        $gradeduser = \core_user::get_user($gradeduserid, '*', MUST_EXIST);

        // One can access its own grades. Others just if they're graders.
        if ($gradeduserid != $USER->id) {
            $gradeitem->require_user_can_grade($gradeduser, $USER);
        }

        return self::get_fetch_data($gradeitem, $gradeduser);
    }

    /**
     * Get the data to be fetched and create the structure ready for Mustache.
     *
     * @param gradeitem $gradeitem
     * @param stdClass $gradeduser
     * @return array
     */
    public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser): array {
        global $USER;
        // Set up all the controllers etc that we'll be needing.
        $hasgrade = $gradeitem->user_has_grade($gradeduser);
        $grade = $gradeitem->get_formatted_grade_for_user($gradeduser, $USER);
        $instance = $gradeitem->get_advanced_grading_instance($USER, $grade);
        if (!$instance) {
            throw new moodle_exception('error:gradingunavailable', 'grading');
        }

        $controller = $instance->get_controller();
        $definition = $controller->get_definition();
        $fillings = $instance->get_rubric_filling();
        $context = $controller->get_context();
        $definitionid = (int) $definition->id;

        // Set up some items we need to return on other interfaces.
        $gradegrade = \grade_grade::fetch(['itemid' => $gradeitem->get_grade_item()->id, 'userid' => $gradeduser->id]);
        $gradername = $gradegrade ? fullname(\core_user::get_user($gradegrade->usermodified)) : null;
        $maxgrade = max(array_keys($controller->get_grade_range()));

        $teacherdescription = self::get_formatted_text(
            $context,
            $definitionid,
            'description',
            $definition->description,
            (int) $definition->descriptionformat
        );

        $criterion = [];
        if ($definition->rubric_criteria) {
            // Iterate over the defined criterion in the rubric and map out what we need to render each item.
            $criterion = array_map(function($criterion) use ($definitionid, $fillings, $context, $hasgrade) {
                // The general structure we'll be returning, we still need to get the remark (if any) and the levels associated.
                $result = [
                    'id' => $criterion['id'],
                    'description' => self::get_formatted_text(
                        $context,
                        $definitionid,
                        'description',
                        $criterion['description'],
                        (int) $criterion['descriptionformat']
                    ),
                ];

                // Do we have an existing grade filling? if so lets get the remark associated to this criteria.
                $filling = [];
                if (array_key_exists($criterion['id'], $fillings['criteria'])) {
                    $filling = $fillings['criteria'][$criterion['id']];
                    $result['remark'] = self::get_formatted_text($context,
                        $definitionid,
                        'remark',
                        $filling['remark'],
                        (int) FORMAT_HTML
                    );
                }

                // Lets build the levels within a criteria and figure out what needs to go where.
                $result['levels'] = array_map(function($level) use ($criterion, $filling, $context, $definitionid) {
                    // The bulk of what'll be returned can be defined easily we'll add to this further down.
                    $result = [
                        'id' => $level['id'],
                        'criterionid' => $criterion['id'],
                        'score' => $level['score'],
                        'definition' => self::get_formatted_text(
                            $context,
                            $definitionid,
                            'definition',
                            $level['definition'],
                            (int) $level['definitionformat']
                        ),
                        'checked' => null,
                    ];

                    // Consult the grade filling to see if a level has been selected and if it is the current level.
                    if (array_key_exists('levelid', $filling) && $filling['levelid'] == $level['id']) {
                        $result['checked'] = true;
                    }

                    return $result;
                }, $criterion['levels']);

                $nulllevel = [
                    'id' => null,
                    'criterionid' => $criterion['id'],
                    'score' => '-',
                    'definition' => get_string('notset', 'gradingform_rubric'),
                    'checked' => !$hasgrade,
                ];
                // Consult the grade filling to see if a level has been selected and if it is the current level.
                if (array_key_exists('levelid', $filling) && $filling['levelid'] == 0) {
                    $nulllevel['checked'] = true;
                }

                array_unshift($result['levels'], $nulllevel);

                return $result;
            }, $definition->rubric_criteria);
        }

        return [
            'templatename' => 'gradingform_rubric/grades/grader/gradingpanel',
            'hasgrade' => $hasgrade,
            'grade' => [
                'instanceid' => $instance->get_id(),
                'criteria' => $criterion,
                'rubricmode' => 'evaluate editable',
                'teacherdescription' => $teacherdescription,
                'canedit' => false,
                'usergrade' => $grade->usergrade,
                'maxgrade' => $maxgrade,
                'gradedby' => $gradername,
                'timecreated' => $grade->timecreated,
                'timemodified' => $grade->timemodified,
            ],
            'warnings' => [],
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 3.8
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'templatename' => new external_value(PARAM_SAFEPATH, 'The template to use when rendering this data'),
            'hasgrade' => new external_value(PARAM_BOOL, 'Does the user have a grade?'),
            'grade' => new external_single_structure([
                'instanceid' => new external_value(PARAM_INT, 'The id of the current grading instance'),
                'rubricmode' => new external_value(PARAM_RAW, 'The mode i.e. evaluate editable'),
                'canedit' => new external_value(PARAM_BOOL, 'Can the user edit this'),
                'criteria' => new external_multiple_structure(
                    new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'ID of the Criteria'),
                        'description' => new external_value(PARAM_RAW, 'Description of the Criteria'),
                        'remark' => new external_value(PARAM_RAW, 'Any remarks for this criterion for the user being assessed', VALUE_OPTIONAL),
                        'levels' => new external_multiple_structure(new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'ID of level'),
                            'criterionid' => new external_value(PARAM_INT, 'ID of the criterion this matches to'),
                            'score' => new external_value(PARAM_RAW, 'What this level is worth'),
                            'definition' => new external_value(PARAM_RAW, 'Definition of the level'),
                            'checked' => new external_value(PARAM_BOOL, 'Selected flag'),
                        ])),
                    ])
                ),
                'timecreated' => new external_value(PARAM_INT, 'The time that the grade was created'),
                'usergrade' => new external_value(PARAM_RAW, 'Current user grade'),
                'maxgrade' => new external_value(PARAM_RAW, 'Max possible grade'),
                'gradedby' => new external_value(PARAM_RAW, 'The assumed grader of this grading instance'),
                'timemodified' => new external_value(PARAM_INT, 'The time that the grade was last updated'),
            ]),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Get a formatted version of the remark/description/etc.
     *
     * @param context $context
     * @param int $definitionid
     * @param string $filearea The file area of the field
     * @param string $text The text to be formatted
     * @param int $format The input format of the string
     * @return string
     */
    protected static function get_formatted_text(context $context, int $definitionid, string $filearea, string $text, int $format): string {
        $formatoptions = [
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
        ];
        [$newtext] = \core_external\util::format_text(
            $text,
            $format,
            $context,
            'grading',
            $filearea,
            $definitionid,
            $formatoptions
        );
        return $newtext;
    }
}
