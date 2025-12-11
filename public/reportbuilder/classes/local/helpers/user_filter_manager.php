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

use core_reportbuilder\local\models\user_filter;
use core_text;

/**
 * This class handles the setting and retrieving of a users' filter values for given reports
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_manager {
    /**
     * Set user filters for given report
     *
     * @param int $reportid
     * @param array $values
     * @param int|null $userid
     * @return bool
     */
    public static function set(int $reportid, array $values, ?int $userid = null): bool {
        global $USER;

        $userid ??= $USER->id;

        $userfilter = user_filter::get_record(['reportid' => $reportid, 'usercreated' => $userid]);
        if ($userfilter === false) {
            $userfilter = new user_filter(0, (object) [
                'reportid' => $reportid,
                'usercreated' => $userid,
            ]);
        }

        $userfilter->set('filterdata', json_encode($values))
            ->save();

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
        global $USER;

        $userid ??= $USER->id;

        $userfilter = user_filter::get_record(['reportid' => $reportid, 'usercreated' => $userid]);
        if ($userfilter === false) {
            return [];
        }

        return (array) json_decode($userfilter->get('filterdata'));
    }

    /**
     * Merge individual user filter values for given report
     *
     * @param int $reportid
     * @param array $values
     * @param int|null $userid
     * @return bool
     *
     * @deprecated since Moodle 5.2 - please do not use this function any more
     */
    #[\core\attribute\deprecated(reason: 'It is no longer used', mdl: 'MDL-86997', since: '5.2')]
    public static function merge(int $reportid, array $values, ?int $userid = null): bool {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $existing = static::get($reportid, $userid);

        return static::set($reportid, array_merge($existing, $values), $userid);
    }

    /**
     * Reset all user filters for given report
     *
     * @param int $reportid
     * @param int|null $userid
     * @return bool
     */
    public static function reset(int $reportid, ?int $userid = null): bool {
        global $DB, $USER;

        $userid ??= $USER->id;

        return $DB->delete_records(user_filter::TABLE, ['reportid' => $reportid, 'usercreated' => $userid]);
    }

    /**
     * Reset all user filters for given report
     *
     * @param int $reportid
     * @param int|null $userid
     * @param int $index Unused
     * @return bool
     *
     * @deprecated since Moodle 5.2 - please use {@see reset} instead
     */
    #[\core\attribute\deprecated('::reset', mdl: 'MDL-86997', since: '5.2')]
    public static function reset_all(int $reportid, ?int $userid = null, int $index = 0): bool {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        return static::reset($reportid, $userid);
    }

    /**
     * Reset single user filter for given report
     *
     * @param int $reportid
     * @param string $uniqueidentifier
     * @param int|null $userid
     * @return bool
     *
     * @deprecated since Moodle 5.2 - please do not use this function any more
     */
    #[\core\attribute\deprecated(reason: 'It is no longer used', mdl: 'MDL-86997', since: '5.2')]
    public static function reset_single(int $reportid, string $uniqueidentifier, ?int $userid = null): bool {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $originalvalues = static::get($reportid, $userid);

        // Remove any filters whose name is prefixed by given identifier.
        $values = array_filter($originalvalues, static function (string $filterkey) use ($uniqueidentifier): bool {
            return core_text::strpos($filterkey, $uniqueidentifier) !== 0;
        }, ARRAY_FILTER_USE_KEY);

        return static::set($reportid, $values, $userid);
    }

    /**
     * Get all report filters for given user
     *
     * @param int $userid
     * @return array
     *
     * @deprecated since Moodle 5.0 - please do not use this function any more
     */
    #[\core\attribute\deprecated(null, reason: 'It is no longer used', mdl: 'MDL-83345', since: '5.0')]
    public static function get_all_for_user(int $userid): array {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        $prefs = [];

        // Retrieve all found filters.
        $preferences = user_filter::get_records(['usercreated' => $userid]);
        foreach ($preferences as $preference) {
            $prefs['reportbuilder-report-' . $preference->get('reportid')] = (array) json_decode($preference->get('filterdata'));
        }

        return $prefs;
    }
}
