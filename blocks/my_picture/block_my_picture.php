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
 * Main class for block_my_picture
 * Connects to LSU web service for downloading and updating user photos
 *
 * @package    block_my_picture
 * @copyright  2008, Adam Zapletal, 2017, Robert Russo, Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set and get the config variable.
global $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/blocks/my_picture/lib.php');

// The main class for the my_picture system.
class block_my_picture extends block_list {
    // Initialise the block.
    public function init() {
        $this->title = get_string('pluginname', 'block_my_picture');
    }

    // Allows configuration within Moodle.
    public function has_config() {
        return true;
    }

    // Returns the applicable formats for this block.
    // @return array of applicable formats.
    public function applicable_formats() {
        return array(
            'site' => true,
            'my' => true,
            'site-index' => true,
            'course-view' => true,
        );
    }

    // Return the content of this block.
    // @return stdClass the content.
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $OUTPUT;

        $this->content = new stdClass;
        $this->content->icons = array();
        $this->content->items = array();
        $this->content->footer = '';

        $reprocessstr = get_string('reprocess', 'block_my_picture');

        $reprocesshelp = $OUTPUT->help_icon('pluginname', 'block_my_picture');

        $reprocesslink = '
            <a href = "' . $CFG->wwwroot . '/blocks/my_picture/reprocess.php"
               alt = "'. $reprocessstr . '">' . $reprocessstr . '</a> ' .
               $reprocesshelp;

        $this->content->items[] = $reprocesslink;

        return $this->content;
    }
}