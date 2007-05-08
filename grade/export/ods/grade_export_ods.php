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

class grade_export_ods extends grade_export {
    
    var $format = 'ods'; // export format
    
    /**
     * To be implemented by child classes
     */
    function print_grades() { 
        
        require_once($CFG->dirroot.'/lib/odslib.class.php');

    /// Calculate file name
        $downloadfilename = clean_filename("$course->shortname $this->strgrades.ods");
    /// Creating a workbook
        $workbook = new MoodleODSWorkbook("-");
    /// Sending HTTP headers
        $workbook->send($downloadfilename);
    /// Adding the worksheet
        $myxls =& $workbook->add_worksheet($this->strgrades);
    
    /// Print names of all the fields
        $myxls->write_string(0,0,get_string("firstname"));
        $myxls->write_string(0,1,get_string("lastname"));
        $myxls->write_string(0,2,get_string("idnumber"));
        $myxls->write_string(0,3,get_string("institution"));
        $myxls->write_string(0,4,get_string("department"));
        $myxls->write_string(0,5,get_string("email"));
        $pos=6;
        foreach ($this->columns as $column) {
            $myxls->write_string(0,$pos++,strip_tags($column));
        }
        $myxls->write_string(0,$pos,get_string("total"));
    
    /// Print all the lines of data.
        $i = 0;
        if (!empty($this->grades)) {
            foreach ($this->grades as $studentid => $studentgrades) {
                $i++;
                $student = $students[$studentid];
                if (empty($this->totals[$student->id])) {
                    $this->totals[$student->id] = '';
                }
        
                $myxls->write_string($i,0,$student->firstname);
                $myxls->write_string($i,1,$student->lastname);
                $myxls->write_string($i,2,$student->idnumber);
                $myxls->write_string($i,3,$student->institution);
                $myxls->write_string($i,4,$student->department);
                $myxls->write_string($i,5,$student->email);
                $j=6;
                foreach ($studentgrades as $grade) {
                    if (is_numeric($grade)) {
                        $myxls->write_number($i,$j++,strip_tags($grade));
                    }
                    else {
                        $myxls->write_string($i,$j++,strip_tags($grade));
                    }
                }
                $myxls->write_number($i,$j,$this->totals[$student->id]);
            }
        }

    /// Close the workbook
        $workbook->close();
    
        exit;
    }
}

?>
