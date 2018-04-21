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
 * Privacy Subsystem implementation for tool_assignmentupgrade.
 *
 * @package    tool_assignmentupgrade
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_assignmentupgrade\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for tool_assignmentupgrade implementing metadata, plugin, and user_preference providers.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_user_preference(
            'tool_assignmentupgrade_perpage',
            'privacy:metadata:preference:perpage'
        );
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences($userid) {
        $perpage = get_user_preferences('tool_assignmentupgrade_perpage', null, $userid);
        if ($perpage !== null) {
            writer::export_user_preference(
                'tool_assignmentupgrade',
                'perpage',
                $perpage,
                get_string('privacy:metadata:preference:perpage', 'tool_assignmentupgrade')
            );
        }
    }

}
