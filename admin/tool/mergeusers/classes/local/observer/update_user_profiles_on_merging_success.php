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
 * Observer to catch successful merge user operations and update
 * custom profile fields initialized also by this plugin.
 *
 * @package tool_mergeusers
 * @author Sam Møller <smo@moxis.dk>
 * @copyright 2019 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\observer;

use tool_mergeusers\event\user_merged_success;
use tool_mergeusers\local\profile_fields;

// @codeCoverageIgnoreStart
defined('MOODLE_INTERNAL') || die();
// @codeCoverageIgnoreEnd


global $CFG;
require_once $CFG->dirroot . '/user/profile/lib.php';

/**
 * Observer class to update information on custom profile fields on both
 * related users on a given merge user operation.
 */
class update_user_profiles_on_merging_success {

    /**
     * Processes the successful merge user operation, updating custom profile fields
     * from both related users.
     *
     * @param user_merged_success $event
     * @return void
     */
    public static function update(user_merged_success $event): void {
        global $DB;
        try {

            $olduserid = $event->get_old_user_id();
            $newuserid = $event->get_new_user_id();
            $logid = $event->get_log_id();
            self::update_old_user($olduserid, $newuserid, $logid, $event->timecreated);
            self::update_new_user($newuserid, $olduserid, $logid, $event->timecreated);

        } catch (\Exception $e) {}
    }

    /**
     * Updates custom profile fields for the old user.
     *
     * @param int $olduserid
     * @param int $newuserid
     * @param int $logid
     * @param int $timecreated
     * @return void
     */
    private static function update_old_user(int $olduserid, int $newuserid, int $logid, int $timecreated): void {
        $fields = [
            profile_fields::MERGE_DATE => $timecreated,
            profile_fields::MERGE_LOG_ID => $logid,
            profile_fields::MERGE_NEW_USER_ID => $newuserid,
            profile_fields::MERGE_OLD_USER_ID => null,
        ];
        profile_save_custom_fields($olduserid, $fields);
    }

    /**
     * Updates custom profile fields for the new user.
     *
     * @param int $newuserid
     * @param int $olduserid
     * @param int $logid
     * @param int $timecreated
     * @return void
     */
    private static function update_new_user(int $newuserid, int $olduserid, int $logid, int $timecreated): void {
        $fields = [
            profile_fields::MERGE_DATE => $timecreated,
            profile_fields::MERGE_LOG_ID => $logid,
            profile_fields::MERGE_NEW_USER_ID => null,
            profile_fields::MERGE_OLD_USER_ID => $olduserid,
        ];
        profile_save_custom_fields($newuserid, $fields);
    }
}
