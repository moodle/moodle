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
 * Blocks external API
 *
 * @package    core_block
 * @category   external
 * @copyright  2017 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Blocks external functions
 *
 * @package    core_block
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.3
 */
class core_block_external extends external_api {

    /**
     * Returns description of get_course_blocks parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_course_blocks_parameters() {
        return new external_function_parameters(
            array(
                'courseid'  => new external_value(PARAM_INT, 'course id')
            )
        );
    }

    /**
     * Returns blocks information for a course.
     *
     * @param int $courseid The course id
     * @return array Blocks list and possible warnings
     * @throws moodle_exception
     * @since Moodle 3.3
     */
    public static function get_course_blocks($courseid) {
        global $OUTPUT, $PAGE;

        $warnings = array();
        $params = self::validate_parameters(self::get_course_blocks_parameters(), ['courseid' => $courseid]);

        $course = get_course($params['courseid']);
        $context = context_course::instance($course->id);
        self::validate_context($context);

        // Specific layout for frontpage course.
        if ($course->id == SITEID) {
            $PAGE->set_pagelayout('frontpage');
            $PAGE->set_pagetype('site-index');
        } else {
            $PAGE->set_pagelayout('course');
            // Ensure course format is set (view course/view.php).
            $course->format = course_get_format($course)->get_format();
            $PAGE->set_pagetype('course-view-' . $course->format);
        }

        // Load the block instances for all the regions.
        $PAGE->blocks->load_blocks();
        $PAGE->blocks->create_all_block_instances();

        $finalblocks = array();
        $blocks = $PAGE->blocks->get_content_for_all_regions($OUTPUT);
        foreach ($blocks as $region => $regionblocks) {
            foreach ($regionblocks as $bc) {
                $finalblocks[] = [
                    'instanceid' => $bc->blockinstanceid,
                    'name' => $bc->attributes['data-block'],
                    'region' => $region,
                    'positionid' => $bc->blockpositionid,
                    'collapsible' => (bool) $bc->collapsible,
                    'dockable' => (bool) $bc->dockable,
                ];
            }
        }

        return array(
            'blocks' => $finalblocks,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of get_course_blocks result values.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_course_blocks_returns() {

        return new external_single_structure(
            array(
                'blocks' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'instanceid'    => new external_value(PARAM_INT, 'Block instance id.'),
                            'name'          => new external_value(PARAM_PLUGIN, 'Block name.'),
                            'region'        => new external_value(PARAM_ALPHANUMEXT, 'Block region.'),
                            'positionid'    => new external_value(PARAM_INT, 'Position id.'),
                            'collapsible'   => new external_value(PARAM_BOOL, 'Whether the block is collapsible.'),
                            'dockable'      => new external_value(PARAM_BOOL, 'hether the block is  dockable.'),
                        ), 'Block information.'
                    ), 'List of blocks in the course.'
                ),
                'warnings'  => new external_warnings(),
            )
        );
    }

}
