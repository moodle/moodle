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
 * Utility class for browsing of curse category files.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a course category context in the tree navigated by @see{file_browser}.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_coursecat extends file_info {
    protected $category;

    public function __construct($browser, $context, $category) {
        parent::__construct($browser, $context);
        $this->category = $category;
    }

    /**
     * Return information about this specific context level
     *
     * @param $component
     * @param $filearea
     * @param $itemid
     * @param $filepath
     * @param $filename
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        global $DB;

        if (!$this->category->visible and !has_capability('moodle/category:viewhiddencategories', $this->context)) {
            if (empty($component)) {
                // we can not list the category contents, so try parent, or top system
                if ($this->category->parent and $pc = $DB->get_record('course_categories', array('id'=>$this->category->parent))) {
                    $parent = get_context_instance(CONTEXT_COURSECAT, $pc->id);
                    return $this->browser->get_file_info($parent);
                } else {
                    return $this->browser->get_file_info();
                }
            }
            return null;
        }

        if (empty($component)) {
            return $this;
        }

        $methodname = "get_area_{$component}_{$filearea}";
        if (method_exists($this, $methodname)) {
            return $this->$methodname($itemid, $filepath, $filename);
        }

        return null;
    }

    protected function get_area_coursecat_description($itemid, $filepath, $filename) {
        global $CFG;

        if (!has_capability('moodle/course:update', $this->context)) {
            return null;
        }

        if (is_null($itemid)) {
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($this->context->id, 'coursecat', 'description', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'coursecat', 'description', 0);
            } else {
                // not found
                return null;
            }
        }

        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areacategoryintro', 'repository'), false, true, true, false);
    }

    /**
     * Returns localised visible name.
     * @return string
     */
    public function get_visible_name() {
        return format_string($this->category->name, true, array('context'=>$this->context));
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
        global $DB;

        $children = array();

        if ($child = $this->get_area_coursecat_description(0, '/', '.')) {
            $children[] = $child;
        }

        $course_cats = $DB->get_records('course_categories', array('parent'=>$this->category->id), 'sortorder', 'id,visible');
        foreach ($course_cats as $category) {
            $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
            if (!$category->visible and !has_capability('moodle/category:viewhiddencategories', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        $courses = $DB->get_records('course', array('category'=>$this->category->id), 'sortorder', 'id,visible');
        foreach ($courses as $course) {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
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
        $cid = get_parent_contextid($this->context);
        $parent = get_context_instance_by_id($cid);
        return $this->browser->get_file_info($parent);
    }
}
