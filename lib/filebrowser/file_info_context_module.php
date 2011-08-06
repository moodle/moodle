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
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a module context in the tree navigated by @see{file_browser}.
 *
 * @package    core
 * @subpackage filebrowser
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_module extends file_info {
    protected $course;
    protected $cm;
    protected $modname;
    protected $areas;

    public function __construct($browser, $context, $course, $cm, $modname) {
        global $CFG;

        parent::__construct($browser, $context);
        $this->course  = $course;
        $this->cm      = $cm;
        $this->modname = $modname;

        include_once("$CFG->dirroot/mod/$modname/lib.php");

        //find out all supported areas
        $functionname     = 'mod_'.$modname.'_get_file_areas';
        $functionname_old = $modname.'_get_file_areas';

        if (function_exists($functionname)) {
            $this->areas = $functionname($course, $cm, $context);
        } else if (function_exists($functionname_old)) {
            $this->areas = $functionname_old($course, $cm, $context);
        } else {
            $this->areas = array();
        }
        unset($this->areas['intro']); // hardcoded, ignore attempts to override it
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
        // try to emulate require_login() tests here
        if (!isloggedin()) {
            return null;
        }

        $coursecontext = get_course_context($this->context);
        if (!$this->course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
            return null;
        }

        if (!is_viewing($this->context) and !is_enrolled($this->context)) {
            // no peaking here if not enrolled or inspector
            return null;
        }

        $modinfo = get_fast_modinfo($this->course);
        $cminfo = $modinfo->get_cm($this->cm->id);
        if (!$cminfo->uservisible) {
            // activity hidden sorry
            return null;
        }

        if (empty($component)) {
            return $this;
        }

        if ($component == 'mod_'.$this->modname and $filearea === 'intro') {
            return $this->get_area_intro($itemid, $filepath, $filename);
        } else if ($component == 'backup' and $filearea === 'activity') {
            return $this->get_area_backup($itemid, $filepath, $filename);
        }

        $functionname     = 'mod_'.$this->modname.'_get_file_info';
        $functionname_old = $this->modname.'_get_file_info';

        if (function_exists($functionname)) {
            return $functionname($this->browser, $this->areas, $this->course, $this->cm, $this->context, $filearea, $itemid, $filepath, $filename);
        } else if (function_exists($functionname_old)) {
            return $functionname_old($this->browser, $this->areas, $this->course, $this->cm, $this->context, $filearea, $itemid, $filepath, $filename);
        }

        return null;
    }

    protected function get_area_intro($itemid, $filepath, $filename) {
        global $CFG;

        if (!plugin_supports('mod', $this->modname, FEATURE_MOD_INTRO, true) or !has_capability('moodle/course:managefiles', $this->context)) {
            return null;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'mod_'.$this->modname, 'intro', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'mod_'.$this->modname, 'intro', 0);
            } else {
                // not found
                return null;
            }
        }

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('moduleintro'), false, true, true, false);
    }

    protected function get_area_backup($itemid, $filepath, $filename) {
        global $CFG;

        if (!has_capability('moodle/backup:backupactivity', $this->context)) {
            return null;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'backup', 'activity', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'backup', 'activity', 0);
            } else {
                // not found
                return null;
            }
        }

        $downloadable = has_capability('moodle/backup:downloadfile', $this->context);
        $uploadable   = has_capability('moodle/restore:uploadfile', $this->context);

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('activitybackup', 'repository'), false, $downloadable, $uploadable, false);
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
     * Is this empty area?
     *
     * @return bool
     */
    public function is_empty_area() {
        if ($child = $this->get_area_backup(0, '/', '.')) {
            if (!$child->is_empty_area()) {
                return false;
            }
        }
        if ($child = $this->get_area_intro(0, '/', '.')) {
            if (!$child->is_empty_area()) {
                return false;
            }
        }

        foreach ($this->areas as $area=>$desctiption) {
            if ($child = $this->get_file_info('mod_'.$this->modname, $area, null, null, null)) {
                if (!$child->is_empty_area()) {
                    return false;
                }
            }
        }

        return true;
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

        if ($child = $this->get_area_backup(0, '/', '.')) {
            $children[] = $child;
        }
        if ($child = $this->get_area_intro(0, '/', '.')) {
            $children[] = $child;
        }

        foreach ($this->areas as $area=>$desctiption) {
            if ($child = $this->get_file_info('mod_'.$this->modname, $area, null, null, null)) {
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
