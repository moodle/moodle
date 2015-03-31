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
 * Course summary block
 *
 * @package    block_course_summary
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_summary extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_course_summary');
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'tag' => false, 'my' => false);
    }

    function specialization() {
        if($this->page->pagetype == PAGE_COURSE_VIEW && $this->page->course->id != SITEID) {
            $this->title = get_string('coursesummary', 'block_course_summary');
        }
    }

    function get_content() {
        global $CFG, $OUTPUT;

        require_once($CFG->libdir . '/filelib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new stdClass();
        $options = new stdClass();
        $options->noclean = true;    // Don't clean Javascripts etc
        $options->overflowdiv = true;
        $context = context_course::instance($this->page->course->id);
        $this->page->course->summary = file_rewrite_pluginfile_urls($this->page->course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);
        $this->content->text = format_text($this->page->course->summary, $this->page->course->summaryformat, $options);
        if ($this->page->user_is_editing()) {
            if($this->page->course->id == SITEID) {
                $editpage = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=frontpagesettings';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->page->course->id;
            }
            $this->content->text .= "<div class=\"editbutton\"><a href=\"$editpage\"><img src=\"" . $OUTPUT->pix_url('t/edit') . "\" alt=\"".get_string('edit')."\" /></a></div>";
        }
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {
        return true;
    }

}


