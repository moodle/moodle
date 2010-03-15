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
 * @package    moodlecore
 * @subpackage repository
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot. '/repository/lib.php');

class block_private_files extends block_base {

    function init() {
        $this->title = get_string('privatefiles', 'block_private_files');
        $this->version = 2010030100;
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
        global $CFG, $USER, $PAGE;
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }
        $this->content->text = '';
        $this->content->footer = '';
        if (isloggedin() && !isguestuser()) {   // Show the block
            $usercontext = get_context_instance(CONTEXT_USER, $USER->id);
            $client_id = uniqid();

            $params = new stdclass;
            $params->accepted_types = '*';
            $params->return_types = FILE_INTERNAL;
            $params->context = $PAGE->context;
            $params->env = 'filemanager';

            $filepicker_options = initialise_filepicker($params);

            $fs = get_file_storage();
            $draftfiles = $fs->get_area_files($usercontext->id, 'user_private', 0, 'id', false);
            // the number of existing files in user private area
            $filecount = count($draftfiles);

            // read existing user private files
            $options = file_get_user_area_files(0, '/', 'user_private');
            $options->maxbytes  = -1;
            $options->maxfiles  = -1;
            $options->filearea  = 'user_private';
            $options->client_id = $client_id;
            $options->filecount = $filecount;
            $options->itemid    = 0;
            $options->subdirs   = true;
            // store filepicker options
            $options->filepicker = $filepicker_options;

            $this->content = new stdClass;
            $this->content->text = print_filemanager($options, true);
;
            $this->content->footer = '';

        }
        return $this->content;
    }
}
