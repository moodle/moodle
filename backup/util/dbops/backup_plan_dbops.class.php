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
        $contextid = context_module::instance($moduleid)->id;

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
        $contextid = context_course::instance($courseid)->id;

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

        // Get the course and sequence of the section
        $secrec = $DB->get_record('course_sections', array('id' => $sectionid), 'course, sequence');
        $courseid = $secrec->course;
        $sequence = $secrec->sequence;

        // Get the section->sequence contents (it roots the activities order)
        // Get all course modules belonging to requested section
        $modulesarr = array();
        $modules = $DB->get_records_sql("
            SELECT cm.id, m.name AS modname
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
             WHERE cm.course = ?
               AND cm.section = ?", array($courseid, $sectionid));
        foreach (explode(',', $sequence) as $moduleid) {
            if (isset($modules[$moduleid])) {
                $module = array('id' => $modules[$moduleid]->id, 'modname' => $modules[$moduleid]->modname);
                $modulesarr[] = (object)$module;
                unset($modules[$moduleid]);
            }
        }
        if (!empty($modules)) { // This shouldn't happen, but one borked sequence can lead to it. Add the rest
            foreach ($modules as $module) {
                $module = array('id' => $module->id, 'modname' => $module->modname);
                $modulesarr[] = (object)$module;
            }
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
        $sections = $DB->get_records('course_sections', array('course' => $courseid), 'section');
        foreach ($sections as $section) {
            $sectionsarr[] = $section->id;
        }
        return $sectionsarr;
    }

    /**
     * Given one course id, return its format in DB
     */
    public static function get_courseformat_from_courseid($courseid) {
        global $DB;

        return $DB->get_field('course', 'format', array('id' => $courseid));
    }

    /**
     * Given a course id, returns its theme. This can either be the course
     * theme or (if not specified in course) the category, site theme.
     *
     * User, session, and inherited-from-mnet themes cannot have backed-up
     * per course data. This is course-related data so it must be in a course
     * theme specified as part of the course structure
     * @param int $courseid
     * @return string Name of course theme
     * @see moodle_page#resolve_theme()
     */
    public static function get_theme_from_courseid($courseid) {
        global $DB, $CFG;

        // Course theme first
        if (!empty($CFG->allowcoursethemes)) {
            $theme = $DB->get_field('course', 'theme', array('id' => $courseid));
            if ($theme) {
                return $theme;
            }
        }

        // Category themes in reverse order
        if (!empty($CFG->allowcategorythemes)) {
            $catid = $DB->get_field('course', 'category', array('id' => $courseid));
            while($catid) {
                $category = $DB->get_record('course_categories', array('id'=>$catid),
                        'theme,parent', MUST_EXIST);
                if ($category->theme) {
                    return $category->theme;
                }
                $catid = $category->parent;
            }
        }

        // Finally use site theme
        return $CFG->theme;
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
    * backup word - format - type - name - date - info . mbz
    * where name is variable (course shortname, section name/id, activity modulename + cmid)
    * and info can be (nu = no user info, an = anonymized). The last param $useidasname,
    * defaulting to false, allows to replace the course shortname by the course id (used
    * by automated backups, to avoid non-ascii chars in OS filesystem)
    *
    * @param string $format One of backup::FORMAT_
    * @param string $type One of backup::TYPE_
    * @param int $courseid/$sectionid/$cmid
    * @param bool $users Should be true is users were included in the backup
    * @param bool $anonymised Should be true is user information was anonymized.
    * @param bool $useidonly only use the ID in the file name
    * @return string The filename to use
    */
    public static function get_default_backup_filename($format, $type, $id, $users, $anonymised, $useidonly = false) {
        global $DB;

        // Calculate backup word
        $backupword = str_replace(' ', '_', textlib::strtolower(get_string('backupfilename')));
        $backupword = trim(clean_filename($backupword), '_');

        // Not $useidonly, lets fetch the name
        $shortname = '';
        if (!$useidonly) {
            // Calculate proper name element (based on type)
            switch ($type) {
                case backup::TYPE_1COURSE:
                    $shortname = $DB->get_field('course', 'shortname', array('id' => $id));
                    $context = context_course::instance($id);
                    $shortname = format_string($shortname, true, array('context' => $context));
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
            $shortname = textlib::strtolower(trim(clean_filename($shortname), '_'));
        }

        // The name will always contain the ID, but we append the course short name if requested.
        $name = $id;
        if (!$useidonly && $shortname != '') {
            $name .= '-' . $shortname;
        }

        // Calculate date
        $backupdateformat = str_replace(' ', '_', get_string('backupnameformat', 'langconfig'));
        $date = userdate(time(), $backupdateformat, 99, false);
        $date = textlib::strtolower(trim(clean_filename($date), '_'));

        // Calculate info
        $info = '';
        if (!$users) {
            $info = '-nu';
        } else if ($anonymised) {
            $info = '-an';
        }

        return $backupword . '-' . $format . '-' . $type . '-' .
               $name . '-' . $date . $info . '.mbz';
    }

    /**
    * Returns a flag indicating the need to backup gradebook elements like calculated grade items and category visibility
    * If all activity related grade items are being backed up we can also backup calculated grade items and categories
    */
    public static function require_gradebook_backup($courseid, $backupid) {
        global $DB;

        $sql = "SELECT count(id)
                  FROM {grade_items}
                 WHERE courseid=:courseid
                   AND itemtype = 'mod'
                   AND id NOT IN (
                       SELECT bi.itemid
                         FROM {backup_ids_temp} bi
                        WHERE bi.itemname = 'grade_itemfinal'
                          AND bi.backupid = :backupid)";
        $params = array('courseid'=>$courseid, 'backupid'=>$backupid);


        $count = $DB->count_records_sql($sql, $params);

        //if there are 0 activity grade items not already included in the backup
        return $count == 0;
    }
}
