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
 * Privacy subsystem implementation for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_launchpad\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use local_launchpad\registration;

defined('MOODLE_INTERNAL') || die();

/**
 * Stores a Fellowship portal preference for self-registered users.
 *
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Describe stored user preferences.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            registration::PREF_PORTAL,
            'privacy:metadata:preference:toba_portal'
        );
        return $collection;
    }

    /**
     * Export user preferences for the privacy subsystem.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid): void {
        $portal = get_user_preferences(registration::PREF_PORTAL, null, $userid);
        if ($portal !== null && $portal !== '') {
            writer::export_user_preference(
                'local_launchpad',
                registration::PREF_PORTAL,
                $portal,
                get_string('privacy:metadata:preference:toba_portal', 'local_launchpad')
            );
        }
    }
}
