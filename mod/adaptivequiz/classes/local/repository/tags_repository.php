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
 * A class to wrap all database queries which are specific to tags and their related data. Normally should contain
 * only static methods to call.
 *
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_adaptivequiz\local\repository;

use coding_exception;
use dml_exception;
use stdClass;

final class tags_repository {
    /**
     * @param string[] $tagnames
     * @return array Map of question difficulty level and tag id, same as what
     * {@link moodle_database::get_records_menu()} would return.
     * @throws dml_exception
     * @throws coding_exception
     */
    public static function get_question_level_to_tag_id_mapping_by_tag_names(array $tagnames): array {
        global $DB;

        list($tagnameselect, $tagnameparams) = $DB->get_in_or_equal($tagnames);

        $sql = 'SELECT t.id, ' . $DB->sql_substr('t.name', strlen(ADAPTIVEQUIZ_QUESTION_TAG) + 1) . ' AS level
             FROM {tag} t
             JOIN {tag_instance} ti ON t.id = ti.tagid AND ti.itemtype = ?
             WHERE t.name ' . $tagnameselect . '
             GROUP BY t.id';
        $params = array_merge(['question'], $tagnameparams);

        if (!$records = $DB->get_records_sql($sql, $params)) {
            return [];
        }

        return array_flip(
            array_map(function(stdClass $record): int {
                return $record->level;
            }, $records)
        );
    }

    /**
     * @param string[] Array of tag names.
     * @return int[] Tag id list.
     * @throws dml_exception
     * @throws coding_exception
     */
    public static function get_tag_id_list_by_tag_names(array $tagnames): array {
        global $DB;

        list($tagnameselect, $tagnameparams) = $DB->get_in_or_equal($tagnames);

        $sql = 'SELECT t.id
                  FROM {tag} t
                  JOIN {tag_instance} ti ON t.id = ti.tagid AND ti.itemtype = ?
                 WHERE t.name ' . $tagnameselect . '
              GROUP BY t.id
              ORDER BY t.id';
        $params = array_merge(['question'], $tagnameparams);

        return ($fieldset = $DB->get_fieldset_sql($sql, $params)) ? $fieldset : [];
    }
}
