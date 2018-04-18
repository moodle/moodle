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
 * Privacy Subsystem implementation for tool_policy.
 *
 * @package    tool_policy
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\moodle_content_writer;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the policy tool.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // This tool stores user data.
        \core_privacy\local\metadata\provider,

        // This tool may provide access to and deletion of user data.
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items A reference to the collection to use to store the metadata.
     * @return collection The updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_database_table(
            'tool_policy_acceptances',
            [
                'policyversionid' => 'privacy:metadata:acceptances:policyversionid',
                'userid' => 'privacy:metadata:acceptances:userid',
                'status' => 'privacy:metadata:acceptances:status',
                'lang' => 'privacy:metadata:acceptances:lang',
                'usermodified' => 'privacy:metadata:acceptances:usermodified',
                'timecreated' => 'privacy:metadata:acceptances:timecreated',
                'timemodified' => 'privacy:metadata:acceptances:timemodified',
                'note' => 'privacy:metadata:acceptances:note',
            ],
            'privacy:metadata:acceptances'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The userid.
     * @return contextlist The list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql('SELECT DISTINCT c.id
            FROM {tool_policy_acceptances} a
            JOIN {context} c ON a.userid = c.instanceid AND c.contextlevel = ?
            WHERE a.userid = ? OR a.usermodified = ?',
            [CONTEXT_USER, $userid, $userid]);
        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist A list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_USER) {
                continue;
            }
            $user = $contextlist->get_user();
            $agreements = $DB->get_records_sql('SELECT a.id, a.userid, v.name, v.revision, a.usermodified, a.timecreated,
                  a.timemodified, a.note, v.archived, p.currentversionid, a.status, a.policyversionid
                FROM {tool_policy_acceptances} a
                JOIN {tool_policy_versions} v ON v.id=a.policyversionid
                JOIN {tool_policy} p ON v.policyid = p.id
                WHERE a.userid = ? AND (a.userid = ? OR a.usermodified = ?)
                ORDER BY a.userid, v.archived, v.timecreated DESC',
                [$context->instanceid, $user->id, $user->id]);
            foreach ($agreements as $agreement) {
                $context = \context_user::instance($agreement->userid);
                $subcontext = [
                    get_string('userpoliciesagreements', 'tool_policy'),
                    transform::user($agreement->userid)
                ];
                $name = 'policyagreement-' . $agreement->policyversionid;
                $agreementcontent = (object) [
                    'userid' => transform::user($agreement->userid),
                    'status' => $agreement->status,
                    'versionid' => $agreement->policyversionid,
                    'name' => $agreement->name,
                    'revision' => $agreement->revision,
                    'isactive' => transform::yesno($agreement->policyversionid == $agreement->currentversionid),
                    'usermodified' => transform::user($agreement->usermodified),
                    'timecreated' => transform::datetime($agreement->timecreated),
                    'timemodified' => transform::datetime($agreement->timemodified),
                    'note' => $agreement->note,
                ];
                writer::with_context($context)->export_related_data($subcontext, $name, $agreementcontent);
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * We never delete user agreements to the policies because they are part of privacy data.
     *
     * @param \context $context The context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * We never delete user agreements to the policies because they are part of privacy data.
     *
     * @param approved_contextlist $contextlist A list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }
}
