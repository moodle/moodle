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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class system_tables_repository extends base_tables_repository {

    /**
     * Exclude tables from the list of tables to be processed.
     *
     * @param $dbtables
     * @return mixed
     */
    public static function exclude_tables($dbtables) {

        $tablestodelete = self::get_excluded_tables($dbtables);

        if (count($tablestodelete)) {
            foreach ($tablestodelete as $key) {
                unset($dbtables[$key]);
            }
        }

        return $dbtables;
    }

    /**
     * Retrieve tables to exclude from the list of tables to be processed.
     *
     * @param $dbtables
     * @return array
     */
    public static function get_excluded_tables($dbtables) {
        $tablestodelete = [];

        foreach (self::get_defined_tables() as $table) {
            self::validate_single_table($dbtables, $table, $tablestodelete);
            self::validate_table_by_template($dbtables, $table, $tablestodelete);
        }

        return $tablestodelete;
    }

    /**
     * Validate single table to exclude from the list.
     *
     * @param $dbtables
     * @param $table
     * @param $keystodelete
     */
    private static function validate_single_table($dbtables, $table, &$keystodelete) {
        if (($key = array_search($table, $dbtables)) !== false) {
            $keystodelete[$key] = $table;
        }
    }

    /**
     * Validate table by template to exclude from the list.
     *
     * @param $dbtables
     * @param $table
     * @param $keystodelete
     */
    private static function validate_table_by_template($dbtables, $table, &$keystodelete) {
        if (stristr($table, '*')) {
            array_walk($dbtables, function($item, $key, $tblname) use (&$keystodelete) {
                if (strpos($item, $tblname) === 0) {
                    $keystodelete[$key] = $item;
                }
            }, str_replace('*', '', $table));
        }
    }

    /**
     * Tables list to exclude from the list of tables to be processed.
     *
     * @return array
     */
    protected static function get_defined_tables() {
        return [
            'adminpresets*',
            'analytics_*',
            'assign_overrides',
            'assign_plugin_config',
            'assign_user_mapping',
            'assignment*',
            'assignfeedback*',
            'assignsubmission*',
            'auth_*',
            'badge_backpack',
            'badge_backpack_oauth2',
            'badge_criteria',
            'badge_criteria_met',
            'badge_criteria_param',
            'badge_endorsement',
            'badge_external',
            'badge_external_backpack',
            'badge_external_identifier',
            'backup_*',
            'block*',
            'blog*',
            'cache_*',
            'capabilities',
            'config*',
            'context_temp',
            'contentbank_content',
            'enrol_*',
            'editor_*',
            'events_*',
            'event_subscriptions',
            'external_*',
            'favourite',
            'forum_digests',
            'forum_discussion_subs',
            'forum_grades',
            'forum_queue',
            'forum_read	',
            'forum_subscriptions',
            'forum_track_prefs',
            'files*',
            'file_conversion',
            'filter_*',
            'grade_categories*',
            'grade_grades_history',
            'grade_import*',
            'grade_items_history',
            'grade_outcomes*',
            'grade_settings',
            'grading*',
            'h5p_libraries*',
            'h5p_library*',
            'h5p_contents_libraries',
            'infected_files',
            'license',
            'local_intelliboard*',
            'local_intellidata*',
            'lock_db',
            'log',
            'log_display',
            'log_queries',
            'lti_types_config',
            'lti_tool*',
            'lti_access_tokens',
            'ltiservice*',
            'messageinbound*',
            'mnet_*',
            'mnetservice_*',
            'oauth_*',
            'oauth2_*',
            'paygw*',
            'payment_*',
            'payments',
            'portfolio_*',
            'rating',
            'registration_hubs',
            'reportbuilder_*',
            'repository*',
            'sessions',
            'stats*',
            'task*',
            'qtype*',
            'question_calculated*',
            'question_dataset*',
            'question_ddwtos',
            'question_gapselect',
            'question_hints',
            'question_multianswer',
            'question_numerical*',
            'question_response*',
            'question_statistics',
            'question_truefalse',
            'question_usages',
            'quiz_feedback',
            'quiz_grades',
            'quiz_overrides',
            'quiz_overview_regrades',
            'quiz_reports',
            'quiz_sections',
            'quiz_slot_tags',
            'quiz_statistics',
            'quizaccess*',
            'role_allow_*',
            'role_capabilities',
            'role_context_levels',
            'role_names',
            'scorm_seq_*',
            'tool_brickfield_*',
            'tool_customlang*',
            'tool_dataprivacy*',
            'tool_monitor_*',
            'tool_recyclebin_*',
            'tool_usertours_*',
            'upgrade_log',
            'user_password_*',
            'user_preferences',
            'user_private_key',
            'search_index_requests',
            'search_simpledb_index',
        ];
    }
}
