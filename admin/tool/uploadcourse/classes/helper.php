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
 * File containing the helper class.
 *
 * @package    tool_uploadcourse
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Class containing a set of helpers.
 */
class tool_uploadcourse_helper {


    /** @var array category idnumber cache. */
    static protected $categoryidnumbercache = array();

    /** @var array category path cache. */
    static protected $categorypathcache = array();

    /** @var array course formats cache. */
    static protected $courseformatscache;

    /** @var array enrolment plugins cache. */
    static protected $enrolpluginscache;

    /** @var string restore content path cache. */
    static protected $restorecontentcache;

    /** @var array roles cache. */
    static protected $rolescache;

    /**
     * Remove the restore content from disk and cache.
     *
     * @return void
     */
    public static function clean_restore_content() {
        global $CFG;
        if (!empty($CFG->keeptempdirectoriesonbackup)) {
            foreach (self::$restorecontentcache as $backupid) {
                fulldelete("$CFG->tempdir/backup/$backupid/");
            }
        }
        self::$restorecontentcache = array();
    }

    /**
     * Generate a shortname based on a template.
     *
     * @param array|object $data course data.
     * @param string $templateshortname template of shortname.
     * @return null|string shortname based on the template.
     */
    public static function generate_shortname($data, $templateshortname) {
        if (is_null($templateshortname)) {
            return null;
        }
        if (strpos($templateshortname, '%') === false) {
            return $templateshortname;
        }

        $course = (object) $data;
        $shortname  = isset($course->shortname) ? $course->shortname  : '';
        $fullname   = isset($course->fullname) ? $course->fullname : '';
        $idnumber   = isset($course->idnumber) ? $course->idnumber  : '';

        $callback = partial(array('tool_uploadcourse_helper', 'generate_shortname_callback'), $shortname, $fullname, $idnumber);
        $result = preg_replace_callback('/(?<!%)%([+~-])?(\d)*([fi])/', $callback, $templateshortname);

        if (!is_null($result)) {
            $result = clean_param($result, PARAM_TEXT);
        }

        return $result;
    }

    /**
     * Callback used when generating a shortname based on a template.
     *
     * @param string $shortname short name.
     * @param string $fullname full name.
     * @param string $idnumber ID number.
     * @param array $block result from preg_replace_callback.
     * @return string
     */
    public static function generate_shortname_callback($shortname, $fullname, $idnumber, $block) {
        switch ($block[3]) {
            case 'f':
                $repl = $fullname;
                break;
            case 'i':
                $repl = $idnumber;
                break;
            default:
                return $block[0];
        }

        switch ($block[1]) {
            case '+':
                $repl = textlib::strtoupper($repl);
                break;
            case '-':
                $repl = textlib::strtolower($repl);
                break;
            case '~':
                $repl = textlib::strtotitle($repl);
                break;
        }

        if (!empty($block[2])) {
            $repl = textlib::substr($repl, 0, $block[2]);
        }

        return $repl;
    }

    /**
     * Return the available course formats.
     *
     * The result is cached for faster execution.
     *
     * @return array
     */
    public static function get_course_formats() {
        if (empty(self::$courseformatscache)) {
            self::$courseformatscache = array_keys(get_plugin_list('format'));
        }
        return self::$courseformatscache;
    }

    /**
     * Extract enrolment data from passed data.
     *
     * Constructs an array of methods, and their options:
     * array(
     *     'method1' => array(
     *         'option1' => value,
     *         'option2' => value
     *     ),
     *     'method2' => array(
     *         'option1' => value,
     *         'option2' => value
     *     )
     * )
     *
     * @param array $data data to extract the enrolment data from.
     * @return array
     */
    public static function get_enrolment_data($data) {
        $enrolmethods = array();
        $enroloptions = array();
        foreach ($data as $field => $value) {

            // Enrolmnent data.
            $matches = array();
            if (preg_match('/^enrolment_(\d+)(_(.+))?$/', $field, $matches)) {
                $key = $matches[1];
                if (!isset($enroloptions[$key])) {
                    $enroloptions[$key] = array();
                }
                if (empty($matches[3])) {
                    $enrolmethods[$key] = $value;
                } else {
                    $enroloptions[$key][$matches[3]] = $value;
                }
            }
        }

        // Combining enrolment methods and their options in a single array.
        $enrolmentdata = array();
        if (!empty($enrolmethods)) {
            $enrolmentplugins = self::get_enrolment_plugins();
            foreach ($enrolmethods as $key => $method) {
                if (!array_key_exists($method, $enrolmentplugins)) {
                    // Error!
                    continue;
                }
                $enrolmentdata[$enrolmethods[$key]] = $enroloptions[$key];
            }
        }
        return $enrolmentdata;
    }

