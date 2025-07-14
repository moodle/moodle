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

namespace core_courseformat\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider implementation for courseformat core subsystem.
 *
 * @package    core_courseformat
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This system has data.
    \core_privacy\local\metadata\provider,
    // This system has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /** The user preference for the navigation drawer. */
    public const SECTION_PREFERENCES_PREFIX = 'coursesectionspreferences';

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {

        $collection->add_user_preference(
            self::SECTION_PREFERENCES_PREFIX,
            'privacy:metadata:preference:' . self::SECTION_PREFERENCES_PREFIX
        );

        return $collection;
    }

    /**
     * Store all user preferences for this system.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {

        // Get user courses.
        $courses = enrol_get_all_users_courses($userid);

        if (empty($courses)) {
            return;
        }

        foreach ($courses as $course) {
            $preferencename = self::SECTION_PREFERENCES_PREFIX . '_' . $course->id;

            $preference = get_user_preferences($preferencename, null, $userid);

            if (isset($preference)) {
                $preferencestring = get_string('preference:' . self::SECTION_PREFERENCES_PREFIX, 'courseformat', $course->fullname);
                \core_privacy\local\request\writer::export_user_preference(
                    'core_courseformat',
                    $preferencename,
                    $preference,
                    $preferencestring
                );
            }
        }
    }
}
