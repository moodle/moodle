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
 * @package   block_mycourses
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_mycourses extends block_base {

    function init() {
        $this->title = get_string('title', 'block_mycourses');
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // Check if the tab to select wasn't passed in the URL, if so see if the user has any preference.
        if (!$tab = optional_param('mycoursestab', null, PARAM_ALPHA)) {
            $tab = 'inprogress';
        }

        $renderable = new \block_mycourses\output\main($tab);
        $renderer = $this->page->get_renderer('block_mycourses');
        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        return array('all' => false,
                     'site' => false,
                     'site-index' => false,
                     'course-view' => false, 
                     'course-view-social' => false,
                     'mod' => false,
                     'my' => true,
                     'mod-quiz' => false);
    }

    public function instance_allow_multiple() {
        return false;
    }

    function has_config() {return true;}

    public function cron() {
        mtrace( "Hey, my cron script is running" );
        // do something
        return true;
    }
}
