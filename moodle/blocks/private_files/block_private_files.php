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
 * Manage user private area files
 *
 * @package    block_private_files
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_private_files extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_private_files');
    }

    function specialization() {
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG, $USER, $PAGE, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        if (isloggedin() && !isguestuser()) {   // Show the block
            $this->content = new stdClass();

            //TODO: add capability check here!

            $renderer = $this->page->get_renderer('block_private_files');
            $this->content->text = $renderer->private_files_tree();
            if (has_capability('moodle/user:manageownfiles', $this->context)) {
                $this->content->footer = html_writer::link(
                    new moodle_url('/user/files.php', array('returnurl' => $PAGE->url->out())),
                    get_string('privatefilesmanage') . '...');
            }

        }
        return $this->content;
    }
}
