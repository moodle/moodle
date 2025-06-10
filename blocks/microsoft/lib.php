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
 * Plugin local library.
 *
 * @package block_microsoft
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2021 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

use local_o365\feature\coursesync\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Return the course sync option of the course with the given ID.
 *
 * @param int $courseid
 *
 * @return int
 */
function block_microsoft_get_course_sync_option(int $courseid) {
    $coursesyncoption = MICROSOFT365_COURSE_SYNC_DISABLED;

    $syncenabledcourseids = utils::get_enabled_courses();

    if ($syncenabledcourseids === true) {
        // Sync is enabled on all courses.
        $coursesyncoption = MICROSOFT365_COURSE_SYNC_ENABLED;
    } else if (in_array($courseid, $syncenabledcourseids)) {
        $coursesyncoption = MICROSOFT365_COURSE_SYNC_ENABLED;
    }

    return $coursesyncoption;
}

/**
 * Set course sync options.
 *
 * @param int $courseid
 * @param int $syncsetting
 */
function block_microsoft_set_course_sync_option(int $courseid, int $syncsetting) {
    if ($syncsetting == MICROSOFT365_COURSE_SYNC_ENABLED) {
        utils::set_course_sync_enabled($courseid);
    } else {
        utils::set_course_sync_enabled($courseid, false);
    }
}

/**
 * Return the existing course reset setting of the course with the given ID.
 *
 * @param int $courseid
 *
 * @return string|null
 */
function block_microsoft_get_course_reset_setting(int $courseid) {
    $courseresetsettings = get_config('local_o365', 'courseresetsettings');
    $courseresetsettings = @json_decode($courseresetsettings, true);
    if (!empty($courseresetsettings) && is_array($courseresetsettings)) {
        if (isset($courseresetsettings[$courseid])) {
            return $courseresetsettings[$courseid];
        }
    }

    return null;
}

/**
 * Set course reset settings for the given course to the given value.
 *
 * @param int $courseid
 * @param string $resetsetting
 */
function block_microsoft_set_course_reset_setting(int $courseid, string $resetsetting) {
    $courseresetsettings = get_config('local_o365', 'courseresetsettings');
    $courseresetsettings = @json_decode($courseresetsettings, true);
    if (empty($courseresetsettings) || !is_array($courseresetsettings)) {
        $courseresetsettings = [$courseid => $resetsetting];
    } else {
        $courseresetsettings[$courseid] = $resetsetting;
    }

    set_config('courseresetsettings', json_encode($courseresetsettings), 'local_o365');
}
