<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_user;

/**
 * Update public key against registered user device.
 *
 * @package     core
 * @copyright   Alex Morris <alex.morris@catalyst.net.nz>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 4.2
 */
class devicekey {
    /**
     * Update the users public key for the specified device and app.
     *
     * @param string $uuid The device UUID.
     * @param string $appid The app id, usually something like com.moodle.moodlemobile.
     * @param string $publickey The app generated public key.
     * @return bool
     * @since Moodle 4.2
     */
    public static function update_device_public_key(string $uuid, string $appid, string $publickey): bool {
        global $DB, $USER;

        $params = [
            'uuid' => $uuid,
            'appid' => $appid,
            'userid' => $USER->id,
        ];

        if ($DB->record_exists('user_devices', $params)) {
            $DB->set_field('user_devices', 'publickey', $publickey, $params);
            return true;
        }
        return false;
    }
}
