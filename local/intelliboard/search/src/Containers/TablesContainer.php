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
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace Containers;

use DataExtractor;

class TablesContainer extends BaseContainer {

    protected static $tables;
    protected static $joins;
    protected static $relations;

    protected static function init() {

        static::$tables = array(
            1  => array("name" => "u",  "sql" => "{user}"),
            2  => array("name" => "ue", "sql" => "{user_enrolments}"),
            3  => array("name" => "e",  "sql" => "{enrol}"),
            4  => array("name" => "c",  "sql" => "{course}"),
            5  => array("name" => "gi", "sql" => "{grade_items}"),
            6  => array("name" => "g",  "sql" => "{grade_grades}"),
            7  => array("name" => "l", "sql" => "{lesson}"),
            8  => array("name" => "cat","sql" => "{course_categories}"),
            9  => array("name" => "lit1", "sql" => "{local_intelliboard_tracking}"),
            10 => array("name" => "s", "sql" => "{scorm}"),
            11 => array("name" => "sst", "sql" => "{scorm_scoes_track}"),
            12 => array("name" => "lsg", "sql" => "{lesson_grades}"),
            13 => array("name" => "lp", "sql" => "{lesson_pages}"),
            14 => array("name" => "cm", "sql" => "{course_modules}"),
            15 => array("name" => "m", "sql" => "{modules}"),
            16 => array("name" => "q",  "sql" => "{quiz}"),
            17 => array("name" => "qa",  "sql" => "{quiz_attempts}"),
            18 => array("name" => "fl",  "sql" => "{files}"),
            19 => array("name" => "co",  "sql" => "{cohort}"),
            20 => array("name" => "chm",  "sql" => "{cohort_members}"),
            21 => array("name" => "cmc",  "sql" => "{course_modules_completion}"),
            22 => array("name" => "a",  "sql" => "{assign}"),
            23 => array("name" => "ass", "sql" => "{assign_submission}"),
            24 => array("name" => "b",  "sql" => "{badge}"),
            25 => array("name" => "bi",  "sql" => "{badge_issued}"),
            26 => array("name" => "ctx",  "sql" => "{context}"),
            27 => array("name" => "ra",  "sql" => "{role_assignments}"),
            28 => array("name" => "la",  "sql" => "{lesson_attempts}"),
            29 => array("name" => "llts",  "sql" => "{local_intelliboard_totals}"),
            30 => array("name" => "log", "sql" => "{logstore_standard_log}"),
            31 => array("name" => "asg", "sql" => "{assign_grades}"),
            32 => array("name" => "ula", "sql" => "{user_lastaccess}"),
            33 => array("name" => "gp", "sql" => "{groups}"),
            34 => array("name" => "gpm", "sql" => "{groups_members}"),
            35 => array("name" => "cc", "sql" => "{course_completions}"),
            36 => array("name" => "lil", "sql" => "{local_intelliboard_logs}"),
            37 => array("name" => "lit", "sql" => "{local_intelliboard_tracking}"),
            38 => array("name" => "f", "sql" => "{forum}"),
            39 => array("name" => "fd", "sql" => "{forum_discussions}"),
            40 => array("name" => "fp", "sql" => "{forum_posts}"),
            41 => array("name" => "sw", "sql" => "{survey}"),
            42 => array("name" => "r", "sql" => "{role}"),
            43 => array("name" => "qs", "sql" => "{quiz_slots}"),
            44 => array("name" => "ct", "sql" => "{certificate}"),
            45 => array("name" => "cti", "sql" => "{certificate_issues}"),
            46 => array("name" => "t", "sql" => "{tag}"),
            47 => array("name" => "lid", "sql" => "{local_intelliboard_details}"),
            48 => array("name" => "days", "sql" => "(SELECT 0 as day UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6)"),
            49 => array("name" => "mnt", "sql" => "(SELECT 1 as month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12)"),
            50 => array("name" => "hrs", "sql" => "(SELECT 0 as hour UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23)"),
            51 => array("name" => "uid", "sql" => "{user_info_data}"),
            52 => array("name" => "uif", "sql" => "{user_info_field}"),
            53 => array("name" => "swa", "sql" => "{survey_answers}"),
            54 => array("name" => "qs", "sql" => "{questionnaire}"),
            55 => array("name" => "qsa", "sql" => "{questionnaire_attempts}"),
            56 => array("name" => "qt", "sql" => "{question}"),
            57 => array("name" => "qtat", "sql" => "{question_attempts}"),
            58 => array("name" => "qtan", "sql" => "{question_answers}"),
            59 => array("name" => "cmp", "sql" => "{competency}"),
            60 => array("name" => "cmpc", "sql" => "{competency_usercompcourse}"),
            61 => array("name" => "qas", "sql" => "{question_attempt_steps}"),
            62 => array("name" => "lia", "sql" => "{local_intelliboard_assign}"),
            63 => array("name" => "cs", "sql" => "{course_sections}"),
            64 => array("name" => "cri", "sql" => "{course_completion_criteria}"),
            65 => array("name" => "go", "sql" => "{grade_outcomes}"),
            66 => array("name" => "sci", "sql" => "{scale}"),
            67 => array("name" => "res", "sql" => "{resource}"),
            68 => array("name" => "w", "sql" => "{wiki}"),
            69 => array("name" => "ch", "sql" => "{chat}"),
            70 => array("name" => "cp", "sql" => "{config_plugins}"),
            71 => array("name" => "up", "sql" => "{upgrade_log}"),
            72 => array("name" => "up", "sql" => "{tag_instance}"),
            73 => array("name" => "icrel", "sql" => "{local_intellicart_relations}"),
            74 => array("name" => "licl", "sql" => "{local_intellicart_logs}"),
            75 => array("name" => "lics", "sql" => "{local_intellicart_seats}"),
            76 => array("name" => "liu", "sql" => "{local_intellicart_users}"),
            77 => array("name" => "licv", "sql" => "{local_intellicart_vendors}"),
            78 => array("name" => "licvr", "sql" => "{local_intellicart_vrelations}"),
        );

        static::$joins = array(
            1 => 'LEFT',
            2 => 'INNER',
            3 => 'RIGHT'
        );

    }

