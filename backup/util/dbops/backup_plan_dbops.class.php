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
 * @subpackage backup-dbops
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing DB support to the @backup_plan class
 *
 * This class contains various static methods available for all the DB operations
 * performed by the @backup_plan (and builder) classes
 *
 * TODO: Finish phpdocs
 */
abstract class backup_plan_dbops extends backup_dbops {

    /**
     * Given one course module id, return one array with all the block intances that belong to it
     */
    public static function get_blockids_from_moduleid($moduleid) {
        global $DB;

        // Get the context of the module
        $contextid = get_context_instance(CONTEXT_MODULE, $moduleid)->id;

        // Get all the block instances which parentcontextid is the module contextid
        $blockids = array();
        $instances = $DB->get_records('block_instances', array('parentcontextid' => $contextid), '', 'id');
        foreach ($instances as $instance) {
            $blockids[] = $instance->id;
        }
        return $blockids;
    }

    /**
     * Given one course id, return one array with all the block intances that belong to it
     */
    public static function get_blockids_from_courseid($courseid) {
        global $DB;

        // Get the context of the course
        $contextid = get_context_instance(CONTEXT_COURSE, $courseid)->id;

        // Get all the block instances which parentcontextid is the course contextid
        $blockids = array();
        $instances = $DB->get_records('block_instances', array('parentcontextid' => $contextid), '', 'id');
        foreach ($instances as $instance) {
            $blockids[] = $instance->id;
        }
        return $blockids;
    }

    /**
     * Given one section id, return one array with all the course modules that belong to it
     */
    public static function get_modules_from_sectionid($sectionid) {
        global $DB;

        // Get the course of the section
        $courseid = $DB->get_field('course_sections', 'course', array('id' => $sectionid));

        // Get all course modules belonging to requested section
        $modulesarr = array();
        $modules = $DB->get_records_sql("
            SELECT cm.id, m.name AS modname
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
             WHERE cm.course = ?
               AND cm.section = ?", array($courseid, $sectionid));
        foreach ($modules as $module) {
            $module = array('id' => $module->id, 'modname' => $module->modname);
            $modulesarr[] = (object)$module;
        }
        return $modulesarr;
    }

    /**
     * Given one course id, return one array with all the course_sections belonging to it
     */
    public static function get_sections_from_courseid($courseid) {
        global $DB;

        // Get all sections belonging to requested course
        $sectionsarr = array();
        $sections = $DB->get_records('course_sections', array('course' => $courseid));
        foreach ($sections as $section) {
            $sectionsarr[] = $section->id;
        }
        return $sectionsarr;
    }

    /**
     * Return the wwwroot of the $CFG->mnet_localhost_id host
     * caching it along the request
     */
    public static function get_mnet_localhost_wwwroot() {
        global $CFG, $DB;

        static $wwwroot = null;

        if (is_null($wwwroot)) {
            $wwwroot = $DB->get_field('mnet_host', 'wwwroot', array('id' => $CFG->mnet_localhost_id));
        }
        return $wwwroot;
    }

    /**
    * Returns the default backup filename, based in passed params.
    *
    * Default format is (see MDL-22145)
    *   backup word - format - type - name - date - info . zip
    * where name is variable (course shortname, section name/id, activity modulename + cmid)
    * and info can be (nu = no user info, an = anonymized)
    */
    public static function get_default_backup_filename($format, $type, $id, $users, $anonymised) {
        global $DB;

        // Calculate backup word
        $backupword = str_replace(' ', '_', moodle_strtolower(get_string('backupfilename')));
        $backupword = trim(clean_filename($backupword), '_');

        // Calculate proper name element (based on type)
        switch ($type) {
            case backup::TYPE_1COURSE:
                $shortname = $DB->get_field('course', 'shortname', array('id' => $id));
                break;
            case backup::TYPE_1SECTION:
                if (!$shortname = $DB->get_field('course_sections', 'name', array('id' => $id))) {
                    $shortname = $DB->get_field('course_sections', 'section', array('id' => $id));
                }
                break;
            case backup::TYPE_1ACTIVITY:
                $cm = get_coursemodule_from_id(null, $id);
                $shortname = $cm->modname . $id;
                break;
        }
        $shortname = str_replace(' ', '_', $shortname);
        $shortname = moodle_strtolower(trim(clean_filename($shortname), '_'));
        $name = empty($shortname) ? $id : $shortname;

        // Calculate date
        $backupdateformat = str_replace(' ', '_', get_string('backupnameformat', 'langconfig'));
        $date = userdate(time(), $backupdateformat, 99, false);
        $date = moodle_strtolower(trim(clean_filename($date), '_'));

        // Calculate info
        $info = '';
        if (!$users) {
            $info = '-nu';
        } else if ($anonymised) {
            $info = '-an';
        }

        return $backupword . '-' . $format . '-' . $type . '-' .
               $name . '-' . $date . $info . '.zip';
    }
}
