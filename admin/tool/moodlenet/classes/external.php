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
 * This is the external API for this component.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_moodlenet;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir .'/externallib.php');
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/../lib.php');

use core_course\external\course_summary_exporter;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * This is the external API for this component.
 *
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * verify_webfinger parameters
     *
     * @return external_function_parameters
     */
    public static function verify_webfinger_parameters() {
        return new external_function_parameters(
            array(
                'profileurl' => new external_value(PARAM_NOTAGS, 'The profile url that the user has given us', VALUE_REQUIRED),
                'course' => new external_value(PARAM_INT, 'The course we are adding to', VALUE_REQUIRED),
                'section' => new external_value(PARAM_INT, 'The section within the course we are adding to', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Figure out if the passed content resolves with a WebFinger account.
     *
     * @param string $profileurl The profile url that the user states exists
     * @param int $course The course we are adding to
     * @param int $section The section within the course we are adding to
     * @return array Contains the result and domain if any
     * @throws \invalid_parameter_exception
     */
    public static function verify_webfinger(string $profileurl, int $course, int $section) {
        global $USER;

        $params = self::validate_parameters(self::verify_webfinger_parameters(), [
                'profileurl' => $profileurl,
                'section' => $section,
                'course' => $course
            ]
        );
        try {
            $mnetprofile = new moodlenet_user_profile($params['profileurl'], $USER->id);
        } catch (\Exception $e) {
            return [
                'result' => false,
                'message' => get_string('profilevalidationfail', 'tool_moodlenet'),
            ];
        }

        $userlink = profile_manager::get_moodlenet_profile_link($mnetprofile);

        // There were no problems verifying the account so lets store it.
        if ($userlink['result'] === true) {
            profile_manager::save_moodlenet_user_profile($mnetprofile);
            $userlink['domain'] = generate_mnet_endpoint($mnetprofile->get_profile_name(), $course, $section);
        }

        return $userlink;
    }

    /**
     * verify_webfinger return.
     *
     * @return \external_description
     */
    public static function verify_webfinger_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Was the passed content a valid WebFinger?'),
            'message' => new external_value(PARAM_TEXT, 'Our message for the user'),
            'domain' => new external_value(PARAM_RAW, 'Domain to redirect the user to', VALUE_OPTIONAL),
        ]);
    }

    /**
     * search_courses_parameters
     *
     * @return external_function_parameters
     */
    public static function search_courses_parameters() {
        return new external_function_parameters(
            array(
                'searchvalue' => new external_value(PARAM_RAW, 'search value'),
            )
        );
    }

    /**
     * For some given input find and return any course that matches it.
     *
     * @param string $searchvalue The profile url that the user states exists
     * @return array Contains the result set of courses for the value
     */
    public static function search_courses(string $searchvalue) {
        global $OUTPUT;

        $params = self::validate_parameters(
            self::search_courses_parameters(),
            ['searchvalue' => $searchvalue]
        );
        self::validate_context(\context_system::instance());

        $courses = array();

        if ($arrcourses = \core_course_category::search_courses(array('search' => $params['searchvalue']))) {
            foreach ($arrcourses as $course) {
                if (has_capability('moodle/course:manageactivities', \context_course::instance($course->id))) {
                    $data = new \stdClass();
                    $data->id = $course->id;
                    $data->fullname = $course->fullname;
                    $data->hidden = $course->visible;
                    $options = [
                        'course' => $course->id,
                    ];
                    $viewurl = new \moodle_url('/admin/tool/moodlenet/options.php', $options);
                    $data->viewurl = $viewurl->out(false);
                    $category = \core_course_category::get($course->category);
                    $data->coursecategory = $category->name;
                    $courseimage = course_summary_exporter::get_course_image($data);
                    if (!$courseimage) {
                        $courseimage = $OUTPUT->get_generated_image_for_id($data->id);
                    }
                    $data->courseimage = $courseimage;
                    $courses[] = $data;
                }
            }
        }
        return array(
            'courses' => $courses
        );
    }

    /**
     * search_courses_returns.
     *
     * @return \external_description
     */
    public static function search_courses_returns() {
        return new external_single_structure([
            'courses' => new \external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'course id'),
                    'fullname' => new external_value(PARAM_TEXT, 'course full name'),
                    'hidden' => new external_value(PARAM_INT, 'is the course visible'),
                    'viewurl' => new external_value(PARAM_URL, 'Next step of import'),
                    'coursecategory' => new external_value(PARAM_TEXT, 'Category name'),
                    'courseimage' => new external_value(PARAM_RAW, 'course image'),
                ]))
        ]);
    }
}
