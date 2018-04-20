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
 * Privacy Subsystem implementation for auth_manual.
 *
 * @package    auth_manual
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_manual\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\writer;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;

/**
 * Privacy provider for the authentication manual.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        // There is a one user preference.
        $collection->add_user_preference('auth_manual_passwordupdatetime',
            'privacy:metadata:preference:passwordupdatetime');

        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $lastpasswordupdatetime = get_user_preferences('auth_manual_passwordupdatetime', null, $userid);
        if ($lastpasswordupdatetime !== null) {
            $time = transform::datetime($lastpasswordupdatetime);
            writer::export_user_preference('auth_manual',
                'auth_manual_passwordupdatetime',
                $time,
                get_string('privacy:metadata:preference:passwordupdatetime', 'auth_manual')
            );
        }
    }
}
