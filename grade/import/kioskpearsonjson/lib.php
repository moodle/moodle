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

require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/import/lib.php';


function import_kioskpearsonjson_grades($items, $course, &$error) {
    global $USER, $DB;
    
    $resultStats = new stdClass();

	$resultStats->numUnfoundUsers = 0;	
	$resultStats->numLockedGrades = 0;
	$resultStats->numGradesUpdated = 0;
	$resultStats->numGradesCreated = 0;
	$resultStats->numItemsUpdated = 0;
	$resultStats->numItemsCreated = 0;

    $status = true;
	
	if (isset($items->items)){
						
		//cycle through each item looking for results
		foreach ($items->items as $newItem ) {
						
			//1. Find old item
			$grade_item = grade_item::fetch(array('idnumber'=>$newItem->id, 'courseid'=>$course->id));
			
			//2. Create new item it does not currently exist
			if (!$grade_item) {
				$grade_item = new grade_item(array(
												'courseid'=>$course->id, 
												'itemtype'=>'manual', 
												'itemname'=>$newItem->title, 
												'idnumber'=>$newItem->id,
												'grademax'=>$newItem->pointsPossible
												), false);
				$grade_item->insert('import');
				$resultStats->numItemsCreated++;																
			}
			else {
				$grade_item->itemtype = 'manual';
				$grade_item->itemname = $newItem->title;	
				$grade_item->grademax = $newItem->pointsPossible;
				$grade_item->update();
				$resultStats->numItemsUpdated++;
			}
						
			//5. Create new items grades
			foreach ($newItem->results as $newImportGrade) {				   
					
					//Get studentUser from regular moodle ID:
					if (!$gradeUser = $DB->get_record('user', array('id' => $newImportGrade->userId))) {                        
                        //increment for reporting
                        $resultStats->numUnfoundUsers++;
                        
                        $status = false;

						//TPLMS-5655 remove break 3
                        continue;
                    }

					//1. Check to see if grade exists, if not, create it... if it does, check other things					
					$currentgrade = grade_grade::fetch(array('itemid'=>$grade_item->id, 'userid'=>$gradeUser->id));
					if(!$currentgrade) {
						//Create the grade to insert
						$currentgrade = new stdClass();
						$currentgrade->itemid     = $grade_item->id;
						$currentgrade->userid     = $gradeUser->id;					
						$currentgrade->importer   = $USER->id;
						$currentgrade->finalgrade = $newImportGrade->score;
						$currentgrade->newgradeitem = $grade_item;
						$currentgrade->feedback = $newImportGrade->comments;
						$currentgrade->rawgrade = $newImportGrade->score;
						$currentgrade->rawgrademax = $newItem->pointsPossible;
					
						$DB->insert_record('grade_grades', $currentgrade);
						$resultStats->numGradesCreated++;
					}
					else if($currentgrade->locked or $grade_item->locked) {
						//if the item or grade for this item is locked, don't update the grade.
						$resultStats->numLockedGrades++;
					} 		 					
					else {	
						$currentgrade->feedback = $newImportGrade->comments;
						$currentgrade->rawgrade = $newImportGrade->score;
						$currentgrade->finalgrade = $newImportGrade->score;
						$currentgrade->rawgrademax = $newItem->pointsPossible;
						$currentgrade->update();
						$resultStats->numGradesUpdated++;
					}																			
			}
			
			$grade_item->force_regrading();	
		}		
	}	
	
    if ($resultStats) {
        return $resultStats;
    } else {
        return false;
    }
}

