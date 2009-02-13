 
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
 * Stats class for finding the mode of a set of grades in an item in a course.
 * NOTE: To pervent students from being able to find a list of all grades when
 * there are no grades the same (or to many in the same) the mode will not be
 * shown when there are over $maxmode number of modes.
 */
class mode extends stats {
    public static $maxmode = 5;
	
	public function __construct() {
        parent::__construct(get_string('mode', 'gradereport_stats'));
		$this->capability = 'gradereport/stats:stat:mode';
	}

    public function report_data($final_grades, $item=null){
        $occurrences = array();
		$modes = array();
	
		foreach($final_grades as $grade) {
			if(!array_key_exists(sprintf('%f', round($grade,2)), $occurrences)) {
				$occurrences[sprintf('%f', round($grade,2))] = 1;
			} else {
				$occurrences[sprintf('%f', round($grade,2))]++;
			}
		}
	
		arsort($occurrences);
		$modes = array_keys($occurrences, current($occurrences));
	
		if(count($modes) <= mode::$maxmode) {
			return $modes;
		} else {
			return null;
		}
    }
}
?>