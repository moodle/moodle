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
 * Utility class for browsing of course files.
 *
 * @package    moodlecore
 * @subpackage file-browser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a course context in the tree navigated by @see{file_browser}.
 */
class file_info_course extends file_info {
    protected $course;

    public function __construct($browser, $context, $course) {
        global $DB;
        parent::__construct($browser, $context);
        $this->course   = $course;
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

    public function get_visible_name() {
        return ($this->course->id == SITEID) ? get_string('frontpage', 'admin') : format_string($this->course->fullname);
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
        $children = array();

        if ($child = $this->browser->get_file_info($this->context, 'course_intro', 0)) {
            $children[] = $child;
        }
        if ($child = $this->browser->get_file_info($this->context, 'course_section')) {
            $children[] = $child;
        }
        if ($child = $this->browser->get_file_info($this->context, 'section_backup')) {
            $children[] = $child;
        }

        if ($child = $this->browser->get_file_info($this->context, 'course_backup', 0)) {
            $children[] = $child;
        }

        if ($this->course->legacyfiles == 2) {
            if ($child = $this->browser->get_file_info($this->context, 'course_content', 0)) {
                $children[] = $child;
            }
        }

        $modinfo = get_fast_modinfo($this->course);
        foreach ($modinfo->cms as $cminfo) {
            if (empty($cminfo->uservisible)) {
                continue;
            }
            $modcontext = get_context_instance(CONTEXT_MODULE, $cminfo->id);
            if ($child = $this->browser->get_file_info($modcontext)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        //TODO: error checking if get_parent_contextid() returns false
        $pcid = get_parent_contextid($this->context);
        $parent = get_context_instance_by_id($pcid);
        return $this->browser->get_file_info($parent);
    }
}
