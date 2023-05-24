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

namespace core_user\external;

use context_system;
use core_user\devicekey;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

/**
 * Update public key against registered user device.
 *
 * @package     core
 * @copyright   Alex Morris <alex.morris@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 4.2
 */
class update_user_device_public_key extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'uuid' => new external_value(PARAM_RAW, 'the device UUID'),
            'appid' => new external_value(PARAM_NOTAGS, 'The app id, something like com.moodle.moodlemobile'),
            'publickey' => new external_value(PARAM_RAW, 'the app generated public key'),
        ]);
    }

    /**
     * Update public key against registered user device.
     *
     * @param string $uuid The device UUID.
     * @param string $appid The app id, usually something like com.moodle.moodlemobile.
     * @param string $publickey The app generated public key.
     * @return array Status and list of possible warnings
     */
    public static function execute($uuid, $appid, $publickey): array {
        [
            'uuid' => $uuid,
            'appid' => $appid,
            'publickey' => $publickey
        ] = self::validate_parameters(self::execute_parameters(), [
            'uuid' => $uuid,
            'appid' => $appid,
            'publickey' => $publickey
        ]);

        $context = context_system::instance();
        self::validate_context($context);

        $warnings = [];

        $status = devicekey::update_device_public_key($uuid, $appid, $publickey);
        if (!$status) {
            $warnings[] = [
                'item' => $uuid,
                'warningcode' => 'devicedoesnotexist',
                'message' => 'Could not find a device with the specified device UUID and app ID for this user'
            ];
        }

        return [
            'status' => $status,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     * @since Moodle 4.2
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'Whether the request was successful'),
            'warnings' => new external_warnings()
        ]);
    }
}
