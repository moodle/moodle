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
 * User utils.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\utils;

use block_xp\di;
use context_course;
use stdClass;

/**
 * User utils.
 *
 * @package    block_xp
 * @copyright  2021 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_utils {

    /**
     * Whether a user can earn points.
     *
     * @param \context $context The context.
     * @param int $userid The user ID.
     * @return bool
     */
    public static function can_earn_points($context, $userid) {

        // This is a fail safe, as there are known instances where, events for instance,
        // do not return a valid context. So might as well handle this edge case.
        if (!$context) {
            return false;
        }

        if (!$userid) {
            return false;
        } else if (!\core_user::is_real_user($userid)) {
            return false;
        } else if (isguestuser($userid)) {
            return false;
        }

        // Site admins always have all permissions, so we defer to the admin setting.
        if (is_siteadmin($userid)) {
            $config = di::get('config');
            return (bool) $config->get('adminscanearnxp');
        }

        // It has been reported that this can throw an exception when the context got missing
        // but is still cached within the event object. Or something like that...
        try {
            return has_capability('block/xp:earnxp', $context, $userid);
        } catch (\moodle_exception $e) {
            return false;
        }
    }

    /**
     * Get the default picture URL.
     *
     * @return \moodle_url The URL.
     */
    public static function default_picture() {
        return di::get('renderer')->pix_url('u/f1');
    }

    /**
     * Get a user's primary group ID.
     *
     * This is useful when attempting to determine the primary group of a user
     * when the group selector does not apply, such as on the course page.
     *
     * @param int $courseid The course ID.
     * @param int $userid The user ID.
     * @return int
     */
    public static function get_primary_group_id($courseid, $userid) {
        $course = get_fast_modinfo($courseid)->get_course();
        $groupmode = groups_get_course_groupmode($course);
        $context = context_course::instance($courseid);
        $aag = has_capability('moodle/site:accessallgroups', $context, $userid);

        if ($groupmode == NOGROUPS && !$aag) {
            $allowedgroups = [];
            $usergroups = [];
        } else if ($groupmode == VISIBLEGROUPS || $aag) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
            $usergroups = groups_get_all_groups($course->id, $userid, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $userid, $course->defaultgroupingid);
            $usergroups = $allowedgroups;
        }

        // If we don't have at least a group, then we can see everybody.
        if (empty($usergroups)) {
            return 0;
        }
        return reset($usergroups)->id;
    }

    /**
     * Get a user's timezone.
     *
     * @param int|object $userorid The user, or its ID.
     * @return \DateTimeZone
     */
    public static function get_timezone($userorid) {
        global $USER;
        $user = $userorid;

        if (!is_object($userorid)) {
            if ($userorid == $USER->id) {
                $user = $USER;
            }
            $user = \core_user::get_user($userorid, '*', MUST_EXIST);
        }

        return \core_date::get_user_timezone_object($user);
    }

    /**
     * Get all the name fields.
     *
     * This almost replicates the behaviour of get_all_user_name_fields() whilst seamlessly
     * handling its deprecation across the various versions we support. Most parameters
     * which we do not use have been dropped.
     *
     * @param string $tableprefix Name of the database prefix, without the '.'.
     * @return string
     */
    public static function name_fields($tableprefix = null) {
        if (class_exists('core_user\fields')) {
            $userfields = \core_user\fields::for_name();
            $selects = $userfields->get_sql($tableprefix, false, '', '', false)->selects;
            if (!$tableprefix) {
                $selects = str_replace('{user}.', '', $selects);
            }
            return $selects;
        }
        return get_all_user_name_fields(true, $tableprefix);
    }

    /**
     * Get the "picture fields" of the user.
     *
     * This almost replicates the behaviour of user_picture::fields whilst seamlessly
     * handling its deprecation across the various versions we support. The option
     * for extrafields is not supported.
     *
     * The "picture fields" contain everything we need to display a
     * user's full name and its avatar. To extract these from a SQL
     * result, refer to {@see self::unalias_picture_fields()}.
     *
     * @param string $tableprefix Name of the database prefix, without the '.'.
     * @param string $idalias The alias for the user ID field.
     * @param string $fieldprefix Prefix to use for aliasing the fields.
     * @return string
     */
    public static function picture_fields($tableprefix = '', $idalias = 'id', $fieldprefix = '') {
        if (class_exists('core_user\fields')) {
            // Logic mostly copied from deprecation of user_picture::fields().
            $userfields = \core_user\fields::for_userpic();
            $selects = $userfields->get_sql($tableprefix, false, $fieldprefix, $idalias, false)->selects;
            if ($tableprefix === '') {
                $selects = str_replace('{user}.', '', $selects);
            }
            return $selects;
        }
        return \user_picture::fields($tableprefix, null, $idalias, $fieldprefix);
    }

    /**
     * Extract the "picture fields" from a record.
     *
     * @param stdClass $record The database record.
     * @param string $idalias The alias for the user ID field.
     * @param string $fieldprefix Prefix to use for aliasing the fields.
     * @return stdClass
     */
    public static function unalias_picture_fields(stdClass $record, $idalias = 'id', $fieldprefix = '') {
        return \user_picture::unalias($record, null, $idalias, $fieldprefix);
    }

    /**
     * Get the default picture URL.
     *
     * @param object $user The user.
     * @return \moodle_url The URL.
     */
    public static function user_picture($user) {
        return di::get('renderer')->get_user_picture($user);
    }

}
