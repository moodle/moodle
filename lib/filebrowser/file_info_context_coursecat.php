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
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a course category context in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_coursecat extends file_info {
    /** @var stdClass Category object */
    protected $category;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stdClass $category category object
     */
    public function __construct($browser, $context, $category) {
        parent::__construct($browser, $context);
        $this->category = $category;
    }

    /**
     * Return information about this specific context level
     *
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return fileinfo|null
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        global $DB;

        if (!$this->category->visible and !has_capability('moodle/category:viewhiddencategories', $this->context)) {
            if (empty($component)) {
                // we can not list the category contents, so try parent, or top system
                if ($this->category->parent and $pc = $DB->get_record('course_categories', array('id'=>$this->category->parent))) {
                    $parent = context_coursecat::instance($pc->id, IGNORE_MISSING);
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

    /**
     * Return a file from course category description area
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return fileinfo|null
     */
    protected function get_area_coursecat_description($itemid, $filepath, $filename) {
        global $CFG;

        if (!$this->category->visible and !has_capability('moodle/category:viewhiddencategories', $this->context)) {
            return null;
        }
        if (!has_capability('moodle/category:manage', $this->context)) {
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
     *
     * @return string
     */
    public function get_visible_name() {
        return format_string($this->category->name, true, array('context'=>$this->context));
    }

    /**
     * Whether or not new files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Whether or not this is a directory
     *
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns list of children.
     *
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
            $context = context_coursecat::instance($category->id);
            if (!$category->visible and !has_capability('moodle/category:viewhiddencategories', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        $courses = $DB->get_records('course', array('category'=>$this->category->id), 'sortorder', 'id,visible');
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
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
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        global $DB;
        $cnt = 0;
        if (($child = $this->get_area_coursecat_description(0, '/', '.'))
                && $child->count_non_empty_children($extensions) && (++$cnt) >= $limit) {
            return $cnt;
        }

        $rs = $DB->get_recordset_sql('SELECT ctx.id AS contextid, c.visible
                FROM {context} ctx, {course} c
                WHERE ctx.instanceid = c.id
                AND ctx.contextlevel = :courselevel
                AND c.category = :categoryid
                ORDER BY c.visible DESC', // retrieve visible courses first
                array('categoryid' => $this->category->id, 'courselevel' => CONTEXT_COURSE));
        foreach ($rs as $record) {
            $context = context::instance_by_id($record->contextid);
            if (!$record->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            if (($child = $this->browser->get_file_info($context))
                    && $child->count_non_empty_children($extensions) && (++$cnt) >= $limit) {
                break;
            }
        }
        $rs->close();
        if ($cnt >= $limit) {
            return $cnt;
        }

        $rs = $DB->get_recordset_sql('SELECT ctx.id AS contextid, cat.visible
                FROM {context} ctx, {course_categories} cat
                WHERE ctx.instanceid = cat.id
                AND ctx.contextlevel = :catlevel
                AND cat.parent = :categoryid
                ORDER BY cat.visible DESC', // retrieve visible categories first
                array('categoryid' => $this->category->id, 'catlevel' => CONTEXT_COURSECAT));
        foreach ($rs as $record) {
            $context = context::instance_by_id($record->contextid);
            if (!$record->visible and !has_capability('moodle/category:viewhiddencategories', $context)) {
                continue;
            }
            if (($child = $this->browser->get_file_info($context))
                    && $child->count_non_empty_children($extensions) && (++$cnt) >= $limit) {
                break;
            }
        }
        $rs->close();

        return $cnt;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info|null fileinfo instance or null for root directory
     */
    public function get_parent() {
        $parent = $this->context->get_parent_context();
        return $this->browser->get_file_info($parent);
    }
}
