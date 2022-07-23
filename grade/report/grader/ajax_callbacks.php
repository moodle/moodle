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
 * This file receives ajax callbacks for the grader report
 *
 * @package   gradereport_grader
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
// require_once $CFG->dirroot.'/grade/report/grader/ajaxlib.php';
// require_once $CFG->dirroot.'/grade/report/grader/lib.php';

$courseid = required_param('id', PARAM_INT);                   // course id
$userid = optional_param('userid', false, PARAM_INT);
$itemid = optional_param('itemid', false, PARAM_INT);
$type = optional_param('type', false, PARAM_ALPHA);
$action = optional_param('action', false, PARAM_ALPHA);
$newvalue = optional_param('newvalue', false, PARAM_TEXT);

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}
$context = context_course::instance($course->id);
require_login($course);

switch ($action) {
    case 'update':
        if (!confirm_sesskey()) {
            break;
        }
        require_capability('moodle/grade:edit', $context);

        if (!empty($userid) && !empty($itemid) && $newvalue !== false && !empty($type)) {
            // Save the grade or feedback
            if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$courseid))) { // we must verify course id here!
                throw new \moodle_exception('invalidgradeitemid');
            }

            /**
             * Code copied from grade/report/grader/lib.php line 187+
             */
            $warnings = array();
            $finalvalue = null;
            $finalgrade = null;
            $feedback = null;
            $json_object = new stdClass();
            // Pre-process grade
            if ($type == 'value' || $type == 'scale') {
                $feedback = false;
                $feedbackformat = false;
                if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
                    if ($newvalue == -1) { // -1 means no grade
                        $finalgrade = null;
                    } else {
                        $finalgrade = $newvalue;
                    }
                } else {
                    $finalgrade = unformat_float($newvalue);
                }

                $errorstr = '';
                // Warn if the grade is out of bounds.
                if (is_null($finalgrade)) {
                    // ok
                } else {
                    $bounded = $grade_item->bounded_grade($finalgrade);
                    if ($bounded > $finalgrade) {
                        $errorstr = 'lessthanmin';
                    } else if ($bounded < $finalgrade) {
                        $errorstr = 'morethanmax';
                    }
                }

                if ($errorstr) {
                    $userfieldsapi = \core_user\fields::for_name();
                    $user = $DB->get_record('user', array('id' => $userid), 'id' . $userfieldsapi->get_sql()->selects);
                    $gradestr = new stdClass();
                    $gradestr->username = fullname($user);
                    $gradestr->itemname = $grade_item->get_name();
                    $json_object->message = get_string($errorstr, 'grades', $gradestr);
                    $json_object->result = "error";

                }

                $finalvalue = $finalgrade;

            } else if ($type == 'feedback') {
                $finalgrade = false;
                $trimmed = trim($newvalue);
                if (empty($trimmed)) {
                    $feedback = NULL;
                } else {
                    $feedback = $newvalue;
                }

                $finalvalue = $feedback;
            }

            if (!empty($json_object->result) && $json_object->result == 'error') {
                echo json_encode($json_object);
                die();
            } else {
                $json_object->gradevalue = $finalvalue;

                if ($grade_item->update_final_grade($userid, $finalgrade, 'gradebook', $feedback, FORMAT_MOODLE)) {
                    $json_object->result = 'success';
                    $json_object->message = false;
                } else {
                    $json_object->result = 'error';
                    $json_object->message = "TO BE LOCALISED: Failure to update final grade!";
                    echo json_encode($json_object);
                    die();
                }

                // Get row data
                $sql = "SELECT gg.id, gi.id AS itemid, gi.scaleid AS scale, gg.userid AS userid, finalgrade, gg.overridden AS overridden "
                     . "FROM {grade_grades} gg, {grade_items} gi WHERE "
                     . "gi.courseid = ? AND gg.itemid = gi.id AND gg.userid = ?";
                $records = $DB->get_records_sql($sql, array($courseid, $userid));
                $json_object->row = $records;
                echo json_encode($json_object);
                die();
            }
        } else {
            $json_object = new stdClass();
            $json_object->result = "error";
            $json_object->message = "Missing parameter to ajax UPDATE callback: \n" .
                                    "  userid: $userid,\n  itemid: $itemid\n,  type: $type\n,  newvalue: $newvalue";
            echo json_encode($json_object);
        }

        break;
}


