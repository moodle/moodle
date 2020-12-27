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
 * Privacy Subsystem implementation for enrol_flatfile.
 *
 * @package    enrol_flatfile
 * @category   privacy
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_flatfile\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for enrol_flatfile implementing null_provider.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        return $collection->add_database_table('enrol_flatfile', [
            'action' => 'privacy:metadata:enrol_flatfile:action',
            'roleid' => 'privacy:metadata:enrol_flatfile:roleid',
            'userid' => 'privacy:metadata:enrol_flatfile:userid',
            'courseid' => 'privacy:metadata:enrol_flatfile:courseid',
            'timestart' => 'privacy:metadata:enrol_flatfile:timestart',
            'timeend' => 'privacy:metadata:enrol_flatfile:timeend',
            'timemodified' => 'privacy:metadata:enrol_flatfile:timemodified'
        ], 'privacy:metadata:enrol_flatfile');
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT c.id
                  FROM {enrol_flatfile} ef
                  JOIN {context} c ON c.contextlevel = ? AND c.instanceid = ef.courseid
                 WHERE ef.userid = ?";
        $params = [CONTEXT_COURSE, $userid];

        $contextlist = new contextlist();
        $contextlist->set_component('enrol_flatfile');
        return $contextlist->add_from_sql($sql, $params);
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $sql = "SELECT userid FROM {enrol_flatfile} WHERE courseid = ?";
        $params = [$context->instanceid];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Ensure all contexts are CONTEXT_COURSE.
        $contexts = static::validate_contextlist_contexts($contextlist);
        if (empty($contexts)) {
            return;
        }

        // Get the context instance ids from the contexts. These  are the course ids..
        $contextinstanceids = array_map(function($context) {
            return $context->instanceid;
        }, $contexts);
        $userid = $contextlist->get_user()->id;

        // Now, we just need to fetch and format all entries corresponding to the contextids provided.
        $sql = "SELECT ef.action, r.shortname, ef.courseid, ef.timestart, ef.timeend, ef.timemodified
                  FROM {enrol_flatfile} ef
                  JOIN {context} c ON c.contextlevel = :contextlevel AND c.instanceid = ef.courseid
                  JOIN {role} r ON r.id = ef.roleid
                 WHERE ef.userid = :userid";
        $params = ['contextlevel' => CONTEXT_COURSE, 'userid' => $userid];
        list($insql, $inparams) = $DB->get_in_or_equal($contextinstanceids, SQL_PARAMS_NAMED);
        $sql .= " AND ef.courseid $insql";
        $params = array_merge($params, $inparams);

        $futureenrolments = $DB->get_recordset_sql($sql, $params);
        $enrolmentdata = [];
        foreach ($futureenrolments as $futureenrolment) {
            // It's possible to have more than one future enrolment per course.
            $futureenrolment->timestart = transform::datetime($futureenrolment->timestart);
            $futureenrolment->timeend = transform::datetime($futureenrolment->timeend);
            $futureenrolment->timemodified = transform::datetime($futureenrolment->timemodified);
            $enrolmentdata[$futureenrolment->courseid][] = $futureenrolment;
        }
        $futureenrolments->close();

        // And finally, write out the data to the relevant course contexts.
        $subcontext = \core_enrol\privacy\provider::get_subcontext([get_string('pluginname', 'enrol_flatfile')]);
        foreach ($enrolmentdata as $courseid => $enrolments) {
            $data = (object) [
                'pendingenrolments' => $enrolments,
            ];
            writer::with_context(\context_course::instance($courseid))->export_data($subcontext, $data);
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }
        global $DB;
        $DB->delete_records('enrol_flatfile', ['courseid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // Only delete data from contexts which are at the CONTEXT_COURSE contextlevel.
        $contexts = self::validate_contextlist_contexts($contextlist);
        if (empty($contexts)) {
            return;
        }

        // Get the course ids based on the provided contexts.
        $contextinstanceids = array_map(function($context) {
            return $context->instanceid;
        }, $contextlist->get_contexts());

        global $DB;
        $user = $contextlist->get_user();
        list($insql, $inparams) = $DB->get_in_or_equal($contextinstanceids, SQL_PARAMS_NAMED);
        $params = array_merge(['userid' => $user->id], $inparams);
        $sql = "userid = :userid AND courseid $insql";
        $DB->delete_records_select('enrol_flatfile', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $userids = $userlist->get_userids();

        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params = array_merge(['courseid' => $context->instanceid], $inparams);
        $sql = "courseid = :courseid AND userid $insql";
        $DB->delete_records_select('enrol_flatfile', $sql, $params);
    }

    /**
     * Simple sanity check on the contextlist contexts, making sure they're of CONTEXT_COURSE contextlevel.
     *
     * @param approved_contextlist $contextlist
     * @return array the array of contexts filtered to only include those of CONTEXT_COURSE contextlevel.
     */
    protected static function validate_contextlist_contexts(approved_contextlist $contextlist) {
        return array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                $carry[] = $context;
            }
            return $carry;
        }, []);
    }
}
