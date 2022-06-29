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
 * V4 UUID generator.
 *
 * @package    core
 * @copyright  2019 Matteo Scaramuccia <moodle@matteoscaramuccia.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use Exception;

defined('MOODLE_INTERNAL') || die();

/**
 * V4 UUID generator class.
 *
 * @package    core
 * @copyright  2019 Matteo Scaramuccia <moodle@matteoscaramuccia.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uuid {
    /**
     * Generate a V4 UUID using PECL UUID extension.
     * @see https://github.com/php/pecl-networking-uuid PECL uuid
     * @see https://tools.ietf.org/html/rfc4122
     *
     * @return string|bool The UUID when PECL UUID extension is available;
     *                     otherwise, false.
     */
    protected static function generate_uuid_via_pecl_uuid_extension() {
        $uuid = false;

        // Check if PECL uuid extension has been actually installed.
        if (function_exists('\uuid_time')) {
            // Set V4 version.
            $uuid = \uuid_create(UUID_TYPE_RANDOM);
        }

        return $uuid;
    }

    /**
     * Generate a V4 UUID using PHP 7+ features.
     *
     * @see https://www.php.net/manual/en/function.random-bytes.php
     * @see https://tools.ietf.org/html/rfc4122
     *
     * @return string|bool The UUID when random_bytes() function is available;
     *                     otherwise, false when missing the sources of randomness used by random_bytes().
     */
    protected static function generate_uuid_via_random_bytes() {
        $uuid = false;

        // If none of the sources of randomness are available,
        // then an Exception will be thrown.
        try {
            $data = random_bytes(16);
            $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // Set version to 0100.
            $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // Set bits 6-7 to 10.
            $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        } catch (Exception $e) {
            // Could not generate a random string. Is this OS secure?
            $uuid = false;
        }

        return $uuid;
    }

    /**
     * Generate a V4 UUID.
     *
     * Unique is hard. Very hard. Attempt to use the PECL UUID function if available, and if not then revert to
     * constructing the uuid using random_bytes or mt_rand.
     *
     * It is important that this token is not solely based on time as this could lead
     * to duplicates in a clustered environment (especially on VMs due to poor time precision).
     *
     * UUIDs are just 128 bits long but with different supported versions (RFC 4122), mainly two:
     * - V1: the goal is uniqueness, at the cost of anonymity since it is coupled to the host generating it, via its MAC address.
     * - V4: the goal is randomness, at the cost of (rare) collisions.
     * Here, the V4 type is the preferred choice.
     *
     * The format is:
     * xxxxxxxx-xxxx-4xxx-Yxxx-xxxxxxxxxxxx
     * where x is any hexadecimal digit and Y is a random choice from 8, 9, a, or b.
     *
     * @see https://tools.ietf.org/html/rfc4122
     *
     * @return string The V4 UUID.
     */
    public static function generate() {
        // Try PHP UUID extensions first.
        $uuid = self::generate_uuid_via_pecl_uuid_extension();

        // Fall back to better random features, when possible.
        if (empty($uuid)) {
            $uuid = self::generate_uuid_via_random_bytes();
        }

        // Finally, create it with the available randomness.
        if (empty($uuid)) {
            // Fallback uuid generation based on:
            // "http://www.php.net/manual/en/function.uniqid.php#94959".
            $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

                // 32 bits for "time_low".
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),

                // 16 bits for "time_mid".
                mt_rand(0, 0xffff),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4.
                mt_rand(0, 0x0fff) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1.
                mt_rand(0, 0x3fff) | 0x8000,

                // 48 bits for "node".
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
        }

        return trim($uuid);
    }
}
