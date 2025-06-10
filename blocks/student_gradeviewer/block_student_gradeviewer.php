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
 *
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Philip Cali, Jason Peak, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_student_gradeviewer extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_student_gradeviewer');
    }

    public function applicable_formats() {
        return array('site' => true, 'my' => true, 'course' => false);
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        global $CFG, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $s = function($key, $a = null) {
            return get_string($key, 'block_student_gradeviewer', $a);
        };

        $content = new stdClass;
        $content->items = array();
        $content->icons = array();
        $content->footer = '';

        $context = context_system::instance();

        $admin = (
            has_capability('block/student_gradeviewer:academicadmin', $context) or
            has_capability('block/student_gradeviewer:sportsadmin', $context)
        );

        if ($admin) {
            $url = new moodle_url('/blocks/student_gradeviewer/admin.php');
            $content->items[] = html_writer::link($url, $s('admin'));
        }

        $this->content = $content;

        return $this->content;
    }
}
