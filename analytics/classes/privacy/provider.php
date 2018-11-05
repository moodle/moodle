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
 * Privacy Subsystem implementation for core_analytics.
 *
 * @package    core_analytics
 * @copyright  2018 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\privacy;

use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for core_analytics implementing metadata and plugin providers.
 *
 * @copyright  2018 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\core_userlist_provider,
        \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'analytics_indicator_calc',
            [
                'starttime' => 'privacy:metadata:analytics:indicatorcalc:starttime',
                'endtime' => 'privacy:metadata:analytics:indicatorcalc:endtime',
                'contextid' => 'privacy:metadata:analytics:indicatorcalc:contextid',
                'sampleorigin' => 'privacy:metadata:analytics:indicatorcalc:sampleorigin',
                'sampleid' => 'privacy:metadata:analytics:indicatorcalc:sampleid',
                'indicator' => 'privacy:metadata:analytics:indicatorcalc:indicator',
                'value' => 'privacy:metadata:analytics:indicatorcalc:value',
                'timecreated' => 'privacy:metadata:analytics:indicatorcalc:timecreated',
            ],
            'privacy:metadata:analytics:indicatorcalc'
        );

        $collection->add_database_table(
            'analytics_predictions',
            [
                'modelid' => 'privacy:metadata:analytics:predictions:modelid',
                'contextid' => 'privacy:metadata:analytics:predictions:contextid',
                'sampleid' => 'privacy:metadata:analytics:predictions:sampleid',
                'rangeindex' => 'privacy:metadata:analytics:predictions:rangeindex',
                'prediction' => 'privacy:metadata:analytics:predictions:prediction',
                'predictionscore' => 'privacy:metadata:analytics:predictions:predictionscore',
                'calculations' => 'privacy:metadata:analytics:predictions:calculations',
                'timecreated' => 'privacy:metadata:analytics:predictions:timecreated',
                'timestart' => 'privacy:metadata:analytics:predictions:timestart',
                'timeend' => 'privacy:metadata:analytics:predictions:timeend',
            ],
            'privacy:metadata:analytics:predictions'
        );

        $collection->add_database_table(
            'analytics_prediction_actions',
            [
                'predictionid' => 'privacy:metadata:analytics:predictionactions:predictionid',
                'userid' => 'privacy:metadata:analytics:predictionactions:userid',
                'actionname' => 'privacy:metadata:analytics:predictionactions:actionname',
                'timecreated' => 'privacy:metadata:analytics:predictionactions:timecreated',
            ],
            'privacy:metadata:analytics:predictionactions'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        global $DB;

        $contextlist = new \core_privacy\local\request\contextlist();

        $models = self::get_models_with_user_data();

        foreach ($models as $modelid => $model) {

            $analyser = $model->get_analyser(['notimesplitting' => true]);

            // Analytics predictions.
            $joinusersql = $analyser->join_sample_user('ap');
            $sql = "SELECT DISTINCT ap.contextid FROM {analytics_predictions} ap
                      {$joinusersql}
                     WHERE u.id = :userid AND ap.modelid = :modelid";
            $contextlist->add_from_sql($sql, ['userid' => $userid, 'modelid' => $modelid]);

            // Indicator calculations.
            $joinusersql = $analyser->join_sample_user('aic');
            $sql = "SELECT DISTINCT aic.contextid FROM {analytics_indicator_calc} aic
                      {$joinusersql}
                     WHERE u.id = :userid AND aic.sampleorigin = :analysersamplesorigin";
            $contextlist->add_from_sql($sql, ['userid' => $userid, 'analysersamplesorigin' => $analyser->get_samples_origin()]);
        }

        // We can leave this out of the loop as there is no analyser-dependent stuff.
        list($sql, $params) = self::analytics_prediction_actions_user_sql($userid, array_keys($models));
        $sql = "SELECT DISTINCT ap.contextid" . $sql;
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $models = self::get_models_with_user_data();

        foreach ($models as $modelid => $model) {

            $analyser = $model->get_analyser(['notimesplitting' => true]);

            // Analytics predictions.
            $params = [
                'contextid' => $context->id,
                'modelid' => $modelid,
            ];
            $joinusersql = $analyser->join_sample_user('ap');
            $sql = "SELECT u.id AS userid
                      FROM {analytics_predictions} ap
                           {$joinusersql}
                     WHERE ap.contextid = :contextid
                       AND ap.modelid = :modelid";
            $userlist->add_from_sql('userid', $sql, $params);

            // Indicator calculations.
            $params = [
                'contextid' => $context->id,
                'analysersamplesorigin' => $analyser->get_samples_origin(),
            ];
            $joinusersql = $analyser->join_sample_user('aic');
            $sql = "SELECT u.id AS userid
                      FROM {analytics_indicator_calc} aic
                           {$joinusersql}
                     WHERE aic.contextid = :contextid
                       AND aic.sampleorigin = :analysersamplesorigin";
            $userlist->add_from_sql('userid', $sql, $params);
        }

        // We can leave this out of the loop as there is no analyser-dependent stuff.
        list($sql, $params) = self::analytics_prediction_actions_context_sql($context->id, array_keys($models));
        $sql = "SELECT apa.userid" . $sql;
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = intval($contextlist->get_user()->id);

        $models = self::get_models_with_user_data();
        $modelids = array_keys($models);

        list ($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $rootpath = [get_string('analytics', 'analytics')];
        $ctxfields = \context_helper::get_preload_record_columns_sql('ctx');

        foreach ($models as $modelid => $model) {

            $analyser = $model->get_analyser(['notimesplitting' => true]);

            // Analytics predictions.
            $joinusersql = $analyser->join_sample_user('ap');
            $sql = "SELECT ap.*, $ctxfields FROM {analytics_predictions} ap
                      JOIN {context} ctx ON ctx.id = ap.contextid
                      {$joinusersql}
                     WHERE u.id = :userid AND ap.modelid = :modelid AND ap.contextid {$contextsql}";
            $params = ['userid' => $userid, 'modelid' => $modelid] + $contextparams;
            $predictions = $DB->get_recordset_sql($sql, $params);

            foreach ($predictions as $prediction) {
                \context_helper::preload_from_record($prediction);
                $context = \context::instance_by_id($prediction->contextid);
                $path = $rootpath;
                $path[] = get_string('privacy:metadata:analytics:predictions', 'analytics');
                $path[] = $prediction->id;

                $data = (object)[
                    'target' => $model->get_target()->get_name()->out(),
                    'context' => $context->get_context_name(true, true),
                    'prediction' => $model->get_target()->get_display_value($prediction->prediction),
                    'timestart' => transform::datetime($prediction->timestart),
                    'timeend' => transform::datetime($prediction->timeend),
                    'timecreated' => transform::datetime($prediction->timecreated),
                ];
                writer::with_context($context)->export_data($path, $data);
            }
            $predictions->close();

            // Indicator calculations.
            $joinusersql = $analyser->join_sample_user('aic');
            $sql = "SELECT aic.*, $ctxfields FROM {analytics_indicator_calc} aic
                      JOIN {context} ctx ON ctx.id = aic.contextid
                      {$joinusersql}
                     WHERE u.id = :userid AND aic.sampleorigin = :analysersamplesorigin AND aic.contextid {$contextsql}";
            $params = ['userid' => $userid, 'analysersamplesorigin' => $analyser->get_samples_origin()] + $contextparams;
            $indicatorcalculations = $DB->get_recordset_sql($sql, $params);
            foreach ($indicatorcalculations as $calculation) {
                \context_helper::preload_from_record($calculation);
                $context = \context::instance_by_id($calculation->contextid);
                $path = $rootpath;
                $path[] = get_string('privacy:metadata:analytics:indicatorcalc', 'analytics');
                $path[] = $calculation->id;

                $indicator = \core_analytics\manager::get_indicator($calculation->indicator);
                $data = (object)[
                    'indicator' => $indicator::get_name()->out(),
                    'context' => $context->get_context_name(true, true),
                    'calculation' => $indicator->get_display_value($calculation->value),
                    'starttime' => transform::datetime($calculation->starttime),
                    'endtime' => transform::datetime($calculation->endtime),
                    'timecreated' => transform::datetime($calculation->timecreated),
                ];
                writer::with_context($context)->export_data($path, $data);
            }
            $indicatorcalculations->close();
        }

        // Analytics predictions.
        // Provided contexts are ignored as we export all user-related stuff.
        list($sql, $params) = self::analytics_prediction_actions_user_sql($userid, $modelids, $contextsql);
        $sql = "SELECT apa.*, ap.modelid, ap.contextid, $ctxfields" . $sql;
        $predictionactions = $DB->get_recordset_sql($sql, $params + $contextparams);
        foreach ($predictionactions as $predictionaction) {

            \context_helper::preload_from_record($predictionaction);
            $context = \context::instance_by_id($predictionaction->contextid);
            $path = $rootpath;
            $path[] = get_string('privacy:metadata:analytics:predictionactions', 'analytics');
            $path[] = $predictionaction->id;

            $data = (object)[
                'target' => $models[$predictionaction->modelid]->get_target()->get_name()->out(),
                'context' => $context->get_context_name(true, true),
                'action' => $predictionaction->actionname,
                'timecreated' => transform::datetime($predictionaction->timecreated),
            ];
            writer::with_context($context)->export_data($path, $data);
        }
        $predictionactions->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        $models = self::get_models_with_user_data();
        $modelids = array_keys($models);

        foreach ($models as $modelid => $model) {

            $idssql = "SELECT ap.id FROM {analytics_predictions} ap
                        WHERE ap.contextid = :contextid AND ap.modelid = :modelid";
            $idsparams = ['contextid' => $context->id, 'modelid' => $modelid];
            $predictionids = $DB->get_fieldset_sql($idssql, $idsparams);
            if ($predictionids) {
                list($predictionidssql, $params) = $DB->get_in_or_equal($predictionids, SQL_PARAMS_NAMED);

                $DB->delete_records_select('analytics_prediction_actions', "predictionid IN ($idssql)", $idsparams);
                $DB->delete_records_select('analytics_predictions', "id $predictionidssql", $params);
            }
        }

        // We delete them all this table is just a cache and we don't know which model filled it.
        $DB->delete_records('analytics_indicator_calc', ['contextid' => $context->id]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $userid = intval($contextlist->get_user()->id);

        $models = self::get_models_with_user_data();
        $modelids = array_keys($models);

        list ($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Analytics prediction actions.
        list($sql, $apaparams) = self::analytics_prediction_actions_user_sql($userid, $modelids, $contextsql);
        $sql = "SELECT apa.id " . $sql;

        $predictionactionids = $DB->get_fieldset_sql($sql, $apaparams + $contextparams);
        if ($predictionactionids) {
            list ($predictionactionidssql, $params) = $DB->get_in_or_equal($predictionactionids);
            $DB->delete_records_select('analytics_prediction_actions', "id {$predictionactionidssql}", $params);
        }

        foreach ($models as $modelid => $model) {

            $analyser = $model->get_analyser(['notimesplitting' => true]);

            // Analytics predictions.
            $joinusersql = $analyser->join_sample_user('ap');
            $sql = "SELECT DISTINCT ap.id FROM {analytics_predictions} ap
                      {$joinusersql}
                     WHERE u.id = :userid AND ap.modelid = :modelid AND ap.contextid {$contextsql}";

            $predictionids = $DB->get_fieldset_sql($sql, ['userid' => $userid, 'modelid' => $modelid] + $contextparams);
            if ($predictionids) {
                list($predictionidssql, $params) = $DB->get_in_or_equal($predictionids, SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_predictions', "id $predictionidssql", $params);
            }

            // Indicator calculations.
            $joinusersql = $analyser->join_sample_user('aic');
            $sql = "SELECT DISTINCT aic.id FROM {analytics_indicator_calc} aic
                      {$joinusersql}
                     WHERE u.id = :userid AND aic.sampleorigin = :analysersamplesorigin AND aic.contextid {$contextsql}";

            $params = ['userid' => $userid, 'analysersamplesorigin' => $analyser->get_samples_origin()] + $contextparams;
            $indicatorcalcids = $DB->get_fieldset_sql($sql, $params);
            if ($indicatorcalcids) {
                list ($indicatorcalcidssql, $params) = $DB->get_in_or_equal($indicatorcalcids, SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_indicator_calc', "id $indicatorcalcidssql", $params);
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $models = self::get_models_with_user_data();
        $modelids = array_keys($models);
        list($usersinsql, $baseparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);

        // Analytics prediction actions.
        list($sql, $apaparams) = self::analytics_prediction_actions_context_sql($context->id, $modelids, $usersinsql);
        $sql = "SELECT apa.id" . $sql;
        $predictionactionids = $DB->get_fieldset_sql($sql, $baseparams + $apaparams);

        if ($predictionactionids) {
            list ($predictionactionidssql, $params) = $DB->get_in_or_equal($predictionactionids);
            $DB->delete_records_select('analytics_prediction_actions', "id {$predictionactionidssql}", $params);
        }

        $baseparams['contextid'] = $context->id;

        foreach ($models as $modelid => $model) {
            $analyser = $model->get_analyser(['notimesplitting' => true]);

            // Analytics predictions.
            $joinusersql = $analyser->join_sample_user('ap');
            $sql = "SELECT DISTINCT ap.id
                      FROM {analytics_predictions} ap
                           {$joinusersql}
                     WHERE ap.contextid = :contextid
                       AND ap.modelid = :modelid
                       AND u.id {$usersinsql}";
            $params = $baseparams;
            $params['modelid'] = $modelid;
            $predictionids = $DB->get_fieldset_sql($sql, $params);

            if ($predictionids) {
                list($predictionidssql, $params) = $DB->get_in_or_equal($predictionids, SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_predictions', "id {$predictionidssql}", $params);
            }

            // Indicator calculations.
            $joinusersql = $analyser->join_sample_user('aic');
            $sql = "SELECT DISTINCT aic.id
                      FROM {analytics_indicator_calc} aic
                           {$joinusersql}
                     WHERE aic.contextid = :contextid
                       AND aic.sampleorigin = :analysersamplesorigin
                       AND u.id {$usersinsql}";
            $params = $baseparams;
            $params['analysersamplesorigin'] = $analyser->get_samples_origin();
            $indicatorcalcids = $DB->get_fieldset_sql($sql, $params);

            if ($indicatorcalcids) {
                list ($indicatorcalcidssql, $params) = $DB->get_in_or_equal($indicatorcalcids, SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_indicator_calc', "id $indicatorcalcidssql", $params);
            }
        }
    }

    /**
     * Returns a list of models with user data.
     *
     * @return \core_analytics\model[]
     */
    private static function get_models_with_user_data() {
        $models = \core_analytics\manager::get_all_models();
        foreach ($models as $modelid => $model) {
            $analyser = $model->get_analyser(['notimesplitting' => true]);
            if (!$analyser->processes_user_data()) {
                unset($models[$modelid]);
            }
        }
        return $models;
    }

    /**
     * Returns the sql query to query analytics_prediction_actions table by user ID.
     *
     * @param int $userid The user ID of the analytics prediction.
     * @param int[] $modelids Model IDs to include in the SQL.
     * @param string $contextsql Optional "in or equal" SQL to also query by context ID(s).
     * @return array sql string in [0] and params in [1].
     */
    private static function analytics_prediction_actions_user_sql($userid, $modelids, $contextsql = false) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal($modelids, SQL_PARAMS_NAMED);
        $sql = " FROM {analytics_predictions} ap
                  JOIN {context} ctx ON ctx.id = ap.contextid
                  JOIN {analytics_prediction_actions} apa ON apa.predictionid = ap.id
                  JOIN {analytics_models} am ON ap.modelid = am.id
                 WHERE apa.userid = :userid AND ap.modelid {$insql}";
        $params['userid'] = $userid;

        if ($contextsql) {
            $sql .= " AND ap.contextid $contextsql";
        }

        return [$sql, $params];
    }

    /**
     * Returns the sql query to query analytics_prediction_actions table by context ID.
     *
     * @param int $contextid The context ID of the analytics prediction.
     * @param int[] $modelids Model IDs to include in the SQL.
     * @param string $usersql Optional "in or equal" SQL to also query by user ID(s).
     * @return array sql string in [0] and params in [1].
     */
    private static function analytics_prediction_actions_context_sql($contextid, $modelids, $usersql = false) {
        global $DB;

        list($insql, $params) = $DB->get_in_or_equal($modelids, SQL_PARAMS_NAMED);
        $sql = " FROM {analytics_predictions} ap
                  JOIN {analytics_prediction_actions} apa ON apa.predictionid = ap.id
                 WHERE ap.contextid = :contextid
                   AND ap.modelid {$insql}";
        $params['contextid'] = $contextid;

        if ($usersql) {
            $sql .= " AND apa.userid {$usersql}";
        }

        return [$sql, $params];
    }
}
