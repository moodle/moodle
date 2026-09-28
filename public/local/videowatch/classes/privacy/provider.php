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

namespace local_videowatch\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for watched-video progress.
 *
 * @package    local_videowatch
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * @param collection $collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('local_videowatch', [
            'userid' => 'privacy:metadata:local_videowatch:userid',
            'cmid' => 'privacy:metadata:local_videowatch:cmid',
            'frontier' => 'privacy:metadata:local_videowatch:frontier',
            'duration' => 'privacy:metadata:local_videowatch:duration',
        ], 'privacy:metadata:local_videowatch');
        return $collection;
    }

    /**
     * @param int $userid
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $list = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {local_videowatch} w ON w.cmid = ctx.instanceid
                 WHERE ctx.contextlevel = :level AND w.userid = :userid";
        $list->add_from_sql($sql, ['level' => CONTEXT_MODULE, 'userid' => $userid]);
        return $list;
    }

    /**
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }
            $row = $DB->get_record('local_videowatch', ['userid' => $userid, 'cmid' => $context->instanceid]);
            if (!$row) {
                continue;
            }
            writer::with_context($context)->export_data(
                [get_string('pluginname', 'local_videowatch')],
                (object) [
                    'watchedseconds' => ((int) $row->frontier) / 1000,
                    'durationseconds' => ((int) $row->duration) / 1000,
                ]
            );
        }
    }

    /**
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel == CONTEXT_MODULE) {
            $DB->delete_records('local_videowatch', ['cmid' => $context->instanceid]);
        }
    }

    /**
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $DB->delete_records('local_videowatch', [
                    'userid' => $userid,
                    'cmid' => $context->instanceid,
                ]);
            }
        }
    }

    /**
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }
        $sql = "SELECT userid FROM {local_videowatch} WHERE cmid = :cmid";
        $userlist->add_from_sql('userid', $sql, ['cmid' => $context->instanceid]);
    }

    /**
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }
        $userids = $userlist->get_userids();
        if (!$userids) {
            return;
        }
        list($insql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['cmid'] = $context->instanceid;
        $DB->delete_records_select('local_videowatch', "cmid = :cmid AND userid {$insql}", $params);
    }
}
