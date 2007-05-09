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
            $students = get_group_students($currentgroup, "u.lastname ASC");
        } else {
            $students = grade_get_course_students($course->id);
        }

        if (!empty($students)) {
            foreach ($students as $student) {
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

                $this->columns[] = "$gradeitem->itemmodule: ".format_string($gradeitem->itemname,true)." - $gradeitem->maxgrade";
            
                if (!empty($gradeitem->maxgrade)) {
                    $maxgrade = "$strmax: $gradeitem->maxgrade";
                } else {
                    $maxgrade = "";
                } 
            
                // load as an array of grade_final objects
                if ($itemgrades = $gradeitem -> load_final()) {             
                
                    if (!empty($students)) {                    
                        foreach ($students as $student) {
                      
                            // add support for comment here MDL-9634
                            $studentgrade = $itemgrades[$student->id];            
                            
                            if (!empty($studentgrade->gradevalue)) {
                                $this->grades[$student->id][] = $currentstudentgrade = $studentgrade->gradevalue;
                            } else {
                                $this->grades[$student->id][] = $currentstudentgrade = "";
                                $this->gradeshtml[$student->id][] = "";
                            }
                            if (!empty($maxgrade)) {
                                $this->totals[$student->id] = (float)($totals[$student->id]) + (float)($currentstudentgrade);
                            } else {
                                $this->totals[$student->id] = (float)($totals[$student->id]) + 0;
                            }
                            
                            // load comments here
                            $studentgrade->load_text();
                            // get the actual comment
                            $comment = $studentgrade->grade_grades_text->feedback;
                            
                            if (!empty($comment)) {
                                $this->comments[$student->id][] = $comment;
                            } else {
                                $this->comments[$student->id][] = '';  
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
 
}

?>
