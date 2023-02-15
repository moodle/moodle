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

use mod_feedback\external\feedback_summary_exporter;
use mod_feedback\external\feedback_completedtmp_exporter;
use mod_feedback\external\feedback_item_exporter;
use mod_feedback\external\feedback_valuetmp_exporter;
use mod_feedback\external\feedback_value_exporter;
use mod_feedback\external\feedback_completed_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\util;

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

            list($courses, $warnings) = util::validate_courses($params['courseids'], $mycourses);
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
     * @param int $courseid courseid course where user completes the feedback (for site feedbacks only)
     * @return array containing the feedback, feedback course, context, course module and the course where is being completed.
     * @throws moodle_exception
     * @since  Moodle 3.3
     */
    protected static function validate_feedback($feedbackid, $courseid = 0) {
        global $DB, $USER;

        // Request and permission validation.
        $feedback = $DB->get_record('feedback', array('id' => $feedbackid), '*', MUST_EXIST);
        list($feedbackcourse, $cm) = get_course_and_cm_from_instance($feedback, 'feedback');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Set default completion course.
        $completioncourse = (object) array('id' => 0);
        if ($feedbackcourse->id == SITEID && $courseid) {
            $completioncourse = get_course($courseid);
            self::validate_context(context_course::instance($courseid));

            $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $courseid);
            if (!$feedbackcompletion->check_course_is_mapped()) {
                throw new moodle_exception('cannotaccess', 'mod_feedback');
            }
        }

        return array($feedback, $feedbackcourse, $cm, $context, $completioncourse);
    }

    /**
     * Utility function for validating access to feedback.
     *
     * @param  stdClass   $feedback feedback object
     * @param  stdClass   $course   course where user completes the feedback (for site feedbacks only)
     * @param  stdClass   $cm       course module
     * @param  stdClass   $context  context object
     * @throws moodle_exception
     * @return mod_feedback_completion feedback completion instance
     * @since  Moodle 3.3
     */
    protected static function validate_feedback_access($feedback, $course, $cm, $context, $checksubmit = false) {
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
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id.'),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return access information for a given feedback.
     *
     * @param int $feedbackid feedback instance id
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and the access information
     * @since Moodle 3.3
     * @throws  moodle_exception
     */
    public static function get_feedback_access_information($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array(
            'feedbackid' => $feedbackid,
            'courseid' => $courseid,
        );
        $params = self::validate_parameters(self::get_feedback_access_information_parameters(), $params);

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

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
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $feedbackid feedback instance id
     * @param bool $moduleviewed If we need to mark the module as viewed for completion
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function view_feedback($feedbackid, $moduleviewed = false, $courseid = 0) {

        $params = array('feedbackid' => $feedbackid, 'moduleviewed' => $moduleviewed, 'courseid' => $courseid);
        $params = self::validate_parameters(self::view_feedback_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

        // Trigger module viewed event.
        $feedbackcompletion->trigger_module_viewed();
        if ($params['moduleviewed']) {
            if (!$feedbackcompletion->is_open()) {
                throw new moodle_exception('feedback_is_not_open', 'feedback');
            }
            // Mark activity viewed for completion-tracking.
            $feedbackcompletion->set_module_viewed();
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
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns the temporary completion record for the current user.
     *
     * @param int $feedbackid feedback instance id
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and status result
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_current_completed_tmp($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_current_completed_tmp_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

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
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Returns the items (questions) in the given feedback.
     *
     * @param int $feedbackid feedback instance id
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and feedbacks
     * @since Moodle 3.3
     */
    public static function get_items($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_items_parameters(), $params);
        $warnings = array();
        $returneditems = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);

        $userhasaccess = true;
        try {
            // Check the user has access to the feedback.
            self::validate_feedback_access($feedback, $completioncourse, $cm, $context, true);
        } catch (moodle_exception $e) {
            $userhasaccess = false;
            $warnings[] = [
                'item' => $feedback->id,
                'warningcode' => clean_param($e->errorcode, PARAM_ALPHANUM),
                'message' => $e->getMessage(),
            ];
        }

        // For consistency with the web behaviour, the items should be returned only when the user can edit or view reports (to
        // include non-editing teachers too).
        $capabilities = [
            'mod/feedback:edititems',
            'mod/feedback:viewreports',
        ];
        if ($userhasaccess || has_any_capability($capabilities, $context)) {
            // Remove previous warnings because, although the user might not have access, they have the proper capability.
            $warnings = [];
            $feedbackstructure = new mod_feedback_structure($feedback, $cm, $completioncourse->id);
            if ($items = $feedbackstructure->get_items()) {
                foreach ($items as $item) {
                    $itemnumber = empty($item->itemnr) ? null : $item->itemnr;
                    unset($item->itemnr);   // Added by the function, not part of the record.
                    $exporter = new feedback_item_exporter($item, array('context' => $context, 'itemnumber' => $itemnumber));
                    $returneditems[] = $exporter->export($PAGE->get_renderer('core'));
                }
            }
        } else if ($userhasaccess) {
            $warnings[] = [
                'item' => $feedback->id,
                'warningcode' => 'nopermission',
                'message' => 'nopermission',
            ];
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
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Starts or continues a feedback submission
     *
     * @param array $feedbackid feedback instance id
     * @param int $courseid course where user completes a feedback (for site feedbacks only).
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function launch_feedback($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::launch_feedback_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        // Check we can do a new submission (or continue an existing).
        $feedbackcompletion = self::validate_feedback_access($feedback, $completioncourse, $cm, $context, true);

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

    /**
     * Describes the parameters for get_page_items.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_page_items_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'page' => new external_value(PARAM_INT, 'The page to get starting by 0'),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get a single feedback page items.
     *
     * @param int $feedbackid feedback instance id
     * @param int $page the page to get starting by 0
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function get_page_items($feedbackid, $page, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'page' => $page, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_page_items_parameters(), $params);
        $warnings = array();
        $returneditems = array();
        $hasprevpage = false;
        $hasnextpage = false;

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);

        $userhasaccess = true;
        $feedbackcompletion = null;
        try {
            // Check the user has access to the feedback.
            $feedbackcompletion = self::validate_feedback_access($feedback, $completioncourse, $cm, $context, true);
        } catch (moodle_exception $e) {
            $userhasaccess = false;
            $warnings[] = [
                'item' => $feedback->id,
                'warningcode' => str_replace('_', '', $e->errorcode),
                'message' => $e->getMessage(),
            ];
        }

        // For consistency with the web behaviour, the items should be returned only when the user can edit or view reports (to
        // include non-editing teachers too).
        $capabilities = [
            'mod/feedback:edititems',
            'mod/feedback:viewreports',
        ];
        if ($userhasaccess || has_any_capability($capabilities, $context)) {
            // Remove previous warnings because, although the user might not have access, they have the proper capability.
            $warnings = [];

            if ($feedbackcompletion == null) {
                $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);
            }

            $page = $params['page'];
            $pages = $feedbackcompletion->get_pages();
            $pageitems = $pages[$page];
            $hasnextpage = $page < count($pages) - 1; // Until we complete this page we can not trust get_next_page().
            $hasprevpage = $page && ($feedbackcompletion->get_previous_page($page, false) !== null);

            foreach ($pageitems as $item) {
                $itemnumber = empty($item->itemnr) ? null : $item->itemnr;
                unset($item->itemnr);   // Added by the function, not part of the record.
                $exporter = new feedback_item_exporter($item, array('context' => $context, 'itemnumber' => $itemnumber));
                $returneditems[] = $exporter->export($PAGE->get_renderer('core'));
            }
        } else if ($userhasaccess) {
            $warnings[] = [
                'item' => $feedback->id,
                'warningcode' => 'nopermission',
                'message' => get_string('nopermission', 'mod_feedback'),
            ];
        }

        $result = array(
            'items' => $returneditems,
            'hasprevpage' => $hasprevpage,
            'hasnextpage' => $hasnextpage,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_page_items return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_page_items_returns() {
        return new external_single_structure(
            array(
                'items' => new external_multiple_structure(
                    feedback_item_exporter::get_read_structure()
                ),
                'hasprevpage' => new external_value(PARAM_BOOL, 'Whether is a previous page.'),
                'hasnextpage' => new external_value(PARAM_BOOL, 'Whether there are more pages.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for process_page.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function process_page_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id.'),
                'page' => new external_value(PARAM_INT, 'The page being processed.'),
                'responses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_NOTAGS, 'The response name (usually type[index]_id).'),
                            'value' => new external_value(PARAM_RAW, 'The response value.'),
                        )
                    ), 'The data to be processed.', VALUE_DEFAULT, array()
                ),
                'goprevious' => new external_value(PARAM_BOOL, 'Whether we want to jump to previous page.', VALUE_DEFAULT, false),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Process a jump between pages.
     *
     * @param array $feedbackid feedback instance id
     * @param array $page the page being processed
     * @param array $responses the responses to be processed
     * @param bool $goprevious whether we want to jump to previous page
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function process_page($feedbackid, $page, $responses = [], $goprevious = false, $courseid = 0) {
        global $USER, $SESSION;

        $params = array('feedbackid' => $feedbackid, 'page' => $page, 'responses' => $responses, 'goprevious' => $goprevious,
            'courseid' => $courseid);
        $params = self::validate_parameters(self::process_page_parameters(), $params);
        $warnings = array();
        $siteaftersubmit = $completionpagecontents = '';

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        // Check we can do a new submission (or continue an existing).
        $feedbackcompletion = self::validate_feedback_access($feedback, $completioncourse, $cm, $context, true);

        // Create the $_POST object required by the feedback question engine.
        $_POST = array();
        foreach ($responses as $response) {
            // First check if we are handling array parameters.
            if (preg_match('/(.+)\[(.+)\]$/', $response['name'], $matches)) {
                $_POST[$matches[1]][$matches[2]] = $response['value'];
            } else {
                $_POST[$response['name']] = $response['value'];
            }
        }
        // Force fields.
        $_POST['id'] = $cm->id;
        $_POST['courseid'] = $courseid;
        $_POST['gopage'] = $params['page'];
        $_POST['_qf__mod_feedback_complete_form'] = 1;

        // Determine where to go, backwards or forward.
        if (!$params['goprevious']) {
            $_POST['gonextpage'] = 1;   // Even if we are saving values we need this set.
            if ($feedbackcompletion->get_next_page($params['page'], false) === null) {
                $_POST['savevalues'] = 1;   // If there is no next page, it means we are finishing the feedback.
            }
        }

        // Ignore sesskey (deep in some APIs), the request is already validated.
        $USER->ignoresesskey = true;
        feedback_init_feedback_session();
        $SESSION->feedback->is_started = true;

        $feedbackcompletion->process_page($params['page'], $params['goprevious']);
        $completed = $feedbackcompletion->just_completed();
        if ($completed) {
            $jumpto = 0;
            if ($feedback->page_after_submit) {
                $completionpagecontents = $feedbackcompletion->page_after_submit();
            }

            if ($feedback->site_after_submit) {
                $siteaftersubmit = feedback_encode_target_url($feedback->site_after_submit);
            }
        } else {
            $jumpto = $feedbackcompletion->get_jumpto();
        }

        $result = array(
            'jumpto' => $jumpto,
            'completed' => $completed,
            'completionpagecontents' => $completionpagecontents,
            'siteaftersubmit' => $siteaftersubmit,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the process_page return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function process_page_returns() {
        return new external_single_structure(
            array(
                'jumpto' => new external_value(PARAM_INT, 'The page to jump to.'),
                'completed' => new external_value(PARAM_BOOL, 'If the user completed the feedback.'),
                'completionpagecontents' => new external_value(PARAM_RAW, 'The completion page contents.'),
                'siteaftersubmit' => new external_value(PARAM_RAW, 'The link (could be relative) to show after submit.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_analysis.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_analysis_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group',
                                                VALUE_DEFAULT, 0),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Retrieves the feedback analysis.
     *
     * @param array $feedbackid feedback instance id
     * @param int $groupid group id, 0 means that the function will determine the user group
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function get_analysis($feedbackid, $groupid = 0, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'groupid' => $groupid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_analysis_parameters(), $params);
        $warnings = $itemsdata = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);

        // Check permissions.
        $feedbackstructure = new mod_feedback_structure($feedback, $cm, $completioncourse->id);
        if (!$feedbackstructure->can_view_analysis()) {
            throw new required_capability_exception($context, 'mod/feedback:viewanalysepage', 'nopermission', '');
        }

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                $groupid = groups_get_activity_group($cm);
                // Determine is the group is visible to user (this is particullary for the group 0 -> all groups).
                if (!groups_group_visible($groupid, $course, $cm)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }

        // Summary data.
        $summary = new mod_feedback\output\summary($feedbackstructure, $groupid);
        $summarydata = $summary->export_for_template($PAGE->get_renderer('core'));

        $checkanonymously = true;
        if ($groupid > 0 AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES) {
            $completedcount = $feedbackstructure->count_completed_responses($groupid);
            if ($completedcount < FEEDBACK_MIN_ANONYMOUS_COUNT_IN_GROUP) {
                $checkanonymously = false;
            }
        }

        if ($checkanonymously) {
            // Get the items of the feedback.
            $items = $feedbackstructure->get_items(true);
            foreach ($items as $item) {
                $itemobj = feedback_get_item_class($item->typ);
                $itemnumber = empty($item->itemnr) ? null : $item->itemnr;
                unset($item->itemnr);   // Added by the function, not part of the record.
                $exporter = new feedback_item_exporter($item, array('context' => $context, 'itemnumber' => $itemnumber));

                $itemsdata[] = array(
                    'item' => $exporter->export($PAGE->get_renderer('core')),
                    'data' => $itemobj->get_analysed_for_external($item, $groupid),
                );
            }
        } else {
            $warnings[] = array(
                'item' => 'feedback',
                'itemid' => $feedback->id,
                'warningcode' => 'insufficientresponsesforthisgroup',
                'message' => s(get_string('insufficient_responses_for_this_group', 'feedback'))
            );
        }

        $result = array(
            'completedcount' => $summarydata->completedcount,
            'itemscount' => $summarydata->itemscount,
            'itemsdata' => $itemsdata,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_analysis return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_analysis_returns() {
        return new external_single_structure(
            array(
            'completedcount' => new external_value(PARAM_INT, 'Number of completed submissions.'),
            'itemscount' => new external_value(PARAM_INT, 'Number of items (questions).'),
            'itemsdata' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'item' => feedback_item_exporter::get_read_structure(),
                        'data' => new external_multiple_structure(
                            new external_value(PARAM_RAW, 'The analysis data (can be json encoded)')
                        ),
                    )
                )
            ),
            'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_unfinished_responses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_unfinished_responses_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id.'),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Retrieves responses from the current unfinished attempt.
     *
     * @param array $feedbackid feedback instance id
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and launch information
     * @since Moodle 3.3
     */
    public static function get_unfinished_responses($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_unfinished_responses_parameters(), $params);
        $warnings = $itemsdata = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

        $responses = array();
        $unfinished = $feedbackcompletion->get_unfinished_responses();
        foreach ($unfinished as $u) {
            $exporter = new feedback_valuetmp_exporter($u);
            $responses[] = $exporter->export($PAGE->get_renderer('core'));
        }

        $result = array(
            'responses' => $responses,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_unfinished_responses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_unfinished_responses_returns() {
        return new external_single_structure(
            array(
            'responses' => new external_multiple_structure(
                feedback_valuetmp_exporter::get_read_structure()
            ),
            'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_finished_responses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_finished_responses_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id.'),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Retrieves responses from the last finished attempt.
     *
     * @param array $feedbackid feedback instance id
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and the responses
     * @since Moodle 3.3
     */
    public static function get_finished_responses($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_finished_responses_parameters(), $params);
        $warnings = $itemsdata = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

        $responses = array();
        // Load and get the responses from the last completed feedback.
        $feedbackcompletion->find_last_completed();
        $unfinished = $feedbackcompletion->get_finished_responses();
        foreach ($unfinished as $u) {
            $exporter = new feedback_value_exporter($u);
            $responses[] = $exporter->export($PAGE->get_renderer('core'));
        }

        $result = array(
            'responses' => $responses,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_finished_responses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_finished_responses_returns() {
        return new external_single_structure(
            array(
            'responses' => new external_multiple_structure(
                feedback_value_exporter::get_read_structure()
            ),
            'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_non_respondents.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_non_respondents_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group.',
                                                VALUE_DEFAULT, 0),
                'sort' => new external_value(PARAM_ALPHA, 'Sort param, must be firstname, lastname or lastaccess (default).',
                                                VALUE_DEFAULT, 'lastaccess'),
                'page' => new external_value(PARAM_INT, 'The page of records to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of records to return per page.', VALUE_DEFAULT, 0),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Retrieves a list of students who didn't submit the feedback.
     *
     * @param int $feedbackid feedback instance id
     * @param int $groupid Group id, 0 means that the function will determine the user group'
     * @param str $sort sort param, must be firstname, lastname or lastaccess (default)
     * @param int $page the page of records to return
     * @param int $perpage the number of records to return per page
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and users ids
     * @since Moodle 3.3
     */
    public static function get_non_respondents($feedbackid, $groupid = 0, $sort = 'lastaccess', $page = 0, $perpage = 0,
            $courseid = 0) {

        global $CFG;
        require_once($CFG->dirroot . '/mod/feedback/lib.php');

        $params = array('feedbackid' => $feedbackid, 'groupid' => $groupid, 'sort' => $sort, 'page' => $page,
            'perpage' => $perpage, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_non_respondents_parameters(), $params);
        $warnings = $nonrespondents = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);
        $completioncourseid = $feedbackcompletion->get_courseid();

        if ($feedback->anonymous != FEEDBACK_ANONYMOUS_NO || $feedback->course == SITEID) {
            throw new moodle_exception('anonymous', 'feedback');
        }

        // Check permissions.
        require_capability('mod/feedback:viewreports', $context);

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                $groupid = groups_get_activity_group($cm);
                // Determine is the group is visible to user (this is particullary for the group 0 -> all groups).
                if (!groups_group_visible($groupid, $course, $cm)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }

        if ($params['sort'] !== 'firstname' && $params['sort'] !== 'lastname' && $params['sort'] !== 'lastaccess') {
            throw new invalid_parameter_exception('Invalid sort param, must be firstname, lastname or lastaccess.');
        }

        // Check if we are page filtering.
        if ($params['perpage'] == 0) {
            $page = $params['page'];
            $perpage = FEEDBACK_DEFAULT_PAGE_COUNT;
        } else {
            $perpage = $params['perpage'];
            $page = $perpage * $params['page'];
        }
        $users = feedback_get_incomplete_users($cm, $groupid, $params['sort'], $page, $perpage, true);
        foreach ($users as $user) {
            $nonrespondents[] = [
                'courseid' => $completioncourseid,
                'userid'   => $user->id,
                'fullname' => fullname($user),
                'started'  => $user->feedbackstarted
            ];
        }

        $result = array(
            'users' => $nonrespondents,
            'total' => feedback_count_incomplete_users($cm, $groupid),
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_non_respondents return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_non_respondents_returns() {
        return new external_single_structure(
            array(
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_INT, 'Course id'),
                            'userid' => new external_value(PARAM_INT, 'The user id'),
                            'fullname' => new external_value(PARAM_TEXT, 'User full name'),
                            'started' => new external_value(PARAM_BOOL, 'If the user has started the attempt'),
                        )
                    )
                ),
                'total' => new external_value(PARAM_INT, 'Total number of non respondents'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_responses_analysis.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_responses_analysis_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'groupid' => new external_value(PARAM_INT, 'Group id, 0 means that the function will determine the user group',
                                                VALUE_DEFAULT, 0),
                'page' => new external_value(PARAM_INT, 'The page of records to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of records to return per page', VALUE_DEFAULT, 0),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Return the feedback user responses.
     *
     * @param int $feedbackid feedback instance id
     * @param int $groupid Group id, 0 means that the function will determine the user group
     * @param int $page the page of records to return
     * @param int $perpage the number of records to return per page
     * @param int $courseid course where user completes the feedback (for site feedbacks only)
     * @return array of warnings and users attemps and responses
     * @throws moodle_exception
     * @since Moodle 3.3
     */
    public static function get_responses_analysis($feedbackid, $groupid = 0, $page = 0, $perpage = 0, $courseid = 0) {

        $params = array('feedbackid' => $feedbackid, 'groupid' => $groupid, 'page' => $page, 'perpage' => $perpage,
            'courseid' => $courseid);
        $params = self::validate_parameters(self::get_responses_analysis_parameters(), $params);
        $warnings = $itemsdata = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);

        // Check permissions.
        require_capability('mod/feedback:viewreports', $context);

        if (!empty($params['groupid'])) {
            $groupid = $params['groupid'];
            // Determine is the group is visible to user.
            if (!groups_group_visible($groupid, $course, $cm)) {
                throw new moodle_exception('notingroup');
            }
        } else {
            // Check to see if groups are being used here.
            if ($groupmode = groups_get_activity_groupmode($cm)) {
                $groupid = groups_get_activity_group($cm);
                // Determine is the group is visible to user (this is particullary for the group 0 -> all groups).
                if (!groups_group_visible($groupid, $course, $cm)) {
                    throw new moodle_exception('notingroup');
                }
            } else {
                $groupid = 0;
            }
        }

        $feedbackstructure = new mod_feedback_structure($feedback, $cm, $completioncourse->id);
        $responsestable = new mod_feedback_responses_table($feedbackstructure, $groupid);
        // Ensure responses number is correct prior returning them.
        $feedbackstructure->shuffle_anonym_responses();
        $anonresponsestable = new mod_feedback_responses_anon_table($feedbackstructure, $groupid);

        $result = array(
            'attempts'          => $responsestable->export_external_structure($params['page'], $params['perpage']),
            'totalattempts'     => $responsestable->get_total_responses_count(),
            'anonattempts'      => $anonresponsestable->export_external_structure($params['page'], $params['perpage']),
            'totalanonattempts' => $anonresponsestable->get_total_responses_count(),
            'warnings'       => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_responses_analysis return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_responses_analysis_returns() {
        $responsestructure = new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Response id'),
                    'name' => new external_value(PARAM_RAW, 'Response name'),
                    'printval' => new external_value(PARAM_RAW, 'Response ready for output'),
                    'rawval' => new external_value(PARAM_RAW, 'Response raw value'),
                )
            )
        );

        return new external_single_structure(
            array(
                'attempts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Completed id'),
                            'courseid' => new external_value(PARAM_INT, 'Course id'),
                            'userid' => new external_value(PARAM_INT, 'User who responded'),
                            'timemodified' => new external_value(PARAM_INT, 'Time modified for the response'),
                            'fullname' => new external_value(PARAM_TEXT, 'User full name'),
                            'responses' => $responsestructure
                        )
                    )
                ),
                'totalattempts' => new external_value(PARAM_INT, 'Total responses count.'),
                'anonattempts' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Completed id'),
                            'courseid' => new external_value(PARAM_INT, 'Course id'),
                            'number' => new external_value(PARAM_INT, 'Response number'),
                            'responses' => $responsestructure
                        )
                    )
                ),
                'totalanonattempts' => new external_value(PARAM_INT, 'Total anonymous responses count.'),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Describes the parameters for get_last_completed.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_last_completed_parameters() {
        return new external_function_parameters (
            array(
                'feedbackid' => new external_value(PARAM_INT, 'Feedback instance id'),
                'courseid' => new external_value(PARAM_INT, 'Course where user completes the feedback (for site feedbacks only).',
                    VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Retrieves the last completion record for the current user.
     *
     * @param int $feedbackid feedback instance id
     * @return array of warnings and the last completed record
     * @since Moodle 3.3
     * @throws moodle_exception
     */
    public static function get_last_completed($feedbackid, $courseid = 0) {
        global $PAGE;

        $params = array('feedbackid' => $feedbackid, 'courseid' => $courseid);
        $params = self::validate_parameters(self::get_last_completed_parameters(), $params);
        $warnings = array();

        list($feedback, $course, $cm, $context, $completioncourse) = self::validate_feedback($params['feedbackid'],
            $params['courseid']);
        $feedbackcompletion = new mod_feedback_completion($feedback, $cm, $completioncourse->id);

        if ($feedbackcompletion->is_anonymous()) {
             throw new moodle_exception('anonymous', 'feedback');
        }
        if ($completed = $feedbackcompletion->find_last_completed()) {
            $exporter = new feedback_completed_exporter($completed);
            return array(
                'completed' => $exporter->export($PAGE->get_renderer('core')),
                'warnings' => $warnings,
            );
        }
        throw new moodle_exception('not_completed_yet', 'feedback');
    }

    /**
     * Describes the get_last_completed return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_last_completed_returns() {
        return new external_single_structure(
            array(
                'completed' => feedback_completed_exporter::get_read_structure(),
                'warnings' => new external_warnings(),
            )
        );
    }
}
