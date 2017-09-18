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
        $result['examplesassessed'] = $workshop->check_examples_assessed($USER->id);

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
            'examplesassessed' => new external_value(PARAM_BOOL,
                'Whether the given user has assessed all his required examples (always true if there are no examples to assess).'),
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

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function add_submission_parameters() {
        return new external_function_parameters(array(
            'workshopid' => new external_value(PARAM_INT, 'Workshop id'),
            'title' => new external_value(PARAM_TEXT, 'Submission title'),
            'content' => new external_value(PARAM_RAW, 'Submission text content', VALUE_DEFAULT, ''),
            'contentformat' => new external_value(PARAM_INT, 'The format used for the content', VALUE_DEFAULT, FORMAT_MOODLE),
            'inlineattachmentsid' => new external_value(PARAM_INT, 'The draft file area id for inline attachments in the content',
                VALUE_DEFAULT, 0),
            'attachmentsid' => new external_value(PARAM_INT, 'The draft file area id for attachments', VALUE_DEFAULT, 0),
        ));
    }

    /**
     * Add a new submission to a given workshop.
     *
     * @param int $workshopid the workshop id
     * @param string $title             the submission title
     * @param string  $content          the submission text content
     * @param int  $contentformat       the format used for the content
     * @param int $inlineattachmentsid  the draft file area id for inline attachments in the content
     * @param int $attachmentsid        the draft file area id for attachments
     * @return array Containing the new created submission id and warnings.
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function add_submission($workshopid, $title, $content = '', $contentformat = FORMAT_MOODLE,
            $inlineattachmentsid = 0, $attachmentsid = 0) {
        global $USER;

        $params = self::validate_parameters(self::add_submission_parameters(), array(
            'workshopid' => $workshopid,
            'title' => $title,
            'content' => $content,
            'contentformat' => $contentformat,
            'inlineattachmentsid' => $inlineattachmentsid,
            'attachmentsid' => $attachmentsid,
        ));
        $warnings = array();

        // Get and validate the workshop.
        list($workshop, $course, $cm, $context) = self::validate_workshop($params['workshopid']);
        require_capability('mod/workshop:submit', $context);

        // Check if we can submit now.
        $canaddsubmission = $workshop->creating_submission_allowed($USER->id);
        $canaddsubmission = $canaddsubmission && $workshop->check_examples_assessed($USER->id);
        if (!$canaddsubmission) {
            throw new moodle_exception('nopermissions', 'error', '', 'add submission');
        }

        // Prepare the submission object.
        $submission = new stdClass;
        $submission->id = null;
        $submission->cmid = $cm->id;
        $submission->example = 0;
        $submission->title = trim($params['title']);
        $submission->content_editor = array(
            'text' => $params['content'],
            'format' => $params['contentformat'],
            'itemid' => $params['inlineattachmentsid'],
        );
        $submission->attachment_filemanager = $params['attachmentsid'];

        if (empty($submission->title)) {
            throw new moodle_exception('errorinvalidparam', 'webservice', '', 'title');
        }

        $errors = $workshop->validate_submission_data((array) $submission);
        // We can get several errors, return them in warnings.
        if (!empty($errors)) {
            $submission->id = 0;
            foreach ($errors as $itemname => $message) {
                $warnings[] = array(
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message)
                );
            }
            return array(
                'status' => false,
                'warnings' => $warnings
            );
        } else {
            $submission->id = $workshop->edit_submission($submission);
            return array(
                'status' => true,
                'submissionid' => $submission->id,
                'warnings' => $warnings
            );
        }
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function add_submission_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'True if the submission was created false otherwise.'),
            'submissionid' => new external_value(PARAM_INT, 'New workshop submission id.', VALUE_OPTIONAL),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function update_submission_parameters() {
        return new external_function_parameters(array(
            'submissionid' => new external_value(PARAM_INT, 'Submission id'),
            'title' => new external_value(PARAM_TEXT, 'Submission title'),
            'content' => new external_value(PARAM_RAW, 'Submission text content', VALUE_DEFAULT, ''),
            'contentformat' => new external_value(PARAM_INT, 'The format used for the content', VALUE_DEFAULT, FORMAT_MOODLE),
            'inlineattachmentsid' => new external_value(PARAM_INT, 'The draft file area id for inline attachments in the content',
                VALUE_DEFAULT, 0),
            'attachmentsid' => new external_value(PARAM_INT, 'The draft file area id for attachments', VALUE_DEFAULT, 0),
        ));
    }


    /**
     * Updates the given submission.
     *
     * @param int $submissionid         the submission id
     * @param string $title             the submission title
     * @param string  $content          the submission text content
     * @param int  $contentformat       the format used for the content
     * @param int $inlineattachmentsid  the draft file area id for inline attachments in the content
     * @param int $attachmentsid        the draft file area id for attachments
     * @return array whether the submission was updated and warnings.
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function update_submission($submissionid, $title, $content = '', $contentformat = FORMAT_MOODLE,
            $inlineattachmentsid = 0, $attachmentsid = 0) {
        global $USER, $DB;

        $params = self::validate_parameters(self::update_submission_parameters(), array(
            'submissionid' => $submissionid,
            'title' => $title,
            'content' => $content,
            'contentformat' => $contentformat,
            'inlineattachmentsid' => $inlineattachmentsid,
            'attachmentsid' => $attachmentsid,
        ));
        $warnings = array();

        // Get and validate the submission and workshop.
        $submission = $DB->get_record('workshop_submissions', array('id' => $params['submissionid']), '*', MUST_EXIST);
        list($workshop, $course, $cm, $context) = self::validate_workshop($submission->workshopid);
        require_capability('mod/workshop:submit', $context);

        // Check if we can update the submission.
        $canupdatesubmission = $submission->authorid == $USER->id;
        $canupdatesubmission = $canupdatesubmission && $workshop->modifying_submission_allowed($USER->id);
        $canupdatesubmission = $canupdatesubmission && $workshop->check_examples_assessed($USER->id);
        if (!$canupdatesubmission) {
            throw new moodle_exception('nopermissions', 'error', '', 'update submission');
        }

        // Prepare the submission object.
        $submission->title = trim($params['title']);
        if (empty($submission->title)) {
            throw new moodle_exception('errorinvalidparam', 'webservice', '', 'title');
        }
        $submission->content_editor = array(
            'text' => $params['content'],
            'format' => $params['contentformat'],
            'itemid' => $params['inlineattachmentsid'],
        );
        $submission->attachment_filemanager = $params['attachmentsid'];

        $errors = $workshop->validate_submission_data((array) $submission);
        // We can get several errors, return them in warnings.
        if (!empty($errors)) {
            $status = false;
            foreach ($errors as $itemname => $message) {
                $warnings[] = array(
                    'item' => $itemname,
                    'itemid' => 0,
                    'warningcode' => 'fielderror',
                    'message' => s($message)
                );
            }
        } else {
            $status = true;
            $submission->id = $workshop->edit_submission($submission);
        }

        return array(
            'status' => $status,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function update_submission_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'True if the submission was updated false otherwise.'),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.4
     */
    public static function delete_submission_parameters() {
        return new external_function_parameters(
            array(
                'submissionid' => new external_value(PARAM_INT, 'Submission id'),
            )
        );
    }


    /**
     * Deletes the given submission.
     *
     * @param int $submissionid the submission id.
     * @return array containing the result status and warnings.
     * @since Moodle 3.4
     * @throws moodle_exception
     */
    public static function delete_submission($submissionid) {
        global $USER, $DB;

        $params = self::validate_parameters(self::delete_submission_parameters(), array('submissionid' => $submissionid));
        $warnings = array();

        // Get and validate the submission and workshop.
        $submission = $DB->get_record('workshop_submissions', array('id' => $params['submissionid']), '*', MUST_EXIST);
        list($workshop, $course, $cm, $context) = self::validate_workshop($submission->workshopid);

        // Check if we can delete the submission.
        if (!has_capability('mod/workshop:deletesubmissions', $context)) {
            require_capability('mod/workshop:submit', $context);
            // We can delete our own submission, on time and not yet assessed.
            $candeletesubmission = $submission->authorid == $USER->id;
            $candeletesubmission = $candeletesubmission && $workshop->modifying_submission_allowed($USER->id);
            $candeletesubmission = $candeletesubmission && count($workshop->get_assessments_of_submission($submission->id)) == 0;
            if (!$candeletesubmission) {
                throw new moodle_exception('nopermissions', 'error', '', 'delete submission');
            }
        }

        $workshop->delete_submission($submission);

        return array(
            'status' => true,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.4
     */
    public static function delete_submission_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'True if the submission was deleted.'),
            'warnings' => new external_warnings()
        ));
    }
}
