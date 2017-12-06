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
 * Attendance Block
 *
 * @package    block_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays information about Attendance Module in this course.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_attendance extends block_base {

    /**
     * Set the initial properties for the block
     */
    public function init() {
        $this->title = get_string('blockname', 'block_attendance');
    }

    /**
     * Gets the content for this block
     *
     * @return object $this->content
     */
    public function get_content() {
        global $CFG, $USER, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';

        $attendances = get_all_instances_in_course('attendance', $COURSE, null, true);
        if (count($attendances) == 0) {
             $this->content->text = get_string('needactivity', 'block_attendance');;
             return $this->content;
        }

        require_once($CFG->dirroot.'/mod/attendance/locallib.php');
        require_once($CFG->dirroot.'/mod/attendance/renderhelpers.php');

        foreach ($attendances as $attinst) {
            $cmid = $attinst->coursemodule;
            $cm  = get_coursemodule_from_id('attendance', $cmid, $COURSE->id, false, MUST_EXIST);
            $context = context_module::instance($cmid, MUST_EXIST);
            $divided = $this->divide_databasetable_and_coursemodule_data($attinst);

            $att = new mod_attendance_structure($divided->atttable, $divided->cm, $COURSE, $context);

            $this->content->text .= html_writer::link($att->url_view(), html_writer::tag('b', format_string($att->name)));
            $this->content->text .= html_writer::empty_tag('br');

            // Link to attendance.

            if (has_capability('mod/attendance:takeattendances', $context) or
                has_capability('mod/attendance:changeattendances', $context)) {
                $this->content->text .= html_writer::link($att->url_manage(array('from' => 'block')),
                                                                           get_string('takeattendance', 'attendance'));
                $this->content->text .= html_writer::empty_tag('br');
            }
            if (has_capability('mod/attendance:manageattendances', $context)) {
                $url = $att->url_sessions(array('action' => mod_attendance_sessions_page_params::ACTION_ADD));
                $this->content->text .= html_writer::link($url, get_string('add', 'attendance'));
                $this->content->text .= html_writer::empty_tag('br');
            }
            if (has_capability('mod/attendance:viewreports', $context)) {
                $this->content->text .= html_writer::link($att->url_report(), get_string('report', 'attendance'));
                $this->content->text .= html_writer::empty_tag('br');
            }

            if (has_capability('mod/attendance:canbelisted', $context, null, false) &&
                has_capability('mod/attendance:view', $context)) {
                $this->content->text .= construct_full_user_stat_html_table($attinst, $COURSE, $USER, $cm);
            }
            $this->content->text .= "<br />";
        }
        return $this->content;
    }

    /**
     * parses data to pass into construct.
     * @param object $alldata
     * @return array
     */
    private function divide_databasetable_and_coursemodule_data($alldata) {
        static $cmfields;

        if (!isset($cmfields)) {
            $cmfields = array(
                    'coursemodule' => 'id',
                    'section' => 'section',
                    'visible' => 'visible',
                    'groupmode' => 'groupmode',
                    'groupingid' => 'groupingid',
                    'groupmembersonly' => 'groupmembersonly');
        }

        $atttable = new stdClass();
        $cm = new stdClass();
        foreach ($alldata as $field => $value) {
            if (array_key_exists($field, $cmfields)) {
                $cm->{$cmfields[$field]} = $value;
            } else {
                $atttable->{$field} = $value;
            }
        }

        $ret = new stdClass();
        $ret->atttable = $atttable;
        $ret->cm = $cm;

        return $ret;
    }

    /**
     * Set the applicable formats for this block
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true, 'my' => false, 'admin' => false, 'tag' => false);
    }
}
