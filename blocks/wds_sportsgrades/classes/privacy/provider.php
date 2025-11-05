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
 * Privacy implementation for the Sports Grades block
 *
 * @package    block_wds_sportsgrades
 * @copyright  2025 Onwards - Robert Russo
 * @copyright  2025 Onwards - Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_wds_sportsgrades\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\writer;

/**
 * Privacy provider for the Sports Grades block
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata about this plugin's privacy practices.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'block_wds_sportsgrades_cache',
            [
                'studentid' => 'privacy:metadata:block_wds_sportsgrades_cache:studentid',
                'data' => 'privacy:metadata:block_wds_sportsgrades_cache:data',
                'timecreated' => 'privacy:metadata:block_wds_sportsgrades_cache:timecreated',
                'timeexpires' => 'privacy:metadata:block_wds_sportsgrades_cache:timeexpires',
            ],
            'privacy:metadata:block_wds_sportsgrades_cache'
        );

        $collection->add_database_table(
            'block_wds_sportsgrades_access',
            [
                'userid' => 'privacy:metadata:block_wds_sportsgrades_access:userid',
                'sportid' => 'privacy:metadata:block_wds_sportsgrades_access:sportid',
                'timecreated' => 'privacy:metadata:block_wds_sportsgrades_access:timecreated',
                'timemodified' => 'privacy:metadata:block_wds_sportsgrades_access:timemodified',
                'createdby' => 'privacy:metadata:block_wds_sportsgrades_access:createdby',
                'modifiedby' => 'privacy:metadata:block_wds_sportsgrades_access:modifiedby',
            ],
            'privacy:metadata:block_wds_sportsgrades_access'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();
        
        // Add system context for cache entries.
        $sql = "SELECT c.id
                FROM {context} c
                JOIN {block_wds_sportsgrades_cache} bc ON bc.studentid = :userid1
                WHERE c.contextlevel = :contextlevel
                UNION
                SELECT c.id
                FROM {context} c
                JOIN {block_wds_sportsgrades_access} ba ON ba.userid = :userid2
                WHERE c.contextlevel = :contextlevel2";
                
        $params = [
            'userid1' => $userid,
            'contextlevel' => CONTEXT_SYSTEM,
            'userid2' => $userid,
            'contextlevel2' => CONTEXT_SYSTEM,
        ];
        
        $contextlist->add_from_sql($sql, $params);
        
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        
        if (empty($contextlist->count())) {
            return;
        }
        
        $userid = $contextlist->get_user()->id;
        
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }
            
            // Export cache data for this user.
            $cachedata = [];
            $cacherecords = $DB->get_records('block_wds_sportsgrades_cache', ['studentid' => $userid]);
            
            foreach ($cacherecords as $record) {
                $cachedata[] = [
                    'studentid' => $record->studentid,
                    'timecreated' => transform::datetime($record->timecreated),
                    'timeexpires' => transform::datetime($record->timeexpires),
                ];
            }
            
            if (!empty($cachedata)) {
                $context = context_system::instance();
                writer::with_context($context)->export_data(
                    ['block_wds_sportsgrades', 'cache'],
                    (object) ['cache_records' => $cachedata]
                );
            }
            
            // Export access data for this user.
            $accessdata = [];
            $accessrecords = $DB->get_records('block_wds_sportsgrades_access', ['userid' => $userid]);
            
            foreach ($accessrecords as $record) {
                $accessdata[] = [
                    'userid' => $record->userid,
                    'sportid' => $record->sportid,
                    'timecreated' => transform::datetime($record->timecreated),
                    'timemodified' => transform::datetime($record->timemodified),
                    'createdby' => $record->createdby,
                    'modifiedby' => $record->modifiedby,
                ];
            }
            
            if (!empty($accessdata)) {
                $context = context_system::instance();
                writer::with_context($context)->export_data(
                    ['block_wds_sportsgrades', 'access'],
                    (object) ['access_records' => $accessdata]
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        
        // Delete all cache and access records.
        $DB->delete_records('block_wds_sportsgrades_cache');
        $DB->delete_records('block_wds_sportsgrades_access');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        
        if (empty($contextlist->count())) {
            return;
        }
        
        $userid = $contextlist->get_user()->id;
        
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }
            
            // Delete cache records for this user.
            $DB->delete_records('block_wds_sportsgrades_cache', ['studentid' => $userid]);
            
            // Delete access records for this user.
            $DB->delete_records('block_wds_sportsgrades_access', ['userid' => $userid]);
        }
    }
}
