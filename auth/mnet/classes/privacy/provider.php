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
 * Privacy Subsystem implementation for auth_mnet.
 *
 * @package    auth_mnet
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mnet\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy provider for the mnet authentication
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {

        $sessionfields = [
                'userid' => 'privacy:metadata:mnet_session:userid',
                'username' => 'privacy:metadata:mnet_session:username',
                'token' => 'privacy:metadata:mnet_session:token',
                'mnethostid' => 'privacy:metadata:mnet_session:mnethostid',
                'useragent' => 'privacy:metadata:mnet_session:useragent',
                'expires' => 'privacy:metadata:mnet_session:expires'
        ];

        $collection->add_database_table('mnet_session', $sessionfields, 'privacy:metadata:mnet_session');

        $logfields = [
                'hostid' => 'privacy:metadata:mnet_log:hostid',
                'remoteid' => 'privacy:metadata:mnet_log:remoteid',
                'time' => 'privacy:metadata:mnet_log:time',
                'userid' => 'privacy:metadata:mnet_log:userid',
                'ip' => 'privacy:metadata:mnet_log:ip',
                'course' => 'privacy:metadata:mnet_log:course',
                'coursename' => 'privacy:metadata:mnet_log:coursename',
                'module' => 'privacy:metadata:mnet_log:module',
                'cmid' => 'privacy:metadata:mnet_log:cmid',
                'action' => 'privacy:metadata:mnet_log:action',
                'url' => 'privacy:metadata:mnet_log:url',
                'info' => 'privacy:metadata:mnet_log:info'
        ];

        $collection->add_database_table('mnet_log', $logfields, 'privacy:metadata:mnet_log');

        $externalfields = [
                'address' => 'privacy:metadata:mnet_external:address',
                'aim' => 'privacy:metadata:mnet_external:aim',
                'alternatename' => 'privacy:metadata:mnet_external:alternatename',
                'autosubscribe' => 'privacy:metadata:mnet_external:autosubscribe',
                'calendartype' => 'privacy:metadata:mnet_external:calendartype',
                'city' => 'privacy:metadata:mnet_external:city',
                'country' => 'privacy:metadata:mnet_external:country',
                'currentlogin' => 'privacy:metadata:mnet_external:currentlogin',
                'department' => 'privacy:metadata:mnet_external:department',
                'description' => 'privacy:metadata:mnet_external:description',
                'email' => 'privacy:metadata:mnet_external:email',
                'emailstop' => 'privacy:metadata:mnet_external:emailstop',
                'firstaccess' => 'privacy:metadata:mnet_external:firstaccess',
                'firstname' => 'privacy:metadata:mnet_external:firstname',
                'firstnamephonetic' => 'privacy:metadata:mnet_external:firstnamephonetic',
                'icq' => 'privacy:metadata:mnet_external:icq',
                'id' => 'privacy:metadata:mnet_external:id',
                'idnumber' => 'privacy:metadata:mnet_external:idnumber',
                'imagealt' => 'privacy:metadata:mnet_external:imagealt',
                'institution' => 'privacy:metadata:mnet_external:institution',
                'lang' => 'privacy:metadata:mnet_external:lang',
                'lastaccess' => 'privacy:metadata:mnet_external:lastaccess',
                'lastlogin' => 'privacy:metadata:mnet_external:lastlogin',
                'lastname' => 'privacy:metadata:mnet_external:lastname',
                'lastnamephonetic' => 'privacy:metadata:mnet_external:lastnamephonetic',
                'maildigest' => 'privacy:metadata:mnet_external:maildigest',
                'maildisplay' => 'privacy:metadata:mnet_external:maildisplay',
                'middlename' => 'privacy:metadata:mnet_external:middlename',
                'msn' => 'privacy:metadata:mnet_external:msn',
                'phone1' => 'privacy:metadata:mnet_external:phone1',
                'pnone2' => 'privacy:metadata:mnet_external:phone2',
                'picture' => 'privacy:metadata:mnet_external:picture',
                'policyagreed' => 'privacy:metadata:mnet_external:policyagreed',
                'skype' => 'privacy:metadata:mnet_external:skype',
                'suspended' => 'privacy:metadata:mnet_external:suspended',
                'timezone' => 'privacy:metadata:mnet_external:timezone',
                'trackforums' => 'privacy:metadata:mnet_external:trackforums',
                'trustbitmask' => 'privacy:metadata:mnet_external:trustbitmask',
                'url' => 'privacy:metadata:mnet_external:url',
                'username' => 'privacy:metadata:mnet_external:username',
                'yahoo' => 'privacy:metadata:mnet_external:yahoo',
        ];

        $collection->add_external_location_link('moodle', $externalfields, 'privacy:metadata:external:moodle');

        $collection->add_external_location_link('mahara', $externalfields, 'privacy:metadata:external:mahara');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {mnet_log} ml
                  JOIN {context} ctx ON ctx.instanceid = ml.userid AND ctx.contextlevel = :contextlevel
                 WHERE ml.userid = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $params = [
            'contextuser' => CONTEXT_USER,
            'contextid' => $context->id
        ];

        $sql = "SELECT ctx.instanceid as userid
                  FROM {mnet_log} ml
                  JOIN {context} ctx
                       ON ctx.instanceid = ml.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $context = \context_user::instance($contextlist->get_user()->id);

        $sql = "SELECT ml.id, mh.wwwroot, mh.name, ml.remoteid, ml.time, ml.userid, ml.ip, ml.course,
                       ml.coursename, ml.module, ml.cmid, ml.action, ml.url, ml.info
                  FROM {mnet_log} ml
                  JOIN {mnet_host} mh ON mh.id = ml.hostid
                 WHERE ml.userid = :userid
              ORDER BY mh.name, ml.coursename";
        $params = ['userid' => $contextlist->get_user()->id];

        $data = [];
        $lastcourseid = null;

        $logentries = $DB->get_recordset_sql($sql, $params);
        foreach ($logentries as $logentry) {
            $item = (object) [
                    'time' => transform::datetime($logentry->time),
                    'remoteid' => $logentry->remoteid,
                    'ip' => $logentry->ip,
                    'course' => $logentry->course,
                    'coursename' => format_string($logentry->coursename),
                    'module' => $logentry->module,
                    'cmid' => $logentry->cmid,
                    'action' => $logentry->action,
                    'url' => $logentry->url,
                    'info' => format_string($logentry->info)
            ];

            $item->externalhost =
                    ($logentry->name == '') ? preg_replace('#^https?://#', '', $logentry->wwwroot) :
                            preg_replace('#^https?://#', '', $logentry->name);

            if ($lastcourseid && $lastcourseid != $logentry->course) {
                $path = [get_string('pluginname', 'auth_mnet'), $data[0]->externalhost, $data[0]->coursename];
                writer::with_context($context)->export_data($path, (object) $data);
                $data = [];
            }

            $data[] = $item;
            $lastcourseid = $logentry->course;
        }
        $logentries->close();

        $path = [get_string('pluginname', 'auth_mnet'), $item->externalhost, $item->coursename];
        writer::with_context($context)->export_data($path, (object) $data);
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }

        $DB->delete_records('mnet_log', ['userid' => $context->instanceid]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            $DB->delete_records('mnet_log', ['userid' => $context->instanceid]);
        }
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
            if ($context->contextlevel != CONTEXT_USER) {
                continue;
            }
            if ($context->instanceid == $userid) {
                // Because we only use user contexts the instance ID is the user ID.
                $DB->delete_records('mnet_log', ['userid' => $context->instanceid]);
            }
        }
    }
}
