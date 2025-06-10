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

/**
 * Strings for component 'attendanceregister', language 'en_us', version '4.1'.
 *
 * @package     attendanceregister
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['force_recalc_all_session_help'] = 'Delete and recalculate all online Sessions of all tracked Users.<br />
    Normally you <b>do not need to do it</b>!<br />
    New Sessions are automatically calculated in background (after some delay).<br />
    This operation may be useful <b>only</b>:
    <ul>
      <li>After changing the Role of a User that previously acted in any of the tracked Courses  with a different Role
      (i.e. changing from Teacher to Student, when Studet are tracked and Teacher are not).</li>
      <li>After modifying Register settings that affects Sessions calculation
      (i.e. <i>Attendance Tracking Mode</i>, <i>Online Session timeout</i>)</li>
    </ul>
    You <b>do not need to recalculate when enrolling new Users</b>!<br /><br />
    Recalculation can be executed immediately or scheduled for execution by the next cron.
    Scheduled execution could be more efficient for very crowded courses.';
