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
 * Section links block
 *
 * @package    moodlecore
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_section_links extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_section_links');
    }

    function instance_config($instance) {
        global $DB;

        parent::instance_config($instance);
        $course = $this->page->course;
        if (isset($course->format)) {
            if ($course->format == 'topics') {
                $this->title = get_string('topics', 'block_section_links');
            } else if ($course->format == 'weeks') {
                $this->title = get_string('weeks', 'block_section_links');
            } else {
                $this->title = get_string('pluginname', 'block_section_links');
            }
        }
    }

    function applicable_formats() {
        return (array('course-view-weeks' => true, 'course-view-topics' => true));
    }

    function get_content() {
        global $CFG, $USER, $DB;

        $highlight = 0;
        if(isset($this->config)){
            $config = $this->config;
        } else{
            $config = get_config('blocks/section_links');
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = $this->page->course;
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if ($course->format == 'weeks' or $course->format == 'weekscss') {
            $highlight = ceil((time()-$course->startdate)/604800);
            $linktext = get_string('jumptocurrentweek', 'block_section_links');
            $sectionname = 'week';
        }
        else if ($course->format == 'topics') {
            $highlight = $course->marker;
            $linktext = get_string('jumptocurrenttopic', 'block_section_links');
            $sectionname = 'topic';
        }
        $inc = 1;

        if(!empty($config->numsections1) and ($course->numsections > $config->numsections1)) {
            $inc = $config->incby1;
        } else {
            if ($course->numsections > 22) {
                $inc = 2;
            }
        }

        if(!empty($config->numsections2) and ($course->numsections > $config->numsections2)) {
            $inc = $config->incby2;
        } else {
            if ($course->numsections > 40) {
                $inc = 5;
            }
        }

        if (isloggedin()) {
            $display = $DB->get_field('course_display', 'display', array('course'=>$this->page->course->id, 'userid'=>$USER->id));
        }
        if (!empty($display)) {
            $link = $CFG->wwwroot.'/course/view.php?id='.$this->page->course->id.'&amp;'.$sectionname.'=';
        } else {
            $link = '#section-';
        }

        $sql = "SELECT section, visible
                  FROM {course_sections}
                 WHERE course = ? AND
                       section < ".($course->numsections+1)."
              ORDER BY section";

        if ($sections = $DB->get_records_sql($sql, array($course->id))) {
            $text = '<ol class="inline-list">';
            for ($i = $inc; $i <= $course->numsections; $i += $inc) {
                if (!isset($sections[$i])) {
                    continue;
                }
                $isvisible = $sections[$i]->visible;
                if (!$isvisible and !has_capability('moodle/course:update', $context)) {
                    continue;
                }
                $style = ($isvisible) ? '' : ' class="dimmed"';
                if ($i == $highlight) {
                    $text .= "<li><a href=\"$link$i\"$style><strong>$i</strong></a></li>\n";
                } else {
                    $text .= "<li><a href=\"$link$i\"$style>$i</a></li>\n";
                }
            }
            $text .= '</ol>';
            if ($highlight and isset($sections[$highlight])) {
                $isvisible = $sections[$highlight]->visible;
                if ($isvisible or has_capability('moodle/course:update', $context)) {
                    $style = ($isvisible) ? '' : ' class="dimmed"';
                    $text .= "\n<a href=\"$link$highlight\"$style>$linktext</a>";
                }
            }
        }

        $this->content->text = $text;
        return $this->content;
    }
    /**
     * Has instance config
     * @return boolean
     **/
    function instance_allow_config() {
        return true;
    }
    function before_delete() {
        global $DB;
        $DB->delete_records('config_plugins', array('plugin' => 'blocks/section_links'));
    }

    function has_config() {
        return true;
    }
}


