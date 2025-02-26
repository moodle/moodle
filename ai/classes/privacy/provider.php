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

namespace core_ai\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem for core_ai implementing null_provider.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('ai_policy_register', [
            'userid' => 'privacy:metadata:ai_policy_register:userid',
            'contextid' => 'privacy:metadata:ai_policy_register:contextid',
            'timeaccepted' => 'privacy:metadata:ai_policy_register:timeaccepted',
        ], 'privacy:metadata:ai_policy_register');
        $collection->add_database_table('ai_action_register', [
            'actionname' => 'privacy:metadata:ai_action_register:actionname',
            'actionid' => 'privacy:metadata:ai_action_register:actionid',
            'success' => 'privacy:metadata:ai_action_register:success',
            'userid' => 'privacy:metadata:ai_action_register:userid',
            'provider' => 'privacy:metadata:ai_action_register:provider',
            'timecreated' => 'privacy:metadata:ai_action_register:timecreated',
            'timecompleted' => 'privacy:metadata:ai_action_register:timecompleted',
            'model' => 'privacy:metadata:ai_action_register:model',
        ], 'privacy:metadata:ai_action_register');
        $collection->add_database_table('ai_action_generate_image', [
            'prompt' => 'privacy:metadata:ai_action_generate_image:prompt',
            'numberimages' => 'privacy:metadata:ai_action_generate_image:numberimages',
            'quality' => 'privacy:metadata:ai_action_generate_image:quality',
            'aspectratio' => 'privacy:metadata:ai_action_generate_image:aspectratio',
            'style' => 'privacy:metadata:ai_action_generate_image:style',
            'sourceurl' => 'privacy:metadata:ai_action_generate_image:sourceurl',
            'revisedprompt' => 'privacy:metadata:ai_action_generate_image:revisedprompt',
        ], 'privacy:metadata:ai_action_generate_image');
        $collection->add_database_table('ai_action_generate_text', [
            'prompt' => 'privacy:metadata:ai_action_generate_text:prompt',
            'responseid' => 'privacy:metadata:ai_action_generate_text:responseid',
            'fingerprint' => 'privacy:metadata:ai_action_generate_text:fingerprint',
            'generatedcontent' => 'privacy:metadata:ai_action_generate_text:generatedcontent',
            'prompttokens' => 'privacy:metadata:ai_action_generate_text:prompttokens',
            'completiontoken' => 'privacy:metadata:ai_action_generate_text:completiontoken',
        ], 'privacy:metadata:ai_action_generate_text');
        $collection->add_database_table('ai_action_summarise_text', [
            'prompt' => 'privacy:metadata:ai_action_summarise_text:prompt',
            'responseid' => 'privacy:metadata:ai_action_summarise_text:responseid',
            'fingerprint' => 'privacy:metadata:ai_action_summarise_text:fingerprint',
            'generatedcontent' => 'privacy:metadata:ai_action_summarise_text:generatedcontent',
            'prompttokens' => 'privacy:metadata:ai_action_summarise_text:prompttokens',
            'completiontoken' => 'privacy:metadata:ai_action_summarise_text:completiontoken',
        ], 'privacy:metadata:ai_action_summarise_text');
        $collection->add_database_table('ai_action_explain_text', [
            'prompt' => 'privacy:metadata:ai_action_explain_text:prompt',
            'responseid' => 'privacy:metadata:ai_action_explain_text:responseid',
            'fingerprint' => 'privacy:metadata:ai_action_explain_text:fingerprint',
            'generatedcontent' => 'privacy:metadata:ai_action_explain_text:generatedcontent',
            'prompttokens' => 'privacy:metadata:ai_action_explain_text:prompttokens',
            'completiontoken' => 'privacy:metadata:ai_action_explain_text:completiontoken',
        ], 'privacy:metadata:ai_action_explain_text');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // AI policy.
        $sql = 'SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {ai_policy_register} apr
                    ON apr.contextid = ctx.id
                 WHERE apr.userid = :userid';
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        // AI action generate text.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_generate_text} aagt
                    ON aagt.id = aar.actionid
                 WHERE aar.actionname = 'generate_text'
                       AND aar.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        // AI action generate image.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_generate_image} aagi
                    ON aagi.id = aar.actionid
                 WHERE aar.actionname = 'generate_image'
                       AND aar.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        // AI action summarise text.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_summarise_text} aast
                    ON aast.id = aar.actionid
                 WHERE aar.actionname = 'summarise_text'
                       AND aar.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        // AI action explain text.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_explain_text} aaet
                    ON aaet.id = aar.actionid
                 WHERE aar.actionname = 'explain_text'
                       AND aar.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid]);

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;
        // AI policy.
        $userid = $contextlist->get_user()->id;
        [$contextsql, $contextparams] = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = 'SELECT apr.timeaccepted, ctx.id AS contextid
                  FROM {context} ctx
                  JOIN {ai_policy_register} apr
                    ON apr.contextid = ctx.id
                 WHERE apr.userid = :userid
                       AND ctx.id ' . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $policydetails = $DB->get_recordset_sql($sql, $params);
        foreach ($policydetails as $policydetail) {
            $subcontexts = [
                get_string('ai', 'core_ai'),
            ];
            $name = 'policy';
            $details = (object) [
                'contextid' => $policydetail->contextid,
                'timeaccepted' => transform::datetime($policydetail->timeaccepted),
            ];
            $context = \context::instance_by_id($policydetail->contextid);
            writer::with_context($context)->export_related_data($subcontexts, $name, $details);
        }
        $policydetails->close();

        // AI action generate text.
        $sql = "SELECT aar.actionname, aar.success, aar.provider, aar.timecreated, aar.timecompleted, aar.contextid,
                       aagt.prompt, aagt.responseid, aagt.fingerprint, aagt.generatedcontent,
                       aagt.prompttokens, aagt.completiontoken, aar.model
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_text} aagt
                    ON aar.actionid = aagt.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $textgeneratedetails = $DB->get_recordset_sql($sql, $params);
        foreach ($textgeneratedetails as $textgeneratedetail) {
            $subcontexts = [
                get_string('ai', 'core_ai'),
                get_string('action_generate_text', 'core_ai'),
                date('c', $textgeneratedetail->timecreated),
            ];
            $details = (object) [
                'actionname' => $textgeneratedetail->actionname,
                'contextid' => $textgeneratedetail->contextid,
                'prompt' => $textgeneratedetail->prompt,
                'responseid' => $textgeneratedetail->responseid,
                'fingerprint' => $textgeneratedetail->fingerprint,
                'generatedcontent' => $textgeneratedetail->generatedcontent,
                'prompttokens' => $textgeneratedetail->prompttokens,
                'completiontoken' => $textgeneratedetail->completiontoken,
                'model' => $textgeneratedetail->model,
                'success' => transform::yesno($textgeneratedetail->success),
                'provider' => $textgeneratedetail->provider,
                'timecreated' => transform::datetime($textgeneratedetail->timecreated),
                'timecompleted' => transform::datetime($textgeneratedetail->timecompleted),
            ];
            $name = 'action_generate_text';
            $context = \context::instance_by_id($textgeneratedetail->contextid);
            writer::with_context($context)->export_related_data($subcontexts, $name, $details);
        }
        $textgeneratedetails->close();

        // AI action generate image.
        $sql = "SELECT aar.actionname, aar.success, aar.provider, aar.timecreated, aar.timecompleted, aar.contextid,
                       aagi.prompt, aagi.numberimages, aagi.quality, aagi.aspectratio, aagi.style, aagi.sourceurl,
                       aagi.revisedprompt, aar.model
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_image} aagi
                    ON aar.actionid = aagi.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_image'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $imagegeneratedetails = $DB->get_recordset_sql($sql, $params);
        foreach ($imagegeneratedetails as $imagegeneratedetail) {
            $subcontexts = [
                get_string('ai', 'core_ai'),
                get_string('action_generate_image', 'core_ai'),
                date('c', $imagegeneratedetail->timecreated),
            ];
            $details = (object) [
                'actionname' => $imagegeneratedetail->actionname,
                'contextid' => $imagegeneratedetail->contextid,
                'prompt' => $imagegeneratedetail->prompt,
                'numberimages' => $imagegeneratedetail->numberimages,
                'quality' => $imagegeneratedetail->quality,
                'aspectratio' => $imagegeneratedetail->aspectratio,
                'style' => $imagegeneratedetail->style,
                'sourceurl' => $imagegeneratedetail->sourceurl,
                'revisedprompt' => $imagegeneratedetail->revisedprompt,
                'model' => $imagegeneratedetail->model,
                'success' => transform::yesno($imagegeneratedetail->success),
                'provider' => $imagegeneratedetail->provider,
                'timecreated' => transform::datetime($imagegeneratedetail->timecreated),
                'timecompleted' => transform::datetime($imagegeneratedetail->timecompleted),
            ];
            $name = 'action_generate_image';
            $context = \context::instance_by_id($imagegeneratedetail->contextid);
            writer::with_context($context)->export_related_data($subcontexts, $name, $details);
        }
        $imagegeneratedetails->close();

        // AI action summarise text.
        $sql = "SELECT aar.actionname, aar.success, aar.provider, aar.timecreated, aar.timecompleted, aar.contextid,
                       aast.prompt, aast.responseid, aast.fingerprint, aast.generatedcontent,
                       aast.prompttokens, aast.completiontoken, aar.model
                  FROM {ai_action_register} aar
                  JOIN {ai_action_summarise_text} aast
                    ON aar.actionid = aast.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'summarise_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $textsummarisedetails = $DB->get_recordset_sql($sql, $params);
        foreach ($textsummarisedetails as $textsummarisedetail) {
            $subcontexts = [
                get_string('ai', 'core_ai'),
                get_string('action_summarise_text', 'core_ai'),
                date('c', $textsummarisedetail->timecreated),
            ];
            $details = (object) [
                'actionname' => $textsummarisedetail->actionname,
                'contextid' => $textsummarisedetail->contextid,
                'prompt' => $textsummarisedetail->prompt,
                'responseid' => $textsummarisedetail->responseid,
                'fingerprint' => $textsummarisedetail->fingerprint,
                'generatedcontent' => $textsummarisedetail->generatedcontent,
                'prompttokens' => $textsummarisedetail->prompttokens,
                'completiontoken' => $textsummarisedetail->completiontoken,
                'model' => $textsummarisedetail->model,
                'success' => transform::yesno($textsummarisedetail->success),
                'provider' => $textsummarisedetail->provider,
                'timecreated' => transform::datetime($textsummarisedetail->timecreated),
                'timecompleted' => transform::datetime($textsummarisedetail->timecompleted),
            ];
            $name = 'action_summarise_text';
            $context = \context::instance_by_id($textsummarisedetail->contextid);
            writer::with_context($context)->export_related_data($subcontexts, $name, $details);
        }
        $textsummarisedetails->close();

        // AI action explain text.
        $sql = "SELECT aar.actionname, aar.success, aar.provider, aar.timecreated, aar.timecompleted, aar.contextid,
                       aaet.prompt, aaet.responseid, aaet.fingerprint, aaet.generatedcontent,
                       aaet.prompttokens, aaet.completiontoken, aar.model
                  FROM {ai_action_register} aar
                  JOIN {ai_action_explain_text} aaet
                    ON aar.actionid = aaet.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'explain_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $textexplaindetails = $DB->get_recordset_sql($sql, $params);
        foreach ($textexplaindetails as $textexplaindetail) {
            $subcontexts = [
                get_string('ai', 'core_ai'),
                get_string('action_explain_text', 'core_ai'),
                date('c', $textexplaindetail->timecreated),
            ];
            $details = (object) [
                'actionname' => $textexplaindetail->actionname,
                'contextid' => $textexplaindetail->contextid,
                'prompt' => $textexplaindetail->prompt,
                'responseid' => $textexplaindetail->responseid,
                'fingerprint' => $textexplaindetail->fingerprint,
                'generatedcontent' => $textexplaindetail->generatedcontent,
                'prompttokens' => $textexplaindetail->prompttokens,
                'completiontoken' => $textexplaindetail->completiontoken,
                'model' => $textexplaindetail->model,
                'success' => transform::yesno($textexplaindetail->success),
                'provider' => $textexplaindetail->provider,
                'timecreated' => transform::datetime($textexplaindetail->timecreated),
                'timecompleted' => transform::datetime($textexplaindetail->timecompleted),
            ];
            $name = 'action_explain_text';
            $context = \context::instance_by_id($textexplaindetail->contextid);
            writer::with_context($context)->export_related_data($subcontexts, $name, $details);
        }
        $textexplaindetails->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        // Policy.
        $sql = 'SELECT DISTINCT apr.id
                  FROM {ai_policy_register} apr
                  JOIN {context} ctx
                    ON apr.contextid = ctx.id
                 WHERE ctx.id = :contextid';
        $params = [
            'contextid' => $context->id,
        ];
        $policydetails = $DB->get_records_sql($sql, $params);
        if ($policydetails) {
            $DB->delete_records_list('ai_policy_register', 'id', array_keys($policydetails));
        }

        // AI action generate text.
        $sql = "SELECT DISTINCT aagt.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_text} aagt
                    ON aar.actionid = aagt.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_text'
                       AND ctx.id = :contextid";
        $params = [
            'contextid' => $context->id,
        ];
        $aagtids = $DB->get_records_sql_menu($sql, $params);
        if ($aagtids) {
            [$aagtidsql, $aagtidparams] = $DB->get_in_or_equal(array_keys($aagtids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aagtidsql;
            $DB->execute($sql, $aagtidparams);
        }

        // AI action generate image.
        $sql = "SELECT DISTINCT aagi.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_image} aagi
                    ON aar.actionid = aagi.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_image'
                       AND ctx.id = :contextid";
        $params = [
            'contextid' => $context->id,
        ];
        $aagiids = $DB->get_records_sql_menu($sql, $params);
        if ($aagiids) {
            [$aagiidsql, $aagiidparams] = $DB->get_in_or_equal(array_keys($aagiids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_image}
                   SET prompt = '',
                       sourceurl = '',
                       revisedprompt = ''
                 WHERE id " . $aagiidsql;
            $DB->execute($sql, $aagiidparams);
        }

        // AI action summarise text.
        $sql = "SELECT DISTINCT aast.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_summarise_text} aast
                    ON aar.actionid = aast.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'summarise_text'
                       AND ctx.id = :contextid";
        $params = [
            'contextid' => $context->id,
        ];
        $aastids = $DB->get_records_sql_menu($sql, $params);
        if ($aastids) {
            [$aastidsql, $aastidparams] = $DB->get_in_or_equal(array_keys($aastids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_summarise_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aastidsql;
            $DB->execute($sql, $aastidparams);
        }

        // AI action explain text.
        $sql = "SELECT DISTINCT aaet.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_explain_text} aaet
                    ON aar.actionid = aaet.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'explain_text'
                       AND ctx.id = :contextid";
        $params = [
            'contextid' => $context->id,
        ];
        $aaetids = $DB->get_records_sql_menu($sql, $params);
        if ($aaetids) {
            [$aaetidsql, $aaetidparams] = $DB->get_in_or_equal(array_keys($aaetids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_explain_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aaetidsql;
            $DB->execute($sql, $aaetidparams);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        // Policy.
        $userid = $contextlist->get_user()->id;
        [$contextsql, $contextparams] = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT DISTINCT apr.id AS policyid
                  FROM {context} ctx
                  JOIN {ai_policy_register} apr
                    ON apr.contextid = ctx.id
                 WHERE apr.userid = :userid
                       AND ctx.id " . $contextsql;
        $params = [
            'userid' => $userid,
        ];
        $params += $contextparams;
        $policydetails = $DB->get_recordset_sql($sql, $params);
        $policyids = [];
        foreach ($policydetails as $policydetail) {
            $policyids[] = $policydetail->policyid;
        }
        $policydetails->close();
        $DB->delete_records_list('ai_policy_register', 'id', $policyids);

        // AI action generate text.
        $sql = "SELECT DISTINCT aagt.id AS textgenerateid
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_text} aagt
                    ON aar.actionid = aagt.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $textgeneratedetails = $DB->get_recordset_sql($sql, $params);
        $aagtids = [];
        foreach ($textgeneratedetails as $textgeneratedetail) {
            $aagtids[] = $textgeneratedetail->textgenerateid;
        }
        $textgeneratedetails->close();
        if ($aagtids) {
            [$aagtidsql, $aagtidparams] = $DB->get_in_or_equal($aagtids, SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aagtidsql;
            $DB->execute($sql, $aagtidparams);
        }

        // AI action generate image.
        $sql = "SELECT DISTINCT aagi.id AS imagegenerateid
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_image} aagi
                    ON aar.actionid = aagi.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_image'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $imagegeneratedetails = $DB->get_recordset_sql($sql, $params);
        $aagiids = [];
        foreach ($imagegeneratedetails as $imagegeneratedetail) {
            $aagiids[] = $imagegeneratedetail->imagegenerateid;
        }
        $imagegeneratedetails->close();
        if ($aagiids) {
            [$aagiidsql, $aagiidparams] = $DB->get_in_or_equal($aagiids, SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_image}
                   SET prompt = '',
                       sourceurl = '',
                       revisedprompt = ''
                 WHERE id " . $aagiidsql;
            $DB->execute($sql, $aagiidparams);
        }

        // AI action summarise text.
        $sql = "SELECT DISTINCT aast.id AS textsummariseid
                  FROM {ai_action_register} aar
                  JOIN {ai_action_summarise_text} aast
                    ON aar.actionid = aast.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'summarise_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $textsummarisedetails = $DB->get_recordset_sql($sql, $params);
        $aastids = [];
        foreach ($textsummarisedetails as $textsummarisedetail) {
            $aastids[] = $textsummarisedetail->textsummariseid;
        }
        $textsummarisedetails->close();
        if ($aastids) {
            [$aastidsql, $aastidparams] = $DB->get_in_or_equal($aastids, SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_summarise_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aastidsql;
            $DB->execute($sql, $aastidparams);
        }

        // AI action explain text.
        $sql = "SELECT DISTINCT aaet.id AS textexplainid
                  FROM {ai_action_register} aar
                  JOIN {ai_action_explain_text} aaet
                    ON aar.actionid = aaet.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'explain_text'
                       AND aar.userid = :userid
                       AND ctx.id " . $contextsql;
        $textexplaindetails = $DB->get_recordset_sql($sql, $params);
        $aaetids = [];
        foreach ($textexplaindetails as $textexplaindetail) {
            $aaetids[] = $textexplaindetail->textexplainid;
        }
        $textexplaindetails->close();
        if ($aaetids) {
            [$aaetidsql, $aaetidparams] = $DB->get_in_or_equal($aaetids, SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_explain_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aaetidsql;
            $DB->execute($sql, $aaetidparams);
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        // AI policy.
        $sql = 'SELECT DISTINCT apr.userid
                  FROM {context} ctx
                  JOIN {ai_policy_register} apr
                    ON apr.contextid = ctx.id
                 WHERE apr.contextid = :contextid';
        $userlist->add_from_sql('userid', $sql, ['contextid' => $context->id]);

        // AI action generate text.
        $sql = "SELECT DISTINCT aar.userid
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_generate_text} aagt
                    ON aagt.id = aar.actionid
                 WHERE aar.actionname = 'generate_text'
                       AND aar.contextid = :contextid";
        $userlist->add_from_sql('userid', $sql, ['contextid' => $context->id]);

        // AI action generate image.
        $sql = "SELECT DISTINCT aar.userid
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_generate_image} aagi
                    ON aagi.id = aar.actionid
                 WHERE aar.actionname = 'generate_image'
                       AND aar.contextid = :contextid";
        $userlist->add_from_sql('userid', $sql, ['contextid' => $context->id]);

        // AI action summarise text.
        $sql = "SELECT DISTINCT aar.userid
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_summarise_text} aast
                    ON aast.id = aar.actionid
                 WHERE aar.actionname = 'summarise_text'
                       AND aar.contextid = :contextid";
        $userlist->add_from_sql('userid', $sql, ['contextid' => $context->id]);

        // AI action explain text.
        $sql = "SELECT DISTINCT aar.userid
                  FROM {context} ctx
                  JOIN {ai_action_register} aar
                    ON aar.contextid = ctx.id
                  JOIN {ai_action_explain_text} aaet
                    ON aaet.id = aar.actionid
                 WHERE aar.actionname = 'explain_text'
                       AND aar.contextid = :contextid";
        $userlist->add_from_sql('userid', $sql, ['contextid' => $context->id]);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        [$useridssql, $useridsparams] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = [
            'contextid' => $context->id,
        ];
        $params += $useridsparams;

        // Policy.
        $sql = "SELECT DISTINCT apr.id
                  FROM {ai_policy_register} apr
                  JOIN {context} ctx
                    ON apr.contextid = ctx.id
                 WHERE ctx.id = :contextid
                       AND apr.userid " . $useridssql;
        $policydetails = $DB->get_records_sql($sql, $params);
        if ($policydetails) {
            $DB->delete_records_list('ai_policy_register', 'id', array_keys($policydetails));
        }

        // AI action generate text.
        $sql = "SELECT DISTINCT aagt.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_text} aagt
                    ON aar.actionid = aagt.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_text'
                       AND ctx.id = :contextid
                       AND aar.userid " . $useridssql;
        $aagtids = $DB->get_records_sql_menu($sql, $params);
        if ($aagtids) {
            [$aagtidsql, $aagtidparams] = $DB->get_in_or_equal(array_keys($aagtids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aagtidsql;
            $DB->execute($sql, $aagtidparams);
        }

        // AI action generate image.
        $sql = "SELECT DISTINCT aagi.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_generate_image} aagi
                    ON aar.actionid = aagi.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'generate_image'
                       AND ctx.id = :contextid
                       AND aar.userid " . $useridssql;
        $aagiids = $DB->get_records_sql_menu($sql, $params);
        if ($aagiids) {
            [$aagiidsql, $aagiidparams] = $DB->get_in_or_equal(array_keys($aagiids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_generate_image}
                   SET prompt = '',
                       sourceurl = '',
                       revisedprompt = ''
                 WHERE id " . $aagiidsql;
            $DB->execute($sql, $aagiidparams);
        }

        // AI action summarise text.
        $sql = "SELECT DISTINCT aast.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_summarise_text} aast
                    ON aar.actionid = aast.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'summarise_text'
                       AND ctx.id = :contextid
                       AND aar.userid " . $useridssql;
        $aastids = $DB->get_records_sql_menu($sql, $params);
        if ($aastids) {
            [$aastidsql, $aastidparams] = $DB->get_in_or_equal(array_keys($aastids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_summarise_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aastidsql;
            $DB->execute($sql, $aastidparams);
        }

        // AI action explain text.
        $sql = "SELECT DISTINCT aaet.id
                  FROM {ai_action_register} aar
                  JOIN {ai_action_explain_text} aaet
                    ON aar.actionid = aaet.id
                  JOIN {context} ctx
                    ON aar.contextid = ctx.id
                 WHERE aar.actionname = 'explain_text'
                       AND ctx.id = :contextid
                       AND aar.userid " . $useridssql;
        $aaetids = $DB->get_records_sql_menu($sql, $params);
        if ($aaetids) {
            [$aaetidsql, $aaetidparams] = $DB->get_in_or_equal(array_keys($aaetids), SQL_PARAMS_NAMED);
            $sql = "UPDATE {ai_action_explain_text}
                   SET prompt = '',
                       responseid = '',
                       fingerprint = '',
                       generatedcontent = ''
                 WHERE id " . $aaetidsql;
            $DB->execute($sql, $aaetidparams);
        }
    }
}
