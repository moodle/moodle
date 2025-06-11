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
 * Lib API functions.
 *
 * @package   report_usersessions
 * @copyright 2014 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/lib.php');

/**
 * Show user friendly duration since last activity.
 *
 * @param int $duration in seconds
 * @return string
 */
function report_usersessions_format_duration($duration) {

    // NOTE: The session duration is not accurate thanks to
    //       $CFG->session_update_timemodified_frequency setting.
    //       Also there is no point in showing days here because
    //       the session cleanup should purge all stale sessions
    //       regularly.

    if ($duration < 60) {
        return get_string('now');
    }

    if ($duration < 60 * 60 * 2) {
        $minutes = (int)($duration / 60);
        $ago = $minutes . ' ' . get_string('minutes');
        return get_string('ago', 'core_message', $ago);
    }

    $hours = (int)($duration / (60 * 60));
    $ago = $hours . ' ' . get_string('hours');
    return get_string('ago', 'core_message', $ago);
}

/**
 * Show some user friendly IP address info.
 *
 * @param string $ip
 * @return string
 */
function report_usersessions_format_ip($ip) {
    if (strpos($ip, ':') !== false) {
        // For now ipv6 is not supported yet.
        return $ip;
    }
    $url = new moodle_url('/iplookup/index.php', array('ip' => $ip));
    return html_writer::link($url, $ip);
}

/**
 * Kill user session.
 *
 * @param int $id
 * @return void
 */
function report_usersessions_kill_session(int $id): void {
    global $USER;

    $sessions = \core\session\manager::get_sessions_by_userid($USER->id);
    $filteredsessions = array_filter($sessions, fn ($session) => $session->id === $id);

    foreach ($filteredsessions as $session) {
        if ($session->sid !== session_id()) {
            \core\session\manager::destroy($session->sid);
        }
    }
}
