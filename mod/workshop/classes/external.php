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
 * Workshop external API
 *
 * @package    mod_workshop
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/mod/workshop/locallib.php');

use mod_workshop\external\workshop_summary_exporter;

/**
 * Workshop external functions
 *
 * @package    mod_workshop
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.4
 */
class mod_workshop_external extends external_api {

    /**
     * Describes the parameters for get_workshops_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function get_workshops_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of workshops in a provided list of courses.
     * If no list is provided all workshops that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and workshops
     * @since Moodle 3.4
     */
    public static function get_workshops_by_courses($courseids = array()) {
        global $PAGE;

        $warnings = array();
        $returnedworkshops = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_workshops_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);
            $output = $PAGE->get_renderer('core');

            // Get the workshops in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $workshops = get_all_instances_in_courses("workshop", $courses);
            foreach ($workshops as $workshop) {

                $context = context_module::instance($workshop->coursemodule);
                // Remove fields that are not from the workshop (added by get_all_instances_in_courses).
                unset($workshop->coursemodule, $workshop->context, $workshop->visible, $workshop->section, $workshop->groupmode,
                        $workshop->groupingid);

                $exporter = new workshop_summary_exporter($workshop, array('context' => $context));
                $returnedworkshops[] = $exporter->export($output);
            }
        }

        $result = array(
            'workshops' => $returnedworkshops,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_workshops_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function get_workshops_by_courses_returns() {
        return new external_single_structure(
            array(
                'workshops' => new external_multiple_structure(
                    workshop_summary_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Utility function for validating a workshop.
     *
     * @param int $workshopid workshop instance id
     * @return array array containing the workshop object, course, context and course module objects
     * @since  Moodle 3.4
     */
    protected static function validate_workshop($workshopid) {
        global $DB, $USER;

        // Request and permission validation.
        $workshop = $DB->get_record('workshop', array('id' => $workshopid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($workshop, 'workshop');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $workshop = new workshop($workshop, $cm, $course);

        return array($workshop, $course, $cm, $context);
    }


    /**
     * Describes the parameters for get_workshop_access_information.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.4
     */
    public static function get_workshop_access_information_parameters() {
        return new external_function_parameters (
            array(
                'workshopid' => new external_value(PARAM_INT, 'Workshop instance id.')
            )
        );
    }

    /**
     * Return access information for a given workshop.
     *
     * @param int $workshopid workshop instance id
     * @return array of warnings and the access information
     * @since Moodle 3.4
     * @throws  moodle_exception
     */
    public static function get_workshop_access_information($workshopid) {
        global $USER;

        $params = self::validate_parameters(self::get_workshop_access_information_parameters(), array('workshopid' => $workshopid));

        list($workshop, $course, $cm, $context) = self::validate_workshop($params['workshopid']);

        $result = array();
        // Return all the available capabilities.
        $capabilities = load_capability_def('mod_workshop');
        foreach ($capabilities as $capname => $capdata) {
            // Get fields like cansubmit so it is consistent with the access_information function implemented in other modules.
            $field = 'can' . str_replace('mod/workshop:', '', $capname);
            $result[$field] = has_capability($capname, $context);
        }

        // Now, specific features access information.
        $result['creatingsubmissionallowed'] = $workshop->creating_submission_allowed($USER->id);
        $result['modifyingsubmissionallowed'] = $workshop->modifying_submission_allowed($USER->id);
        $result['assessingallowed'] = $workshop->assessing_allowed($USER->id);
        $result['assessingexamplesallowed'] = $workshop->assessing_examples_allowed();
        if (is_null($result['assessingexamplesallowed'])) {
            $result['assessingexamplesallowed'] = false;
        }

        $result['warnings'] = array();
        return $result;
    }

    /**
     * Describes the get_workshop_access_information return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function get_workshop_access_information_returns() {

        $structure = array(
            'creatingsubmissionallowed' => new external_value(PARAM_BOOL,
                'Is the given user allowed to create their submission?'),
            'modifyingsubmissionallowed' => new external_value(PARAM_BOOL,
                'Is the user allowed to modify his existing submission?'),
            'assessingallowed' => new external_value(PARAM_BOOL,
                'Is the user allowed to create/edit his assessments?'),
            'assessingexamplesallowed' => new external_value(PARAM_BOOL,
                'Are reviewers allowed to create/edit their assessments of the example submissions?.'),
            'warnings' => new external_warnings()
        );

        $capabilities = load_capability_def('mod_workshop');
        foreach ($capabilities as $capname => $capdata) {
            // Get fields like cansubmit so it is consistent with the access_information function implemented in other modules.
            $field = 'can' . str_replace('mod/workshop:', '', $capname);
            $structure[$field] = new external_value(PARAM_BOOL, 'Whether the user has the capability ' . $capname . ' allowed.');
        }

        return new external_single_structure($structure);
    }

    /**
     * Describes the parameters for get_user_plan.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.4
     */
    public static function get_user_plan_parameters() {
        return new external_function_parameters (
            array(
                'workshopid' => new external_value(PARAM_INT, 'Workshop instance id.'),
                'userid' => new external_value(PARAM_INT, 'User id (empty or 0 for current user).', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return the planner information for the given user.
     *
     * @param int $workshopid workshop instance id
     * @param int $userid user id
     * @return array of warnings and the user plan
     * @since Moodle 3.4
     * @throws  moodle_exception
     */
    public static function get_user_plan($workshopid, $userid = 0) {
        global $USER;

        $params = array(
            'workshopid' => $workshopid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_user_plan_parameters(), $params);

        list($workshop, $course, $cm, $context) = self::validate_workshop($params['workshopid']);

        // Extra checks so only users with permissions can view other users plans.
        if (empty($params['userid']) || $params['userid'] == $USER->id) {
            $userid = $USER->id;
        } else {
            require_capability('moodle/course:manageactivities', $context);
            $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
            core_user::require_active_user($user);
            if (!$workshop->check_group_membership($user->id)) {
                throw new moodle_exception('notingroup');
            }
            $userid = $user->id;
        }

        // Get the user plan information ready for external functions.
        $userplan = new workshop_user_plan($workshop, $userid);
        $userplan = array('phases' => $userplan->phases, 'examples' => $userplan->get_examples());
        foreach ($userplan['phases'] as $phasecode => $phase) {
            $phase->code = $phasecode;
            $userplan['phases'][$phasecode] = (array) $phase;
            foreach ($userplan['phases'][$phasecode]['tasks'] as $taskcode => $task) {
                $task->code = $taskcode;
                if ($task->link instanceof moodle_url) {
                    $task->link = $task->link->out(false);
                }
                $userplan['phases'][$phasecode]['tasks'][$taskcode] = (array) $task;
            }
            foreach ($userplan['phases'][$phasecode]['actions'] as $actioncode => $action) {
                if ($action->url instanceof moodle_url) {
                    $action->url = $action->url->out(false);
                }
                $userplan['phases'][$phasecode]['actions'][$actioncode] = (array) $action;
            }
        }

        $result['userplan'] = $userplan;
        $result['warnings'] = array();
        return $result;
    }

    /**
     * Describes the get_user_plan return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function get_user_plan_returns() {
        return new external_single_structure(
            array(
                'userplan' => new external_single_structure(
                    array(
                        'phases' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'code' => new external_value(PARAM_INT, 'Phase code.'),
                                    'title' => new external_value(PARAM_NOTAGS, 'Phase title.'),
                                    'active' => new external_value(PARAM_BOOL, 'Whether is the active task.'),
                                    'tasks' => new external_multiple_structure(
                                        new external_single_structure(
                                            array(
                                                'code' => new external_value(PARAM_ALPHA, 'Task code.'),
                                                'title' => new external_value(PARAM_RAW, 'Task title.'),
                                                'link' => new external_value(PARAM_URL, 'Link to task.'),
                                                'details' => new external_value(PARAM_RAW, 'Task details.', VALUE_OPTIONAL),
                                                'completed' => new external_value(PARAM_NOTAGS,
                                                    'Completion information (maybe empty, maybe a boolean or generic info.'),
                                            )
                                        )
                                    ),
                                    'actions' => new external_multiple_structure(
                                        new external_single_structure(
                                            array(
                                                'type' => new external_value(PARAM_ALPHA, 'Action type.', VALUE_OPTIONAL),
                                                'label' => new external_value(PARAM_RAW, 'Action label.', VALUE_OPTIONAL),
                                                'url' => new external_value(PARAM_URL, 'Link to action.'),
                                                'method' => new external_value(PARAM_ALPHA, 'Get or post.', VALUE_OPTIONAL),
                                            )
                                        )
                                    ),
                                )
                            )
                        ),
                        'examples' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'id' => new external_value(PARAM_INT, 'Example submission id.'),
                                    'title' => new external_value(PARAM_RAW, 'Example submission title.'),
                                    'assessmentid' => new external_value(PARAM_INT, 'Example submission assessment id.'),
                                    'grade' => new external_value(PARAM_FLOAT, 'The submission grade.'),
                                    'gradinggrade' => new external_value(PARAM_FLOAT, 'The assessment grade.'),
                                )
                            )
                        ),
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_workshop.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function view_workshop_parameters() {
        return new external_function_parameters (
            array(
                'workshopid' => new external_value(PARAM_INT, 'Workshop instance id'),
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $workshopid workshop instance id
     * @return array of warnings and status result
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function view_workshop($workshopid) {

        $params = array('workshopid' => $workshopid);
        $params = self::validate_parameters(self::view_workshop_parameters(), $params);
        $warnings = array();

        list($workshop, $course, $cm, $context) = self::validate_workshop($params['workshopid']);

        $workshop->set_module_viewed();

        $result = array(
            'status' => true,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the view_workshop return value.
     *
     * @return external_single_structure
     * @since Moodle 3.4
     */
    public static function view_workshop_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }
}
