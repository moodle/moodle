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
 * External function.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\external;

use block_xp\di;

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_sections extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT),
            'options' => new external_single_structure([], '', VALUE_DEFAULT, []),
        ]);
    }

    /**
     * Search modules.
     *
     * @param int $courseid The course ID.
     * @param array $options The options.
     * @return array
     */
    public static function execute($courseid, $options = []) {
        $params = self::validate_parameters(self::execute_parameters(), compact('courseid', 'options'));
        $courseid = $params['courseid'];
        $options = (array) $params['options'];

        // We fetch the world, but do not update the $courseid as per world::get_courseid, because
        // if we are using the plugin for the whole site, then users should be able to look up in
        // any course they have access to. And if we're using the plugin per course, then they need
        // permissions within that course.
        $world = di::get('course_world_factory')->get_world($courseid);
        self::validate_context($world->get_context());
        $world->get_access_permissions()->require_manage();

        $modinfo = get_fast_modinfo($courseid);
        if (!can_access_course($modinfo->get_course())) {
            throw new \require_login_exception('Cannot access course.');
        }
        $courseformat = course_get_format($courseid);
        $sections = [];

        foreach ($modinfo->get_sections() as $sectionnum => $cmids) {
            $sections[] = [
                'name' => $courseformat->get_section_name($sectionnum),
                'number' => $sectionnum,
            ];
        }

        return $sections;
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_multiple_structure(new external_single_structure([
            'name' => new external_value(PARAM_RAW, 'The section name'),
            'number' => new external_value(PARAM_INT, 'The section number'),
        ]));
    }

}
