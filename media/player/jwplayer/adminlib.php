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
 * JW Player media plugin adminlib.
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Admin setting for hosting, adds verification.
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_jwplayer_hostingmethod_setting extends admin_setting_configselect {

    /**
     * Save a setting
     *
     * @param string $data
     * @return string empty of error string
     */
    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return parent::write_setting($data);
    }

    /**
     * Validate data.
     *
     * This ensures that JWplayer is downloaded and located in lib/jwplayer if
     * self-hosted mode is selected.
     *
     * @param string $data
     * @return mixed True on success, else error message.
     */
    public function validate($data) {
        global $CFG;
        if ($data === 'self') {
            $hostedjwplayerpath = $CFG->dirroot . '/media/player/jwplayer/jwplayer/jwplayer.js';
            if (!is_readable($hostedjwplayerpath)) {
                return get_string('errornoselfhostedlibrary', 'media_jwplayer');
            }
        }
        return true;
    }
}

/**
 * Admin setting for license, adds verification.
 *
 * @package    media_jwplayer
 * @copyright  2017 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_jwplayer_license_setting extends admin_setting_configtext {

    /**
     * Validate data.
     *
     * This ensures that license key is specified for self-hosting mode.
     *
     * @param string $data
     * @return mixed True on success, else error message.
     */
    public function validate($data) {
        $result = parent::validate($data);
        if ($result !== true) {
            return $result;
        }

        if (empty($data) && get_config('media_jwplayer', 'hostingmethod') === 'self') {
            return get_string('errornolicensekey', 'media_jwplayer');
        }
        return true;
    }
}

