<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/lib/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/export/grade_export_form.php');

/**
 * Base export class
 */
class grade_export {

    var $id; // course id
    var $grade_items; // array of grade_items
    var $groupid;
    var $grades = array();    // Collect all grades in this array
    var $comments = array(); // Collect all comments for each grade
    var $columns = array();     // Accumulate column names in this array.
    var $columnidnumbers = array(); // Collect all gradeitem id numbers
    var $students = array();
    var $course; // course
    var $userkey; // Optional MD5 string used to publish this export data via a URL
    var $export_letters;
    var $itemidsurl; // A string of itemids to add to the URL for the export

    // common strings
    var $strgrades;
    var $strgrade;

    /**
     * Constructor should set up all the private variables ready to be pulled
     * @param int $courseid course id
     * @param array $itemids array of grade item ids, empty means all
     * @param stdClass $formdata Optional object of formdata.
     * @note Exporting as letters will lead to data loss if that exported set it re-imported.
     */
    function grade_export($courseid, $itemids=null, $formdata=null) {
        global $CFG, $USER, $COURSE;

        $this->export_letters = false;
        if (isset($formdata->export_letters)) {
            $this->export_letters = $formdata->export_letters;
        }

        $this->userkey = false;
        if (isset($formdata->key)) {
            if ($formdata->key == 1 && isset($formdata->iprestriction) && isset($formdata->validuntil)) { // Create a new key
                $formdata->key = create_user_key('grade/export', $USER->id, $COURSE->id, $formdata->iprestriction, $formdata->validuntil);
            }
            $this->userkey = $formdata->key;
        }

        $this->strgrades = get_string("grades");
        $this->strgrade = get_string("grade");

        if (!$course = get_record("course", "id", $courseid)) {
            error("Course ID was incorrect");
        }
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        require_capability('moodle/grade:export', $context);

        $this->id = $course->id;
        $this->course = $course;

        // fetch all grade items
        if (empty($itemids)) {
            $this->grade_items = grade_item::fetch_all(array('courseid'=>$this->id));
        } else {
            $this->grade_items = array();
            foreach ($itemids as $iid) {
                if ($grade_item = grade_item::fetch(array('id'=>(int)$iid, 'courseid'=>$this->id))) {
                    $this->grade_items[$grade_item->id] = $grade_item;
                }
            }
        }

        // init colums
        foreach ($this->grade_items as $grade_item) {
            if ($grade_item->itemtype == 'mod') {
                $this->columns[$grade_item->id] = get_string('modulename', $grade_item->itemmodule).': '.$grade_item->get_name();
            } else {
                $this->columns[$grade_item->id] = $grade_item->get_name();
            }
            $this->columnidnumbers[$grade_item->id] = $grade_item->idnumber; // this might be needed for some export plugins
        }

        /// Check to see if groups are being used in this course
        if ($groupmode = groupmode($course)) {   // Groups are being used

            if (isset($_GET['group'])) {
                $changegroup = $_GET['group'];  /// 0 or higher
            } else {
                $changegroup = -1;              /// This means no group change was specified
            }

            $currentgroup = get_and_set_current_group($course, $groupmode, $changegroup);

        } else {
            $currentgroup = false;
        }

        $this->groupid = $currentgroup;

        if ($currentgroup) {
            $this->students = get_group_students($currentgroup, "u.lastname ASC");
        } else {
            $this->students = get_role_users(@implode(',', $CFG->gradebookroles), $context);
        }

        if (!empty($this->students)) {
            foreach ($this->students as $student) {
                $this->grades[$student->id] = array();    // Collect all grades in this array
                $this->comments[$student->id] = array(); // Collect all comments in tihs array
            }
        }

        if (isset($formdata->itemids)) {
            // Build itemidsurl for links
            $itemids = array();
            if ($formdata->itemids) {
                foreach ($formdata->itemids as $itemid=>$selected) {
                    if ($selected) {
                        $itemids[] = $itemid;
                    }
                }
                $this->itemidsurl = implode(",", $itemids);
            } else {
                //error?
                $this->itemidsurl = '';
            }
        }
    }

