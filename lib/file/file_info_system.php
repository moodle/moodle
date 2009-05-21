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
 * Utility class for browsing of system files.
 *
 * @package    moodlecore
 * @subpackage file-browser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents the system context in the tree navigated by @see{file_browser}.
 */
class file_info_system extends file_info {
    public function __construct($browser) {
        parent::__construct($browser, get_context_instance(CONTEXT_SYSTEM));
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
        return get_string('arearoot', 'repository');
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
        global $DB, $USER;

        $children = array();

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id))) {
            $children[] = $child;
        }

        $course_cats = $DB->get_records('course_categories', array('parent'=>0), 'sortorder');
        foreach ($course_cats as $category) {
            $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
            if (!$category->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        $courses = $DB->get_records('course', array('category'=>0), 'sortorder');
        foreach ($courses as $course) {
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            if ($child = $this->browser->get_file_info($context)) {
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
        return null;
    }
}
