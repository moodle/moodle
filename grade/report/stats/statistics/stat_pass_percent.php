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
require_once($CFG->dirroot . '/lib/grade/grade_grade.php');

/**
 * Stats class for finding the percent of grades above the set
 * gradepass for the item in a course.
 * NOTE: This statistic depends on gradepass being set for an item,
 * by defualt it is set to 0, witch is also noramly what the mingrade is
 * so it will show the pass percent as 100% if everything is left as default.
 */
class pass_percent extends stats {
    public function __construct() {
        parent::__construct(get_string('pass_percent', 'gradereport_stats'), GRADE_DISPLAY_TYPE_PERCENTAGE);
		$this->capability = 'gradereport/stats:stat:passpercent';
	}

    public function report_data($final_grades, $item=null){
		$numpass = 0;
		foreach($final_grades as $grade) {
			if($grade >= $item->gradepass) {
				$numpass++;
			}
		}
	
		return grade_grade::standardise_score($numpass / count($final_grades),  0, 1, $item->grademin, $item->grademax);
    }
}
?>