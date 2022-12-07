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

namespace core_external;

use context_course;
use context_helper;
use moodle_url;

/**
 * Utility functions for the external API.
 *
 * @package    core_webservice
 * @copyright  2015 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.0
 */
class util {

    /**
     * Validate a list of courses, returning the complete course objects for valid courses.
     *
     * Each course has an additional 'contextvalidated' field, this will be set to true unless
     * you set $keepfails, in which case it will be false if validation fails for a course.
     *
     * @param  array $courseids A list of course ids
     * @param  array $courses   An array of courses already pre-fetched, indexed by course id.
     * @param  bool $addcontext True if the returned course object should include the full context object.
     * @param  bool $keepfails  True to keep all the course objects even if validation fails
     * @return array            An array of courses and the validation warnings
     */
    public static function validate_courses($courseids, $courses = array(), $addcontext = false,
            $keepfails = false) {
        global $DB;

        // Delete duplicates.
        $courseids = array_unique($courseids);
        $warnings = array();

        // Remove courses which are not even requested.
        $courses = array_intersect_key($courses, array_flip($courseids));

        // For any courses NOT loaded already, get them in a single query (and preload contexts)
        // for performance. Preserve ordering because some tests depend on it.
        $newcourseids = [];
        foreach ($courseids as $cid) {
            if (!array_key_exists($cid, $courses)) {
                $newcourseids[] = $cid;
            }
        }
        if ($newcourseids) {
            list ($listsql, $listparams) = $DB->get_in_or_equal($newcourseids);

            // Load list of courses, and preload associated contexts.
            $contextselect = context_helper::get_preload_record_columns_sql('x');
            $newcourses = $DB->get_records_sql("
                            SELECT c.*, $contextselect
                              FROM {course} c
                              JOIN {context} x ON x.instanceid = c.id
                             WHERE x.contextlevel = ? AND c.id $listsql",
                    array_merge([CONTEXT_COURSE], $listparams));
            foreach ($newcourseids as $cid) {
                if (array_key_exists($cid, $newcourses)) {
                    $course = $newcourses[$cid];
                    context_helper::preload_from_record($course);
                    $courses[$course->id] = $course;
                }
            }
        }

        foreach ($courseids as $cid) {
            // Check the user can function in this context.
            try {
                $context = context_course::instance($cid);
                external_api::validate_context($context);

                if ($addcontext) {
                    $courses[$cid]->context = $context;
                }
                $courses[$cid]->contextvalidated = true;
            } catch (Exception $e) {
                if ($keepfails) {
                    $courses[$cid]->contextvalidated = false;
                } else {
                    unset($courses[$cid]);
                }
                $warnings[] = array(
                    'item' => 'course',
                    'itemid' => $cid,
                    'warningcode' => '1',
                    'message' => 'No access rights in course context'
                );
            }
        }

        return array($courses, $warnings);
    }

    /**
     * Returns all area files (optionally limited by itemid).
     *
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID or all files if not specified
     * @param bool $useitemidinurl wether to use the item id in the file URL (modules intro don't use it)
     * @return array of files, compatible with the external_files structure.
     * @since Moodle 3.2
     */
    public static function get_area_files($contextid, $component, $filearea, $itemid = false, $useitemidinurl = true) {
        $files = array();
        $fs = get_file_storage();

        if ($areafiles = $fs->get_area_files($contextid, $component, $filearea, $itemid, 'itemid, filepath, filename', false)) {
            foreach ($areafiles as $areafile) {
                $file = array();
                $file['filename'] = $areafile->get_filename();
                $file['filepath'] = $areafile->get_filepath();
                $file['mimetype'] = $areafile->get_mimetype();
                $file['filesize'] = $areafile->get_filesize();
                $file['timemodified'] = $areafile->get_timemodified();
                $file['isexternalfile'] = $areafile->is_external_file();
                if ($file['isexternalfile']) {
                    $file['repositorytype'] = $areafile->get_repository_type();
                }
                $fileitemid = $useitemidinurl ? $areafile->get_itemid() : null;
                $file['fileurl'] = moodle_url::make_webservice_pluginfile_url($contextid, $component, $filearea,
                                    $fileitemid, $areafile->get_filepath(), $areafile->get_filename())->out(false);
                $files[] = $file;
            }
        }
        return $files;
    }
}
