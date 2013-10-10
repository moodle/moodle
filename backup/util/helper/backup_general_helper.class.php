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
 * @package    moodlecore
 * @subpackage backup-helper
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Non instantiable helper class providing general helper methods for backup/restore
 *
 * This class contains various general helper static methods available for backup/restore
 *
 * TODO: Finish phpdocs
 */
abstract class backup_general_helper extends backup_helper {

    /**
     * Calculate one checksum for any array/object. Works recursively
     */
    public static function array_checksum_recursive($arr) {

        $checksum = ''; // Init checksum

        // Check we are going to process one array always, objects must be cast before
        if (!is_array($arr)) {
            throw new backup_helper_exception('array_expected');
        }
        foreach ($arr as $key => $value) {
            if ($value instanceof checksumable) {
                $checksum = md5($checksum . '-' . $key . '-' . $value->calculate_checksum());
            } else if (is_object($value)) {
                $checksum = md5($checksum . '-' . $key . '-' . self::array_checksum_recursive((array)$value));
            } else if (is_array($value)) {
                $checksum = md5($checksum . '-' . $key . '-' . self::array_checksum_recursive($value));
            } else {
                $checksum = md5($checksum . '-' . $key . '-' . $value);
            }
        }
        return $checksum;
    }

    /**
     * Load all the blocks information needed for a given path within moodle2 backup
     *
     * This function, given one full path (course, activities/xxxx) will look for all the
     * blocks existing in the backup file, returning one array used to build the
     * proper restore plan by the @restore_plan_builder
     */
    public static function get_blocks_from_path($path) {
        global $DB;

        $blocks = array(); // To return results

        static $availableblocks = array(); // Get and cache available blocks
        if (empty($availableblocks)) {
            $availableblocks = array_keys(core_component::get_plugin_list('block'));
        }

        $path = $path . '/blocks'; // Always look under blocks subdir

        if (!is_dir($path)) {
            return array();
        }

        if (!$dir = opendir($path)) {
            return array();
        }
        while (false !== ($file = readdir($dir))) {
            if ($file == '.' || $file == '..') { // Skip dots
                continue;
            }
            if (is_dir($path .'/' . $file)) { // Dir found, check it's a valid block
                if (!file_exists($path .'/' . $file . '/block.xml')) { // Skip if xml file not found
                    continue;
                }
                // Extract block name
                $blockname = preg_replace('/(.*)_\d+/', '\\1', $file);
                // Check block exists and is installed
                if (in_array($blockname, $availableblocks) && $DB->record_exists('block', array('name' => $blockname))) {
                    $blocks[$path .'/' . $file] = $blockname;
                }
            }
        }
        closedir($dir);

        return $blocks;
    }

    /**
     * Load and format all the needed information from moodle_backup.xml
     *
     * This function loads and process all the moodle_backup.xml
     * information, composing a big information structure that will
     * be the used by the plan builder in order to generate the
     * appropiate tasks / steps / settings
     */
    public static function get_backup_information($tempdir) {
        global $CFG;

        $info = new stdclass(); // Final information goes here

        $moodlefile = $CFG->tempdir . '/backup/' . $tempdir . '/moodle_backup.xml';
        if (!file_exists($moodlefile)) { // Shouldn't happen ever, but...
            throw new backup_helper_exception('missing_moodle_backup_xml_file', $moodlefile);
        }
        // Load the entire file to in-memory array
        $xmlparser = new progressive_parser();
        $xmlparser->set_file($moodlefile);
        $xmlprocessor = new restore_moodlexml_parser_processor();
        $xmlparser->set_processor($xmlprocessor);
        $xmlparser->process();
        $infoarr = $xmlprocessor->get_all_chunks();
        if (count($infoarr) !== 1) { // Shouldn't happen ever, but...
            throw new backup_helper_exception('problem_parsing_moodle_backup_xml_file');
        }
        $infoarr = $infoarr[0]['tags']; // for commodity

        // Let's build info
        $info->moodle_version = $infoarr['moodle_version'];
        $info->moodle_release = $infoarr['moodle_release'];
        $info->backup_version = $infoarr['backup_version'];
        $info->backup_release = $infoarr['backup_release'];
        $info->backup_date    = $infoarr['backup_date'];
        $info->mnet_remoteusers         = $infoarr['mnet_remoteusers'];
        $info->original_wwwroot         = $infoarr['original_wwwroot'];
        $info->original_site_identifier_hash = $infoarr['original_site_identifier_hash'];
        $info->original_course_id       = $infoarr['original_course_id'];
        $info->original_course_fullname = $infoarr['original_course_fullname'];
        $info->original_course_shortname= $infoarr['original_course_shortname'];
        $info->original_course_startdate= $infoarr['original_course_startdate'];
        $info->original_course_contextid= $infoarr['original_course_contextid'];
        $info->original_system_contextid= $infoarr['original_system_contextid'];
        // Moodle backup file don't have this option before 2.3
        if (!empty($infoarr['include_file_references_to_external_content'])) {
            $info->include_file_references_to_external_content = 1;
        } else {
            $info->include_file_references_to_external_content = 0;
        }
        // include_files is a new setting in 2.6.
        if (isset($infoarr['include_files'])) {
            $info->include_files = $infoarr['include_files'];
        } else {
            $info->include_files = 1;
        }
        $info->type   =  $infoarr['details']['detail'][0]['type'];
        $info->format =  $infoarr['details']['detail'][0]['format'];
        $info->mode   =  $infoarr['details']['detail'][0]['mode'];
        // Build the role mappings custom object
        $rolemappings = new stdclass();
        $rolemappings->modified = false;
        $rolemappings->mappings = array();
        $info->role_mappings = $rolemappings;
        // Some initially empty containers
        $info->sections = array();
        $info->activities = array();

        // Now the contents
        $contentsarr = $infoarr['contents'];
        if (isset($contentsarr['course']) && isset($contentsarr['course'][0])) {
            $info->course = new stdclass();
            $info->course = (object)$contentsarr['course'][0];
            $info->course->settings = array();
        }
        if (isset($contentsarr['sections']) && isset($contentsarr['sections']['section'])) {
            $sectionarr = $contentsarr['sections']['section'];
            foreach ($sectionarr as $section) {
                $section = (object)$section;
                $section->settings = array();
                $sections[basename($section->directory)] = $section;
            }
            $info->sections = $sections;
        }
        if (isset($contentsarr['activities']) && isset($contentsarr['activities']['activity'])) {
            $activityarr = $contentsarr['activities']['activity'];
            foreach ($activityarr as $activity) {
                $activity = (object)$activity;
                $activity->settings = array();
                $activities[basename($activity->directory)] = $activity;
            }
            $info->activities = $activities;
        }
        $info->root_settings = array(); // For root settings

        // Now the settings, putting each one under its owner
        $settingsarr = $infoarr['settings']['setting'];
        foreach($settingsarr as $setting) {
            switch ($setting['level']) {
                case 'root':
                    $info->root_settings[$setting['name']] = $setting['value'];
                    break;
                case 'course':
                    $info->course->settings[$setting['name']] = $setting['value'];
                    break;
                case 'section':
                    $info->sections[$setting['section']]->settings[$setting['name']] = $setting['value'];
                    break;
                case 'activity':
                    $info->activities[$setting['activity']]->settings[$setting['name']] = $setting['value'];
                    break;
                default: // Shouldn't happen
                    throw new backup_helper_exception('wrong_setting_level_moodle_backup_xml_file', $setting['level']);
            }
        }

        return $info;
    }

