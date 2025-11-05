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

namespace tool_mergeusers\local\merger;

use tool_mergeusers\local\merger\generic_table_merger;

/**
 * Grade grades specific implementation to handle qualification elements.
 *
 * @package    tool_mergeusers
 * @subpackage mergeusers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2025 Servei de Recursos Educatius (http://www.sre.urv.cat)
 */
class grade_grades_table_merger extends generic_table_merger {
    /**
     * Return empty array. It has no other tables rather than 'grade_grades' to process.
     *
     * @return array Empty list.
     */
    public function get_tables_to_skip(): array {
        return [];
    }

    /**
     * Generates an SQL query that selects records from a table based on the user field ID and other fields
     * that are part of the compound index. This query is used by the `mergeCompoundIndex` function to retrieve
     * the relevant records from the database.
     *
     * @param array $data Array containing the table name, `fromid`, and `toid` for the merging operation.
     * @param string $userfield The field name in the table that refers to the user ID.
     * @param string $otherfieldsstr A string representing the other fields in the compound index, separated by commas.
     * @return array Array with sql query and parameters to be used in.
     */
    protected function build_sql_query($data, $userfield, $otherfieldsstr): array {
        $sql = 'SELECT gg.id, gg.' . $userfield . ', ' . $otherfieldsstr . ', gi.itemtype, gg.finalgrade' .
            ' FROM {' . $data['tableName'] . '} gg' .
            ' JOIN {grade_items} gi ON gi.id = gg.itemid' .
            ' WHERE ' . $userfield . ' IN ( ?, ?)';

        return [$sql, [$data["fromid"], $data["toid"]]];
    }

    /**
     * Handles the case where both the current user and the new user have records in the compound index.
     * In such cases, the record associated with the current user is marked for removal to avoid conflicts.
     *
     * @param array $otherinfo The grouped records from the compound index for a specific combination of other fields.
     * @param array $gradedata The records retrieved from the database.
     * @param array $data The merging operation details, including `fromid`, `toid`, and table name.
     * @param array &$recordstomodify Array to store the IDs of records that need to be updated.
     * @param array &$idstoremove Array to store the IDs of records that need to be removed.
     */
    protected function process_duplicated_records_for_compound_index(
        array $conflictingrecords,
        array $result,
        array $data,
        array &$recordstomodify,
        array &$idstoremove,
    ): void {
        if (!isset($conflictingrecords[$data['toid']]) && !isset($conflictingrecords[$data['fromid']])) {
            return;
        }

        $gradeitemtoidismanual = $this->grade_item_is_manual($result[$conflictingrecords[$data['toid']]]->itemtype);
        $gradeitemfromidismanual = $this->grade_item_is_manual($result[$conflictingrecords[$data['fromid']]]->itemtype);

        $useridtoremove = $this->get_user_id_to_delete_on_conflicts($data['toid'], $data['fromid']);
        $useridtokeep = $this->get_user_id_to_keep_on_conflicts($data['toid'], $data['fromid']);
        // Process non-manual grade items.
        if (!$gradeitemtoidismanual && !$gradeitemfromidismanual) {
            $idstoremove[$conflictingrecords[$useridtoremove]] = $conflictingrecords[$useridtoremove];
            return;
        }

        // Process manual grade items.
        $finalgradetokeep = $result[$conflictingrecords[$useridtokeep]]->finalgrade;

        if ($finalgradetokeep != null) {
            // Whenever there is a grade for the user to keep, we keep it.
            $idstoremove[$conflictingrecords[$useridtoremove]] = $conflictingrecords[$useridtoremove];
        } else {
            // Otherwise, we keep the grade from the user to remove.
            // Both grades can be null, in which case we would have to delete always a record.
            $idstoremove[$conflictingrecords[$useridtokeep]] = $conflictingrecords[$useridtokeep];
        }
    }

    /**
     * Check if a grade item is type manual.
     *
     * @param string $gradeitemtype Grade item type.
     * @return bool True if it is manual, otherwise false.
     */
    private function grade_item_is_manual(string $gradeitemtype): bool {
        return $gradeitemtype == "manual";
    }
}
