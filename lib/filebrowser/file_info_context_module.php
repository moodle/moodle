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
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a module context in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_module extends file_info {
    /** @var stdClass Course object */
    protected $course;
    /** @var cm_info Course module object */
    protected $cm;
    /** @var string Module name */
    protected $modname;
    /** @var array Available file areas */
    protected $areas;
    /** @var array caches the result of last call to get_non_empty_children() */
    protected $nonemptychildren;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stdClass $course course object
     * @param stdClass $cm course module object
     * @param string $modname module name
     */
    public function __construct($browser, $context, $course, $cm, $modname) {
        global $CFG;

        parent::__construct($browser, $context);
        $this->course  = $course;
        $this->cm      = cm_info::create($cm);
        $this->modname = $this->cm->modname;
        $this->nonemptychildren = null;

        if ($functionname = component_callback_exists('mod_'.$modname, 'get_file_areas')) {
            $cm = $this->cm->get_course_module_record();
            $this->areas = $functionname($course, $cm, $context);
        } else {
            $this->areas = array();
        }

        unset($this->areas['intro']); // hardcoded, ignore attempts to override it
    }

    /**
     * Return information about this specific context level
     *
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        // try to emulate require_login() tests here
        if (!isloggedin()) {
            return null;
        }

        $coursecontext = $this->context->get_course_context(true);
        if (!$this->course->visible and !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
            return null;
        }

        if (!is_viewing($this->context) and !$this->browser->is_enrolled($this->course->id)) {
            // no peaking here if not enrolled or inspector
            return null;
        }

        if (!$this->cm->uservisible) {
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

        if ($functionname = component_callback_exists('mod_'.$this->modname, 'get_file_info')) {
            $cm = $this->cm->get_course_module_record();
            return $functionname($this->browser, $this->areas, $this->course, $cm,
                $this->context, $filearea, $itemid, $filepath, $filename);
        }

        return null;
    }

    /**
     * Get a file from module intro area
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null
     */
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

    /**
     * Get a file from module backup area
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null
     */
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
     *
     * @return string
     */
    public function get_visible_name() {
        return $this->cm->get_formatted_name().' ('.$this->cm->get_module_type_name().')';
    }

    /**
     * Whether or not files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Whether or not this is an emtpy area
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
        return $this->get_filtered_children('*', false, true);
    }

    /**
     * Help function to return files matching extensions or their count
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param bool|int $countonly if false returns the children, if an int returns just the
     *    count of children but stops counting when $countonly number of children is reached
     * @param bool $returnemptyfolders if true returns items that don't have matching files inside
     * @return array|int array of file_info instances or the count
     */
    private function get_filtered_children($extensions = '*', $countonly = false, $returnemptyfolders = false) {
        global $DB;
        // prepare list of areas including intro and backup
        $areas = array(
            array('mod_'.$this->modname, 'intro'),
            array('backup', 'activity')
        );
        foreach ($this->areas as $area => $desctiption) {
            $areas[] = array('mod_'.$this->modname, $area);
        }

        $params1 = array('contextid' => $this->context->id, 'emptyfilename' => '.');
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $children = array();
        foreach ($areas as $area) {
            if (!$returnemptyfolders) {
                // fast pre-check if there are any files in the filearea
                $params1['component'] = $area[0];
                $params1['filearea'] = $area[1];
                if (!$DB->record_exists_sql('SELECT 1 from {files}
                        WHERE contextid = :contextid
                        AND filename <> :emptyfilename
                        AND component = :component
                        AND filearea = :filearea '.$sql2,
                        array_merge($params1, $params2))) {
                    continue;
                }
            }
            if ($child = $this->get_file_info($area[0], $area[1], null, null, null)) {
                if ($returnemptyfolders || $child->count_non_empty_children($extensions)) {
                    $children[] = $child;
                    if ($countonly !== false && count($children) >= $countonly) {
                        break;
                    }
                }
            }
        }
        if ($countonly !== false) {
            return count($children);
        }
        return $children;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        if ($this->nonemptychildren !== null) {
            return $this->nonemptychildren;
        }
        $this->nonemptychildren = $this->get_filtered_children($extensions);
        return $this->nonemptychildren;
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        if ($this->nonemptychildren !== null) {
            return count($this->nonemptychildren);
        }
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info|null file_info or null for root
     */
    public function get_parent() {
        $parent = $this->context->get_parent_context();
        return $this->browser->get_file_info($parent);
    }
}
