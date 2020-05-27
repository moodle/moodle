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
 * Infected file report
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_infectedfiles\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * Infected file report
 *
 * @package    report_infectedfiles
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        request\plugin\provider,
        request\core_userlist_provider {

    /**
     * This plugin stores the userid of infected users.
     *
     * @param collection $collection the collection object to add data to.
     * @return collection The populated collection.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'infected_files',
            [
                'userid' => 'privacy:metadata:infected_files:userid',
                'filename' => 'privacy:metadata:infected_files:filename',
                'timecreated' => 'privacy:metadata:infected_files:timecreated',
            ],
            'privacy:metadata:infected_files'
        );

        return $collection;
    }

    /**
     * This function gets the contexts containing data for a userid.
     *
     * @param int $userid The userid to get contexts for.
     * @return request\contextlist the context list for the user.
     */
    public static function get_contexts_for_userid(int $userid) : request\contextlist {
        $contextlist = new request\contextlist();

        // The system context is the only context where information is stored.
        $contextlist->add_system_context();
        return $contextlist;
    }

    /**
     * This function exports user data on infected files from the contextlist provided.
     *
     * @param request\approved_contextlist $contextlist
     * @return void
     */
    public static function export_user_data(request\approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist as $context) {
            // We only export from system context.
            if ($context->contextlevel === CONTEXT_SYSTEM) {

                $userid = $contextlist->get_user()->id;
                $exportdata = [];

                $records = $DB->get_records('infected_files', ['userid' => $userid]);
                foreach ($records as $record) {
                    // Export only the data that does not expose internal information.
                    $data = [];
                    $data['userid'] = $record->userid;
                    $data['timecreated'] = $record->timecreated;
                    $data['filename'] = $record->filename;

                    $exportdata[] = $data;
                }

                // Now export this data in the infected files table as subcontext.
                request\writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:infected_files_subcontext', 'report_infectedfiles')],
                    (object) $exportdata
                );
            }
        }
    }

    /**
     * As this report tracks potential attempted security violations,
     * This data should not be deleted at request. This would allow for an
     * avenue for a malicious user to cover their tracks. This function deliberately
     * does no deletes.
     *
     * @param \context $context the context to delete for.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        return;
    }

    /**
     * As this report tracks potential attempted security violations,
     * This data should not be deleted at request. This would allow for an
     * avenue for a malicious user to cover their tracks. This function deliberately
     * does no deletes.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist the contextlist to delete for.
     * @return void
     */
    public static function delete_data_for_user(request\approved_contextlist $contextlist) {
        return;
    }

    /**
     * This gets the list of users inside of the provided context. In this case, its only system context
     * which contains users.
     *
     * @param \core_privacy\local\request\userlist $userlist
     * @return void
     */
    public static function get_users_in_context(request\userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel === CONTEXT_SYSTEM) {
            // If we are checking system context, we need to get all distinct userids from the table.
            $sql = 'SELECT DISTINCT userid
                      FROM {infected_files}';

            $userlist->add_from_sql('userid', $sql, []);
        }
    }

    /**
     * As this report tracks potential attempted security violations,
     * This data should not be deleted at request. This would allow for an
     * avenue for a malicious user to cover their tracks. This function deliberately
     * does no deletes.
     *
     * @param request\approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(request\approved_userlist $userlist) {
        return;
    }
}
