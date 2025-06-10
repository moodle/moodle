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
 * Course Hider Tool
 *
 * @package   block_course_hider
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Block Base
// class block_course_hider extends block_base {

// Or Block List
class block_course_hider extends block_list {

    public $listview;

    function init() {
        $this->title = get_string('pluginname', 'block_course_hider');
    }

    /**
     * Indicates that this block has its own configuration settings
     * @return @bool
     */
    public function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $OUTPUT;

        $this->listview = true;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = $this->get_new_content_container();

        // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        if (! empty($this->config->text)) {
            $this->content->text = $this->config->text;
        }

        $this->content->text = '';
        if (empty($currentcontext)) {
            return $this->content;
        }
        if ($this->page->course->id == SITEID) {
            $this->content->text .= "site context";
        }

        if (! empty($this->config->text)) {
            $this->content->text .= $this->config->text;
        }

        return $this->content;
    }

    public function instance_allow_multiple() {
          return false;
    }

    /**
     * [cron description]
     * @return [type] [description]
     */
    public function cron() {
        return true;
    }

    /**
     * Returns an empty "block list" content container to be filled with content.
     *
     * @return @object
     */
    private function get_new_content_container() {
        $content = new stdClass;
        $content->items = array();
        $content->icons = array();
        $content->footer = '';

        return $content;
    }
}
