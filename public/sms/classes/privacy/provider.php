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

namespace core_sms\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;

/**
 * Class provider
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider {
    #[\Override]
    public static function get_metadata(
        collection $collection,
    ): collection {
        $collection->add_database_table(
            'sms_messages',
            [
                'id' => 'privacy:metadata:sms_messages:id',
                'recipient' => 'privacy:metadata:sms_messages:recipient',
                'recipientuserid' => 'privacy:metadata:sms_messages:recipientuserid',
                'content' => 'privacy:metadata:sms_messages:content',
                'status' => 'privacy:metadata:sms_messages:status',
                'timecreated' => 'privacy:metadata:sms_messages:timecreated',

            ],
            'privacy:metadata:sms_messages'
        );
        return $collection;
    }

    #[\Override]
    public static function get_contexts_for_userid(
        int $userid,
    ): \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->add_from_sql(
            <<<EOF
                SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {sms_messages} m
                    ON m.recipientuserid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                 WHERE m.recipientuserid = :userid
            EOF,
            [
               'userid' => $userid,
               'contextlevel' => CONTEXT_USER,
            ]
        );

        return $contextlist;
    }

    #[\Override]
    public static function export_user_data(
        \core_privacy\local\request\approved_contextlist $contextlist,
    ) {
        global $DB;

        foreach ($contextlist as $context) {
            // All data is against the recipientuserid and stored in a user context.
            if (!$context instanceof \core\context\user) {
                return;
            }
            $messages = array_map(
                function ($data) {
                    return [
                        'recipient' => $data->recipientnumber,
                        'content' => $data->issensitive ?
                            get_string('privacy:sms:sensitive_not_shown', 'core_sms') : $data->content,
                        'messagetype' => $data->messagetype,
                        'status' => $data->status,
                        'timecreated' => $data->timecreated,
                    ];
                },
                $DB->get_records(
                    table: 'sms_messages',
                    conditions: [
                        'recipientuserid' => $context->instanceid,
                    ],
                ),
            );

            if (!empty($messages)) {
                \core_privacy\local\request\writer::with_context($context)->export_data(
                    [
                        get_string('sms', 'core_sms'),
                    ],
                    (object) [
                        'messages' => $messages,
                    ],
                );
            }
        }
    }

    #[\Override]
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \core\context\user) {
            return;
        }
        $DB->delete_records('sms_messages', ['recipientuserid' => $context->instanceid]);
    }

    #[\Override]
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
        foreach ($contextlist as $context) {
            self::delete_data_for_all_users_in_context($context);
        }
    }

    #[\Override]
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ($context instanceof \core\context\user) {
            $userlist->add_user($context->instanceid);
        }
    }

    #[\Override]
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \core\context\user) {
            return;
        }

        self::delete_data_for_all_users_in_context($context);
    }
}
