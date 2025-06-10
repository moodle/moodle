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
 * @package    block_helpdesk
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_helpdesk extends block_list {
    public function init() {
        $this->title = get_string('pluginname', 'block_helpdesk');
    }

    public function applicable_formats() {
        return array('site' => true, 'my' => true, 'course' => false);
    }

    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $context = context_system::instance();
        if (!has_capability('block/helpdesk:viewenrollments', $context)) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // TODO 10/11/19 Remove $searchcourse and $searchusers when refactoring.
        $searchcourse = get_string('search_courses', 'block_helpdesk');
        $searchusers = get_string('search_users', 'block_helpdesk');

        $genlink = function ($mode) {
            return html_writer::link(
                new moodle_url('/blocks/helpdesk/', array('mode' => $mode)),
                get_string("search_{$mode}s", 'block_helpdesk')
            );
        };

        $this->content->items[] = $genlink('course');
        $this->content->items[] = $genlink('user');

        return $this->content;
    }
}
