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
 * Contains class mod_zoom\privacy\provider
 *
 * @package    mod_zoom
 * @copyright  2018 UC Regents
 * @author     Kubilay Agi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_zoom\privacy;

defined('MOODLE_INTERNAL') || die();


/**
 * Ad hoc task that performs the actions for approved data privacy requests.
 *
 * @package   mod_zoom
 * @copyright 2018 UC Regents
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    \core_privacy\local\request\core_userlist_provider,

    // This plugin currently implements the original plugin_provider interface.
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $coll The collection to add metadata to.
     * @return  collection  The array of metadata
     */
    public static function get_metadata(\core_privacy\local\metadata\collection $coll): \core_privacy\local\metadata\collection {
        // Add all user data fields to the collection.

        $coll->add_database_table('zoom_meeting_participants', [
            'name' => 'privacy:metadata:zoom_meeting_participants:name',
            'user_email' => 'privacy:metadata:zoom_meeting_participants:user_email',
            'join_time' => 'privacy:metadata:zoom_meeting_participants:join_time',
            'leave_time' => 'privacy:metadata:zoom_meeting_participants:leave_time',
            'duration' => 'privacy:metadata:zoom_meeting_participants:duration'
        ], 'privacy:metadata:zoom_meeting_participants');

        $coll->add_database_table('zoom_meeting_details',
                                        ['topic' => 'privacy:metadata:zoom_meeting_details:topic'],
                                        'privacy:metadata:zoom_meeting_details');
        return $coll;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        // Query the database for context IDs give a specific user ID and return these to the user.

        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = 'SELECT c.id
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {zoom} z ON z.id = cm.instance
            INNER JOIN {zoom_meeting_details} zmd ON zmd.zoomid = z.id
             LEFT JOIN {zoom_meeting_participants} zmp ON zmp.detailsid = zmd.id
                 WHERE zmp.userid = :uclauserid
        ';

        $params = [
            'modname' => 'zoom',
            'contextlevel' => CONTEXT_MODULE,
            'uclauserid' => $userid
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $context = $userlist->get_context();

        if (!($context instanceof \context_module)) {
            return;
        }

        $sql = "SELECT zmp.userid
                  FROM {zoom_meeting_participants} zmp
                  JOIN {zoom_meeting_details} zmd ON zmd.id = zmp.detailsid
                  JOIN {zoom} z ON zmd.zoomid = z.id
                  JOIN {modules} m ON m.name = 'zoom'
                  JOIN {course_modules} cm ON cm.id = z.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modlevel
                  WHERE ctx.id = :contextid";

        $params = ['modlevel' => CONTEXT_MODULE, 'contextid' => $context->id];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     * @link http://tandl.churchward.ca/2018/06/implementing-moodles-privacy-api-in.html
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT zmp.id,
                       zmd.topic,
                       zmp.name,
                       zmp.user_email,
                       zmp.join_time,
                       zmp.leave_time,
                       zmp.duration,
                       cm.id AS cmid
                  FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {zoom} z ON z.id = cm.instance
            INNER JOIN {zoom_meeting_details} zmd ON zmd.zoomid = z.id
            INNER JOIN {zoom_meeting_participants} zmp ON zmp.detailsid = zmd.id
                 WHERE c.id $contextsql
                       AND zmp.userid = :userid
              ORDER BY cm.id ASC
        ";

        $params = [
            'modname' => 'zoom',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $user->id
        ] + $contextparams;

        $participantinstances = $DB->get_recordset_sql($sql, $params);
        foreach ($participantinstances as $participantinstance) {
            $context = \context_module::instance($participantinstance->cmid);
            $contextdata = \core_privacy\local\request\helper::get_context_data($context, $user);

            $instancedata = [
                'topic' => $participantinstance->topic,
                'name' => $participantinstance->name,
                'user_email' => $participantinstance->user_email,
                'join_time' => \core_privacy\local\request\transform::datetime($participantinstance->join_time),
                'leave_time' => \core_privacy\local\request\transform::datetime($participantinstance->leave_time),
                'duration' => $participantinstance->duration
            ];

            $contextdata = (object) array_merge((array) $contextdata, $instancedata);
            \core_privacy\local\request\writer::with_context($context)->export_data(array(), $contextdata);
        }

        $participantinstances->close();
    }

    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!($context instanceof \context_module)) {
            return;
        }

        // We delete each participant entry manually because deletes do not cascade.
        if ($cm = get_coursemodule_from_id('zoom', $context->instanceid)) {
            $meetingdetails = $DB->get_records('zoom_meeting_details', array('zoomid' => $cm->instance));
            foreach ($meetingdetails as $meetingdetail) {
                $DB->delete_records('zoom_meeting_participants', array('detailsid' => $meetingdetail->id));
            }
            $DB->delete_records('zoom_meeting_details', array('zoomid' => $cm->instance));
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if (!($context instanceof \context_module)) {
                continue;
            }
            if ($cm = get_coursemodule_from_id('zoom', $context->instanceid)) {
                $meetingdetails = $DB->get_records('zoom_meeting_details', array('zoomid' => $cm->instance));
                foreach ($meetingdetails as $meetingdetail) {
                    $DB->delete_records('zoom_meeting_participants',
                            array('detailsid' => $meetingdetail->id, 'userid' => $user->id));
                }
            }
        }
    }


    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();

        if (!($context instanceof \context_module)) {
            return;
        }

        // Prepare SQL to gather all completed IDs.
        $userids = $userlist->get_userids();
        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $sql = "SELECT zmp.id
                  FROM {zoom_meeting_participants} zmp
                  JOIN {zoom_meeting_details} zmd ON zmd.id = zmp.detailsid
                  JOIN {zoom} z ON zmd.zoomid = z.id
                  JOIN {modules} m ON m.name = 'zoom'
                  JOIN {course_modules} cm ON cm.id = z.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modlevel
                  WHERE ctx.id = :contextid
                    AND zmp.userid $insql";

        $params = array_merge($inparams, ['contextid' => $context->id]);

        $DB->delete_records_select('zoom_meeting_participants', "id $sql", $params);
    }
}
