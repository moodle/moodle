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
 * TableMerger for the user_enrolments table.
 *
 * @package    tool
 * @subpackage mergeusers
 * @author     Jordi Pujol-Ahulló <jordi.pujol@urv.cat>,  SREd, Universitat Rovira i Virgili
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class UserEnrolmentsMerger extends GenericTableMerger
{

    /**
     * Disables course enrolments for the old user.
     *
     * The user_enrolments table is similar to grade_grades in that it also
     * has a compound unique key. The approach here is not to replace the
     * user in the case of a duplicate, but to disable the old user for that
     * particular course.
     *
     * @param array $data array with the necessary data for merging records.
     * @param array $actionLog list of action performed.
     * @param array $errorMessages list of error messages.
     */
    public function merge($data, &$actionLog, &$errorMessages)
    {
        global $CFG, $DB;

        $sql = 'SELECT id, enrolid, userid, status from ' . $CFG->prefix .
                'user_enrolments WHERE userid in (' . $data['fromid'] . ', ' .
                $data['toid'] . ')';
        $result = $DB->get_records_sql($sql);

        if (empty($result)) {
            return;
        }

        $enrolArr = array();
        $idsToDisable = array();
        $enrolmentsToUpdate = array();
        $enrolmentsToReactivate = array();

        foreach ($result as $id => $resObj) {
            $enrolArr[$resObj->enrolid][$resObj->userid] = $id;
        }

        foreach ($enrolArr as $enrolId => $enrolInfo) {
            if (sizeof($enrolInfo) != 2) {
                //if we don't have 2 results, then these users did not both complete this activity.
                if (key($enrolInfo) == $data['fromid']) {
                    //if we have the old user, we have to assign this course to the new user.
                    $enrolmentsToUpdate[] = $enrolInfo[$data['fromid']]; //disable the old user
                    continue;
                } else {
                    //we don't have anything here for this course. We actually shouldn't get to this point ever.
                    continue;
                }
            }
            // check if it is actually enabled
            if ($result[$enrolInfo[$data['fromid']]]->status != 2) {
                $idsToDisable[] = $enrolInfo[$data['fromid']];
            }
            //check if it was already disabled
            if ($result[$enrolInfo[$data['toid']]]->status == 2) {
                $enrolmentsToReactivate[] = $enrolInfo[$data['toid']]; // reactivate new user.
            }
        }
        unset($enrolArr); //free memory
        unset($result); //free memory

        if (!empty($enrolmentsToUpdate)) { // it's possible we won't have any
            // First, let's move the courses belonging to the old user over to the new one.
            $updateIds = implode(', ', $enrolmentsToUpdate);
            $sql = 'UPDATE ' . $CFG->prefix . 'user_enrolments SET userid = ' . $data['toid'] .
                    ' WHERE id IN (' . $updateIds . ')';
            if ($DB->execute($sql)) {
                //all was ok: action done.
                $actionLog[] = $sql;
            } else {
                // a database error occurred.
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', "user_enrolments (#1)") .
                        ': ' . $DB->get_last_error();
            }
        }
        unset($enrolmentsToUpdate); //free memory
        unset($sql);

        // ok, now let's lock this user out from using the common courses.
        if (!empty($idsToDisable)) {
            $idsGoByebye = implode(', ', $idsToDisable);
            $sql = 'UPDATE ' . $CFG->prefix . 'user_enrolments SET status = 2 WHERE id IN (' .
                    $idsGoByebye . ')  AND status = 0';
            if ($DB->execute($sql)) {
                //all was ok: action done.
                $actionLog[] = $sql;
            } else {
                // a database error occurred.
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', "user_enrolments (#2)") .
                        ': ' . $DB->get_last_error();
            }
        }
        unset($idsToDisable); //free memory
        unset($sql);

        // the enrolment was deactivated before by us.
        // reactivate it again.
        if (!empty($enrolmentsToReactivate)) {
            $idsReactivate = implode(', ', $enrolmentsToReactivate);
            $sql = 'UPDATE ' . $CFG->prefix . 'user_enrolments SET status = 0 WHERE id IN (' .
                    $idsReactivate . ')  AND status = 2';
            if ($DB->execute($sql)) {
                //all was ok: action done.
                $actionLog[] = $sql;
            } else {
                // a database error occurred.
                $errorMessages[] = get_string('tableko', 'tool_mergeusers', "user_enrolments (#3)") .
                        ': ' . $DB->get_last_error();
            }
        }
        unset($enrolmentsToReactivate); //free memory
        unset($sql);
    }

}
