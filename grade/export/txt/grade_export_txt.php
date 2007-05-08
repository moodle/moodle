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
    function print_grades() { 
        
/// Print header to force download

        header("Content-Type: application/download\n"); 
        $downloadfilename = clean_filename("$this->course->shortname $this->strgrades");
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
        }
        echo "\t".get_string("total")."\n";
    
/// Print all the lines of data.
        foreach ($this->grades as $studentid => $studentgrades) {
            $student = $students[$studentid];
            if (empty($this->totals[$student->id])) {
                $this->totals[$student->id] = '';
            }
            echo "$student->firstname\t$student->lastname\t$student->idnumber\t$student->institution\t$student->department\t$student->email";
            foreach ($studentgrades as $grade) {
                $grade = strip_tags($grade);
                echo "\t$grade";
            }
            echo "\t".$this->totals[$student->id];
            echo "\n";
        }
    
        exit;
    }
}

?>
