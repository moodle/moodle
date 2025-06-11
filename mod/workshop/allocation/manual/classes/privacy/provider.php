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
 * Provides the class {@link workshopallocation_manual\privacy\provider}
 *
 * @package     workshopallocation_manual
 * @category    privacy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace workshopallocation_manual\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy API implementation for the Manual allocation method.
 *
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\user_preference_provider {

    /**
     * Describe all the places where this plugin stores some personal data.
     *
     * @param collection $collection Collection of items to add metadata to.
     * @return collection Collection with our added items.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_user_preference('workshopallocation_manual_perpage', 'privacy:metadata:preference:perpage');

        return $collection;
    }

    /**
     * Export user preferences controlled by this plugin.
     *
     * @param int $userid ID of the user we are exporting data form.
     */
    public static function export_user_preferences(int $userid) {

        $perpage = get_user_preferences('workshopallocation_manual_perpage', null, $userid);

        if ($perpage !== null) {
            writer::export_user_preference('workshopallocation_manual', 'workshopallocation_manual_perpage', $perpage,
                get_string('privacy:metadata:preference:perpage', 'workshopallocation_manual'));
        }
    }
}
