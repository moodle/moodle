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
 * Privacy class for requesting user data.
 *
 * @package    mod_scorm
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_scorm\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table('scorm_scoes_track', [
                'userid' => 'privacy:metadata:userid',
                'attempt' => 'privacy:metadata:attempt',
                'element' => 'privacy:metadata:scoes_track:element',
                'value' => 'privacy:metadata:scoes_track:value',
                'timemodified' => 'privacy:metadata:timemodified'
            ], 'privacy:metadata:scorm_scoes_track');

        $collection->add_database_table('scorm_aicc_session', [
                'userid' => 'privacy:metadata:userid',
                'scormmode' => 'privacy:metadata:aicc_session:scormmode',
                'scormstatus' => 'privacy:metadata:aicc_session:scormstatus',
                'attempt' => 'privacy:metadata:attempt',
                'lessonstatus' => 'privacy:metadata:aicc_session:lessonstatus',
                'sessiontime' => 'privacy:metadata:aicc_session:sessiontime',
                'timecreated' => 'privacy:metadata:aicc_session:timecreated',
                'timemodified' => 'privacy:metadata:timemodified',
            ], 'privacy:metadata:scorm_aicc_session');

        $collection->add_external_location_link('aicc', [
                'data' => 'privacy:metadata:aicc:data'
            ], 'privacy:metadata:aicc:externalpurpose');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $sql = "SELECT ctx.id
                  FROM {%s} ss
                  JOIN {modules} m
                    ON m.name = 'scorm'
                  JOIN {course_modules} cm
                    ON cm.instance = ss.scormid
                   AND cm.module = m.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                   AND ctx.contextlevel = :modlevel
                 WHERE ss.userid = :userid";

        $params = ['modlevel' => CONTEXT_MODULE, 'userid' => $userid];
        $contextlist = new contextlist();
        $contextlist->add_from_sql(sprintf($sql, 'scorm_scoes_track'), $params);
        $contextlist->add_from_sql(sprintf($sql, 'scorm_aicc_session'), $params);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from COURSE_MODULE.
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contexts)) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        list($insql, $inparams) = $DB->get_in_or_equal($contexts, SQL_PARAMS_NAMED);

        // Get scoes_track data.
        $sql = "SELECT ss.id,
                       ss.attempt,
                       ss.element,
                       ss.value,
                       ss.timemodified,
                       ctx.id as contextid
                  FROM {scorm_scoes_track} ss
                  JOIN {course_modules} cm
                    ON cm.instance = ss.scormid
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                 WHERE ctx.id $insql
                   AND ss.userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        $alldata = [];
        $scoestracks = $DB->get_recordset_sql($sql, $params);
        foreach ($scoestracks as $track) {
            $alldata[$track->contextid][$track->attempt][] = (object)[
                    'element' => $track->element,
                    'value' => $track->value,
                    'timemodified' => transform::datetime($track->timemodified),
                ];
        }
        $scoestracks->close();

        // The scoes_track data is organised in: {Course name}/{SCORM activity name}/attempt-X.json.
        // where X is the attempt number.
        array_walk($alldata, function($attemptsdata, $contextid) {
            $context = \context::instance_by_id($contextid);
            array_walk($attemptsdata, function($data, $attempt) use ($context) {
                writer::with_context($context)->export_related_data(
                    [],
                    'attempt-'.$attempt,
                    (object)['scoestrack' => $data]
                );
            });
        });

        // Get aicc_session data.
        $sql = "SELECT ss.id,
                       ss.scormmode,
                       ss.scormstatus,
                       ss.attempt,
                       ss.lessonstatus,
                       ss.sessiontime,
                       ss.timecreated,
                       ss.timemodified,
                       ctx.id as contextid
                  FROM {scorm_aicc_session} ss
                  JOIN {course_modules} cm
                    ON cm.instance = ss.scormid
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                 WHERE ctx.id $insql
                   AND ss.userid = :userid";
        $params = array_merge($inparams, ['userid' => $userid]);

        $alldata = [];
        $aiccsessions = $DB->get_recordset_sql($sql, $params);
        foreach ($aiccsessions as $aiccsession) {
            $alldata[$aiccsession->contextid][] = (object)[
                    'scormmode' => $aiccsession->scormmode,
                    'scormstatus' => $aiccsession->scormstatus,
                    'lessonstatus' => $aiccsession->lessonstatus,
                    'attempt' => $aiccsession->attempt,
                    'sessiontime' => $aiccsession->sessiontime,
                    'timecreated' => transform::datetime($aiccsession->timecreated),
                    'timemodified' => transform::datetime($aiccsession->timemodified),
                ];
        }
        $aiccsessions->close();

        // The aicc_session data is organised in: {Course name}/{SCORM activity name}/aiccsession.json.
        // In this case, the attempt hasn't been included in the json file because it can be null.
        array_walk($alldata, function($data, $contextid) {
            $context = \context::instance_by_id($contextid);
            writer::with_context($context)->export_related_data(
                [],
                'aiccsession',
                (object)['sessions' => $data]
            );
        });
    }

    /**
     * Delete all user data which matches the specified context.
     *
     * @param context $context A user context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // This should not happen, but just in case.
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Prepare SQL to gather all IDs to delete.
        $sql = "SELECT ss.id
                  FROM {%s} ss
                  JOIN {modules} m
                    ON m.name = 'scorm'
                  JOIN {course_modules} cm
                    ON cm.instance = ss.scormid
                   AND cm.module = m.id
                 WHERE cm.id = :cmid";
        $params = ['cmid' => $context->instanceid];

        static::delete_data('scorm_scoes_track', $sql, $params);
        static::delete_data('scorm_aicc_session', $sql, $params);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        // Remove contexts different from COURSE_MODULE.
        $contextids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context->id;
            }
            return $carry;
        }, []);

        if (empty($contextids)) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        // Prepare SQL to gather all completed IDs.
        list($insql, $inparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $sql = "SELECT ss.id
                  FROM {%s} ss
                  JOIN {modules} m
                    ON m.name = 'scorm'
                  JOIN {course_modules} cm
                    ON cm.instance = ss.scormid
                   AND cm.module = m.id
                  JOIN {context} ctx
                    ON ctx.instanceid = cm.id
                 WHERE ss.userid = :userid
                   AND ctx.id $insql";
        $params = array_merge($inparams, ['userid' => $userid]);

        static::delete_data('scorm_scoes_track', $sql, $params);
        static::delete_data('scorm_aicc_session', $sql, $params);
    }

    /**
     * Delete data from $tablename with the IDs returned by $sql query.
     *
     * @param  string $tablename  Table name where executing the SQL query.
     * @param  string $sql    SQL query for getting the IDs of the scoestrack entries to delete.
     * @param  array  $params SQL params for the query.
     */
    protected static function delete_data(string $tablename, string $sql, array $params) {
        global $DB;

        $scoestracksids = $DB->get_fieldset_sql(sprintf($sql, $tablename), $params);
        if (!empty($scoestracksids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($scoestracksids, SQL_PARAMS_NAMED);
            $DB->delete_records_select($tablename, "id $insql", $inparams);
        }
    }
}
