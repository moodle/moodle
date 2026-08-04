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
 * Fake auth plugin implementing check_password_policy(), for testing which $user
 * gets forwarded to plugin password policy callbacks.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Captures the $user argument passed to the callback, for test inspection.
 */
class auth_passwordpolicyprobe_capture {
    /** @var stdClass|null the last user object the plugin callback was invoked with */
    public static $user = null;
}

/**
 * Fake check_password_policy callback, recording the $user it was invoked with.
 *
 * @param string $password
 * @param stdClass|null $user
 * @return string empty string, meaning the password always passes.
 */
function auth_passwordpolicyprobe_check_password_policy(string $password, ?stdClass $user = null): string {
    auth_passwordpolicyprobe_capture::$user = $user;
    return '';
}
