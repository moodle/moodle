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
 * Privacy Subsystem implementation for local_intelliboard
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

if (interface_exists('\core_privacy\local\request\core_userlist_provider')) {
    interface ib_userlist_provider extends \core_privacy\local\request\core_userlist_provider{}
} else {
    interface ib_userlist_provider {};
}

/**
 * Implementation of the privacy subsystem plugin provider for the intelliboard activity module.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,

        \core_privacy\local\request\subsystem\provider,

        \core_privacy\local\request\subsystem\plugin_provider,

        ib_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $items The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        // The 'local_intelliboard_tracking' table stores the metadata about what [managers] can see in the reports.
        $items->add_database_table('local_intelliboard_assign', [
            'userid' => 'privacy:metadata:local_intelliboard_assign:userid',
            'rel' => 'privacy:metadata:local_intelliboard_assign:rel',
            'type' => 'privacy:metadata:local_intelliboard_assign:type',
            'instance' => 'privacy:metadata:local_intelliboard_assign:instance',
            'timecreated' => 'privacy:metadata:local_intelliboard_assign:timecreated',
        ], 'privacy:metadata:local_intelliboard_assign');

        // The 'local_intelliboard_details' table stores the metadata about timespent per-hour.
        $items->add_database_table('local_intelliboard_details', [
            'logid' => 'privacy:metadata:local_intelliboard_details:logid',
            'visits' => 'privacy:metadata:local_intelliboard_details:visits',
            'timespend' => 'privacy:metadata:local_intelliboard_details:timespend',
            'timepoint' => 'privacy:metadata:local_intelliboard_details:timepoint',
        ], 'privacy:metadata:local_intelliboard_details');

        // The 'local_intelliboard_logs' table stores information about timespent per-day.
        $items->add_database_table('local_intelliboard_logs', [
            'trackid' => 'privacy:metadata:local_intelliboard_logs:trackid',
            'visits' => 'privacy:metadata:local_intelliboard_logs:visits',
            'timespend' => 'privacy:metadata:local_intelliboard_logs:timespend',
            'timepoint' => 'privacy:metadata:local_intelliboard_logs:timepoint',
        ], 'privacy:metadata:local_intelliboard_logs');

        // The 'local_intelliboard_totals' table stores information about totals on a site.
        $items->add_database_table('local_intelliboard_totals', [
            'sessions' => 'privacy:metadata:local_intelliboard_totals:sessions',
            'courses' => 'privacy:metadata:local_intelliboard_totals:courses',
            'visits' => 'privacy:metadata:local_intelliboard_totals:visits',
            'timespend' => 'privacy:metadata:local_intelliboard_totals:timespend',
            'timepoint' => 'privacy:metadata:local_intelliboard_totals:timepoint',
        ], 'privacy:metadata:local_intelliboard_totals');

        // The 'local_intelliboard_reports' table stores information custom admin reports.
        $items->add_database_table('local_intelliboard_reports', [
            'status' => 'privacy:metadata:local_intelliboard_reports:status',
            'name' => 'privacy:metadata:local_intelliboard_reports:name',
            'sqlcode' => 'privacy:metadata:local_intelliboard_reports:sqlcode',
            'timecreated' => 'privacy:metadata:local_intelliboard_reports:timecreated',
        ], 'privacy:metadata:local_intelliboard_reports');

        // The 'local_intelliboard_tracking' table stores the metadata about visits and time.
        $items->add_database_table('local_intelliboard_tracking', [
            'userid' => 'privacy:metadata:local_intelliboard_tracking:userid',
            'courseid' => 'privacy:metadata:local_intelliboard_tracking:courseid',
            'page' => 'privacy:metadata:local_intelliboard_tracking:page',
            'param' => 'privacy:metadata:local_intelliboard_tracking:param',
            'visits' => 'privacy:metadata:local_intelliboard_tracking:visits',
            'timespend' => 'privacy:metadata:local_intelliboard_tracking:timespend',
            'firstaccess' => 'privacy:metadata:local_intelliboard_tracking:firstaccess',
            'lastaccess' => 'privacy:metadata:local_intelliboard_tracking:lastaccess',
            'useragent' => 'privacy:metadata:local_intelliboard_tracking:useragent',
            'useros' => 'privacy:metadata:local_intelliboard_tracking:useros',
            'userlang' => 'privacy:metadata:local_intelliboard_tracking:userlang',
            'userip' => 'privacy:metadata:local_intelliboard_tracking:userip',
        ], 'privacy:metadata:local_intelliboard_tracking');

         // The 'local_intelliboard_ntf' table stores information about notification.
        $items->add_database_table('local_intelliboard_ntf', [
            'id' => 'privacy:metadata:local_intelliboard_ntf:id',
            'type' => 'privacy:metadata:local_intelliboard_ntf:type',
            'externalid' => 'privacy:metadata:local_intelliboard_ntf:externalid',
            'userid' => 'privacy:metadata:local_intelliboard_ntf:userid',
            'email' => 'privacy:metadata:local_intelliboard_ntf:email',
            'cc' => 'privacy:metadata:local_intelliboard_ntf:cc',
            'subject' => 'privacy:metadata:local_intelliboard_ntf:subject',
            'message' => 'privacy:metadata:local_intelliboard_ntf:message',
            'state' => 'privacy:metadata:local_intelliboard_ntf:state',
            'attachment' => 'privacy:metadata:local_intelliboard_ntf:attachment',
            'tags' => 'privacy:metadata:local_intelliboard_ntf:tags',
        ], 'privacy:metadata:local_intelliboard_ntf');

         // The 'local_intelliboard_ntf_hst' table stores information about notification history.
        $items->add_database_table('local_intelliboard_ntf_hst', [
            'id' => 'privacy:metadata:local_intelliboard_ntf_hst:id',
            'notificationid' => 'privacy:metadata:local_intelliboard_ntf_hst:notificationid',
            'userid' => 'privacy:metadata:local_intelliboard_ntf_hst:userid',
            'notificationname' => 'privacy:metadata:local_intelliboard_ntf_hst:notificationname',
            'email' => 'privacy:metadata:local_intelliboard_ntf_hst:email',
            'timesent' => 'privacy:metadata:local_intelliboard_ntf_hst:timesent',
        ], 'privacy:metadata:local_intelliboard_ntf_hst');

         // The 'local_intelliboard_bbb_meet' table stores information about BigBlueButton meetings.
        $items->add_database_table('local_intelliboard_bbb_meet', [
            'id' => 'privacy:metadata:local_intelliboard_bbb_meet:id',
            'meetingname' => 'privacy:metadata:local_intelliboard_bbb_meet:meetingname',
            'meetingid' => 'privacy:metadata:local_intelliboard_bbb_meet:meetingid',
            'internalmeetingid' => 'privacy:metadata:local_intelliboard_bbb_meet:internalmeetingid',
            'createtime' => 'privacy:metadata:local_intelliboard_bbb_meet:createtime',
            'createdate' => 'privacy:metadata:local_intelliboard_bbb_meet:createdate',
            'voicebridge' => 'privacy:metadata:local_intelliboard_bbb_meet:voicebridge',
            'dialnumber' => 'privacy:metadata:local_intelliboard_bbb_meet:dialnumber',
            'attendeepw' => 'privacy:metadata:local_intelliboard_bbb_meet:attendeepw',
            'moderatorpw' => 'privacy:metadata:local_intelliboard_bbb_meet:moderatorpw',
            'running' => 'privacy:metadata:local_intelliboard_bbb_meet:running',
            'duration' => 'privacy:metadata:local_intelliboard_bbb_meet:duration',
            'hasuserjoined' => 'privacy:metadata:local_intelliboard_bbb_meet:hasuserjoined',
            'recording' => 'privacy:metadata:local_intelliboard_bbb_meet:recording',
            'hasbeenforciblyended' => 'privacy:metadata:local_intelliboard_bbb_meet:hasbeenforciblyended',
            'starttime' => 'privacy:metadata:local_intelliboard_bbb_meet:starttime',
            'endtime' => 'privacy:metadata:local_intelliboard_bbb_meet:endtime',
            'participantcount' => 'privacy:metadata:local_intelliboard_bbb_meet:participantcount',
            'listenercount' => 'privacy:metadata:local_intelliboard_bbb_meet:listenercount',
            'voiceparticipantcount' => 'privacy:metadata:local_intelliboard_bbb_meet:voiceparticipantcount',
            'videocount' => 'privacy:metadata:local_intelliboard_bbb_meet:videocount',
            'maxusers' => 'privacy:metadata:local_intelliboard_bbb_meet:maxusers',
            'moderatorcount' => 'privacy:metadata:local_intelliboard_bbb_meet:moderatorcount',
            'courseid' => 'privacy:metadata:local_intelliboard_bbb_meet:courseid',
            'cmid' => 'privacy:metadata:local_intelliboard_bbb_meet:cmid',
            'bigbluebuttonbnid' => 'privacy:metadata:local_intelliboard_bbb_meet:bigbluebuttonbnid',
            'ownerid' => 'privacy:metadata:local_intelliboard_bbb_meet:ownerid',
        ], 'privacy:metadata:local_intelliboard_bbb_meet');

        // The 'local_intelliboard_bbb_atten' table stores information about
        // attendees of BigBlueButton meetings.
        $items->add_database_table('local_intelliboard_bbb_atten', [
            'id' => 'privacy:metadata:local_intelliboard_bbb_atten:id',
            'userid' => 'privacy:metadata:local_intelliboard_bbb_atten:userid',
            'fullname' => 'privacy:metadata:local_intelliboard_bbb_atten:fullname',
            'role' => 'privacy:metadata:local_intelliboard_bbb_atten:role',
            'ispresenter' => 'privacy:metadata:local_intelliboard_bbb_atten:ispresenter',
            'islisteningonly' => 'privacy:metadata:local_intelliboard_bbb_atten:islisteningonly',
            'hasjoinedvoice' => 'privacy:metadata:local_intelliboard_bbb_atten:hasjoinedvoice',
            'hasvideo' => 'privacy:metadata:local_intelliboard_bbb_atten:hasvideo',
            'meetingid' => 'privacy:metadata:local_intelliboard_bbb_atten:meetingid',
            'localmeetingid' => 'privacy:metadata:local_intelliboard_bbb_atten:localmeetingid',
            'arrivaltime' => 'privacy:metadata:local_intelliboard_bbb_atten:arrivaltime',
            'departuretime' => 'privacy:metadata:local_intelliboard_bbb_atten:departuretime',
        ], 'privacy:metadata:local_intelliboard_bbb_atten');

        $items->add_database_table('local_intelliboard_bb_partic', [
            'id' => 'privacy:metadata:local_intelliboard_bb_partic:id',
            'sessionuid' => 'privacy:metadata:local_intelliboard_bb_partic:sessionuid',
            'useruid' => 'privacy:metadata:local_intelliboard_bb_partic:useruid',
            'external_user_id' => 'privacy:metadata:local_intelliboard_bb_partic:external_user_id',
            'role' => 'privacy:metadata:local_intelliboard_bb_partic:role',
            'display_name' => 'privacy:metadata:local_intelliboard_bb_partic:display_name',
            'first_join_time' => 'privacy:metadata:local_intelliboard_bb_partic:first_join_time',
            'last_left_time' => 'privacy:metadata:local_intelliboard_bb_partic:last_left_time',
            'duration' => 'privacy:metadata:local_intelliboard_bb_partic:duration',
            'rejoins' => 'privacy:metadata:local_intelliboard_bb_partic:rejoins',
        ], 'privacy:metadata:local_intelliboard_bb_partic');

        $items->add_database_table('local_intelliboard_bb_trck_m', [
            'id' => 'privacy:metadata:local_intelliboard_bb_trck_m:id',
            'sessionuid' => 'privacy:metadata:local_intelliboard_bb_trck_m:sessionuid',
            'track_time' => 'privacy:metadata:local_intelliboard_bb_trck_m:track_time',
        ], 'privacy:metadata:local_intelliboard_bb_trck_m');

        $items->add_database_table('local_intelliboard_att_sync', [
            'id' => 'privacy:metadata:local_intelliboard_att_sync:id',
            'type' => 'privacy:metadata:local_intelliboard_att_sync:type',
            'instance' => 'privacy:metadata:local_intelliboard_att_sync:instance',
            'data' => 'privacy:metadata:local_intelliboard_att_sync:data',
        ], 'privacy:metadata:local_intelliboard_att_sync');

        // The 'local_intelliboard_trns_c' table stores the metadata about course actions.
        $items->add_database_table('local_intelliboard_trns_c', [
            'userid' => 'privacy:metadata:local_intelliboard_trns_c:userid',
            'useremail' => 'privacy:metadata:local_intelliboard_trns_c:useremail',
            'firstname' => 'privacy:metadata:local_intelliboard_trns_c:firstname',
            'lastname' => 'privacy:metadata:local_intelliboard_trns_c:lastname',
            'userenrolid' => 'privacy:metadata:local_intelliboard_trns_c:userenrolid',
            'enrolid' => 'privacy:metadata:local_intelliboard_trns_c:enrolid',
            'enroltype' => 'privacy:metadata:local_intelliboard_trns_c:enroltype',
            'courseid' => 'privacy:metadata:local_intelliboard_trns_c:courseid',
            'coursename' => 'privacy:metadata:local_intelliboard_trns_c:coursename',
            'enroldate' => 'privacy:metadata:local_intelliboard_trns_c:enroldate',
            'unenroldate' => 'privacy:metadata:local_intelliboard_trns_c:unenroldate',
            'completeddate' => 'privacy:metadata:local_intelliboard_trns_c:completeddate',
            'status' => 'privacy:metadata:local_intelliboard_trns_c:status',
            'gradeitemid' => 'privacy:metadata:local_intelliboard_trns_c:gradeitemid',
            'gradeid' => 'privacy:metadata:local_intelliboard_trns_c:gradeid',
            'grademax' => 'privacy:metadata:local_intelliboard_trns_c:grademax',
            'grademin' => 'privacy:metadata:local_intelliboard_trns_c:grademin',
            'finalgrade' => 'privacy:metadata:local_intelliboard_trns_c:finalgrade',
            'formattedgrade' => 'privacy:metadata:local_intelliboard_trns_c:formattedgrade',
            'rolesids' => 'privacy:metadata:local_intelliboard_trns_c:rolesids',
            'groupsids' => 'privacy:metadata:local_intelliboard_trns_c:groupsids',
            'timecreated' => 'privacy:metadata:local_intelliboard_trns_c:timecreated',
            'timemodified' => 'privacy:metadata:local_intelliboard_trns_c:timemodified',
        ], 'privacy:metadata:local_intelliboard_trns_c');

        // The 'local_intelliboard_trns_c' table stores the metadata about course actions.
        $items->add_database_table('local_intelliboard_trns_m', [
            'userenrolid' => 'privacy:metadata:local_intelliboard_trns_m:userenrolid',
            'courseid' => 'privacy:metadata:local_intelliboard_trns_m:courseid',
            'userid' => 'privacy:metadata:local_intelliboard_trns_m:userid',
            'cmid' => 'privacy:metadata:local_intelliboard_trns_m:cmid',
            'moduleid' => 'privacy:metadata:local_intelliboard_trns_m:moduleid',
            'modulename' => 'privacy:metadata:local_intelliboard_trns_m:modulename',
            'moduletype' => 'privacy:metadata:local_intelliboard_trns_m:moduletype',
            'startdate' => 'privacy:metadata:local_intelliboard_trns_m:startdate',
            'completeddate' => 'privacy:metadata:local_intelliboard_trns_m:completeddate',
            'status' => 'privacy:metadata:local_intelliboard_trns_m:status',
            'gradeitemid' => 'privacy:metadata:local_intelliboard_trns_m:gradeitemid',
            'gradeid' => 'privacy:metadata:local_intelliboard_trns_m:gradeid',
            'grademax' => 'privacy:metadata:local_intelliboard_trns_m:grademax',
            'grademin' => 'privacy:metadata:local_intelliboard_trns_m:grademin',
            'finalgrade' => 'privacy:metadata:local_intelliboard_trns_m:finalgrade',
            'formattedgrade' => 'privacy:metadata:local_intelliboard_trns_m:formattedgrade',
            'timecreated' => 'privacy:metadata:local_intelliboard_trns_m:timecreated',
            'timemodified' => 'privacy:metadata:local_intelliboard_trns_m:timemodified',
        ], 'privacy:metadata:local_intelliboard_trns_m');

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * In the case of intelliboard, that is any intelliboard where the user has made any post, rated any content, or has any preferences.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        return new contextlist();
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;


        $user = $contextlist->get_user();

        $records = $DB->get_records_sql("SELECT (CASE
                WHEN d.id > 0 THEN d.id*l.id*t.id
                WHEN l.id > 0 THEN l.id*t.id*t.id
                    ELSE t.id
            END) AS unid, t.*,
            l.timepoint AS day_time,
            l.visits AS day_visits,
            l.timespend AS day_timespent,
            l.timepoint AS hour_time,
            d.visits AS hour_visits,
            d.timespend AS hour_timespent
            FROM {local_intelliboard_tracking} t
            LEFT JOIN {local_intelliboard_logs} l ON l.trackid = t.id
            LEFT JOIN {local_intelliboard_details} d ON d.logid = l.id
            WHERE t.userid = :userid", ['userid' => $user->id]);

        if (!empty($records)) {
            \core_privacy\local\request\writer::with_context($context)
                    ->export_data([], (object) [
                        'records' => $records,
                    ]);
        }

        $records = $DB->get_records_sql("(
            SELECT id,rel,type,timecreated FROM {local_intelliboard_assign} WHERE userid = :userid)
            UNION
            (SELECT id, rel,type,timecreated FROM {local_intelliboard_assign} WHERE type = 'users' AND instance = :instance)", ['userid' => $user->id, 'instance' => $user->id]);

        if (!empty($records)) {
            \core_privacy\local\request\writer::with_context($context)
                    ->export_data([], (object) [
                        'records' => $records,
                    ]);
        }
    }


    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_COURSE) {
          $params = [
            'courseid' => $context->instanceid
          ];
          $items = $DB->get_records("local_intelliboard_tracking", $params);

          foreach ($items as $item) {
              $logs = $DB->get_records("local_intelliboard_logs", ['trackid' => $item->id]);

              foreach ($logs as $log) {
                  $DB->delete_records('local_intelliboard_details', [
                      'logid' => $log->id,
                  ]);
              }
              $DB->delete_records('local_intelliboard_logs', [
                  'trackid' => $item->id,
              ]);
          }
          $DB->delete_records('local_intelliboard_tracking', $params);
          $DB->delete_records('local_intelliboard_trns_c', $params);
          $DB->delete_records('local_intelliboard_trns_m', $params);
        } elseif ($context->contextlevel == CONTEXT_MODULE) {
          $params = [
            'page' => 'module',
            'param' => $context->instanceid
          ];

          $items = $DB->get_records("local_intelliboard_tracking", $params);

          foreach ($items as $item) {
              $logs = $DB->get_records("local_intelliboard_logs", ['trackid' => $item->id]);

              foreach ($logs as $log) {
                  $DB->delete_records('local_intelliboard_details', [
                      'logid' => $log->id,
                  ]);
              }
              $DB->delete_records('local_intelliboard_logs', [
                  'trackid' => $item->id,
              ]);
          }
          $DB->delete_records('local_intelliboard_tracking', $params);
        }
        return;
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();
        $userid = $user->id;

        $DB->delete_records('local_intelliboard_assign', [
            'userid' => $userid,
        ]);
        $DB->delete_records('local_intelliboard_assign', [
            'type' => 'users',
            'instance' => $userid,
        ]);
        $DB->delete_records('local_intelliboard_bb_partic', [
            'external_user_id' => $userid,
        ]);

        $items = $DB->get_records("local_intelliboard_tracking", ['userid' => $userid]);

        foreach ($items as $item) {
            $logs = $DB->get_records("local_intelliboard_logs", ['trackid' => $item->id]);

            foreach ($logs as $log) {
                $DB->delete_records('local_intelliboard_details', [
                    'logid' => $log->id,
                ]);
            }
            $DB->delete_records('local_intelliboard_logs', [
                'trackid' => $item->id,
            ]);
        }
        $DB->delete_records('local_intelliboard_tracking', [
            'userid' => $userid,
        ]);
        $DB->delete_records('local_intelliboard_trns_c', [
            'userid' => $userid,
        ]);
        $DB->delete_records('local_intelliboard_trns_m', [
            'userid' => $userid,
        ]);
    }


      /**
      * Get the list of users who have data within a context.
      *
      * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
      */
      public static function get_users_in_context(userlist $userlist)
      {
        $context = $userlist->get_context();
        if ($context->contextlevel == CONTEXT_COURSE) {
          $params = [
            'courseid' => $context->instanceid
          ];
          $sql = "SELECT userid FROM {local_intelliboard_tracking} WHERE courseid = :courseid";
          $userlist->add_from_sql('userid', $sql, $params);

        } elseif ($context->contextlevel == CONTEXT_MODULE) {
          $params = [
            'cmid' => $context->instanceid
          ];
          $sql = "SELECT userid FROM {local_intelliboard_tracking} WHERE page = 'module' AND param = :cmid";
          $userlist->add_from_sql('userid', $sql, $params);
        }
        return;
    }
    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $users = $userlist->get_userids();

        foreach ($users as  $userid) {

          $DB->delete_records('local_intelliboard_assign', [
              'userid' => $userid,
          ]);
          $DB->delete_records('local_intelliboard_assign', [
              'type' => 'users',
              'instance' => $userid,
          ]);
          $DB->delete_records('local_intelliboard_bb_partic', [
              'external_user_id' => $userid,
          ]);
          $items = $DB->get_records("local_intelliboard_tracking", ['userid' => $userid]);

          foreach ($items as $item) {
              $logs = $DB->get_records("local_intelliboard_logs", ['trackid' => $item->id]);

              foreach ($logs as $log) {
                  $DB->delete_records('local_intelliboard_details', [
                      'logid' => $log->id,
                  ]);
              }
              $DB->delete_records('local_intelliboard_logs', [
                  'trackid' => $item->id,
              ]);
          }
          $DB->delete_records('local_intelliboard_tracking', [
              'userid' => $userid,
          ]);
          $DB->delete_records('local_intelliboard_trns_c', [
              'userid' => $userid,
          ]);
          $DB->delete_records('local_intelliboard_trns_m', [
              'userid' => $userid,
          ]);
        }
    }
}
