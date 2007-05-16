<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
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
include_once('../../../config.php');
include_once($CFG->dirroot.'/lib/gradelib.php');
include_once($CFG->dirroot.'/grade/lib.php');
/**
 * Prints all grade items for selection
 * @input int id - course id
 */
function print_gradeitem_selections($id) {
    
    // print all items for selections
    // make this a standard function in lib maybe
    if ($grade_items = grade_get_items($id)) {
        echo '<form action="index.php" method="post">';
        echo '<div>';
        foreach ($grade_items as $grade_item) {
            
            echo '<br/><input type="checkbox" name="itemids[]" value="'.$grade_item->id.'" checked="checked"/>';
            
            if ($grade_item->itemtype == 'category') {
                // grade categories should be displayed bold
                echo '<b>'.$grade_item->itemname.'</b>';
            } else {
                echo $grade_item->itemname;
            } 
        }
        echo '<input type="hidden" name="id" value="'.$id.'"/>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'"/>';
        echo '<input type="submit" value="'.get_string('submit').'" />';
        echo '</div>';
        echo '</form>';
    }
}
/**
 * Base export class
 */
class grade_export {
    
    var $format = ''; // export format
    var $id; // course id
    var $itemids; // comma separated grade_item ids;
    var $grades = array();    // Collect all grades in this array
    var $gradeshtml= array(); // Collect all grades html formatted in this array
    var $comments = array(); // Collect all comments for each grade
    var $totals = array();    // Collect all totals in this array
    var $columns = array();     // Accumulate column names in this array.
    var $columnhtml = array();  // Accumulate column html in this array. 
    var $columnidnumbers = array(); // Collect all gradeitem id numbers
    var $students = array();
    var $course; // course
    
    // common strings
    var $strgrades; 
    var $strgrade;    
    
    /**
     * Constructor should set up all the private variables ready to be pulled
     * @input int id - course id
     * @input string itemids - comma separated value of itemids to process for this export
     */
    function grade_export($id, $itemids = '') {
        
        $this->strgrades = get_string("grades");
        $this->strgrade = get_string("grade");
        $this->itemids = $itemids;
        
        $strmax = get_string("maximumshort");  
        
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID was incorrect");
        }
        
        require_capability('moodle/course:viewcoursegrades', get_context_instance(CONTEXT_COURSE, $id));
        
        $this->id = $id;
        $this->course = $course;

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

        if ($currentgroup) {
            $this->students = get_group_students($currentgroup, "u.lastname ASC");
        } else {
            $this->students = grade_get_course_students($course->id);
        }

        if (!empty($this->students)) {
            foreach ($this->students as $student) {
                $this->grades[$student->id] = array();    // Collect all grades in this array
                $this->gradeshtml[$student->id] = array(); // Collect all grades html formatted in this array
                $this->totals[$student->id] = array();    // Collect all totals in this array
                $this->comments[$student->id] = array(); // Collect all comments in tihs array
            }
        }
        
        // if grade_item ids are specified
        if ($iids = explode(",", $itemids)) {
            foreach ($iids as $iid) {
                $params->id = $iid;
                $gradeitems[] = new grade_item($params);               
            }  
        } else {
            // else we get all items for this course
            $gradeitems = grade_get_items($this->id);
        }        
        
        if ($gradeitems) {
            
            foreach ($gradeitems as $gradeitem) {

                // load as an array of grade_final objects
                if ($itemgrades = $gradeitem -> load_final()) {
                    
                    $this->columns[$itemgrades->id] = "$gradeitem->itemmodule: ".format_string($gradeitem->itemname,true)." - $gradeitem->grademax";
                
                    $this->columnidnumbers[$itemgrades->id] = $gradeitem->idnumber; // this might be needed for some export plugins  
            
                    if (!empty($gradeitem->grademax)) {
                        $maxgrade = "$strmax: $gradeitem->grademax";
                    } else {
                        $maxgrade = "";
                    } 
                    
                    if (!empty($this->students)) {                    
                        foreach ($this->students as $student) {
                      
                            // add support for comment here MDL-9634
                            $studentgrade = $itemgrades[$student->id];            
                            
                            if (!empty($studentgrade->gradevalue)) {
                                $this->grades[$student->id][$itemgrades->id] = $currentstudentgrade = $studentgrade->gradevalue;
                            } else {
                                $this->grades[$student->id][$itemgrades->id] = $currentstudentgrade = "";
                                $this->gradeshtml[$student->id][$itemgrades->id] = "";
                            }
                            if (!empty($maxgrade)) {
                                $this->totals[$student->id] = (float)($this->totals[$student->id]) + (float)($currentstudentgrade);
                            } else {
                                $this->totals[$student->id] = (float)($this->totals[$student->id]) + 0;
                            }
                            
                            // load comments here
                            $studentgrade->load_text();
                            // get the actual comment
                            $comment = $studentgrade->grade_grades_text->feedback;
                            
                            if (!empty($comment)) {
                                $this->comments[$student->id][$itemgrades->id] = $comment;
                            } else {
                                $this->comments[$student->id][$itemgrades->id] = '';  
                            }
                        }
                    }
                }
            }
        }     
    }
    
    /**
     * To be implemented by child classes
     */
    function print_grades() { }
    
    /**
     * Displays all the grades on screen as a feedback mechanism
     */
    function display_grades($feedback=false) {
        echo get_string("firstname").",".
             get_string("lastname").",".
             get_string("idnumber").",".
             get_string("institution").",".
             get_string("department").",".
             get_string("email");
        foreach ($this->columns as $column) {
            $column = strip_tags($column);
            echo ",$column";
        
            /// add a column_feedback column            
            if ($feedback) {
                echo ",{$column}_feedback";
            }        
        }
        echo ",".get_string("total")."<br/>";
    
        /// Print all the lines of data.
        foreach ($this->grades as $studentid => $studentgrades) {
            $student = $this->students[$studentid];
            if (empty($this->totals[$student->id])) {
                $this->totals[$student->id] = '';
            }
            echo "$student->firstname,$student->lastname,$student->idnumber,$student->institution,$student->department,$student->email";
            foreach ($studentgrades as $grade) {
                $grade = strip_tags($grade);
                echo ",$grade";            
                
                if ($feedback) {
                    echo ",".array_shift($this->comments[$student->id]);
                }       
            }
            echo ",".$this->totals[$student->id];
            echo "<br/>";
        }  
    }
}

?>