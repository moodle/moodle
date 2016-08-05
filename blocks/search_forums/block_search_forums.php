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
 * Block to search forum posts.
 *
 * @package   block_search_forums
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_search_forums extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_search_forums');
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content->text   = '';
            return $this->content;
        }

        $output = $this->page->get_renderer('block_search_forums');
        $searchform = new \block_search_forums\output\search_form($this->page->course->id);
        $this->content->text = $output->render($searchform);

        return $this->content;
    }

    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }

    /**
     * Returns the role that best describes the forum search block.
     *
     * @return string
     */
    public function get_aria_role() {
        return 'search';
    }
}


