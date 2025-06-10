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
 * @package    block_migrate_users
 * @copyright  2019 onwards Louisiana State University
 * @copyright  2019 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/blocks/migrate_users/locallib.php');

class block_migrate_users extends block_base {

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
        $this->set_course_context();
    }

    /**
     * All multiple instances of this block
     * @return bool Returns true
     */
    function instance_allow_multiple() {
        return false;
    }

    public function get_title() {
        return get_string('migrate_users', 'block_migrate_users');
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
     * @return context
     */
    private function set_system_context()
    {
        $this->system_context = context_system::instance();
    }

    /**
     * Returns this course's context
     *
     * @return context
     */
    private function set_course_context()
    {
        $this->course_context = context_course::instance($this->course->id);
    }

    /**
     * Indicates which pages types this block may be added to.
     * Specifically only allowed users can add the block to a course.
     *
     * @return array
     */
    public function applicable_formats() {
        if (migrate::can_use()) {
            return array(
                'site' => false,
                'my' => false,
                'course-view' => true
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

        $renderer = $this->page->get_renderer('block_migrate_users');
	$this->content = new stdClass();

        if (migrate::can_use()) {
            $this->content->text = '';
            $this->content->footer = $renderer->users_form(
                new moodle_url($CFG->wwwroot . '/blocks/migrate_users/migrate.php'),
                optional_param('userfrom', '', PARAM_TEXT),
                optional_param('userto', '', PARAM_TEXT),
                optional_param('courseid', $this->course->id, PARAM_INT)
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
