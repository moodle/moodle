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
 * Utility class for browsing of user files.
 *
 * @package    moodlecore
 * @subpackage file-browser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a user context in the tree navigated by @see{file_browser}.
 */
class file_info_user extends file_info {
    protected $user;

    public function __construct($browser, $context) {
        global $DB, $USER;

        parent::__construct($browser, $context);

        $userid = $context->instanceid;

        if ($userid == $USER->id) {
            $this->user = $USER;
        } else {
            // if context exists user record should exist too ;-)
            $this->user = $DB->get_record('user', array('id'=>$userid));
        }
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    /**
     * Returns localised visible name.
     * @return string
     */
    public function get_visible_name() {
        return fullname($this->user, true);
    }

    /**
     * Can I add new files or directories?
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Is directory?
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns list of children.
     * @return array of file_info instances
     */
    public function get_children() {
        global $USER, $CFG;

        $children = array();

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id), 'user_private', 0)) {
            $children[] = $child;
        }

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id), 'user_profile', 0)) {
            $children[] = $child;
        }

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id), 'user_backup', 0)) {
            $children[] = $child;
        }

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id), 'user_private', 0)) {
            $children[] = $child;
        }
        // do not list user_draft here - it is browsable only if you know the draft itemid ;-)

        return $children;
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info(get_context_instance(CONTEXT_SYSTEM));
    }
}
