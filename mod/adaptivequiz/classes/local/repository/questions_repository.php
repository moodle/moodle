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
 * A class to wrap all database queries which are specific to questions and their related data. Normally should contain
 * only static methods to call.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local\repository;

use coding_exception;
use core_question\local\bank\question_version_status;
use core_tag_tag;
use dml_exception;
use question_finder;
use stdClass;

final class questions_repository {
    /**
     * Counts all questions in the pool tagged as 'adaptive' with a certain difficulty level.
     *
     * @param int[] $qcategoryidlist A list of id of questions categories.
     * @param int $level Question difficulty which is contained in the question's tag.
     */
    public static function count_adaptive_questions_in_pool_with_level(array $qcategoryidlist, int $level): int {
        if (!$raw = question_finder::get_instance()->get_questions_from_categories($qcategoryidlist, '')) {
            return 0;
        }

        $questionstags = core_tag_tag::get_items_tags('core_question', 'question', array_keys($raw));

        // Filter 'non-adaptive' and level mismatching tags out.
        $questionstags = array_map(function(array $tags) use ($level) {
            return array_filter($tags, function(core_tag_tag $tag) use ($level) {
                return substr($tag->name, strlen(ADAPTIVEQUIZ_QUESTION_TAG)) === (string)$level;
            });
        }, $questionstags);

        // Filter empty tags arrays out.
        $questionstags = array_filter($questionstags, function(array $tags) {
            return !empty($tags);
        });

        return count($questionstags);
    }

    /**
     * @param int[] $tagidlist
     * @param int[] $categoryidlist
     * @return questions_number_per_difficulty[]
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function count_questions_number_per_difficulty(array $tagidlist, array $categoryidlist): array {
        global $DB;

        if (empty($tagidlist) || empty($categoryidlist)) {
            return [];
        }

        list($tagidlistsql, $tagidlistparam) = $DB->get_in_or_equal($tagidlist);
        list($categoryidlistsql, $categoryidlistparam) = $DB->get_in_or_equal($categoryidlist);

        $difficultyselect = $DB->sql_substr('t.name', strlen(ADAPTIVEQUIZ_QUESTION_TAG) + 1);
        $sql = "SELECT {$difficultyselect} AS difficultylevel, COUNT(*) AS questionsnumber
            FROM {tag} t
            JOIN {tag_instance} ti ON t.id = ti.tagid
            JOIN {question} q ON q.id = ti.itemid
            JOIN {question_versions} qv ON qv.questionid = q.id
            JOIN (
                SELECT questionbankentryid, MAX(version)
                FROM {question_versions}
                WHERE status = ?
                GROUP BY questionbankentryid
            ) questionlatestversion ON questionlatestversion.questionbankentryid = qv.questionbankentryid
            JOIN {question_bank_entries} qbe ON qbe.id = questionlatestversion.questionbankentryid
            WHERE ti.itemtype = ?
            AND ti.tagid {$tagidlistsql}
            AND qbe.questioncategoryid {$categoryidlistsql}
            GROUP BY t.name";

        $params = array_merge([question_version_status::QUESTION_STATUS_READY, 'question'], $tagidlistparam,
            $categoryidlistparam);

        $records = $DB->get_records_sql($sql, $params);
        if (empty($records)) {
            return [];
        }

        $return = [];
        foreach ($records as $record) {
            $return[] = new questions_number_per_difficulty($record->difficultylevel, $record->questionsnumber);
        }

        return $return;
    }

    /**
     * @param int[] $tagidlist
     * @param int[] $categoryidlist
     * @param int[] $excludequestionidlist
     * @return stdClass[] A list of records from {question} table, the fields are id, name.
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function find_questions_with_tags(
        array $tagidlist,
        array $categoryidlist,
        array $excludequestionidlist
    ): array {
        global $DB;

        if (empty($tagidlist) || empty($categoryidlist)) {
            return [];
        }

        $params = [];

        list($tagswhere, $tempparam) = $DB->get_in_or_equal($tagidlist, SQL_PARAMS_NAMED, 'tagids');
        $params += $tempparam;

        list($categorywhere, $tempparam) = $DB->get_in_or_equal($categoryidlist, SQL_PARAMS_NAMED, 'qcatids');
        $params += $tempparam;

        $excludequestionsclause = '';
        if (!empty($excludequestionidlist)) {
            list($excludequestionssql, $tempparam) = $DB->get_in_or_equal($excludequestionidlist, SQL_PARAMS_NAMED, 'excqids',
                false);
            $excludequestionsclause = "AND q.id {$excludequestionssql}";
            $params += $tempparam;
        }

        $sql = "SELECT q.id, q.name
            FROM {question} q
            JOIN {tag_instance} ti ON q.id = ti.itemid
            JOIN {question_versions} qv ON qv.questionid = q.id
            JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
            JOIN (
                SELECT qv.questionid
                FROM {question_versions} qv
                JOIN (
                    SELECT questionbankentryid, MAX(version) latestversion
                    FROM {question_versions}
                    WHERE status = :questionstatus
                    GROUP BY questionbankentryid
                ) qbelv ON qv.version = qbelv.latestversion AND qv.questionbankentryid = qbelv.questionbankentryid
            ) qlv ON q.id = qlv.questionid
            WHERE ti.itemtype = :itemtype
              AND ti.tagid {$tagswhere}
              AND qbe.questioncategoryid {$categorywhere}
                  {$excludequestionsclause}
            ORDER BY q.id ASC";

        $params += ['questionstatus' => question_version_status::QUESTION_STATUS_READY, 'itemtype' => 'question'];

        if (!$records = $DB->get_records_sql($sql, $params)) {
            return [];
        }

        return $records;
    }
}