    /**
     * Return the enrolment plugins.
     *
     * The result is cached for faster execution.
     *
     * @return array
     */
    public static function get_enrolment_plugins() {
        if (empty(self::$enrolpluginscache)) {
            self::$enrolpluginscache = enrol_get_plugins(false);
        }
        return self::$enrolpluginscache;
    }

    /**
     * Get the restore content tempdir.
     *
     * The tempdir is the sub directory in which the backup has been extracted.
     * This caches the result for better performance.
     *
     * @param string $backupfile path to a backup file.
     * @param string $shortname shortname of a course.
     * @param array $errors will be populated with errors found.
     * @return string|false false when the backup couldn't retrieved.
     */
    public static function get_restore_content_dir($backupfile = null, $shortname = null, &$errors = array()) {
        global $CFG, $DB, $USER;

        $cachekey = null;
        if (!empty($backupfile)) {
            $backupfile = realpath($backupfile);
            $cachekey = '[path]:' . $backupfile;
        } else if (!empty($shortname) || is_numeric($shortname)) {
            $cachekey = '[sn]:' . $shortname;
        }

        if (empty($cachekey)) {
            return false;
        }

        if (!isset(self::$restorecontentcache[$cachekey])) {
            // Use false instead of null because it would consider that the cache
            // key has not been set.
            $backupid = false;
            if (!empty($backupfile)) {
                if (!is_readable($backupfile)) {
                    $errors['cannotreadbackupfile'] = new lang_string('cannotreadbackupfile', 'tool_uploadcourse');
                } else {
                    // Extracting the backup file.
                    $packer = get_file_packer('application/vnd.moodle.backup');
                    $backupid = restore_controller::get_tempdir_name(SITEID, $USER->id);
                    $path = "$CFG->tempdir/backup/$backupid/";
                    $result = $packer->extract_to_pathname($backupfile, $path);
                    if (!$result) {
                        $errors['invalidbackupfile'] = new lang_string('invalidbackupfile', 'tool_uploadcourse');
                    }
                }
            } else if (!empty($shortname) || is_numeric($shortname)) {
                // Creating restore from an existing course.
                $courseid = $DB->get_field('course', 'id', array('shortname' => $shortname), IGNORE_MISSING);
                if (!empty($courseid)) {
                    $bc = new backup_controller(backup::TYPE_1COURSE, $courseid, backup::FORMAT_MOODLE,
                        backup::INTERACTIVE_NO, backup::MODE_IMPORT, $USER->id);
                    $bc->execute_plan();
                    $backupid = $bc->get_backupid();
                    $bc->destroy();
                } else {
                    $errors['coursetorestorefromdoesnotexist'] =
                        new lang_string('coursetorestorefromdoesnotexist', 'tool_uploadcourse');
                }
            }
            self::$restorecontentcache[$cachekey] = $backupid;
        }

        return self::$restorecontentcache[$cachekey];
    }

    /**
     * Return the role IDs.
     *
     * The result is cached for faster execution.
     *
     * @param bool $invalidate invalidate the cache.
     * @return array
     */
    public static function get_role_ids($invalidate = false) {
        if (empty(self::$rolescache) || $invalidate) {
            $roles = get_all_roles();
            foreach ($roles as $role) {
                self::$rolescache[$role->shortname] = $role->id;
            }
        }
        return self::$rolescache;
    }

    /**
     * Get the role renaming data from the passed data.
     *
     * @param array $data data to extract the names from.
     * @param array $errors will be populated with errors found.
     * @return array where the key is the role_<id>, the value is the new name.
     */
    public static function get_role_names($data, &$errors = array()) {
        $rolenames = array();
        $rolesids = self::get_role_ids();
        $invalidroles = array();
        foreach ($data as $field => $value) {

            $matches = array();
            if (preg_match('/^role_(.+)?$/', $field, $matches)) {
                if (!isset($rolesids[$matches[1]])) {
                    $invalidroles[] = $matches[1];
                    continue;
                }
                $rolenames['role_' . $rolesids[$matches[1]]] = $value;
            }

        }

        if (!empty($invalidroles)) {
            $errors['invalidroles'] = new lang_string('invalidroles', 'tool_uploadcourse', implode(', ', $invalidroles));
        }

        // Roles names.
        return $rolenames;
    }

