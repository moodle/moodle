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
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

use local_intellidata\services\datatypes_service;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class required_tables_repository extends base_tables_repository {

    /**
     * Exclude tables.
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
     * Get excluded tables.
     *
     * @param $dbtables
     * @return array
     */
    public static function get_excluded_tables($dbtables) {
        $tablestodelete = [];

        foreach (self::get_defined_tables() as $table) {
            self::validate_single_table($dbtables, $table, $tablestodelete);
        }

        return $tablestodelete;
    }

    /**
     * Validate single table.
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
     * Get defined tables.
     *
     * @return string[]
     */
    public static function get_defined_tables() {

        $tables = [];

        foreach (datatypes_service::get_required_datatypes() as $datatype) {
            $migration = datatypes_service::init_migration($datatype, null, false);

            if ($migration instanceof \local_intellidata\entities\migration) {
                $tables[$migration->table] = $migration->table;
            }
        }

        return $tables;
    }

    /**
     * Get tables fields.
     *
     * @return array
     */
    public static function get_tables_fields() {
        $entities = [];

        foreach (datatypes_service::get_required_datatypes() as $datatype) {

            $entityclass = datatypes_service::get_datatype_entity_class($datatype['entity']);
            $entityfields = $entityclass::properties_definition();

            $exportlogrepository = new export_log_repository();
            $exportlog = $exportlogrepository->get_datatype_export_log($datatype['name']);

            $entityfields['crud'] = [
                'type' => PARAM_TEXT,
                'description' => 'Event crud.',
                'default' => 'с',
                'null' => false,
            ];

            $entities[$datatype['name']] = [
                'name' => $datatype['name'],
                'export' => (bool)$exportlog,
                'fields' => $entityfields,
            ];
        }

        return $entities;
    }

    /**
     * Return required native tables list.
     *
     * @return string[]
     */
    public static function get_required_native_tables() {
        return [
            'competency', 'competency_usercomp', 'competency_coursecomp',
            'competency_usercompcourse', 'competency_modulecomp', 'competency_plan',
            'competency_usercompplan', 'competency_framework', 'scale',

            // For 'tenants_' unified tables.
            'tenant', 'tool_tenant', 'tool_tenant_user',

            // For 'survey' unified table.
            'questionnaire', 'questionnaire_question',
            'questionnaire_question_type', 'questionnaire_response', 'questionnaire_response_bool',
            'questionnaire_response_date', 'questionnaire_response_other', 'questionnaire_response_rank',
            'questionnaire_response_text', 'questionnaire_resp_single', 'questionnaire_resp_multiple',
            'questionnaire_quest_choice', 'questionnaire_survey', 'feedback', 'feedback_item',
            'feedback_value', 'feedback_completed', 'survey', 'survey_questions', 'survey_answers',

            // For 'course_custom_' unified tables.
            'customfield_field', 'customfield_data', 'customfield_category',
            'course_info_field', 'course_info_data',

            // For 'fact_job_assignments_as' unified table.
            'tool_organisation_department', 'tool_organisation_position', 'tool_organisation_job',
            'job_assignment', 'org_framework', 'org', 'pos',

            'assign_user_flags', 'roleassignments', 'question_categories', 'user_lastaccess',
            'org_type_info_field', 'org_type_info_data',
            'course_completion_criteria',

            // For 'conversations...' unified table.
            'messages', 'message_conversation_members', 'message_user_actions',
        ];
    }
}
