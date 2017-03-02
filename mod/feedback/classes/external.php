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
 * Feedback external API
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use mod_feedback\external\feedback_summary_exporter;
use mod_feedback\external\feedback_completedtmp_exporter;
use mod_feedback\external\feedback_item_exporter;

/**
 * Feedback external functions
 *
 * @package    mod_feedback
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class mod_feedback_external extends external_api {

    /**
     * Describes the parameters for get_feedbacks_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_feedbacks_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of feedbacks in a provided list of courses.
     * If no list is provided all feedbacks that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and feedbacks
     * @since Moodle 3.3
     */
    public static function get_feedbacks_by_courses($courseids = array()) {
        global $PAGE;

        $warnings = array();
        $returnedfeedbacks = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::get_feedbacks_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);
            $output = $PAGE->get_renderer('core');

            // Get the feedbacks in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $feedbacks = get_all_instances_in_courses("feedback", $courses);
            foreach ($feedbacks as $feedback) {

                $context = context_module::instance($feedback->coursemodule);

                // Remove fields that are not from the feedback (added by get_all_instances_in_courses).
                unset($feedback->coursemodule, $feedback->context, $feedback->visible, $feedback->section, $feedback->groupmode,
                        $feedback->groupingid);

                // Check permissions.
                if (!has_capability('mod/feedback:edititems', $context)) {
                    // Don't return the optional properties.
                    $properties = feedback_summary_exporter::properties_definition();
                    foreach ($properties as $property => $config) {
                        if (!empty($config['optional'])) {
                            unset($feedback->{$property});
                        }
                    }
                }
                $exporter = new feedback_summary_exporter($feedback, array('context' => $context));
                $returnedfeedbacks[] = $exporter->export($output);
            }
        }

        $result = array(
            'feedbacks' => $returnedfeedbacks,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_feedbacks_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_feedbacks_by_courses_returns() {
        return new external_single_structure(
            array(
                'feedbacks' => new external_multiple_structure(
                    feedback_summary_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Utility function for validating a feedback.
     *
     * @param int $feedbackid feedback instance id
     * @return array array containing the feedback persistent, course, context and course module objects
     * @since  Moodle 3.3
     */
    protected static function validate_feedback($feedbackid) {
        global $DB, $USER;

        // Request and permission validation.
        $feedback = $DB->get_record('feedback', array('id' => $feedbackid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($feedback, 'feedback');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        return array($feedback, $course, $cm, $context);
    }

    /**
     * Utility function for validating access to feedback.
     *
     * @param  stdClass   $feedback feedback object
     * @param  stdClass   $course   course object
     * @param  stdClass   $cm       course module
     * @param  stdClass   $context  context object
     * @throws moodle_exception
     * @return feedback_completion feedback completion instance
     * @since  Moodle 3.3
     */
    protected static function validate_feedback_access($feedback,  $course, $cm, $context, $checksubmit = false) {
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $course->id);

        if (!$feedbackcompletion->can_complete()) {
            throw new required_capability_exception($context, 'mod/feedback:complete', 'nopermission', '');
        }

        if (!$feedbackcompletion->is_open()) {
            throw new moodle_exception('feedback_is_not_open', 'feedback');
        }

        if ($feedbackcompletion->is_empty()) {
            throw new moodle_exception('no_items_available_yet', 'feedback');
        }

        if ($checksubmit && !$feedbackcompletion->can_submit()) {
            throw new moodle_exception('this_feedback_is_already_submitted', 'feedback');
        }
        return $feedbackcompletion;
    }

    /**
     * Describes the parameters for get_feedback_access_information.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_feedback_access_information_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id.')
            )
        );
    }

    /**
     * Return access information for a given feedback.
     *
     * @param int $feedbackid feedback instance id
     * @return array of warnings and the access information
     * @since Moodle 3.3
     * @throws  moodle_exception
     */
    public static function get_feedback_access_information($feedbackid) {
        global $PAGE;

        $params = array(
            'feedbackid' => $feedbackid
        );
        $params = self::validate_parameters(self::get_feedback_access_information_parameters(), $params);

        list($feedback, $course, $cm, $context) = self::validate_feedback($params['feedbackid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $course->id);

        $result = array();
        // Capabilities first.
        $result['canviewanalysis'] = $feedbackcompletion->can_view_analysis();
        $result['cancomplete'] = $feedbackcompletion->can_complete();
        $result['cansubmit'] = $feedbackcompletion->can_submit();
        $result['candeletesubmissions'] = has_capability('mod/feedback:deletesubmissions', $context);
        $result['canviewreports'] = has_capability('mod/feedback:viewreports', $context);
        $result['canedititems'] = has_capability('mod/feedback:edititems', $context);

        // Status information.
        $result['isempty'] = $feedbackcompletion->is_empty();
        $result['isopen'] = $feedbackcompletion->is_open();
        $anycourse = ($course->id == SITEID);
        $result['isalreadysubmitted'] = $feedbackcompletion->is_already_submitted($anycourse);
        $result['isanonymous'] = $feedbackcompletion->is_anonymous();

        $result['warnings'] = [];
        return $result;
    }

    /**
     * Describes the get_feedback_access_information return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_feedback_access_information_returns() {
        return new external_single_structure(
            array(
                'canviewanalysis' => new external_value(PARAM_BOOL, 'Whether the user can view the analysis or not.'),
                'cancomplete' => new external_value(PARAM_BOOL, 'Whether the user can complete the feedback or not.'),
                'cansubmit' => new external_value(PARAM_BOOL, 'Whether the user can submit the feedback or not.'),
                'candeletesubmissions' => new external_value(PARAM_BOOL, 'Whether the user can delete submissions or not.'),
                'canviewreports' => new external_value(PARAM_BOOL, 'Whether the user can view the feedback reports or not.'),
                'canedititems' => new external_value(PARAM_BOOL, 'Whether the user can edit feedback items or not.'),
                'isempty' => new external_value(PARAM_BOOL, 'Whether the feedback has questions or not.'),
                'isopen' => new external_value(PARAM_BOOL, 'Whether the feedback has active access time restrictions or not.'),
                'isalreadysubmitted' => new external_value(PARAM_BOOL, 'Whether the feedback is already submitted or not.'),
                'isanonymous' => new external_value(PARAM_BOOL, 'Whether the feedback is anonymous or not.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for view_feedback.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function view_feedback_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'moduleviewed' => new external_value(PARAM_BOOL, 'If we need to mark the module as viewed for completion',
                    VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $feedbackid feedback instance id
     * @param bool $moduleviewed If we need to mark the module as viewed for completion
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function view_feedback($feedbackid, $moduleviewed = false) {

        $params = array('feedbackid' => $feedbackid, 'moduleviewed' => $moduleviewed);
        $params = self::validate_parameters(self::view_feedback_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context) = self::validate_feedback($params['feedbackid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $course->id);

        // Trigger module viewed event.
        $feedbackcompletion->trigger_module_viewed($course);
        if ($params['moduleviewed']) {
            if (!$feedbackcompletion->is_open()) {
                throw new moodle_exception('feedback_is_not_open', 'feedback');
            }
            // Mark activity viewed for completion-tracking.
            $feedbackcompletion->set_module_viewed($course);
        }

        $result = array(
            'status' => true,
            'warnings' => $warnings,
        );
        return $result;
    }

    /**
     * Describes the view_feedback return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function view_feedback_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_current_completed_tmp.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_current_completed_tmp_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
            )
        );
    }

    /**
     * Returns the temporary completion record for the current user.
     *
     * @param int $feedbackid feedback instance id
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_current_completed_tmp($feedbackid) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid);
        $params = self::validate_parameters(self::get_current_completed_tmp_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context) = self::validate_feedback($params['feedbackid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $course->id);

        if ($completed = $feedbackcompletion->get_current_completed_tmp()) {
            $exporter = new feedback_completedtmp_exporter($completed);
            return array(
                'feedback' => $exporter->export($PAGE->get_renderer('core')),
                'warnings' => $warnings,
            );
        }
        throw new moodle_exception('not_started', 'feedback');
    }

    /**
     * Describes the get_current_completed_tmp return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_current_completed_tmp_returns() {
        return new external_single_structure(
            array(
                'feedback' => feedback_completedtmp_exporter::get_read_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_items.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_items_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
            )
        );
    }

    /**
     * Returns the items (questions) in the given feedback.
     *
     * @param int $feedbackid feedback instance id
     * @return array of warnings and feedbacks
     * @since Moodle 3.3
     */
    public static function get_items($feedbackid) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid);
        $params = self::validate_parameters(self::get_items_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context) = self::validate_feedback($params['feedbackid']);
        self::validate_feedback_access($feedback,  $course, $cm, $context);

        $feedbackstructure = new mod_feedback_structure($feedback, $cm, $course->id);
        $returneditems = array();
        if ($items = $feedbackstructure->get_items()) {
            foreach ($items as $item) {
                $itemnumber = empty($item->itemnr) ? null : $item->itemnr;
                unset($item->itemnr);   // Added by the function, not part of the record.
                $exporter = new feedback_item_exporter($item, array('context' => $context, 'itemnumber' => $itemnumber));
                $returneditems[] = $exporter->export($PAGE->get_renderer('core'));
            }
        }

        $result = array(
            'items' => $returneditems,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_items return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_items_returns() {
        return new external_single_structure(
            array(
                'items' => new external_multiple_structure(
                    feedback_item_exporter::get_read_structure()
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for launch_feedback.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function launch_feedback_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
            )
        );
    }

    /**
     * Starts or continues a feedback submission
     *
     * @param array $feedbackid feedback instance id
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function launch_feedback($feedbackid) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid);
        $params = self::validate_parameters(self::launch_feedback_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context) = self::validate_feedback($params['feedbackid']);
        // Check we can do a new submission (or continue an existing).
        $feedbackcompletion = self::validate_feedback_access($feedback,  $course, $cm, $context, true);

        $gopage = $feedbackcompletion->get_resume_page();
        if ($gopage === null) {
            $gopage = -1; // Last page.
        }

        $result = array(
            'gopage' => $gopage,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the launch_feedback return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function launch_feedback_returns() {
        return new external_single_structure(
            array(
                'gopage' => new external_value(PARAM_INT, 'The next page to go (-1 if we were already in the last page). 0 for first page.'),
                'warnings' => new external_warnings(),
            )
        );
    }
}