    /**
     * Helper to increment an ID number.
     *
     * This first checks if the ID number is in use.
     *
     * @param string $idnumber ID number to increment.
     * @return string new ID number.
     */
    public static function increment_idnumber($idnumber) {
        global $DB;
        while ($DB->record_exists('course', array('idnumber' => $idnumber))) {
            $matches = array();
            if (!preg_match('/(.*?)([0-9]+)$/', $idnumber, $matches)) {
                $newidnumber = $idnumber . '_2';
            } else {
                $newidnumber = $matches[1] . ((int) $matches[2] + 1);
            }
            $idnumber = $newidnumber;
        }
        return $idnumber;
    }

    /**
     * Helper to increment a shortname.
     *
     * This considers that the shortname passed has to be incremented.
     *
     * @param string $shortname shortname to increment.
     * @return string new shortname.
     */
    public static function increment_shortname($shortname) {
        global $DB;
        do {
            $matches = array();
            if (!preg_match('/(.*?)([0-9]+)$/', $shortname, $matches)) {
                $newshortname = $shortname . '_2';
            } else {
                $newshortname = $matches[1] . ($matches[2]+1);
            }
            $shortname = $newshortname;
        } while ($DB->record_exists('course', array('shortname' => $shortname)));
        return $shortname;
    }

    /**
     * Resolve a category based on the data passed.
     *
     * Key accepted are:
     * - category, which is supposed to be a category ID.
     * - category_idnumber
     * - category_path, array of categories from parent to child.
     *
     * @param array $data to resolve the category from.
     * @param array $errors will be populated with errors found.
     * @return int category ID.
     */
    public static function resolve_category($data, &$errors = array()) {
        global $DB;
        $catid = null;

        if (!empty($data['category'])) {
            $category = coursecat::get((int) $data['category'], IGNORE_MISSING);
            if (!empty($category) && !empty($category->id)) {
                $catid = $category->id;
            } else {
                $errors['couldnotresolvecatgorybyid'] =
                    new lang_string('couldnotresolvecatgorybyid', 'tool_uploadcourse');
            }
        }

        if (empty($catid) && !empty($data['category_idnumber'])) {
            $catid = self::resolve_category_by_idnumber($data['category_idnumber']);
            if (empty($catid)) {
                $errors['couldnotresolvecatgorybyidnumber'] =
                    new lang_string('couldnotresolvecatgorybyidnumber', 'tool_uploadcourse');
            }
        }
        if (empty($catid) && !empty($data['category_path'])) {
            $catid = self::resolve_category_by_path(explode(' / ', $data['category_path']));
            if (empty($catid)) {
                $errors['couldnotresolvecatgorybypath'] =
                    new lang_string('couldnotresolvecatgorybypath', 'tool_uploadcourse');
            }
        }

        return $catid;
    }

    /**
     * Resolve a category by ID number.
     *
     * @param string $idnumber category ID number.
     * @return int category ID.
     */
    public static function resolve_category_by_idnumber($idnumber) {
        global $DB;
        if (!isset(self::$categoryidnumbercache[$idnumber])) {
            $params = array('idnumber' => $idnumber);
            $id = $DB->get_field_select('course_categories', 'id', 'idnumber = :idnumber', $params, IGNORE_MISSING);
            self::$categoryidnumbercache[$idnumber] = $id;
        }
        return self::$categoryidnumbercache[$idnumber];
    }

    /**
     * Resolve a category by path.
     *
     * @param array $path category names indexed from parent to children.
     * @return int category ID.
     */
    public static function resolve_category_by_path(array $path) {
        global $DB;
        $cachekey = serialize($path);
        if (!isset(self::$categorypathcache[$cachekey])) {
            $parent = 0;
            $sql = 'name = :name AND parent = :parent';
            while ($name = array_shift($path)) {
                $params = array('name' => $name, 'parent' => $parent);
                if ($records = $DB->get_records_select('course_categories', $sql, $params, null, 'id, parent')) {
                    if (count($records) > 1) {
                        // Too many records with the same name!
                        $id = false;
                        break;
                    }
                    $record = reset($records);
                    $id = $record->id;
                    $parent = $record->id;
                } else {
                    // Not found.
                    $id = false;
                    break;
                }
            }
            self::$categorypathcache[$cachekey] = $id;
        }
        return self::$categorypathcache[$cachekey];
    }

}
