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
 * Course overview block
 *
 * @package    block
 * @subpackage course_overview
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot.'/blocks/course_overview/locallib.php');

class block_course_overview extends block_base {
    /**
     * block initializations
     */
    public function init() {
        $this->title   = get_string('displaytitle', 'block_course_overview');
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $USER, $CFG, $DB;
        require_once($CFG->dirroot.'/user/profile/lib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        $config = get_config('block_course_overview');

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $content = array();

        $moving = optional_param('course_moveid', 0, PARAM_INT);
        $updatemynumber = optional_param('mynumber', null, PARAM_INT);
        if (!is_null($updatemynumber)) {
            block_course_overview_update_mynumber($updatemynumber);
        }

        profile_load_custom_fields($USER);
        list($courses_sorted, $courses_total) = block_course_overview_get_sorted_courses();
        $overviews = block_course_overview_get_overviews($courses_sorted);

        $renderer = $this->page->get_renderer('block_course_overview');
        if (!isset($config->showwelcomearea) || $config->showwelcomearea) {
            $this->content->text = $renderer->welcome_area();
        }

        //number of sites to display
        if ($this->page->user_is_editing()) {
            $count = count(enrol_get_my_courses('id'));
            $this->content->text .= $renderer->editing_bar_head($count);
        }

        if (empty($courses_sorted)) {
            $this->content->text .= get_string('nocourses','my');
        } else {
            //for each course, build category cache
            $this->content->text .= $renderer->course_overview($courses_sorted, $overviews, $moving);
            $this->content->text .= $renderer->hidden_courses($courses_total - count($courses_sorted));
            if ($this->page->user_is_editing() && ajaxenabled() && !$moving) {
                $this->page->requires->js_init_call('M.block_course_overview.add_handles');
            }
        }

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my-index' => true);
    }

    public function instance_can_be_hidden() {
        return false;
    }

    public function hide_header() {
        return true;
    }
}
?>