    public static function get($selected, DataExtractor $extractor, $params = array()) {
        static::init();

        $joins = static::$joins;
        $relations = static::$relations;

        $result = array_map(function($table) use ($joins, $relations, $extractor) {

            if (is_array($table['id'])) {
                $chosen = array('sql' => '(' . $extractor->construct($table['id']) . ')');
            } else {
                $chosen = static::getById($table['id'], $extractor);
            }

            if (isset($table['name'])) {
                $chosen['name'] = $table['name'];
            }

            if (isset($table['lead'])) {
                $chosen['lead'] = $table['lead'];
            } else {
                $chosen['join'] = isset($table['join'])? $joins[$table['join']] : $joins[1];
                $chosen['relation'] = FiltersContainer::get($table['relation'], $extractor);
            }

            return $chosen;
        }, $selected);

        return $result;
    }


    public static function construct($tables, DataExtractor $extractor, $params = array()) {
        $leadTables = array();
        $joinTables = array();

        foreach($tables as $table) {

            if(!empty($table['lead'])) {
                $leadTables[] = $table['sql'] . ' AS ' . $table['name'];
            } else {
                $current = ' ' . $table['join'] . ' JOIN ' . $table['sql'] . ' AS ' . $table['name'] . ' ON ';
                $current .= FiltersContainer::construct($table['relation'], $extractor);

                $joinTables[] = $current;
            }

        }

        $sep = $extractor->getSeparator();
        return implode(',' . $sep . ' ', $leadTables) . $sep . implode($joinTables);
    }


    public static function getById($id) {
        static::init();

        return static::$tables[$id];
    }

}
