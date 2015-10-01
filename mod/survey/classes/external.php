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
 * Survey external API
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Survey external functions
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_survey_external extends external_api {

    /**
     * Describes the parameters for get_surveys_by_courses.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_surveys_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of surveys in a provided list of courses,
     * if no list is provided all surveys that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array of surveys details
     * @since Moodle 3.0
     */
    public static function get_surveys_by_courses($courseids = array()) {
        global $CFG, $USER, $DB;

        $returnedsurveys = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_surveys_by_courses_parameters(), array('courseids' => $courseids));

        if (empty($params['courseids'])) {
            $params['courseids'] = array_keys(enrol_get_my_courses());
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids']);

            // Get the surveys in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $surveys = get_all_instances_in_courses("survey", $courses);
            foreach ($surveys as $survey) {
                $context = context_module::instance($survey->coursemodule);
                // Entry to return.
                $surveydetails = array();
                // First, we return information that any user can see in the web interface.
                $surveydetails['id'] = $survey->id;
                $surveydetails['coursemodule']      = $survey->coursemodule;
                $surveydetails['course']            = $survey->course;
                $surveydetails['name']              = external_format_string($survey->name, $context->id);

                if (has_capability('mod/survey:participate', $context)) {
                    $trimmedintro = trim($survey->intro);
                    if (empty($trimmedintro)) {
                        $tempo = $DB->get_field("survey", "intro", array("id" => $survey->template));
                        $survey->intro = get_string($tempo, "survey");
                    }

                    // Format intro.
                    list($surveydetails['intro'], $surveydetails['introformat']) =
                        external_format_text($survey->intro, $survey->introformat, $context->id, 'mod_survey', 'intro', null);

                    $surveydetails['template']  = $survey->template;
                    $surveydetails['days']      = $survey->days;
                    $surveydetails['questions'] = $survey->questions;
                    $surveydetails['surveydone'] = survey_already_done($survey->id, $USER->id) ? 1 : 0;

                }

                if (has_capability('moodle/course:manageactivities', $context)) {
                    $surveydetails['timecreated']   = $survey->timecreated;
                    $surveydetails['timemodified']  = $survey->timemodified;
                    $surveydetails['section']       = $survey->section;
                    $surveydetails['visible']       = $survey->visible;
                    $surveydetails['groupmode']     = $survey->groupmode;
                    $surveydetails['groupingid']    = $survey->groupingid;
                }
                $returnedsurveys[] = $surveydetails;
            }
        }
        $result = array();
        $result['surveys'] = $returnedsurveys;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_surveys_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_surveys_by_courses_returns() {
        return new external_single_structure(
            array(
                'surveys' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Survey id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'Survey name'),
                            'intro' => new external_value(PARAM_RAW, 'The Survey intro', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                            'template' => new external_value(PARAM_INT, 'Survey type', VALUE_OPTIONAL),
                            'days' => new external_value(PARAM_INT, 'Days', VALUE_OPTIONAL),
                            'questions' => new external_value(PARAM_RAW, 'Question ids', VALUE_OPTIONAL),
                            'surveydone' => new external_value(PARAM_INT, 'Did I finish the survey?', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'Time of creation', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time of last modification', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'Course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'Visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL),
                        ), 'Surveys'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

}
