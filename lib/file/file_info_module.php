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
 * Utility class for browsing of module files.
 *
 * @package    moodlecore
 * @subpackage file-browser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a module context in the tree navigated by @see{file_browser}.
 */
class file_info_module extends file_info {
    protected $course;
    protected $cm;
    protected $areas;

    public function __construct($browser, $course, $cm, $context, $areas) {
        global $DB;
        parent::__construct($browser, $context);
        $this->course = $course;
        $this->cm     = $cm;
        $this->areas  = $areas;
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
        return $this->cm->name.' ('.get_string('modulename', $this->cm->modname).')';
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
        foreach ($this->areas as $area=>$desctiption) {
            if ($child = $this->browser->get_file_info($this->context, $area, null)) {
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
        $pcid = get_parent_contextid($this->context);
        $parent = get_context_instance_by_id($pcid);
        return $this->browser->get_file_info($parent);
    }
}
