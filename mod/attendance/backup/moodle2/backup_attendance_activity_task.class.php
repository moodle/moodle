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
 * Class {@link backup_attendance_activity_task} definition
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/attendance/backup/moodle2/backup_attendance_stepslib.php');

/**
 * Provides all the settings and steps to perform one complete backup of attendance activity
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_attendance_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        $this->add_step(new backup_attendance_activity_structure_step('attendance_structure', 'attendance.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     * @param string $content
     * @return string
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to attendance view by moduleid.
        $search = "/(" . $base . "\/mod\/attendance\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@ATTENDANCEVIEWBYID*$2@$', $content);

        // Link to attendance view by moduleid and studentid.
        $search = "/(" . $base . "\/mod\/attendance\/view.php\?id\=)([0-9]+)\&studentid\=([0-9]+)/";
        $content = preg_replace($search, '$@ATTENDANCEVIEWBYIDSTUD*$2*$3@$', $content);

        return $content;
    }
}
