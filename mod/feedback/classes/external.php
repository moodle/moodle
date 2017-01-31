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
}
