<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
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

require_once($CFG->dirroot . '/grade/report/stats/statistics/stats.php');

/**
 * Stats class for finding the median of a set of grades for an item in a course.
 */
class median extends stats {
    public function __construct() {
        parent::__construct(get_string('median', 'gradereport_stats'));
        $this->capability = 'gradereport/stats:stat:median';
    }

    public function report_data($final_grades, $item=null){
        $midpoint = (count($final_grades) - 1) / 2;
        return ($final_grades[floor($midpoint)] + $final_grades[ceil($midpoint)]) / 2;
    }
}

?>