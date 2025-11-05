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
 * Validates if the file should be pushed to Ally.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use stored_file;
use context;
use context_course;
use coding_exception;
use tool_ally\local_content;

/**
 * Validates if the file should be pushed to Ally.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_validator {

    /**
     * Ally whitelisted components.
     * NOTE: These are component areas where ONLY teachers and admins can create files.
     * (Not taking into account role overrides, but hey - who uses those?).
     */
    const TEACHER_WHITELIST = [
        'block_html~content',
        'calendar~event_description', // Only teachers can create event descriptions within a course context.
        'course~overviewfiles',
        'course~section',
        'course~summary',
        'group~description',
        'mod_assign~intro',
        'mod_assign~introattachment',
        'mod_book~chapter',
        'mod_book~intro',
        'mod_chat~intro',
        'mod_choice~intro',
        'mod_feedback~intro',
        'mod_folder~content',
        'mod_folder~intro',
        'mod_forum~intro',
        'mod_glossary~intro',
        'mod_hsuforum~intro',
        'mod_imscp~content',
        'mod_kalvidres~intro',
        'mod_label~intro',
        'mod_lesson~intro',
        'mod_lesson~page_answers',
        'mod_lesson~page_contents',
        'mod_lesson~page_responses',
        'mod_page~content',
        'mod_page~intro',
        'mod_questionnaire~intro',
        'mod_questionnaire~question',
        'mod_quiz~intro',
        'mod_resource~intro',
        'mod_resource~content',
        'mod_turnitintooltwo~intro',
        'mod_url~intro'
    ];

    /**
     * White list of component / fileareas where both teachers AND students can create files but we still want to
     * include the teacher files if we can prove they were authored by teachers. So basically this is a whitelist
     * where we must ALSO check the role of the user who created the file.
     */
    const CHECKROLE_WHITELIST = [
        'mod_lesson~mediafile',
        'mod_forum~attachment',
        'mod_forum~post',
        'mod_hsuforum~attachment',
        'mod_hsuforum~post',
        'mod_hsuforum~comments',
        'mod_glossary~entry',
        'mod_glossary~attachment',
        'mod_data~content',
        'mod_lightboxgallery~gallery_images',
        'mod_questionnaire~info'
    ];

    /**
     * Fileareas where all files should be considered in use.
     * These are fileareas where files in them are automatically in use, even though they don't appear in
     * any HTML content.
     */
    const ALWAYS_IN_USE = [
        'course~overviewfiles',
        'calendar~event_description',
        'mod_assign~introattachment',
        'mod_folder~content',
        'mod_forum~attachment',
        'mod_glossary~attachment',
        'mod_hsuforum~attachment',
        'mod_imscp~content',
        'mod_resource~content'
    ];
    /**
     * @var array
     */
    private $userids;

    /**
     * @var role_assignments
     */
    private $assignments;

    /**
     * Creates a new file_validator.
     * @param array $userids
     * @param role_assignments|null $assignments
     */
    public function __construct(array $userids = [], role_assignments $assignments = null) {
        $this->userids        = $userids;
        $this->assignments    = $assignments ?: new role_assignments();;
    }

    /**
     * @return array
     */
    public static function whitelist() {
        return array_merge(self::TEACHER_WHITELIST, self::CHECKROLE_WHITELIST);
    }

    /**
     * Validates if the file should be pushed to Ally.
     * @param stored_file $file
     * @param context|null $context
     * @param bool $skipinusecheck Don't check files for being in use, even if the setting is on. Used during
     *                           deletions, since we can't check the HTML content at that point.
     * @return bool
     * @throws coding_exception
     */
    public function validate_stored_file(stored_file $file, context $context = null, $skipinusecheck = false) {
        // Can a course context be gotten?
        try {
            $context = $context ?: context::instance_by_id($file->get_contextid());
            $coursectx = $context->get_course_context(false);
            if (!$coursectx instanceof context_course) {
                // We couldn't get a course context for this file. We are only interested in course files so abort.
                return false;
            }
        } catch (\moodle_exception $mex) {
            // Context may not exist, hence, record might not be found on DB.
            return false;
        }

        // Is it whitelisted?
        // i.e. is it in a component that only teachers should have access to use.
        $component = $file->get_component();
        $area = $file->get_filearea();
        if (!self::check_pathname($file)) {
            return false;
        }

        // Check if the file is in a teacher whitelist area, or if in a valid area with a creator that is
        // an editing teacher/admin/manager/etc.
        if ($this->check_component_area_teacher_whitelist($component, $area) ||
                $this->check_component_area_whitelist_and_user_type($component, $area, $file, $context)) {
            // At this point we do not need to check that the user is still an editing teacher / manager / admin / etc.
            // That is because we know that the file belongs to a context that is whitelisted as teacher only.
            // E.g. the file was created as resource content, or a forum intro.
            // A student would NOT have been able to create this file (not without some role shenanigans anyway).
            // WE DO NOT bother checking if the user who created this file is still an editing teacher / manager /
            // admin / etc because they might a) No longer be enrolled on the course, b) Have a different role to the
            // one they had when they created the file.

            if (!$skipinusecheck) {
                // If we are checking files to see if they are in use, do that now.
                return files_in_use::check_file_in_use($file, $context);
            }

            return true;
        }

        // At this point, the file isn't in any area we know about, so it can be considered invalid.
        return false;
    }

    /**
     * Check component and area to see if it's whitelisted as a teacher authored file - return true if it is.
     * @param string $component
     * @param string $filearea
     * @return bool
     */
    private function check_component_area_teacher_whitelist($component, $filearea) {
        $key = $component.'~'.$filearea;
        return in_array($key, self::TEACHER_WHITELIST);
    }

    /**
     * Check component and area to see if it's whitelisted for both teacher and student authorship.
     * This DOES NOT mean that student files will be accepted - it means that a further check on user type
     * is required to ensure a teacher created the file.
     * @param string $component
     * @param string $filearea
     * @param stored_file $file
     * @param context $context
     * @return bool
     */
    private function check_component_area_whitelist_and_user_type($component, $filearea,
                                                                  stored_file $file, context $context) {
        $key = $component.'~'.$filearea;
        if (!in_array($key, self::CHECKROLE_WHITELIST)) {
            return false;
        }
        $userid = $file->get_userid();
        return empty($userid) || array_key_exists($userid, $this->userids) ||
            $this->assignments->has($userid, $context);
    }

    /**
     * @param stored_file $file
     * @return bool
     */
    public static function check_pathname(stored_file $file) {
        if ($file->get_filepath() == '/gridimage/') {
            return false;
        }
        return true;
    }
}
