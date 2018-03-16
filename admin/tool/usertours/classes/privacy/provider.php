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
 * Privacy Subsystem implementation for tool_usertours.
 *
 * @package    tool_usertours
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\privacy;

use \core_privacy\local\request\writer;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the user tours feature.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider
{

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $itemcollection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) {
        // There are several user preferences.
        $items->add_user_preference(\tool_usertours\tour::TOUR_REQUESTED_BY_USER, 'privacy:metadata:preference:requested');
        $items->add_user_preference(\tool_usertours\tour::TOUR_LAST_COMPLETED_BY_USER, 'privacy:metadata:preference:completed');

        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences($userid) {
        $preferences = get_user_preferences();
        foreach ($preferences as $name => $value) {
            $descriptionidentifier = null;
            $tourid = null;
            if (strpos($name, \tool_usertours\tour::TOUR_REQUESTED_BY_USER) === 0) {
                $descriptionidentifier = 'privacy:request:preference:requested';
                $tourid = substr($name, strlen(\tool_usertours\tour::TOUR_REQUESTED_BY_USER));
            } else if (strpos($name, \tool_usertours\tour::TOUR_LAST_COMPLETED_BY_USER) === 0) {
                $descriptionidentifier = 'privacy:request:preference:completed';
                $tourid = substr($name, strlen(\tool_usertours\tour::TOUR_LAST_COMPLETED_BY_USER));
            }

            if ($descriptionidentifier !== null) {
                $time = transform::datetime($value);
                $tour = \tool_usertours\tour::instance($tourid);

                writer::export_user_preference(
                    'tool_usertours',
                    $name,
                    $time,
                    get_string($descriptionidentifier, 'tool_usertours', (object) [
                        'name' => $tour->get_name(),
                        'time' => $time,
                    ])
                );
            }
        }
    }
}
