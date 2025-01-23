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

namespace core_badges\local\backpack;

use core_badges\achievement_credential;

/**
 * Helper class for Open Badges, used for methods that are common.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Converts Open Badges API version to the format used in Moodle classes.
     *
     * @param string $apiversion The Open Badges API version, e.g., '2.0', '2.1', etc.
     * @return string The converted API version in the format 'v2p0', 'v2p1', etc.
     * @throws \coding_exception
     */
    public static function convert_apiversion(string $apiversion): string {
        if (!is_numeric($apiversion)) {
            throw new \coding_exception('Invalid Open Badges API version');
        }

        if (strpos($apiversion, '.') === false) {
            $apiversion .= '.0';
        }

        $apiversion = str_replace(".", "p", $apiversion);
        return 'v' . $apiversion;
    }

    /**
     * Checks if an assertion exists in the database.
     *
     * @param string $hash Badge unique hash.
     * @return bool True if the assertion exists, false otherwise.
     */
    public static function assertion_exists(
        string $hash,
    ): bool {
        global $DB;

        $column = $DB->sql_compare_text('uniquehash', 255);
        return $DB->record_exists_select(
            'badge_issued',
            $column . ' = ?',
            [$hash],
        );
    }

    /**
     * Checks if a badge is published and active.
     *
     * @param string $id Badge ID.
     * @return bool True if the badge is available, false otherwise.
     */
    public static function badge_available(
        string $id,
    ): bool {
        global $DB;

        return $DB->record_exists_select(
            'badge',
            'id = :id AND (status = :status1 OR status = :status2)',
            ['id' => $id, 'status1' => BADGE_STATUS_ACTIVE, 'status2' => BADGE_STATUS_ACTIVE_LOCKED],
        );
    }

    /**
     * Gets the badge ID from a badge assertion hash.
     *
     * @param string $hash Badge unique hash.
     * @return int|null Badge ID if the assertion exists, null otherwise.
     */
    public static function get_badgeid_from_hash(
        string $hash,
    ): ?int {
        return achievement_credential::instance($hash)?->get_badge_id();
    }

    /**
     * Export the badge achievement credential (aka assertion) with the given hash.
     *
     * @param int $obversion Open Badges version to use for the export.
     * @param string $badgehash Badge unique hash.
     * @param bool $issued Include the nested badge issued information.
     * @param bool $usesalt Whether to hash the identity and include the salt information for the hash.
     * @return array Badge assertion.
     */
    public static function export_achievement_credential(
        int $obversion,
        string $badgehash,
        bool $issued = true,
        bool $usesalt = true,
    ): array {
        $assertionexporter = ob_factory::create_assertion_exporter_from_hash(
            $badgehash,
            self::convert_apiversion($obversion),
        );
        return $assertionexporter->export($issued, $usesalt);
    }

    /**
     * Export the achievement (aka badgeclass) information for this achievement credential.
     *
     * @param int $obversion Open Badges version to use for the export.
     * @param int $badgeid Badge ID.
     * @param bool $issued Include the nested badge issuer information.
     * @return array Badge class information.
     */
    public static function export_credential(
        int $obversion,
        int $badgeid,
        bool $issued = true,
    ): array {
        $badgeexporter = ob_factory::create_badge_exporter_from_id(
            $badgeid,
            self::convert_apiversion($obversion),
        );
        return $badgeexporter->export($issued);
    }

    /**
     * Export badge issuer information.
     *
     * @param int $obversion Open Badges version to use for the export.
     * @param int $badgeid Badge ID.
     * @return array Issuer information.
     */
    public static function export_issuer(
        int $obversion,
        int $badgeid,
    ): array {
        $issuerexporter = ob_factory::create_issuer_exporter_from_id(
            $badgeid,
            self::convert_apiversion($obversion),
        );
        return $issuerexporter->export();
    }
}
