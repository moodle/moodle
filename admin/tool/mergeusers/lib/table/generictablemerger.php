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
 * Generic implementation of the TableMerger.
 *
 * @package    tool
 * @subpackage mergeusers
 * @author     Jordi Pujol-Ahulló <jordi.pujol@urv.cat>,  SREd, Universitat Rovira i Virgili
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Generic implementation of a TableMerger
 *
 * @author jordi
 */
class GenericTableMerger implements TableMerger
{
    /**
     * Sets that in case of conflict, data related to new user is kept.
     * Otherwise (when false), data related to old user is kept.
     * @var int
     */
    protected $newidtomaintain;

    public function __construct()
    {
        $this->newidtomaintain = get_config('uniquekeynewidtomaintain', 'tool_mergeusers');
    }

    /**
     * The given TableMerger can assist the merging of the users in
     * a table, but afecting to multiple tables. If so, return an
     * array with the list of table names to skip.
     *
     * @return array List of database table names without the $CFG->prefix.
     * Returns an empty array when nothing to do.
     */
    public function getTablesToSkip()
    {
        return array(); // empty array when doing nothing.
    }

    /**
     * Merges the records related to the given users given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $actionLog list of action performed.
     * @param array $errorMessages list of error messages.
     */
    public function merge($data, &$actionLog, &$errorMessages)
    {
        global $CFG, $DB;

        foreach ($data['userFields'] as $fieldName) {
            $recordsToUpdate = $DB->get_records_sql("SELECT " . self::PRIMARY_KEY .
                    " FROM " . $CFG->prefix . $data['tableName'] . " WHERE " .
                    $fieldName . " = '" . $data['fromid'] . "'");
            if (count($recordsToUpdate) == 0) {
                //this userid is not present in these table and field names
                continue;
            }

            $keys = array_keys($recordsToUpdate); // get the 'id' field from the resultset
            $recordsToModify = array_combine($keys, $keys);

            if (isset($data['compoundIndex'])) {
                $this->mergeCompoundIndex($data, $fieldName,
                        $this->getOtherFieldsOnCompoundIndex($fieldName, $data['compoundIndex']), $recordsToModify,
                        $actionLog, $errorMessages);
            }

            $this->updateRecords($data, $recordsToModify, $fieldName, $actionLog, $errorMessages);
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
     * @global object $CFG
     * @global moodle_database $DB
     * @param array $data array with the details of merging
     * @param string $userfield table's field name that refers to the user id.
     * @param array $otherfields table's field names that refers to the other members of the coumpund index.
     * @param array $recordsToModify array with current $table's id to update.
     * @param array $actionLog Array where to append the list of actions done.
     * @param array $errorMessages Array where to append any error occurred.
     */
    protected function mergeCompoundIndex($data, $userfield, $otherfields, &$recordsToModify, &$actionLog,
            &$errorMessages)
    {
        global $CFG, $DB;

        $otherfieldsstr = implode(', ', $otherfields);
        $sql = 'SELECT id, ' . $userfield . ', ' . $otherfieldsstr .
                ' FROM ' . $CFG->prefix . $data['tableName'] .
                ' WHERE ' . $userfield . ' in (' . $data['fromid'] . ', ' . $data['toid'] . ')';
        $result = $DB->get_records_sql($sql);

        $itemArr = array();
        $idsToRemove = array();
        foreach ($result as $id => $resObj) {
            $keyfromother = array();
            foreach ($otherfields as $of) {
                $keyfromother[] = $resObj->$of;
            }
            $keyfromotherstr = implode('-', $keyfromother);
            $itemArr[$keyfromotherstr][$resObj->$userfield] = $id;
        }
        unset($result); //free memory

        foreach ($itemArr as $otherInfo) {
            //iff we have only one result and it is from the current user => update record
            if (sizeof($otherInfo) == 1) {
                if (isset($otherInfo[$data['fromid']])) {
                    $recordsToModify[$otherInfo[$data['fromid']]] = $otherInfo[$data['fromid']];
                }
            } else { // both users appears in the group
                //confirm both records exist, preventing problems from inconsistent data in database
                if (isset($otherInfo[$data['toid']]) && isset($otherInfo[$data['fromid']])) {
                    $idsToRemove[$otherInfo[$data['fromid']]] = $otherInfo[$data['fromid']];
                }
            }
        }
        unset($itemArr); //free memory
        // we know that idsToRemove have always to be removed and NOT to be updated.
        foreach ($idsToRemove as $id) {
            if (isset($recordsToModify[$id])) {
                unset($recordsToModify[$id]);
            }
        }

        $this->cleanRecordsOnCompoundIndex($data, $idsToRemove, $actionLog, $errorMessages);

        unset($idsToRemove); //free memory
        unset($sql);
    }

    /**
     * Processes accordingly the cleaning up of records after a compound index is already processed.
     *
     * This implementation execute an SQL DELETE of all $idsToRemove. Subclasses may redefine this
     * behavior accordingly.
     *
     * @global object $CFG
     * @global moodle_database $DB
     *
     * @param array $data array with details of merging.
     * @param array $idsToRemove array with ids of records to delete.
     * @param array $actionLog array of actions being performed for merging.
     * @param array $errorMessages array with found errors while merging users' data.
     */
    protected function cleanRecordsOnCompoundIndex($data, $idsToRemove, &$actionLog, &$errorMessages)
    {
        global $CFG, $DB;

        $idsGoByebye = implode(', ', $idsToRemove);

        if ($idsGoByebye) {
            $sql = 'DELETE FROM ' . $CFG->prefix . $data['tableName'] . ' WHERE id IN (' . $idsGoByebye . ')';

            if ($DB->execute($sql)) {
                $actionLog[] = $sql;
            } else {
                // an error occured during DB query
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', $data['tableName']) . ': ' .
                        $DB->get_last_error();
            }
        }
        unset($idsGoByebye); //free memory
    }

    /**
     * Updates the table, replacing the user.id for the $data['toid'] on all
     * records specified by the ids on $recordsToModify.
     *
     * @global object $CFG
     * @global moodle_database $DB
     *
     * @param array $data array with details of merging.
     * @param array $recordsToModify list of record ids to update with $toid.
     * @param string $fieldName field name of the table to update.
     * @param array $actionLog list of performed actions.
     * @param array $errorMessages list of error messages.
     */
    protected function updateRecords($data, $recordsToModify, $fieldName, &$actionLog, &$errorMessages)
    {
        global $CFG, $DB;

        if (count($recordsToModify) == 0) {
            // if no records, do nothing ;-)
            return;
        }

        $idString = implode(', ', $recordsToModify);
        $updateRecords = "UPDATE " . $CFG->prefix . $data['tableName'] .
                " SET " . $fieldName . " = '" . $data['toid'] .
                "' WHERE " . self::PRIMARY_KEY . " IN (" . $idString . ")";

        try {
            if (!$DB->execute($updateRecords)) {
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', $this->tableName) .
                        ': ' . $DB->get_last_error();
            }
            $actionLog[] = $updateRecords;
        } catch (Exception $e) {
            // if we get here, we have found a unique index on a user-id related column.
            // therefore, there will be only a single record from one or other user.
            $useridtoclean = ($this->newidtomaintain) ? $data['fromid'] : $data['toid'];
            $deleteRecord = "DELETE FROM " . $CFG->prefix . $data['tableName'] .
                    " WHERE " . $fieldName . " = '" . $useridtoclean . "'";

            if (!$DB->execute($deleteRecord)) {
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', $this->tableName) .
                        ': ' . $DB->get_last_error();
            }
            $actionLog[] = $deleteRecord;
        }
    }

    /**
     * Gets the fields name on a compound index case, excluding the given $userField.
     * Therefore, if there are multiple user-related fields in a compound index,
     * return the rest of the column names except the given $userField. Otherwise,
     * it returns simply the 'otherfields' array from the $compoundIndex definition.
     *
     * @param string $userField current user-related field being analyized.
     * @param array $compoundIndex related config data for the compound index.
     * @return array an array with the other field names of the compound index.
     */
    protected function getOtherFieldsOnCompoundIndex($userField, $compoundIndex)
    {
        // we can alternate column names when both fields are user-related.
        if (sizeof($compoundIndex['userfield']) > 1) {
            $all = array_merge($compoundIndex['userfield'], $compoundIndex['otherfields']);
            $all = array_flip($all);
            unset($all[$userField]);
            return array_flip($all);
        }
        // default behavior
        return $compoundIndex['otherfields'];
    }

}
