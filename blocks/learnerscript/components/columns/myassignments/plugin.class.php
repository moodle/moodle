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

/** LearnerScript Reports
  * A Moodle block for creating customizable reports
  * @package blocks
  * @subpackage learnerscript
  * @author: sowmya<sowmya@eabyas.in>
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;
use completion_info;
use stdClass;

class plugin_myassignments extends pluginbase{
	public function init(){
		$this->fullname = get_string('myassignments','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('myassignments');
	}
	public function summary($data){
		return format_string($data->columname);
	}
	public function colformat($data){
		$align = (isset($data->align))? $data->align : '';
		$size = (isset($data->size))? $data->size : '';
		$wrap = (isset($data->wrap))? $data->wrap : '';
		return array($align,$size,$wrap);
	}
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0,$reporttype){
		global $DB, $CFG, $OUTPUT;
		$params = array();
		$params['userid'] = $user;
		$datesql = '';
		switch ($data->column) {
			case 'gradepass':
	            if(!isset($row->gradepass) && isset($data->subquery)){
	                $gradepass =  $DB->get_field_sql($data->subquery);
	            }else{
	                $gradepass = $row->{$data->column};
	            }
	            if($reporttype == 'table'){
                    $row->{$data->column} = !empty($gradepass) ? ROUND($gradepass, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($gradepass) ? ROUND($gradepass, 2) : 0;
                }
        		break;
        	case 'grademax':
	            if(!isset($row->grademax) && isset($data->subquery)){
	                $grademax =  $DB->get_field_sql($data->subquery);
	            }else{
	                $grademax = $row->{$data->column};
	            }
	            if($reporttype == 'table'){
                    $row->{$data->column} = !empty($grademax) ? ROUND($grademax, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($grademax) ? ROUND($grademax, 2) : 0;
                }
        		break;
		    case 'finalgrade':
	            if(!isset($row->finalgrade) && isset($data->subquery)){
	                $finalgrade =  $DB->get_field_sql($data->subquery);
	            }else{
	                $finalgrade = $row->{$data->column};
	            }
	            if($reporttype == 'table'){
                    $row->{$data->column} = !empty($finalgrade) ? ROUND($finalgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($finalgrade) ? ROUND($finalgrade, 2) : 0;
                }
        		break;
        	case 'lowestgrade':
	            if(!isset($row->lowestgrade) && isset($data->subquery)){
	                $lowestgrade =  $DB->get_field_sql($data->subquery);
	            }else{
	                $lowestgrade = $row->{$data->column};
	            }
	            if($reporttype == 'table'){
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : 0;
                }
        		break;
        	case 'highestgrade':
	            if(!isset($row->highestgrade) && isset($data->subquery)){
	                $highestgrade =  $DB->get_field_sql($data->subquery);
	            }else{
	                $highestgrade = $row->{$data->column};
	            }
	            if($reporttype == 'table'){
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : 0;
                }
        		break;
        	case 'noofsubmissions':
	            if(!isset($row->noofsubmissions) && isset($data->subquery)){
	                $noofsubmissions =  $DB->get_field_sql($data->subquery);
	            }else{
	                $noofsubmissions = $row->{$data->column};
	            }
	            $row->{$data->column} = !empty($noofsubmissions) ? $noofsubmissions : '0';
        		break;
			case 'status':
				$courserecord = $DB->get_record('course', array('id' => $row->courseid));
				$completion_info = new completion_info($courserecord);
				$coursemodulecompletion = $DB->get_record_sql("SELECT id FROM {course_modules_completion} WHERE userid = :userid AND coursemoduleid = :activityid", array('userid' => $row->userid, 'activityid' => $row->activityid), IGNORE_MULTIPLE);
				if(!empty($coursemodulecompletion)){
					try {
						$cm = new stdClass();
						$cm->id = $row->activityid;
						$completion = $completion_info->get_data($cm, false, $row->userid);
						switch ($completion->completionstate) {
						case COMPLETION_INCOMPLETE:
							$completionstatus = 'In-Complete';
							break;
						case COMPLETION_COMPLETE:
							$completionstatus = 'Completed';
							break;
						case COMPLETION_COMPLETE_PASS:
							$completionstatus = 'Completed (achieved pass grade)';
							break;
						case COMPLETION_COMPLETE_FAIL:
							$completionstatus = 'Fail';
							break;
						}
					} catch (exception $e) {
						$completionstatus = 'Not Yet Start';
					}
				} else{
				    $submissionsql = "SELECT id FROM {qbassign_submission}
				                       WHERE qbassignment = $row->id AND status = 'submitted'
				                        AND userid = :userid $datesql";
				    $assignsubmission = $DB->get_record_sql($submissionsql, $params, IGNORE_MULTIPLE);
				    if(!empty($assignsubmission)){
				         $completionstatus = '<span class="completed">Submitted</span>';
				     } else{
				         $completionstatus = '<span class="notyetstart">Not Yet Start</span>';
				     }
				}
				$row->{$data->column} = !empty($completionstatus) ? $completionstatus : '--';
			break;
			case 'noofdaysdelayed':
			    $latedaydifference = $row->overduedate - $row->duedate;
				$latedaydays = floor($latedaydifference / (60*60*24));
			    if($latedaydays >= 0 && $row->submissionstatus == 'submitted' && $row->duedate !=0){
                	if($latedaydays == 1){
					   $noofdaysdelayedstatus = 'Assignment was submitted - '.$latedaydays.' Day late';
					} else {
                       $noofdaysdelayedstatus = 'Assignment was submitted - '.$latedaydays.' Days late';
				   	}
		     	
			      $row->{$data->column} = !empty($noofdaysdelayedstatus) ? $noofdaysdelayedstatus : '--'; 
		     	} else {
			     	$row->{$data->column} = '--';
		     	}
                break;
            case 'totaltimespent':
                if(!isset($row->totaltimespent) && isset($data->subquery)){
                     $totaltimespent =  $DB->get_field_sql($data->subquery);
                 }else{
                    $totaltimespent = $row->{$data->column};
                 }
                if($reporttype == 'table'){
                  $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strTime($totaltimespent) : '--';
                }else{
                  $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
            break;
            case 'overduedate':
                if($row->submissionstatus == 'submitted' &&  $row->duedate !=0){
            	 	$row->{$data->column} = $row->overduedate > $row->duedate ? userdate($row->overduedate) : '--' ;
                } else {
                	$row->{$data->column} = '--';
                }
            break;
		}
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