    function load_grades() {
        global $CFG;

        // first make sure we have all final grades
        // TODO: check that no grade_item has needsupdate set
        grade_regrade_final_grades($this->id);

        if ($this->export_letters) {
            require_once($CFG->dirroot . '/grade/report/lib.php');
            $report = new grade_report($this->id, null, null);
            $letters = $report->get_grade_letters();
        } else {
            $letters = null;
        }

        if ($this->grade_items) {
            foreach ($this->grade_items as $gradeitem) {
                // load as an array of grade_final objects
                if ($itemgrades = $gradeitem->get_final() and !empty($this->students)) {
                    foreach ($this->students as $student) {
                        $finalgrade = null;
                        $feedback = '';
                        if (array_key_exists($student->id, $itemgrades)) {
                            $finalgrade = $itemgrades[$student->id]->finalgrade;
                            $grade = new grade_grade($itemgrades[$student->id], false);
                            if ($grade_text = $grade->load_text()) {
                                $feedback = format_text($grade_text->feedback, $grade_text->feedbackformat);
                            }
                        }

                        if ($this->export_letters) {
                            $grade_item_displaytype = $report->get_pref('gradedisplaytype', $gradeitem->id);
                            // TODO Convert final grade to letter if export option is on, and grade_item is set to letter type MDL-10490
                            if ($grade_item_displaytype == GRADE_REPORT_GRADE_DISPLAY_TYPE_LETTER) {
                                $finalgrade = grade_grade::get_letter($letters, $finalgrade, $gradeitem->grademin, $gradeitem->grademax);
                            }
                        }

                        $this->grades[$student->id][$gradeitem->id] = $finalgrade;
                        $this->comments[$student->id][$gradeitem->id] = $feedback;
                    }
                }
            }
        }
    }

    /**
     * To be implemented by child class
     * TODO finish PHPdocs
     */
    function print_grades() { }

    /**
     * Displays all the grades on screen as a feedback mechanism
     * TODO finish PHPdoc
     */
    function display_grades($feedback=false, $rows=10) {

        $this->load_grades();

        echo '<table>';
        echo '<tr>';
        echo '<th>'.get_string("firstname")."</th>".
             '<th>'.get_string("lastname")."</th>".
             '<th>'.get_string("idnumber")."</th>".
             '<th>'.get_string("institution")."</th>".
             '<th>'.get_string("department")."</th>".
             '<th>'.get_string("email")."</th>";
        foreach ($this->columns as $column) {
            $column = strip_tags($column);
            echo "<th>$column</th>";

            /// add a column_feedback column
            if ($feedback) {
                echo "<th>{$column}_feedback</th>";
            }
        }
        echo '</tr>';
        /// Print all the lines of data.

        $i = 0;
        foreach ($this->grades as $studentid => $studentgrades) {

            // number of preview rows
            if ($i++ == $rows) {
                break;
            }
            echo '<tr>';
            $student = $this->students[$studentid];

            echo "<td>$student->firstname</td><td>$student->lastname</td><td>$student->idnumber</td><td>$student->institution</td><td>$student->department</td><td>$student->email</td>";
            foreach ($studentgrades as $itemid=>$grade) {
                $grade = strip_tags($grade);
                echo "<td>$grade</td>";

                if ($feedback) {
                    echo '<td>'.$this->comments[$studentid][$itemid].'</td>';
                }
            }
            echo "</tr>";
        }
        echo '</table>';
    }

    /**
     * Either prints a "continue" box, which will redirect the user to the download page, or prints the URL for the published data.
     * @note exit() at the end of the method
     * @param string $plugin Required: name of the plugin calling this method. Used for building the URL.
     * @return void
     */
    function print_continue($plugin) {
        global $CFG;

        // this redirect should trigger a download prompt
        if (!$this->userkey) {
            print_continue('export.php?id='.$this->id.'&amp;itemids='.$this->itemidsurl.'&amp;export_letters='.$this->export_letters);

        } else {
            $link = $CFG->wwwroot.'/grade/export/'.$plugin.'/dump.php?id='.$this->id.'&amp;itemids='
                  . $this->itemidsurl.'&amp;export_letters='.$this->export_letters.'&amp;key='.$this->userkey;

            echo '<p>';
            echo '<a href="'.$link.'">'.$link.'</a>';
            echo '</p>';
            print_footer();
        }
        exit();
    }
}

?>
