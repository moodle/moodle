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
 * Privacy Subsystem implementation for local_email.
 *
 * @package    local_email
 * @copyright  2018 E-Learn Design http://www.e-learndesign.co.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_email\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\deletion_criteria;
use core_privacy\local\request\helper;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the choice activity module.
 *
 * @copyright  2018 E-Learn Design (http://www.e-learndesign.co.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This plugin stores personal data.
        \core_privacy\local\metadata\provider,

        // This plugin is a core_user_data_provider.
        \core_privacy\local\request\plugin\provider {
    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) {
        $items->add_database_table(
            'local_email',
            [
                'id' => 'privacy:metadata:local_email:id',
                'templatename' => 'privacy:metadata:local_email:templatename',
                'sent' => 'privacy:metadata:local_email:sent',
                'subject' => 'privacy:metadata:local_email:subject',
                'body' => 'privacy:metadata:local_email:body',
                'courseid' => 'privacy:metadata:local_email:courseid',
                'userid' => 'privacy:metadata:local_email:userid',
                'invoiceid' => 'privacy:metadata:local_email:invoiceid',
                'classroomid' => 'privacy:metadata:local_email:senderid',
                'headers' => 'privacy:metadata:local_email:headers',
            ],
            'privacy:metadata:local_email'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid($userid) {
        // Fetch all choice answers.
        $sql = "SELECT c.id
                  FROM {context} c
                WHERE contextlevel = :contextlevel";

        $params = [
            'userid'  => $userid,
            'contextlevel'  => CONTEXT_SYSTEM,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $context = context_system::instance();

        // Get the emails information.
        $emailsql = "SELECT * FROM {email}
                     WHERE userid = :userid
                     OR senderid = :senderid
                     OR " . $DB->sql_like('headers', ':email');
        $params = array('userid' => $user->id,
                        'senderid' => $user->id,
                        'email' => $user->email);
        if ($emails = $DB->get_records_sql($emailsql, $params)) {
            foreach ($emails as $email) {
                writer::with_context($context)->export_data($context, $email);
            }
        }
    }


    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (empty($context)) {
            return;
        }
        $DB->delete_records('email');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $emailsql = "SELECT * FROM {email}
                     WHERE userid = :userid
                     OR senderid = :senderid
                     OR " . $DB->sql_like('headers', ':email');
        $params = array('userid' => $user->id,
                        'senderid' => $user->id,
                        'email' => $user->email);
        if ($emails = $DB->get_records_sql($emailsql, $params)) {
            $DB->delete_records('email', array('id' => $email->id));
        }
    }
}
