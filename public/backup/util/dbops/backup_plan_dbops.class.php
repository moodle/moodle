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

use core\context\course;
use core\context\module;
use core\exception\coding_exception;
use core\output\mustache_engine;
use core\output\mustache_string_helper;

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
     * @var string Default template for course backups
     */
    public const DEFAULT_FILENAME_TEMPLATE_COURSE = '{{#str}}backupfilename{{/str}}-{{format}}-{{type}}-{{id}}{{^useidonly}}-' .
    '{{course.shortname}}{{/useidonly}}-{{date}}{{^users}}-nu{{/users}}{{#anonymised}}{{#users}}-an{{/users}}{{/anonymised}}' .
    '{{^files}}-nf{{/files}}';

    /**
     * @var string Default template for section backups
     */
    public const DEFAULT_FILENAME_TEMPLATE_SECTION = '{{#str}}backupfilename{{/str}}-{{format}}-{{type}}-{{id}}{{^useidonly}}' .
    '{{#section.name}}-{{section.name}}{{/section.name}}{{^section.name}}-{{section.section}}{{/section.name}}{{/useidonly}}-' .
    '{{date}}{{^users}}-nu{{/users}}{{#anonymised}}{{#users}}-an{{/users}}{{/anonymised}}{{^files}}-nf{{/files}}';

    /**
     * @var string Default template for activity backups
     */
    public const DEFAULT_FILENAME_TEMPLATE_ACTIVITY = '{{#str}}backupfilename{{/str}}-{{format}}-{{type}}-{{id}}{{^useidonly}}' .
    '-{{activity.modname}}{{id}}{{/useidonly}}-{{date}}{{^users}}-nu{{/users}}{{#anonymised}}{{#users}}-an{{/users}}' .
    '{{/anonymised}}{{^files}}-nf{{/files}}';

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

        // Get the section->sequence contents (it roots the activities order)
        // Get all course modules belonging to requested section
        $modulesarr = array();
        $modules = $DB->get_records_sql("
            SELECT cm.id, m.name AS modname
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
             WHERE cm.course = ?
               AND cm.section = ?
               AND cm.deletioninprogress <> 1", array($courseid, $sectionid));
        foreach (explode(',', (string) $secrec->sequence) as $moduleid) {
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
     * Given one section id, returns the full section record.
     *
     * @param int $sectionid
     * @return stdClass
     */
    public static function get_section_from_id($sectionid): stdClass {
        global $DB;
        return $DB->get_record('course_sections', ['id' => $sectionid]);
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
     * and info can be (nu = no user info, an = anonymized). The last param $useidonly,
     * defaulting to false, allows to replace the course shortname by the course id (used
     * by automated backups, to avoid non-ascii chars in OS filesystem)
     *
     * @param string $format One of backup::FORMAT_
     * @param string $type One of backup::TYPE_
     * @param int $id course id, section id, or course module id
     * @param bool $users Should be true is users were included in the backup
     * @param bool $anonymised Should be true is user information was anonymized
     * @param bool $useidonly only use the ID in the file name
     * @param bool $files if files are included
     * @param int|null $time time to use in any dates, if not given uses current time
     * @return string The filename to use
     */
    public static function get_default_backup_filename(
        string $format,
        string $type,
        int $id,
        bool $users,
        bool $anonymised,
        bool $useidonly = false,
        bool $files = true,
        ?int $time = null
    ): string {
        global $DB;

        if ($time === null) {
            $time = time();
        }

        $backupdateformat = str_replace(' ', '_', get_string('backupnameformat', 'langconfig'));
        $formatdate = function (int $date) use ($backupdateformat): string {
            $date = userdate($date, $backupdateformat, 99, false);
            return core_text::strtolower(trim(clean_filename($date), '_'));
        };

        $mustachecontext = [
            'format' => $format,
            'type' => $type,
            'id' => $id,
            'users' => $users,
            'anonymised' => $anonymised,
            'files' => $files,
            'useidonly' => $useidonly,
            'time' => $time,
            'date' => $formatdate($time),
        ];

        // Add extra context based on the type of backup.
        // It is important to use array and not stdClass here, otherwise array_walk_recursive will not work.
        // Additionally get the moodle context of an item, which is used for format_string.
        $itemcontext = null;
        switch ($type) {
            case backup::TYPE_1COURSE:
                $mustachecontext['course'] = (array) $DB->get_record(
                    'course',
                    ['id' => $id],
                    'shortname,fullname,startdate,enddate',
                    MUST_EXIST
                );
                $mustachecontext['course']['startdate'] = $formatdate($mustachecontext['course']['startdate']);
                $mustachecontext['course']['enddate'] = $formatdate($mustachecontext['course']['enddate']);

                $itemcontext = course::instance($id);
                break;
            case backup::TYPE_1SECTION:
                $mustachecontext['section'] = (array) $DB->get_record('course_sections', ['id' => $id], 'name,section', MUST_EXIST);

                // A section is still course context, but needs an extra step to find the course id.
                $courseid = $DB->get_field('course_sections', 'course', ['id' => $id], MUST_EXIST);
                $itemcontext = course::instance($courseid);
                break;
            case backup::TYPE_1ACTIVITY:
                $cm = get_coursemodule_from_id(null, $id, 0, false, MUST_EXIST);
                $mustachecontext['activity'] = [
                    'modname' => $cm->modname,
                    'name' => $cm->name,
                ];

                $itemcontext = module::instance($id);
                break;
            default:
                throw new coding_exception('Unknown backup type ' . $type);
        }

        // Recursively format all the strings and trim any extra whitespace.
        array_walk_recursive($mustachecontext, function (&$item) use ($itemcontext) {
            if (is_string($item)) {
                // Update by reference.
                $item = trim(format_string($item, true, ['context' => $itemcontext]));
            }
        });

        // List of templates in order (if one fails, go to next) for each type.
        $templates = [
            backup::TYPE_1COURSE => [
                get_config('backup', 'backup_default_filename_template_course'),
                self::DEFAULT_FILENAME_TEMPLATE_COURSE,
            ],
            backup::TYPE_1SECTION => [
                get_config('backup', 'backup_default_filename_template_section'),
                self::DEFAULT_FILENAME_TEMPLATE_SECTION,
            ],
            backup::TYPE_1ACTIVITY => [
                get_config('backup', 'backup_default_filename_template_activity'),
                self::DEFAULT_FILENAME_TEMPLATE_ACTIVITY,
            ],
        ];

        $mustache = self::get_mustache_for_filename_generation();

        // Render the templates until one succeeds.
        foreach ($templates[$type] as $possibletemplate) {
            try {
                $new = @$mustache->render($possibletemplate, $mustachecontext);

                // Clean as filename, remove spaces, and trim to max 251 chars (filename limit, 255 including .mbz extension).
                $cleaned = substr(str_replace(' ', '_', clean_filename($new)), 0, 251);

                // Success - this template rendered - return it.
                return $cleaned . '.mbz';
            } catch (Throwable $e) {
                // Skip and try the next.
                continue;
            }
        }

        // At a minumum the fallback default filenames should have rendered correctly.
        // If we reached here it means this did not happen and that something is very wrong.
        throw new coding_exception("No backup filename templates rendered correctly");
    }

    /**
     * Get mustache engine instance to be used in filename generation.
     * @return mustache_engine
     */
    private static function get_mustache_for_filename_generation(): mustache_engine {
        return new mustache_engine([
            'helpers' => [
                'str' => [new mustache_string_helper(), 'str'],
            ],
        ]);
    }

    /**
     * Validates the given backup filename template is syntatically valid.
     *
     * Used mainly for form validation.
     * @param string $template mustache template
     * @return array array of string error messages, if empty then there are no errors and it is valid
     */
    public static function get_default_backup_filename_template_syntax_errors(string $template): array {
        try {
            // Render without any context, if it is syntatically invalid,
            // this will throw an exception.
            // This also outputs warnings if invalid, so we just ignore them using '@'.
            @self::get_mustache_for_filename_generation()->render($template);

            // No exceptions thrown - is valid!
            return [];
        } catch (Throwable $e) {
            return [$e->getMessage()];
        }
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
