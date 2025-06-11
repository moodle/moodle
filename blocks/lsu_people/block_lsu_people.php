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
 * @package    block_lsu_people
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Required as it's not a visitable page.
defined('MOODLE_INTERNAL') || die();

class block_lsu_people extends block_base {

    // Base function for initializing the block.
    public function init() {
        $this->title = get_string('pluginname', 'block_lsu_people');
    }

    // Where this can be added.
    public function applicable_formats() {
        return ['course-view' => true];
    }

    // Do not allow more than one.
    public function instance_allow_multiple() {
        return false;
    }

    // main function for getting block data.
    public function get_content() {
        global $COURSE, $OUTPUT;

        // If we don't have content, don't show content.
        if ($this->content !== null) {
            return $this->content;
        }

        // Buid out the content class.
        $this->content = new stdClass();

        // Set the default base url with course and group.
        $url = new moodle_url('/blocks/lsu_people/view.php',
            ['id' => $COURSE->id, 'group' => 0]
        );

        // Add the link with the string.
        $this->content->text = html_writer::link($url, get_string('courseroster', 'block_lsu_people'));

        // Footer should be nothing.
        $this->content->footer = '';

        // Return the data.
        return $this->content;
    }
}
