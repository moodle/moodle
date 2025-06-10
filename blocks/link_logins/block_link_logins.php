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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/blocks/link_logins/locallib.php');

class block_link_logins extends block_base {

    public $course;
    public $nonuser;
    public $system_context;
    public $course_context;
    public $content;

    public function init() {
        $this->title = $this->get_title();
        $this->set_course();
        $this->set_nonuser();
        $this->set_system_context();
    }

    /**
     * All multiple instances of this block
     * @return bool Returns true
     */
    function instance_allow_multiple() {
        return false;
    }

    public function get_title() {
        return get_string('link_logins', 'block_link_logins');
    }

    public function set_course() {
        global $COURSE;

        $this->course = $COURSE;
    }

    public function set_nonuser() {
        global $USER;

        $this->nonuser = $USER;
    }

    /**
     * Returns the system context
     *
     * @return @context
     */
    private function set_system_context()
    {
        $this->system_context = context_system::instance();
    }

    /**
     * Indicates which pages types this block may be added to.
     * Specifically only allowed users can add the block to a course.
     *
     * @return @array
     */
    public function applicable_formats() {
        if (link::can_use()) {
            return array(
                'site' => true,
                'my' => false,
                'course-view' => false
            );
        } else {
            return array(
                'site' => false,
                'my' => false,
                'course-view' => false
            );
        }
    }

    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
          return $this->content;
        }

        $renderer = $this->page->get_renderer('block_link_logins');
        $this->content = new stdClass();

        if (link::can_use()) {
            $this->content->text = '';
            $this->content->footer = $renderer->users_form(
                new moodle_url($CFG->wwwroot . '/blocks/link_logins/link.php'),
                optional_param('existingusername', '', PARAM_TEXT),
                optional_param('prospectiveemail', '', PARAM_TEXT)
            );
        }
        return $this->content;
   }

    /**
     * Indicates that this block has its own configuration settings
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    function _self_test() {
        return true;
    }
}