    /**
     * Load and format all the needed information from a backup file.
     *
     * This will only extract the moodle_backup.xml file from an MBZ
     * file and then call {@link self::get_backup_information()}.
     *
     * @param string $filepath absolute path to the MBZ file.
     * @return stdClass containing information.
     * @since 2.4
     */
    public static function get_backup_information_from_mbz($filepath) {
        global $CFG;
        if (!is_readable($filepath)) {
            throw new backup_helper_exception('missing_moodle_backup_file', $filepath);
        }

        // Extract moodle_backup.xml.
        $tmpname = 'info_from_mbz_' . time() . '_' . random_string(4);
        $tmpdir = $CFG->tempdir . '/backup/' . $tmpname;
        $fp = get_file_packer('application/vnd.moodle.backup');

        $extracted = $fp->extract_to_pathname($filepath, $tmpdir, array('moodle_backup.xml'));
        $moodlefile =  $tmpdir . '/' . 'moodle_backup.xml';
        if (!$extracted || !is_readable($moodlefile)) {
            throw new backup_helper_exception('missing_moodle_backup_xml_file', $moodlefile);
        }

        // Read the information and delete the temporary directory.
        $info = self::get_backup_information($tmpname);
        remove_dir($tmpdir);
        return $info;
    }

    /**
     * Given the information fetched from moodle_backup.xml file
     * decide if we are restoring in the same site the backup was
     * generated or no. Behavior of various parts of restore are
     * dependent of this.
     *
     * Backups created natively in 2.0 and later declare the hashed
     * site identifier. Backups created by conversion from a 1.9
     * backup do not declare such identifier, so there is a fallback
     * to wwwroot comparison. See MDL-16614.
     */
    public static function backup_is_samesite($info) {
        global $CFG;
        $hashedsiteid = md5(get_site_identifier());
        if (isset($info->original_site_identifier_hash) && !empty($info->original_site_identifier_hash)) {
            return $info->original_site_identifier_hash == $hashedsiteid;
        } else {
            return $info->original_wwwroot == $CFG->wwwroot;
        }
    }

    /**
     * Detects the format of the given unpacked backup directory
     *
     * @param string $tempdir the name of the backup directory
     * @return string one of backup::FORMAT_xxx constants
     */
    public static function detect_backup_format($tempdir) {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/helper/convert_helper.class.php');

        if (convert_helper::detect_moodle2_format($tempdir)) {
            return backup::FORMAT_MOODLE;
        }

        // see if a converter can identify the format
        $converters = convert_helper::available_converters();
        foreach ($converters as $name) {
            $classname = "{$name}_converter";
            if (!class_exists($classname)) {
                throw new coding_exception("available_converters() is supposed to load
                    converter classes but class $classname not found");
            }

            $detected = call_user_func($classname .'::detect_format', $tempdir);
            if (!empty($detected)) {
                return $detected;
            }
        }

        return backup::FORMAT_UNKNOWN;
    }
}
