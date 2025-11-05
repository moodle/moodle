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
 * Generic implementation of the table_merger.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local\merger;

use coding_exception;
use dml_exception;
use Exception;

/**
 * Generic implementation of a table_merger.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generic_table_merger implements table_merger {
    /** @var int Number of records to process at a time. */
    const CHUNK_SIZE = 500;

    /** @var bool true to keep de user.id from the user to keep; false to keep the user.id from the user to remove. */
    protected bool $newidtomaintain;

    /**
     * Builds the instance.
     *
     * @throws dml_exception
     */
    public function __construct() {
        $this->newidtomaintain = (bool) (int) get_config('tool_mergeusers', 'uniquekeynewidtomaintain');
    }

    /**
     * The generic table merger does not exclude any other table from processing by other table mergers.
     *
     * @return array an empty list.
     */
    public function get_tables_to_skip(): array {
        return [];
    }

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errors messages and $logs for the actions performed.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $logs list of action performed.
     * @param array $errors list of error messages.
     */
    public function merge($data, &$logs, &$errors): void {
        foreach ($data['userFields'] as $fieldname) {
            $recordstoupdate = $this->get_records_to_be_updated($data, $fieldname);
            if (count($recordstoupdate) == 0) {
                // This userid is not present in these table and field names.
                continue;
            }

            // Get the 'id' field from the resultset.
            $keys = array_keys($recordstoupdate);
            $recordstomodify = array_combine($keys, $keys);

            if (isset($data['compoundIndex'])) {
                $this->merge_compound_index(
                    $data,
                    $fieldname,
                    $this->get_other_fields_on_compound_index($fieldname, $data['compoundIndex']),
                    $recordstomodify,
                    $logs,
                    $errors
                );
            }

            $this->update_all_records($data, $recordstomodify, $fieldname, $logs, $errors);
        }
    }

    /*     * ****************** UTILITY METHODS ***************************** */

    /**
     * Both users may appear in the same table under the same database index or so,
     * making some kind of conflict on Moodle and the database model. For simplicity, we always
     * use "compound index" to refer to it below.
     *
     * The merging operation for these cases are treated as follows:
     *
     * Possible scenarios:
     *
     * <ul>
     *   <li>$currentId only appears in a given compound index: we have to update it.</li>
     *   <li>$newId only appears in a given compound index: do nothing, skip.</li>
     *   <li>$currentId and $newId appears in the given compount index: delete the record for the $currentId.</li>
     * </ul>
     *
     * This function extracts the records' ids that have to be updated to the $newId, appearing only the
     * $currentId, and deletes the records for the $currentId when both appear.
     *
     * @param array $data array with the details of merging
     * @param string $userfield table's field name that refers to the user id.
     * @param array $otherfields table's field names that refers to the other members of the coumpund index.
     * @param array $recordstomodify array with current $table's id to update.
     * @param array $logs Array where to append the list of actions done.
     * @param array $errors Array where to append any error occurred.
     * @return void
     * @throws dml_exception
     */
    protected function merge_compound_index(
        array $data,
        string $userfield,
        array $otherfields,
        array &$recordstomodify,
        array &$logs,
        array &$errors,
    ): void {
        global $DB;

        $otherfieldsstr = implode(', ', $otherfields);
        [$sql, $params] = $this->build_sql_query($data, $userfield, $otherfieldsstr);
        $result = $DB->get_records_sql($sql, $params);

        $itemarr = [];
        $idstoremove = [];
        foreach ($result as $id => $resobj) {
            $keyfromother = [];
            foreach ($otherfields as $of) {
                $keyfromother[] = $resobj->$of;
            }
            $keyfromotherstr = implode('-', $keyfromother);
            $itemarr[$keyfromotherstr][$resobj->$userfield] = $id;
        }

        $this->find_ids_to_update_and_remove($itemarr, $result, $data, $recordstomodify, $idstoremove);

        unset($result);
        unset($itemarr);
        // We know that idstoremove have always to be removed and NOT to be updated.
        foreach ($idstoremove as $id) {
            if (isset($recordstomodify[$id])) {
                unset($recordstomodify[$id]);
            }
        }

        $this->clean_records_on_compound_index($data, $idstoremove, $logs, $errors);

        unset($idstoremove);
        unset($sql);
    }

    /**
     * Generates an SQL query that selects records from a table based on the user field ID.
     *
     * This method can be overriden by subclasses to adapt the SQL query to their needs.
     *
     * @param array $data Array containing the table name, `fromid`, and `toid` for the merging operation.
     * @param string $userfield The field name in the table that refers to the user ID.
     * @param string $otherfieldsstr A string representing the other fields in the compound index, separated by commas.
     * @return array Array with the form [$sql, $params] to be used.
     */
    protected function build_sql_query(array $data, string $userfield, string $otherfieldsstr): array {
        $sql = 'SELECT id, ' . $userfield . ', ' . $otherfieldsstr .
            ' FROM {' . $data['tableName'] . '} ' .
            ' WHERE ' . $userfield . ' IN ( ?, ?)';
        return [$sql, [$data['fromid'], $data['toid']]];
    }

    /**
     * Finds the records to update and remove from a compound index.
     *
     * This method can be overridden by subclasses to adapt the logic to their needs.
     *
     * At current implementation, $result is not used, but it is provided for subclasses
     * just in case they have extra detail returned from self::build_sql_query() implementation.
     *
     * @param array $itemarr The grouped records from the compound index for a specific combination of other fields.
     * @param array $result The records retrieved from the database.
     * @param array $data The merging operation details, including `fromid`, `toid`, and table name.
     * @param array &$recordstomodify Array to store the IDs of records that need to be updated.
     * @param array &$idstoremove Array to store the IDs of records that need to be removed.
     * @return void
     */
    protected function find_ids_to_update_and_remove(
        array $itemarr,
        array $result,
        array $data,
        array &$recordstomodify,
        array &$idstoremove,
    ): void {
        foreach ($itemarr as $otherinfo) {
            // If and only if we have only one result, and it is from the current user => update record.
            if (count($otherinfo) == 1) {
                if (isset($otherinfo[$data['fromid']])) {
                    $recordstomodify[$otherinfo[$data['fromid']]] = $otherinfo[$data['fromid']];
                }
            } else {
                $this->process_duplicated_records_for_compound_index(
                    $otherinfo,
                    $result,
                    $data,
                    $recordstomodify,
                    $idstoremove
                );
            }
        }
    }

    /**
     * Prevents database and PHP processing errors due to multiple records for the same compound index.
     *
     * To do so, it removes the records for one of the users, either the user to keep or the user to remove,
     * depending on the configuration setting 'uniquekeynewidtomaintain'.
     *
     * In current implementation, there is only need to check whether to delete some records.
     * However, the method signature provides also the possibility to update records,
     * just in case a subclass needs to do so.
     *
     * @param array $conflictingrecords The grouped records from the compound index for a specific combination of other fields.
     * @param array $result The records retrieved from the database.
     * @param array $data The merging operation details, including `fromid`, `toid`, and table name.
     * @param array &$recordstomodify Array to store the IDs of records that need to be updated.
     * @param array &$idstoremove Array to store the IDs of records that need to be removed.
     * @return void
     */
    protected function process_duplicated_records_for_compound_index(
        array $conflictingrecords,
        array $result,
        array $data,
        array &$recordstomodify,
        array &$idstoremove,
    ): void {
        // Both users appear in the group.
        // Confirm both records exist, preventing problems from inconsistent data in database.
        if (isset($conflictingrecords[$data['toid']]) && isset($conflictingrecords[$data['fromid']])) {
            $useridtoclean = $this->get_user_id_to_delete_on_conflicts($data['toid'], $data['fromid']);
            $idstoremove[$conflictingrecords[$useridtoclean]] = $conflictingrecords[$useridtoclean];
        }
    }

    /**
     * Processes accordingly the cleaning up of records after a compound index is already processed.
     *
     * This implementation execute an SQL DELETE of all $idsToRemove. Subclasses may redefine this
     * behavior accordingly.
     *
     * @param array $data array with details of merging.
     * @param array $idstoremove array with ids of records to delete.
     * @param array $logs array of actions being performed for merging.
     * @param array $errors array with found errors while merging users' data.
     * @return void
     */
    protected function clean_records_on_compound_index(
        array $data,
        array $idstoremove,
        array &$logs,
        array &$errors,
    ): void {
        if (empty($idstoremove)) {
            return;
        }

        $chunks = array_chunk($idstoremove, self::CHUNK_SIZE);
        foreach ($chunks as $someidstoremove) {
            $this->clean_records($data, $someidstoremove, $logs, $errors);
        }
    }

    /**
     * Finally remove the records provided their $idstoremove.
     *
     * It is expected not to be used directly, but only by self::clean_records_on_compound_index().
     *
     * @param array $data array with details of merging.
     * @param array $idstoremove list of record ids to remove.
     * @param array $logs list of performed actions.
     * @param array $errors list of error messages.
     * @return void
     * @see self::clean_records_on_compound_index()
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function clean_records(
        array $data,
        array $idstoremove,
        array &$logs,
        array &$errors,
    ): void {
        global $CFG, $DB;

        if (empty($idstoremove)) {
            return;
        }

        $tablename = $CFG->prefix . $data['tableName'];
        $idsgobyebye = implode(', ', $idstoremove);
        $sql = 'DELETE FROM ' . $tablename . ' WHERE id IN (' . $idsgobyebye . ')';

        if ($DB->execute($sql)) {
            $logs[] = $sql;
        } else {
            // An error occured during DB query.
            $errors[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) . ': ' .
                $DB->get_last_error();
        }
        unset($idsgobyebye);
    }

    /**
     * Updates the table, replacing the user.id for the $data['toid'] on all
     * records specified by the ids on $recordsToModify.
     *
     * @param array $data array with details of merging.
     * @param array $recordstomodify list of record ids to update with $toid.
     * @param string $fieldname field name of the table to update.
     * @param array $logs list of performed actions.
     * @param array $errors list of error messages.
     * @return void
     */
    protected function update_all_records(
        array $data,
        array $recordstomodify,
        string $fieldname,
        array &$logs,
        array &$errors,
    ): void {
        if (count($recordstomodify) == 0) {
            return;
        }

        $chunks = array_chunk($recordstomodify, self::CHUNK_SIZE);
        foreach ($chunks as $chunk) {
            $this->update_records($data, $chunk, $fieldname, $logs, $errors);
        }
    }

    /**
     * Update the list of records provided.
     *
     * @param array $data array with details of merging.
     * @param array $recordstomodify list of record ids to update with $toid.
     * @param string $fieldname field name of the table to update.
     * @param array $logs list of performed actions.
     * @param array $errors list of error messages.
     * @return void
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function update_records(
        array $data,
        array $recordstomodify,
        string $fieldname,
        array &$logs,
        array &$errors,
    ): void {
        global $CFG, $DB;
        $tablename = $CFG->prefix . $data['tableName'];
        $idstring = implode(', ', $recordstomodify);
        $updaterecords = "UPDATE " . $tablename . ' ' .
            " SET " . $fieldname . " = '" . $data['toid'] .
            "' WHERE " . self::PRIMARY_KEY . " IN (" . $idstring . ")";

        try {
            if (!$DB->execute($updaterecords)) {
                $errors[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                    ': ' . $DB->get_last_error();
            }
            $logs[] = $updaterecords;
        } catch (Exception $e) {
            // If we get here, we have found a non-informed unique index on a user.id related column.
            // Therefore, there must be only a single record from one or other user.
            $useridtoclean = $this->get_user_id_to_delete_on_conflicts($data['toid'], $data['fromid']);
            $deleterecord = "DELETE FROM " . $tablename .
                " WHERE " . $fieldname . " = '" . $useridtoclean . "'";

            if (!$DB->execute($deleterecord)) {
                $errors[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) .
                    ': ' . $DB->get_last_error();
            }
            $logs[] = $deleterecord;
        }
    }

    /**
     * Informs the user.id from which records have to be deleted when conflicting
     * records arises.
     *
     * @param int $toid user.id from user to keep.
     * @param int $fromid user.id from user to remove.
     * @return int user.id from which records have to be deleted.
     */
    protected function get_user_id_to_delete_on_conflicts(int $toid, int $fromid): int {
        return $this->newidtomaintain ? $fromid : $toid;
    }

    /**
     * Informs the user.id from which records have to be kept when conflicting
     * records arises.
     *
     * @param int $toid user.id from user to keep.
     * @param int $fromid user.id from user to remove.
     * @return int user.id from which records have to be kept.
     */
    protected function get_user_id_to_keep_on_conflicts(int $toid, int $fromid): int {
        return $this->newidtomaintain ? $toid : $fromid;
    }

    /**
     * Gets the fields name on a compound index case, excluding the given $userField.
     * Therefore, if there are multiple user-related fields in a compound index,
     * return the rest of the column names except the given $userField. Otherwise,
     * it returns simply the 'otherfields' array from the $compoundIndex definition.
     *
     * @param string $userfield current user-related field being analyized.
     * @param array $compoundindex related config data for the compound index.
     * @return array an array with the other field names of the compound index.
     */
    protected function get_other_fields_on_compound_index(string $userfield, array $compoundindex) {
        if (count($compoundindex['userfield']) > 1) {
            // We can alternate column names when both fields are user-related.
            $all = array_merge($compoundindex['userfield'], $compoundindex['otherfields']);
            $all = array_flip($all);
            unset($all[$userfield]);

            return array_flip($all);
        }

        // Default behavior.
        return $compoundindex['otherfields'];
    }

    /**
     * List the records candidate for being updated.
     *
     * @param array $data detail of merging
     * @param string $fieldName field name to look for the user.id from the user to remove.
     * @return array list of matching records' ids.
     * @throws dml_exception
     */
    protected function get_records_to_be_updated($data, $fieldname) {
        global $DB;

        return $DB->get_records_sql("SELECT " . self::PRIMARY_KEY .
            " FROM {" . $data['tableName'] . "} WHERE " .
            $fieldname . " = '" . $data['fromid'] . "'");
    }
}
