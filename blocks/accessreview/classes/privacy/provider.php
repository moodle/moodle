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
 * Privacy Subsystem implementation for block_accessreview.
 *
 * @package    block_accessreview
 * @copyright  2020 Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_accessreview\privacy;

use \core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * The accessreview block stores a user preference data.
 *
 * @copyright  2020 Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,
    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /** The user preference for the toggle state. */
    const TOGGLE_STATE = 'block_accessreviewtogglestate';

    /**
     * Returns meta data about this system.
     *
     * @param  collection $items The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(self::TOGGLE_STATE, 'privacy:metadata:preference:block_accessreviewtogglestate');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $togglestate = get_user_preferences(self::TOGGLE_STATE, null, $userid);

        if (isset($togglestate)) {
            $preferencestring = get_string('privacy:togglestateoff', 'block_accessreview');
            if ($togglestate == true) {
                $preferencestring = get_string('privacy:togglestateon', 'block_accessreview');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'block_accessreview',
                self::TOGGLE_STATE,
                ($togglestate) ? 'On' : 'Off',
                $preferencestring
            );
        }
    }
}
