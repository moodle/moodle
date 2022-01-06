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
 * Provides {@link \core_form\privacy\provider} class.
 *
 * @package     core_form
 * @category    privacy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_form\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the privacy API for the core_form subsystem.
 *
 * @package   core_files
 * @copyright 2018 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The forms subsystem does not store any data itself, it has no database tables.
        \core_privacy\local\metadata\provider,

        // The forms subsystem has user preferences.
        \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $collection->add_user_preference('filemanager_recentviewmode', 'privacy:metadata:preference:filemanager_recentviewmode');

        return $collection;
    }

    /**
     * Export all user preferences for the subsystem.
     *
     * @param int $userid The ID of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('filemanager_recentviewmode', null, $userid);
        if ($preference !== null) {
            switch ($preference) {
                case 1:
                    $value = get_string('displayasicons', 'core_repository');
                    break;
                case 2:
                    $value = get_string('displayastree', 'core_repository');
                    break;
                case 3:
                    $value = get_string('displaydetails', 'core_repository');
                    break;
                default:
                    $value = $preference;
            }

            $desc = get_string('privacy:preference:filemanager_recentviewmode', 'core_form', $value);
            writer::export_user_preference('core_form', 'filemanager_recentviewmode', $preference, $desc);
        }
    }
}
