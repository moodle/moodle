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
class UserAccessHelper {

    /**
     * Is user fully set up.
     *
     * @return bool
     * @throws \coding_exception
     */
    public static function is_user_fully_set_up() {
        global $USER;

        return (!isloggedin() || isguestuser() || user_not_fully_set_up($USER) ||
            get_user_preferences('auth_forcepasswordchange')) ? false : true;
    }

    /**
     * User policy agreed.
     *
     * @return bool
     */
    public static function user_policy_agreed() {
        global $USER;

        return (empty($USER->policyagreed) &&
                (class_exists('\core_privacy\local\sitepolicy\manager') &&
                $manager = new \core_privacy\local\sitepolicy\manager()) && $manager->is_defined()) ? false : true;
    }

    /**
     * Is logged in.
     *
     * @return bool
     * @throws \coding_exception
     */
    public static function is_logged_in() {
        return (isloggedin() && !isguestuser()) ? true : false;
    }
}
