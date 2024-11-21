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
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\external;

use block_xp\di;

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mark_popup_notification_seen extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT),
            'level' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * Search courses.
     *
     * The only reason this exists is to include the frontpage in the search.
     *
     * @param int $courseid The course ID.
     * @param int $level The level.
     * @return bool
     */
    public static function execute($courseid, $level) {
        global $SITE, $USER;

        $params = self::validate_parameters(self::execute_parameters(), compact('courseid', 'level'));
        $courseid = $params['courseid'];
        $level = $params['level'];

        // Pre-checks.
        $worldfactory = di::get('course_world_factory');
        $world = $worldfactory->get_world($courseid);
        $courseid = $world->get_courseid(); // Ensure that we get the real course ID.
        self::validate_context($world->get_context());

        // Permission checks.
        $perms = $world->get_access_permissions();
        $perms->require_access();

        $userlevel = $world->get_store()->get_state($USER->id)->get_level()->get_level();
        $service = $world->get_level_up_notification_service();
        $service->mark_as_notified($USER->id, $level);

        // Special case to remove 0 when we are at the same level.
        if ($level == $userlevel) {
            $service->mark_as_notified($USER->id, 0);
        }

        return true;
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_value(PARAM_BOOL);
    }

}
