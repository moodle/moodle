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
 * Privacy Subsystem implementation for block_myoverview.
 *
 * @package    block_myoverview
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_myoverview\privacy;

use core_privacy\local\request\user_preference_provider;
use core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for block_myoverview.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, user_preference_provider {

    /**
     * Returns meta-data information about the myoverview block.
     *
     * @param  \core_privacy\local\metadata\collection $collection A collection of meta-data.
     * @return \core_privacy\local\metadata\collection Return the collection of meta-data.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('block_myoverview_user_sort_preference', 'privacy:metadata:overviewsortpreference');
        $collection->add_user_preference('block_myoverview_user_view_preference', 'privacy:metadata:overviewviewpreference');
        $collection->add_user_preference('block_myoverview_user_grouping_preference',
            'privacy:metadata:overviewgroupingpreference');
        $collection->add_user_preference('block_myoverview_user_paging_preference',
            'privacy:metadata:overviewpagingpreference');
        return $collection;
    }
    /**
     * Export all user preferences for the myoverview block
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('block_myoverview_user_sort_preference', null, $userid);
        if (isset($preference)) {
            writer::export_user_preference('block_myoverview',
                'block_myoverview_user_sort_preference', get_string($preference, 'block_myoverview'),
                get_string('privacy:metadata:overviewsortpreference', 'block_myoverview'));
        }

        $preference = get_user_preferences('block_myoverview_user_view_preference', null, $userid);
        if (isset($preference)) {
            writer::export_user_preference('block_myoverview',
                'block_myoverview_user_view_preference',
                get_string($preference, 'block_myoverview'),
                get_string('privacy:metadata:overviewviewpreference', 'block_myoverview'));
        }

        $preference = get_user_preferences('block_myoverview_user_grouping_preference', null, $userid);
        if (isset($preference)) {
            writer::export_user_preference('block_myoverview',
                'block_myoverview_user_grouping_preference',
                get_string($preference, 'block_myoverview'),
                get_string('privacy:metadata:overviewgroupingpreference', 'block_myoverview'));
        }

        $preferences = get_user_preferences(null, null, $userid);
        foreach ($preferences as $name => $value) {
            if ((substr($name, 0, 30) == 'block_myoverview_hidden_course')) {
                writer::export_user_preference(
                    'block_myoverview',
                    $name,
                    $value,
                    get_string('privacy:request:preference:set', 'block_myoverview', (object) [
                        'name' => $name,
                        'value' => $value,
                    ])
                );
            }
        }

        $preference = get_user_preferences('block_myoverview_user_paging_preference', null, $userid);
        if (isset($preference)) {
            \core_privacy\local\request\writer::export_user_preference('block_myoverview',
                'block_myoverview_user_paging_preference',
                $preference,
                get_string('privacy:metadata:overviewpagingpreference', 'block_myoverview'));
        }
    }
}