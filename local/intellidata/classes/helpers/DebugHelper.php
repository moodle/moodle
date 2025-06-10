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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class DebugHelper {

    /**
     * Debug enabled.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function debugenabled() {
        return SettingsHelper::get_setting('debugenabled');
    }

    /**
     * Error log.
     *
     * @param $errorstring
     * @throws \dml_exception
     */
    public static function error_log($errorstring) {
        if (self::debugenabled()) {
            syslog(LOG_ERR, 'IntelliData Debug: ' . $errorstring);
        }
    }

    /**
     * Enable moodle debug.
     *
     * @return void
     * @throws \dml_exception
     */
    public static function enable_moodle_debug() {
        global $CFG;

        if (self::debugenabled()) {
            $CFG->debug = DEBUG_DEVELOPER;
            $CFG->debugdeveloper = true;
            $CFG->debugdisplay = true;
        }
    }

    /**
     * Disable moodle debug.
     *
     * @return void
     * @throws \dml_exception
     */
    public static function disable_moodle_debug() {
        global $CFG;

        if (self::debugenabled()) {
            $CFG->debug = 0;
            $CFG->debugdeveloper = false;
            $CFG->debugdisplay = false;
        }
    }
}
