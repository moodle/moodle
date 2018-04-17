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
 * Data provider.
 *
 * @package    mod_feedback
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_feedback\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_helper;
use stdClass;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

require_once($CFG->dirroot . '/mod/feedback/lib.php');

/**
 * Data provider class.
 *
 * @package    mod_feedback
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $completedfields = [
            'userid' => 'privacy:metadata:completed:userid',
            'timemodified' => 'privacy:metadata:completed:timemodified',
            'anonymous_response' => 'privacy:metadata:completed:anonymousresponse',
        ];

        $collection->add_database_table('feedback_completed', $completedfields, 'privacy:metadata:completed');
        $collection->add_database_table('feedback_completedtmp', $completedfields, 'privacy:metadata:completedtmp');

        $valuefields = [
            'value' => 'privacy:metadata:value:value'
        ];

        $collection->add_database_table('feedback_value', $valuefields, 'privacy:metadata:value');
        $collection->add_database_table('feedback_valuetmp', $valuefields, 'privacy:metadata:valuetmp');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        $sql = "
            SELECT DISTINCT ctx.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :feedback
              JOIN {course_modules} cm
                ON cm.instance = fc.feedback
               AND cm.module = m.id
              JOIN {context} ctx
                ON ctx.instanceid = cm.id
               AND ctx.contextlevel = :modlevel
             WHERE fc.userid = :userid";
        $params = ['feedback' => 'feedback', 'modlevel' => CONTEXT_MODULE, 'userid' => $userid];
        $contextlist = new contextlist();
        $contextlist->add_from_sql(sprintf($sql, 'feedback_completed'), $params);
        $contextlist->add_from_sql(sprintf($sql, 'feedback_completedtmp'), $params);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();
        $userid = $user->id;
        $contextids = array_map(function($context) {
            return $context->id;
        }, array_filter($contextlist->get_contexts(), function($context) {
            return $context->contextlevel == CONTEXT_MODULE;
        }));

        if (empty($contextids)) {
            return;
        }

        $flushdata = function($context, $data) use ($user) {
            $contextdata = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);
            $mergeddata = array_merge((array) $contextdata, (array) $data);

            // Drop the temporary keys.
            if (array_key_exists('submissions', $mergeddata)) {
                $mergeddata['submissions'] = array_values($mergeddata['submissions']);
            }

            writer::with_context($context)->export_data([], (object) $mergeddata);
        };

        $lastctxid = null;
        $data = (object) [];
        list($sql, $params) = static::prepare_export_query($contextids, $userid);
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            if ($lastctxid && $lastctxid != $record->contextid) {
                $flushdata(context::instance_by_id($lastctxid), $data);
                $data = (object) [];
            }

            context_helper::preload_from_record($record);
            $id = ($record->istmp ? 'tmp' : 'notmp') . $record->submissionid;

            if (!isset($data->submissions)) {
                $data->submissions = [];
            }

            if (!isset($data->submissions[$id])) {
                $data->submissions[$id] = [
                    'inprogress' => transform::yesno($record->istmp),
                    'anonymousresponse' => transform::yesno($record->anonymousresponse == FEEDBACK_ANONYMOUS_YES),
                    'timemodified' => transform::datetime($record->timemodified),
                    'answers' => []
                ];
            }
            $item = static::extract_item_record_from_record($record);
            $value = static::extract_value_record_from_record($record);
            $itemobj = feedback_get_item_class($record->itemtyp);
            $data->submissions[$id]['answers'][] = [
                'question' => format_text($record->itemname, FORMAT_HTML, [
                    'context' => context::instance_by_id($record->contextid),
                    'para' => false,
                    'noclean' => true,
                ]),
                'answer' => $itemobj->get_printval($item, $value)
            ];

            $lastctxid = $record->contextid;
        }

        if (!empty($lastctxid)) {
            $flushdata(context::instance_by_id($lastctxid), $data);
        }

        $recordset->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // This should not happen, but just in case.
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        // Prepare SQL to gather all completed IDs.

        $completedsql = "
            SELECT fc.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :feedback
              JOIN {course_modules} cm
                ON cm.instance = fc.feedback
               AND cm.module = m.id
             WHERE cm.id = :cmid";
        $completedparams = ['cmid' => $context->instanceid, 'feedback' => 'feedback'];

        // Delete temp answers and submissions.
        $completedtmpids = $DB->get_fieldset_sql(sprintf($completedsql, 'feedback_completedtmp'), $completedparams);
        if (!empty($completedtmpids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($completedtmpids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('feedback_valuetmp', "completed $insql", $inparams);
            $DB->delete_records_select('feedback_completedtmp', "id $insql", $inparams);
        }

        // Delete answers and submissions.
        $completedids = $DB->get_fieldset_sql(sprintf($completedsql, 'feedback_completed'), $completedparams);
        if (!empty($completedids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($completedids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('feedback_value', "completed $insql", $inparams);
            $DB->delete_records_select('feedback_completed', "id $insql", $inparams);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        // Ensure that we only act on module contexts.
        $contextids = array_map(function($context) {
            return $context->instanceid;
        }, array_filter($contextlist->get_contexts(), function($context) {
            return $context->contextlevel == CONTEXT_MODULE;
        }));

        // Prepare SQL to gather all completed IDs.
        list($insql, $inparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);
        $completedsql = "
            SELECT fc.id
              FROM {%s} fc
              JOIN {modules} m
                ON m.name = :feedback
              JOIN {course_modules} cm
                ON cm.instance = fc.feedback
               AND cm.module = m.id
             WHERE fc.userid = :userid
               AND cm.id $insql";
        $completedparams = array_merge($inparams, ['userid' => $userid, 'feedback' => 'feedback']);

        // Delete all submissions in progress.
        $completedtmpids = $DB->get_fieldset_sql(sprintf($completedsql, 'feedback_completedtmp'), $completedparams);
        if (!empty($completedtmpids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($completedtmpids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('feedback_valuetmp', "completed $insql", $inparams);
            $DB->delete_records_select('feedback_completedtmp', "id $insql", $inparams);
        }

        // Delete all final submissions.
        $completedids = $DB->get_fieldset_sql(sprintf($completedsql, 'feedback_completed'), $completedparams);
        if (!empty($completedids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($completedids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('feedback_value', "completed $insql", $inparams);
            $DB->delete_records_select('feedback_completed', "id $insql", $inparams);
        }
    }

    /**
     * Extract an item record from a database record.
     *
     * @param stdClass $record The record.
     * @return The item record.
     */
    protected static function extract_item_record_from_record(stdClass $record) {
        $newrec = new stdClass();
        foreach ($record as $key => $value) {
            if (strpos($key, 'item') !== 0) {
                continue;
            }
            $key = substr($key, 4);
            $newrec->{$key} = $value;
        }
        return $newrec;
    }

    /**
     * Extract a value record from a database record.
     *
     * @param stdClass $record The record.
     * @return The value record.
     */
    protected static function extract_value_record_from_record(stdClass $record) {
        $newrec = new stdClass();
        foreach ($record as $key => $value) {
            if (strpos($key, 'value') !== 0) {
                continue;
            }
            $key = substr($key, 5);
            $newrec->{$key} = $value;
        }
        return $newrec;
    }

    /**
     * Prepare the query to export all data.
     *
     * Doing it this way allows for merging all records from both the temporary and final tables
     * as most of their columns are shared. It is a lot easier to deal with the records when
     * exporting as we do not need to try to manually group the two types of submissions in the
     * same reported dataset.
     *
     * The ordering may affect performance on large datasets.
     *
     * @param array $contextids The context IDs.
     * @param int $userid The user ID.
     * @return array With SQL and params.
     */
    protected static function prepare_export_query(array $contextids, $userid) {
        global $DB;

        $makefetchsql = function($istmp) use ($DB, $contextids, $userid) {
            $ctxfields = context_helper::get_preload_record_columns_sql('ctx');
            list($insql, $inparams) = $DB->get_in_or_equal($contextids, SQL_PARAMS_NAMED);

            $i = $istmp ? 0 : 1;
            $istmpsqlval = $istmp ? 1 : 0;
            $prefix = $istmp ? 'idtmp' : 'id';
            $uniqid = $DB->sql_concat("'$prefix'", 'fc.id');

            $sql = "
                SELECT $uniqid AS uniqid,
                       f.id AS feedbackid,
                       ctx.id AS contextid,

                       $istmpsqlval AS istmp,
                       fc.id AS submissionid,
                       fc.anonymous_response AS anonymousresponse,
                       fc.timemodified AS timemodified,

                       fv.id AS valueid,
                       fv.course_id AS valuecourse_id,
                       fv.item AS valueitem,
                       fv.completed AS valuecompleted,
                       fv.tmp_completed AS valuetmp_completed,

                       $ctxfields
                  FROM {context} ctx
                  JOIN {course_modules} cm
                    ON cm.id = ctx.instanceid
                  JOIN {feedback} f
                    ON f.id = cm.instance
                  JOIN {%s} fc
                    ON fc.feedback = f.id
                  JOIN {%s} fv
                    ON fv.completed = fc.id
                 WHERE ctx.id $insql
                   AND fc.userid = :userid{$i}";

            $params = array_merge($inparams, [
                'userid' . $i => $userid,
            ]);

            $completedtbl = $istmp ? 'feedback_completedtmp' : 'feedback_completed';
            $valuetbl = $istmp ? 'feedback_valuetmp' : 'feedback_value';
            return [sprintf($sql, $completedtbl, $valuetbl), $params];
        };

        list($nontmpsql, $nontmpparams) = $makefetchsql(false);
        list($tmpsql, $tmpparams) = $makefetchsql(true);

        // Oracle does not support UNION on text fields, therefore we must get the itemdescription
        // and valuevalue after doing the union by joining on the result.
        $sql = "
            SELECT q.*,

                   COALESCE(fv.value, fvt.value) AS valuevalue,

                   fi.id AS itemid,
                   fi.feedback AS itemfeedback,
                   fi.template AS itemtemplate,
                   fi.name AS itemname,
                   fi.label AS itemlabel,
                   fi.presentation AS itempresentation,
                   fi.typ AS itemtyp,
                   fi.hasvalue AS itemhasvalue,
                   fi.position AS itemposition,
                   fi.required AS itemrequired,
                   fi.dependitem AS itemdependitem,
                   fi.dependvalue AS itemdependvalue,
                   fi.options AS itemoptions

              FROM ($nontmpsql UNION $tmpsql) q
         LEFT JOIN {feedback_value} fv
                ON fv.id = q.valueid AND q.istmp = 0
         LEFT JOIN {feedback_valuetmp} fvt
                ON fvt.id = q.valueid AND q.istmp = 1
              JOIN {feedback_item} fi
                ON (fi.id = fv.item OR fi.id = fvt.item)
          ORDER BY q.contextid, q.istmp, q.submissionid, q.valueid";
        $params = array_merge($nontmpparams, $tmpparams);

        return [$sql, $params];
    }
}
