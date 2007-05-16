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

require_once($CFG->dirroot.'/grade/export/lib.php');

class grade_export_txt extends grade_export {
    
    var $format = 'txt'; // export format
    
    /**
     * To be implemented by child classes
     */
    function print_grades($feedback = false) {        

        global $CFG;

        /// Whether this plugin is entitled to update export time
        if ($expplugins = explode(",", $CFG->gradeexport)) {
            if (in_array($this->format, $expplugins)) {
                $export = true;
            } else {
            $export = false;  
          }
        } else {
            $export = false; 
        }
        
        /// Print header to force download        
        header("Content-Type: application/download\n"); 
        $downloadfilename = clean_filename("{$this->course->shortname} $this->strgrades");
        header("Content-Disposition: attachment; filename=\"$downloadfilename.txt\"");

/// Print names of all the fields

        echo get_string("firstname")."\t".
             get_string("lastname")."\t".
             get_string("idnumber")."\t".
             get_string("institution")."\t".
             get_string("department")."\t".
             get_string("email");
        foreach ($this->columns as $column) {
            $column = strip_tags($column);
            echo "\t$column";
        
            /// add a column_feedback column            
            if ($feedback) {
                echo "\t{$column}_feedback";
            }        
        }
        echo "\t".get_string("total")."\n";
    
/// Print all the lines of data.
        foreach ($this->grades as $studentid => $studentgrades) {
          
            $student = $this->students[$studentid];
            if (empty($this->totals[$student->id])) {
                $this->totals[$student->id] = '';
            }
            echo "$student->firstname\t$student->lastname\t$student->idnumber\t$student->institution\t$student->department\t$student->email";

            foreach ($studentgrades as $gradeitemid => $grade) {
                $grade = strip_tags($grade);
                echo "\t$grade";            
                
                if ($feedback) {
                    echo "\t".array_shift($this->comments[$student->id]);
                }                
                
                /// if export flag needs to be set
                /// construct the grade_grades_final object and update timestamp if CFG flag is set

                if ($export) {
                    unset($params);
                    $params->itemid = $gradeitemid;
                    $params->userid = $studentid;
                
                    $grade_grades_final = new grade_grades_final($params);
                    $grade_grades_final->exported = time();
                    // update the time stamp;
                    $grade_grades_final->update();
                }
            }
            echo "\t".$this->totals[$student->id];
            echo "\n";
        }
    
        exit;
    }
}

?>