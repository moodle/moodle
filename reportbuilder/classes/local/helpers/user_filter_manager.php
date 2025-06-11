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

declare(strict_types=1);

namespace core_reportbuilder\local\helpers;

use core_text;

/**
 * This class handles the setting and retrieving of a users' filter values for given reports
 *
 * It is currently using the user preference API as a storage mechanism
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_manager {

    /** @var int The size of each chunk, matching the maximum length of a single user preference */
    private const PREFERENCE_CHUNK_SIZE = 1333;

    /** @var string The prefix used to name the stored user preferences */
    private const PREFERENCE_NAME_PREFIX = 'reportbuilder-report-';

    /**
     * Generate user preference name for given report
     *
     * @param int $reportid
     * @param int $index
     * @return string
     */
    private static function user_preference_name(int $reportid, int $index): string {
        return static::PREFERENCE_NAME_PREFIX . "{$reportid}-{$index}";
    }

    /**
     * Set user filters for given report
     *
     * @param int $reportid
     * @param array $values
     * @param int|null $userid
     * @return bool
     */
    public static function set(int $reportid, array $values, ?int $userid = null): bool {
        $jsonvalues = json_encode($values);

        $jsonchunks = str_split($jsonvalues, static::PREFERENCE_CHUNK_SIZE);
        foreach ($jsonchunks as $index => $jsonchunk) {
            $userpreference = static::user_preference_name($reportid, $index);
            set_user_preference($userpreference, $jsonchunk, $userid);
        }

        // Ensure any subsequent preferences are reset (to account for number of chunks decreasing).
        static::reset_all($reportid, $userid, $index + 1);

        return true;
    }

    /**
     * Get user filters for given report
     *
     * @param int $reportid
     * @param int|null $userid
     * @return array
     */
    public static function get(int $reportid, ?int $userid = null): array {
        $jsonvalues = '';
        $index = 0;

        // We'll repeatedly append chunks to our JSON string, until we hit one that is below the maximum length.
        do {
            $userpreference = static::user_preference_name($reportid, $index++);
            $jsonchunk = get_user_preferences($userpreference, '', $userid);
            $jsonvalues .= $jsonchunk;
        } while (core_text::strlen($jsonchunk) === static::PREFERENCE_CHUNK_SIZE);

        return (array) json_decode($jsonvalues);
    }

    /**
     * Merge individual user filter values for given report
     *
     * @param int $reportid
     * @param array $values
     * @param int|null $userid
     * @return bool
     */
    public static function merge(int $reportid, array $values, ?int $userid = null): bool {
        $existing = static::get($reportid, $userid);

        return static::set($reportid, array_merge($existing, $values), $userid);
    }

    /**
     * Reset all user filters for given report
     *
     * @param int $reportid
     * @param int|null $userid
     * @param int $index If specified, then preferences will be reset starting from this index
     * @return bool
     */
    public static function reset_all(int $reportid, ?int $userid = null, int $index = 0): bool {
        // We'll repeatedly retrieve and reset preferences, until we hit one that is below the maximum length.
        do {
            $userpreference = static::user_preference_name($reportid, $index++);
            $jsonchunk = get_user_preferences($userpreference, '', $userid);
            unset_user_preference($userpreference, $userid);
        } while (core_text::strlen($jsonchunk) === static::PREFERENCE_CHUNK_SIZE);

        return true;
    }

    /**
     * Reset single user filter for given report
     *
     * @param int $reportid
     * @param string $uniqueidentifier
     * @param int|null $userid
     * @return bool
     */
    public static function reset_single(int $reportid, string $uniqueidentifier, ?int $userid = null): bool {
        $originalvalues = static::get($reportid, $userid);

        // Remove any filters whose name is prefixed by given identifier.
        $values = array_filter($originalvalues, static function(string $filterkey) use ($uniqueidentifier): bool {
            return core_text::strpos($filterkey, $uniqueidentifier) !== 0;
        }, ARRAY_FILTER_USE_KEY);

        return static::set($reportid, $values, $userid);
    }

    /**
     * Get all report filters for given user
     *
     * This is primarily designed for the privacy provider, and allows us to preserve all the preference logic within this class.
     *
     * @param int $userid
     * @return array
     */
    public static function get_all_for_user(int $userid): array {
        global $DB;
        $prefs = [];

        // We need to locate the first preference chunk of all report filters.
        $select = 'userid = :userid AND ' . $DB->sql_like('name', ':namelike');
        $params = [
            'userid' => $userid,
            'namelike' => $DB->sql_like_escape(static::PREFERENCE_NAME_PREFIX) . '%-0',
        ];
        $preferences = $DB->get_fieldset_select('user_preferences', 'name', $select, $params);

        // Retrieve all found filters.
        foreach ($preferences as $preference) {
            preg_match('/^' . static::PREFERENCE_NAME_PREFIX . '(?<reportid>\d+)\-/', $preference, $matches);
            $prefs[static::PREFERENCE_NAME_PREFIX . $matches['reportid']] = static::get((int) $matches['reportid'], $userid);
        }

        return $prefs;
    }
}
