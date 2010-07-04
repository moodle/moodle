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


require_once($CFG->dirroot. '/repository/lib.php');

class block_private_files extends block_base {

    function init() {
        $this->title = get_string('areauserpersonal', 'repository');
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

        $this->content->text = '';
        $this->content->footer = '';
        if (isloggedin() && !isguestuser()) {   // Show the block

            $options = new stdclass;
            $options->maxbytes  = -1;
            $options->maxfiles  = -1;
            $options->subdirs   = true;
            $options->accepted_types = '*';
            $options->return_types = FILE_INTERNAL;
            $options->context   = $PAGE->context;
            $options->disable_types = array('user');

            $this->content = new object();

            //TODO: add capability check here!

            //TODO: add list of available files here
            $this->content->text = $OUTPUT->single_button(new moodle_url('/blocks/private_files/edit.php'), get_string('edit'), 'get');
;
            $this->content->footer = '';

        }
        return $this->content;
    }
}
