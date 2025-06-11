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

namespace tool_uploadcourse;

use context_course;
use context_coursecat;
use core_course_category;
use core_tag_tag;
use lang_string;
use tool_uploadcourse_course;

/**
 * Checks various permissions related to the course upload process.
 *
 * @package     tool_uploadcourse
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permissions {

    /**
     * Check permission to use tool_uploadcourse in a given category.
     *
     * @param int $catid
     * @param lang_string|null $customerror
     * @return lang_string|null
     */
    protected static function check_permission_to_use_uploadcourse_tool(
        int $catid,
        ?lang_string $customerror = null
    ): ?lang_string {
        $category = core_course_category::get($catid, IGNORE_MISSING);
        if (!$category || !has_capability('tool/uploadcourse:use', $category->get_context())) {
            if ($customerror) {
                return $customerror;
            }
            return new lang_string('courseuploadnotallowed', 'tool_uploadcourse',
                $category ? $category->get_formatted_name() : $catid);
        }
        return null;
    }

    /**
     * Check capabilities to delete a course and to use tool_uploadcourse for it.
     *
     * @param string $shortname course shortname
     * @return lang_string|null
     */
    public static function check_permission_to_delete(string $shortname): ?lang_string {
        global $DB;
        $course = $DB->get_record('course', ['shortname' => $shortname]);
        if ($error = self::check_permission_to_use_uploadcourse_tool($course->category)) {
            return $error;
        }

        if (!has_capability('moodle/course:delete', context_course::instance($course->id))) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:delete'));
        }
        return null;
    }

    /**
     * Check capability in a course (that exists or is about to be created).
     *
     * @param int $do one of tool_uploadcourse_course::DO_UPDATE or tool_uploadcourse_course::DO_ADD
     * @param array $coursedata data to update/create course with, must contain either 'id' or 'category' respectively
     * @param string $capability capability to check
     * @return lang_string|null error string or null
     */
    protected static function check_capability(int $do, array $coursedata, string $capability): ?lang_string {
        if ($do == tool_uploadcourse_course::DO_UPDATE) {
            $context = context_course::instance($coursedata['id']);
            $hascap = has_capability($capability, $context);
        } else {
            $catcontext = context_coursecat::instance($coursedata['category']);
            $hascap = guess_if_creator_will_have_course_capability($capability, $catcontext);
        }

        if (!$hascap) {
            return new lang_string('nopermissions', 'error', get_capability_string($capability));
        }

        return null;
    }

    /**
     * Check permission to update the course.
     *
     * This checks capabilities:
     * - to use tool_uploadcourse in the category where course is in and in the category where it will be moved to (if applicable).
     * - to change course category (if applicable).
     * - to update course details.
     * - to force course language (if applicable).
     * - to change course idnumber, shortname, fullname, summary, visibility, tags (if applicable).
     *
     * @param array $coursedata data to update a course with, always contains 'id'
     * @return lang_string|null
     */
    public static function check_permission_to_update(array $coursedata): ?lang_string {
        $course = get_course($coursedata['id']);

        if ($error = self::check_permission_to_use_uploadcourse_tool($course->category,
                new lang_string('courseuploadupdatenotallowed', 'tool_uploadcourse'))) {
            return $error;
        }

        if (!has_capability('moodle/course:update', context_course::instance($course->id))) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:update'));
        }

        // If user requested to change course category check permissions to use tool in target category
        // and capabilities to change category.
        if (!empty($coursedata['category']) && $coursedata['category'] != $course->category) {
            if ($error = self::check_permission_to_use_uploadcourse_tool($coursedata['category'])) {
                return $error;
            }

            if (!has_capability('moodle/course:changecategory', context_coursecat::instance($course->category))) {
                return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changecategory'));
            }

            if (!has_capability('moodle/course:changecategory', context_coursecat::instance($coursedata['category']))) {
                return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changecategory'));
            }
        }

        $context = context_course::instance($coursedata['id']);

        // If lang is specified, check the user is allowed to set that field.
        if (!empty($coursedata['lang']) && $coursedata['lang'] !== $course->lang) {
            if (!has_capability('moodle/course:setforcedlanguage', $context)) {
                return new lang_string('cannotforcelang', 'tool_uploadcourse');
            }
        }

        // Check permission to change course idnumber.
        if (array_key_exists('idnumber', $coursedata) && $coursedata['idnumber'] !== $course->idnumber &&
                !has_capability('moodle/course:changeidnumber', $context)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changeidnumber'));
        }

        // Check permission to change course shortname.
        if (array_key_exists('shortname', $coursedata) && $coursedata['shortname'] !== $course->shortname &&
                !has_capability('moodle/course:changeshortname', $context)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changeshortname'));
        }

        // Check permission to change course fullname.
        if (array_key_exists('fullname', $coursedata) && $coursedata['fullname'] !== $course->fullname &&
                !has_capability('moodle/course:changefullname', $context)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changefullname'));
        }

        // Check permission to change course summary.
        if (array_key_exists('summary', $coursedata) && $coursedata['summary'] !== $course->summary &&
                !has_capability('moodle/course:changesummary', $context)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:changesummary'));
        }

        // Check permission to change course visibility.
        if (array_key_exists('visible', $coursedata) && $coursedata['visible'] !== $course->visible &&
                !has_capability('moodle/course:visibility', $context)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:visibility'));
        }

        // If tags are specified and enabled check if user can updat them.
        if (core_tag_tag::is_enabled('core', 'course') &&
            (array_key_exists('tags', $coursedata) && strval($coursedata['tags']) !== '') &&
            ($error = self::check_capability(tool_uploadcourse_course::DO_UPDATE, $coursedata, 'moodle/course:tag'))) {
            return $error;
        }

        return null;
    }

    /**
     * Check permission to create course.
     *
     * This checks capabilities:
     * - to use tool_uploadcourse in the category where course will be created.
     * - to create a course.
     * - to force course language (if applicable).
     * - to set course tags (if applicable).
     *
     * @param array $coursedata data to create a course with, always contains 'category'
     * @return lang_string|null
     */
    public static function check_permission_to_create(array $coursedata): ?lang_string {

        if ($error = self::check_permission_to_use_uploadcourse_tool($coursedata['category'])) {
            return $error;
        }

        $catcontext = context_coursecat::instance($coursedata['category']);

        // Check user is allowed to create courses in this category.
        if (!has_capability('moodle/course:create', $catcontext)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:create'));
        }

        // If lang is specified, check the user is allowed to set that field.
        if (!empty($coursedata['lang'])) {
            if (!guess_if_creator_will_have_course_capability('moodle/course:setforcedlanguage', $catcontext)) {
                return new lang_string('cannotforcelang', 'tool_uploadcourse');
            }
        }

        // Check permission to change course visibility.
        if (array_key_exists('visible', $coursedata) && !$coursedata['visible'] &&
                !guess_if_creator_will_have_course_capability('moodle/course:visibility', $catcontext)) {
            return new lang_string('nopermissions', 'error', get_capability_string('moodle/course:visibility'));
        }

        // If tags are specified and enabled check if user can updat them.
        if (core_tag_tag::is_enabled('core', 'course') &&
                (array_key_exists('tags', $coursedata) && strval($coursedata['tags']) !== '') &&
                ($error = self::check_capability(tool_uploadcourse_course::DO_CREATE, $coursedata, 'moodle/course:tag'))) {
            return $error;
        }

        return null;
    }

    /**
     * Check if the user is able to reset a course.
     *
     * Capability to use the tool and update the course is already checked earlier.
     *
     * @param array $coursedata data to update course with, always contains 'id'
     * @return lang_string|null error string or null
     */
    public static function check_permission_to_reset(array $coursedata): ?lang_string {
        return self::check_capability(tool_uploadcourse_course::DO_UPDATE, $coursedata, 'moodle/course:reset');
    }

    /**
     * Check if the user is able to restore the mbz into a course.
     *
     * This method does not need to check if the course can be updated/created, this is checked earlier.
     *
     * @param int $do one of tool_uploadcourse_course::DO_UPDATE or tool_uploadcourse_course::DO_ADD
     * @param array $coursedata data to update/create course with, must contain either 'id' or 'category' respectively
     * @return lang_string|null error string or null
     */
    public static function check_permission_to_restore(int $do, array $coursedata): ?lang_string {
        return self::check_capability($do, $coursedata, 'moodle/restore:restorecourse');
    }
}
