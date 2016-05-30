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
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a course context in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_context_course extends file_info {
    /** @var stdClass course object */
    protected $course;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stdClass $course course object
     */
    public function __construct($browser, $context, $course) {
        parent::__construct($browser, $context);
        $this->course   = $course;
    }

    /**
     * Return information about this specific context level
     *
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    public function get_file_info($component, $filearea, $itemid, $filepath, $filename) {
        // try to emulate require_login() tests here
        if (!isloggedin()) {
            return null;
        }

        if (!$this->course->visible and !has_capability('moodle/course:viewhiddencourses', $this->context)) {
            return null;
        }

        if (!is_viewing($this->context) and !is_enrolled($this->context)) {
            // no peaking here if not enrolled or inspector
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
     * Gets a stored file for the course summary filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_course_summary($itemid, $filepath, $filename) {
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
        if (!$storedfile = $fs->get_file($this->context->id, 'course', 'summary', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'course', 'summary', 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areacourseintro', 'repository'), false, true, true, false);
    }

    /**
     * Gets a stored file for the course images filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_course_overviewfiles($itemid, $filepath, $filename) {
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
        if (!$storedfile = $fs->get_file($this->context->id, 'course', 'overviewfiles', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'course', 'overviewfiles', 0);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('areacourseoverviewfiles', 'repository'), false, true, true, false);
    }

    /**
     * Gets a stored file for the course section filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_course_section($itemid, $filepath, $filename) {
        global $CFG, $DB;

        if (!has_capability('moodle/course:update', $this->context)) {
            return null;
        }

        if (empty($itemid)) {
            // list all sections
            return new file_info_area_course_section($this->browser, $this->context, $this->course, $this);
        }

        if (!$section = $DB->get_record('course_sections', array('course'=>$this->course->id, 'id'=>$itemid))) {
            return null; // does not exist
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'course', 'section', $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'course', 'section', $itemid);
            } else {
                // not found
                return null;
            }
        }
        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, $section->section, true, true, true, false);
    }

    /**
     * Gets a stored file for the course legacy filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_course_legacy($itemid, $filepath, $filename) {
        if (!has_capability('moodle/course:managefiles', $this->context)) {
            return null;
        }

        if ($this->course->id != SITEID and $this->course->legacyfiles != 2) {
            // bad luck, legacy course files not used any more
        }

        if (is_null($itemid)) {
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'course', 'legacy', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'course', 'legacy', 0);
            } else {
                // not found
                return null;
            }
        }

        return new file_info_area_course_legacy($this->browser, $this->context, $storedfile);
    }

    /**
     * Gets a stored file for the backup course filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_backup_course($itemid, $filepath, $filename) {
        global $CFG;

        if (!has_capability('moodle/backup:backupcourse', $this->context) and !has_capability('moodle/restore:restorecourse', $this->context)) {
            return null;
        }
        if (is_null($itemid)) {
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'backup', 'course', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'backup', 'course', 0);
            } else {
                // not found
                return null;
            }
        }

        $downloadable = has_capability('moodle/backup:downloadfile', $this->context);
        $uploadable   = has_capability('moodle/restore:uploadfile', $this->context);

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('coursebackup', 'repository'), false, $downloadable, $uploadable, false);
    }

    /**
     * Gets a stored file for the automated backup filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null
     */
    protected function get_area_backup_automated($itemid, $filepath, $filename) {
        global $CFG;

        if (!has_capability('moodle/restore:viewautomatedfilearea', $this->context)) {
            return null;
        }
        if (is_null($itemid)) {
            return $this;
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'backup', 'automated', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'backup', 'automated', 0);
            } else {
                // not found
                return null;
            }
        }

        $downloadable = has_capability('moodle/site:config', $this->context);
        $uploadable   = false;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, get_string('automatedbackup', 'repository'), true, $downloadable, $uploadable, false);
    }

    /**
     * Gets a stored file for the backup section filearea directory
     *
     * @param int $itemid item ID
     * @param string $filepath file path
     * @param string $filename file name
     * @return file_info|null file_info instance or null if not found or access not allowed
     */
    protected function get_area_backup_section($itemid, $filepath, $filename) {
        global $CFG, $DB;

        if (!has_capability('moodle/backup:backupcourse', $this->context) and !has_capability('moodle/restore:restorecourse', $this->context)) {
            return null;
        }

        if (empty($itemid)) {
            // list all sections
            return new file_info_area_backup_section($this->browser, $this->context, $this->course, $this);
        }

        if (!$section = $DB->get_record('course_sections', array('course'=>$this->course->id, 'id'=>$itemid))) {
            return null; // does not exist
        }

        $fs = get_file_storage();

        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;
        if (!$storedfile = $fs->get_file($this->context->id, 'backup', 'section', $itemid, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($this->context->id, 'backup', 'section', $itemid);
            } else {
                // not found
                return null;
            }
        }

        $downloadable = has_capability('moodle/backup:downloadfile', $this->context);
        $uploadable   = has_capability('moodle/restore:uploadfile', $this->context);

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        return new file_info_stored($this->browser, $this->context, $storedfile, $urlbase, $section->id, true, $downloadable, $uploadable, false);
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public function get_visible_name() {
        return ($this->course->id == SITEID) ? get_string('frontpage', 'admin') : format_string(get_course_display_name_for_list($this->course), true, array('context'=>$this->context));
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
        $areas = array(
            array('course', 'summary'),
            array('course', 'overviewfiles'),
            array('course', 'section'),
            array('backup', 'section'),
            array('backup', 'course'),
            array('backup', 'automated'),
            array('course', 'legacy')
        );
        $children = array();
        foreach ($areas as $area) {
            if ($child = $this->get_file_info($area[0], $area[1], 0, '/', '.')) {
                if ($returnemptyfolders || $child->count_non_empty_children($extensions)) {
                    $children[] = $child;
                    if (($countonly !== false) && count($children) >= $countonly) {
                        return $countonly;
                    }
                }
            }
        }

        if (!has_capability('moodle/course:managefiles', $this->context)) {
            // 'managefiles' capability is checked in every activity module callback.
            // Don't even waste time on retrieving the modules if we can't browse the files anyway
        } else {
            // now list all modules
            $modinfo = get_fast_modinfo($this->course);
            foreach ($modinfo->cms as $cminfo) {
                if (empty($cminfo->uservisible)) {
                    continue;
                }
                $modcontext = context_module::instance($cminfo->id, IGNORE_MISSING);
                if ($child = $this->browser->get_file_info($modcontext)) {
                    if ($returnemptyfolders || $child->count_non_empty_children($extensions)) {
                        $children[] = $child;
                        if (($countonly !== false) && count($children) >= $countonly) {
                            return $countonly;
                        }
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
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        return $this->get_filtered_children($extensions, false);
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
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public function get_parent() {
        $parent = $this->context->get_parent_context();
        return $this->browser->get_file_info($parent);
    }
}


/**
 * Subclass of file_info_stored for files in the course files area.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_area_course_legacy extends file_info_stored {
    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stored_file $storedfile stored_file instance
     */
    public function __construct($browser, $context, $storedfile) {
        global $CFG;
        $urlbase = $CFG->wwwroot.'/file.php';
        parent::__construct($browser, $context, $storedfile, $urlbase, get_string('coursefiles'), false, true, true, false);
    }

    /**
     * Returns file download url
     *
     * @param bool $forcedownload whether or not force download
     * @param bool $https whether or not force https
     * @return string url
     */
    public function get_url($forcedownload=false, $https=false) {
        if (!$this->is_readable()) {
            return null;
        }

        if ($this->lf->is_directory()) {
            return null;
        }

        $filepath = $this->lf->get_filepath();
        $filename = $this->lf->get_filename();
        $courseid = $this->context->instanceid;

        $path = '/'.$courseid.$filepath.$filename;

        return file_encode_url($this->urlbase, $path, $forcedownload, $https);
    }

    /**
     * Returns list of children.
     *
     * @return array of file_info instances
     */
    public function get_children() {
        if (!$this->lf->is_directory()) {
            return array();
        }

        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($this->context->id, 'course', 'legacy', 0, $this->lf->get_filepath(), false, true, "filepath ASC, filename ASC");
        foreach ($storedfiles as $file) {
            $result[] = new file_info_area_course_legacy($this->browser, $this->context, $file);
        }

        return $result;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        if (!$this->lf->is_directory()) {
            return array();
        }

        $result = array();
        $fs = get_file_storage();

        $storedfiles = $fs->get_directory_files($this->context->id, 'course', 'legacy', 0,
                                                $this->lf->get_filepath(), false, true, "filepath, filename");
        foreach ($storedfiles as $file) {
            $extension = core_text::strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION));
            if ($file->is_directory() || $extensions === '*' || (!empty($extension) && in_array('.'.$extension, $extensions))) {
                $fileinfo = new file_info_area_course_legacy($this->browser, $this->context, $file, $this->urlbase, $this->topvisiblename,
                                                 $this->itemidused, $this->readaccess, $this->writeaccess, false);
                if (!$file->is_directory() || $fileinfo->count_non_empty_children($extensions)) {
                    $result[] = $fileinfo;
                }
            }
        }

        return $result;
    }
}

/**
 * Represents a course category context in the tree navigated by {@link file_browser}.
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_area_course_section extends file_info {
    /** @var stdClass course object */
    protected $course;
    /** @var file_info_context_course course file info object */
    protected $courseinfo;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stdClass $course course object
     * @param file_info_context_course $courseinfo file info instance
     */
    public function __construct($browser, $context, $course, file_info_context_course $courseinfo) {
        parent::__construct($browser, $context);
        $this->course     = $course;
        $this->courseinfo = $courseinfo;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     *
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid' => $this->context->id,
                     'component' => 'course',
                     'filearea'  => 'section',
                     'itemid'    => null,
                     'filepath'  => null,
                     'filename'  => null);
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public function get_visible_name() {
        //$format = $this->course->format;
        $sectionsname = get_string("coursesectionsummaries");

        return $sectionsname;
    }

    /**
     * Return whether or not new files or directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Return whether or not this is a empty area
     *
     * @return bool
     */
    public function is_empty_area() {
        $fs = get_file_storage();
        return $fs->is_area_empty($this->context->id, 'course', 'section');
    }

    /**
     * Return whether or not this is a empty area
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

        $course_sections = $DB->get_records('course_sections', array('course'=>$this->course->id), 'section');
        foreach ($course_sections as $section) {
            if ($child = $this->courseinfo->get_file_info('course', 'section', $section->id, '/', '.')) {
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
        $params1 = array(
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'component' => 'course',
            'filearea' => 'section',
            'emptyfilename' => '.');
        $sql1 = "SELECT DISTINCT cs.id FROM {files} f, {course_sections} cs
            WHERE cs.course = :courseid
            AND f.contextid = :contextid
            AND f.component = :component
            AND f.filearea = :filearea
            AND f.itemid = cs.id
            AND f.filename <> :emptyfilename";
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $rs = $DB->get_recordset_sql($sql1. ' '. $sql2, array_merge($params1, $params2));
        $cnt = 0;
        foreach ($rs as $record) {
            if ((++$cnt) >= $limit) {
                break;
            }
        }
        $rs->close();
        return $cnt;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info|null file_info or null for root
     */
    public function get_parent() {
        return $this->courseinfo;
    }
}


/**
 * Implementation of course section backup area
 *
 * @package    core_files
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_info_area_backup_section extends file_info {
    /** @var stdClass course object */
    protected $course;
    /** @var file_info_context_course course file info object */
    protected $courseinfo;

    /**
     * Constructor
     *
     * @param file_browser $browser file browser instance
     * @param stdClass $context context object
     * @param stdClass $course course object
     * @param file_info_context_course $courseinfo file info instance
     */
    public function __construct($browser, $context, $course, file_info_context_course $courseinfo) {
        parent::__construct($browser, $context);
        $this->course     = $course;
        $this->courseinfo = $courseinfo;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     *
     * @return array with keys contextid, component, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid' => $this->context->id,
                     'component' => 'backup',
                     'filearea'  => 'section',
                     'itemid'    => null,
                     'filepath'  => null,
                     'filename'  => null);
    }

    /**
     * Returns localised visible name.
     *
     * @return string
     */
    public function get_visible_name() {
        return get_string('sectionbackup', 'repository');
    }

    /**
     * Return whether or not new files and directories can be added
     *
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Whether or not this is an empty area
     *
     * @return bool
     */
    public function is_empty_area() {
        $fs = get_file_storage();
        return $fs->is_area_empty($this->context->id, 'backup', 'section');
    }

    /**
     * Return whether or not this is a directory
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

        $course_sections = $DB->get_records('course_sections', array('course'=>$this->course->id), 'section');
        foreach ($course_sections as $section) {
            if ($child = $this->courseinfo->get_file_info('backup', 'section', $section->id, '/', '.')) {
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
        $params1 = array(
            'courseid' => $this->course->id,
            'contextid' => $this->context->id,
            'component' => 'backup',
            'filearea' => 'section',
            'emptyfilename' => '.');
        $sql1 = "SELECT DISTINCT cs.id AS sectionid FROM {files} f, {course_sections} cs
            WHERE cs.course = :courseid
            AND f.contextid = :contextid
            AND f.component = :component
            AND f.filearea = :filearea
            AND f.itemid = cs.id
            AND f.filename <> :emptyfilename";
        list($sql2, $params2) = $this->build_search_files_sql($extensions);
        $rs = $DB->get_recordset_sql($sql1. ' '. $sql2, array_merge($params1, $params2));
        $cnt = 0;
        foreach ($rs as $record) {
            if ((++$cnt) >= $limit) {
                break;
            }
        }
        $rs->close();
        return $cnt;
    }

    /**
     * Returns parent file_info instance
     *
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
