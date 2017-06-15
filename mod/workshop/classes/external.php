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
}
