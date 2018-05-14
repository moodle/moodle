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
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
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

        $collection->add_database_table(
            'tool_policy_versions',
            [
                'name' => 'privacy:metadata:versions:name',
                'type' => 'privacy:metadata:versions:type',
                'audience' => 'privacy:metadata:versions:audience',
                'archived' => 'privacy:metadata:versions:archived',
                'usermodified' => 'privacy:metadata:versions:usermodified',
                'timecreated' => 'privacy:metadata:versions:timecreated',
                'timemodified' => 'privacy:metadata:versions:timemodified',
                'policyid' => 'privacy:metadata:versions:policyid',
                'revision' => 'privacy:metadata:versions:revision',
                'summary' => 'privacy:metadata:versions:summary',
                'summaryformat' => 'privacy:metadata:versions:summaryformat',
                'content' => 'privacy:metadata:versions:content',
                'contentformat' => 'privacy:metadata:versions:contentformat',
            ],
            'privacy:metadata:versions'
        );

        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:subsystem:corefiles');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The userid.
     * @return contextlist The list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT c.id
                  FROM {context} c
             LEFT JOIN {tool_policy_versions} v ON v.usermodified = c.instanceid
             LEFT JOIN {tool_policy_acceptances} a ON a.userid = c.instanceid
                 WHERE c.contextlevel = :contextlevel
                   AND (v.usermodified = :usermodified OR a.userid = :userid OR a.usermodified = :behalfuserid)";
        $params = [
            'contextlevel' => CONTEXT_USER,
            'usermodified' => $userid,
            'userid'       => $userid,
            'behalfuserid' => $userid,
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist A list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from USER.
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_USER) {
                $carry[$context->instanceid] = $context;
            }
            return $carry;
        }, []);

        if (empty($contexts)) {
            return;
        }

        // Export user agreements.
        $subcontext = [
            get_string('privacyandpolicies', 'admin'),
            get_string('useracceptances', 'tool_policy')
        ];
        $policyversionids = [];
        foreach ($contexts as $context) {
            $user = $contextlist->get_user();
            $agreements = $DB->get_records_sql('SELECT a.id, a.userid, v.name, v.revision, a.usermodified, a.timecreated,
                  a.timemodified, a.note, v.archived, p.currentversionid, a.status, a.policyversionid
                FROM {tool_policy_acceptances} a
                JOIN {tool_policy_versions} v ON v.id = a.policyversionid
                JOIN {tool_policy} p ON v.policyid = p.id
                WHERE a.userid = ? AND (a.userid = ? OR a.usermodified = ?)
                ORDER BY a.userid, v.archived, v.timecreated DESC',
                [$context->instanceid, $user->id, $user->id]);
            foreach ($agreements as $agreement) {
                $context = \context_user::instance($agreement->userid);
                $name = 'policyagreement-' . $agreement->policyversionid;
                $agreementcontent = (object) [
                    'name' => $agreement->name,
                    'revision' => $agreement->revision,
                    'isactive' => transform::yesno($agreement->policyversionid == $agreement->currentversionid),
                    'isagreed' => transform::yesno($agreement->status),
                    'agreedby' => transform::user($agreement->usermodified),
                    'timecreated' => transform::datetime($agreement->timecreated),
                    'timemodified' => transform::datetime($agreement->timemodified),
                    'note' => $agreement->note,
                ];
                writer::with_context($context)->export_related_data($subcontext, $name, $agreementcontent);
                $policyversionids[$agreement->policyversionid] = $agreement->policyversionid;
            }
        }

        // Export policy versions (agreed or modified by the user).
        $userid = $contextlist->get_user()->id;
        $context = \context_system::instance();
        $subcontext = [
            get_string('policydocuments', 'tool_policy')
        ];
        $writer = writer::with_context($context);
        list($contextsql, $contextparams) = $DB->get_in_or_equal(array_keys($contexts), SQL_PARAMS_NAMED);
        list($versionsql, $versionparams) = $DB->get_in_or_equal($policyversionids, SQL_PARAMS_NAMED);
        $sql = "SELECT v.id,
                       v.name,
                       v.revision,
                       v.summary,
                       v.content,
                       v.archived,
                       v.usermodified,
                       v.timecreated,
                       v.timemodified,
                       p.currentversionid
                  FROM {tool_policy_versions} v
                  JOIN {tool_policy} p ON p.id = v.policyid
                 WHERE v.usermodified {$contextsql} OR v.id {$versionsql}";
        $params = array_merge($contextparams, $versionparams);
        $versions = $DB->get_recordset_sql($sql, $params);
        foreach ($versions as $version) {
            $name = 'policyversion-' . $version->id;
            $versioncontent = (object) [
                'name' => $version->name,
                'revision' => $version->revision,
                'summary' => $writer->rewrite_pluginfile_urls(
                    $subcontext,
                    'tool_policy',
                    'policydocumentsummary',
                    $version->id,
                    $version->summary
                ),
                'content' => $writer->rewrite_pluginfile_urls(
                    $subcontext,
                    'tool_policy',
                    'policydocumentcontent',
                    $version->id,
                    $version->content
                ),
                'isactive' => transform::yesno($version->id == $version->currentversionid),
                'isarchived' => transform::yesno($version->archived),
                'createdbyme' => transform::yesno($version->usermodified == $userid),
                'timecreated' => transform::datetime($version->timecreated),
                'timemodified' => transform::datetime($version->timemodified),
            ];
            $writer->export_related_data($subcontext, $name, $versioncontent);
            $writer->export_area_files($subcontext, 'tool_policy', 'policydocumentsummary', $version->id);
            $writer->export_area_files($subcontext, 'tool_policy', 'policydocumentcontent', $version->id);
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
