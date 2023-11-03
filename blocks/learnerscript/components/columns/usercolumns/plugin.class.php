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
use block_learnerscript\local\reportbase;
use context_system;
use moodle_url;
use html_writer;

class plugin_usercolumns extends pluginbase{
	public function init(){
		$this->fullname = get_string('usercolumns','block_learnerscript');
		$this->type = 'undefined';
		$this->form = false;
		$this->reporttypes = array('users');
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
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0){
		global $DB, $USER;
        $context = context_system::instance();
		$reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'coursesoverview'), IGNORE_MULTIPLE);
		$quizreportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'myquizs'), IGNORE_MULTIPLE);
		$assignreportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'myassignments'), IGNORE_MULTIPLE);
		$userbadgeid = $DB->get_field('block_learnerscript', 'id', array('type' => 'userbadges'), IGNORE_MULTIPLE);
        $courseoverviewpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
		switch ($data->column) {
			case 'enrolled':
			     if(!isset($row->enrolled)){
		            $enrolled =  $DB->get_field_sql($data->subquery);
		         }else{
		            $enrolled = $row->{$data->column};
		         }
				$allurl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $reportid, 'filter_users' => $row->id));
				if(empty($courseoverviewpermissions) || empty($reportid)){
					$row->{$data->column} = $enrolled;
				} else{
					$row->{$data->column} = html_writer::tag('a', $enrolled,
					array('href' => $allurl));
				}
	            break;
			case 'inprogress':
			     if(!isset($row->inprogress)){
		            $inprogress =  $DB->get_field_sql($data->subquery);
		         }else{
		            $inprogress = $row->{$data->column};
		         }
				$inprogressurl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $reportid, 'filter_users' => $row->id, 'filter_status' => 'inprogress'));
				if(empty($courseoverviewpermissions) || empty($reportid)){
					$row->{$data->column} = $inprogress;
				} else{
					$row->{$data->column} = html_writer::tag('a', $inprogress,
					array('href' => $inprogressurl));
				}
	            break;
			case 'completed':
			     if(!isset($row->completed)){
		            $completed =  $DB->get_field_sql($data->subquery);
		         }else{
		            $completed = $row->{$data->column};
		         }
				$completedurl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $reportid, 'filter_users' => $row->id, 'filter_status' => 'completed'));
				if(empty($courseoverviewpermissions) || empty($reportid)){
					$row->{$data->column} = $completed;
				} else{
					$row->{$data->column} = html_writer::tag('a', $completed,
					array('href' => $completedurl));
				}
	            break;
			case 'assignments':
        		$assignpermissions = empty($assignreportid) ? false : (new reportbase($assignreportid))->check_permissions($USER->id, $context);
				$assignmenturl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $assignreportid, 'filter_users' => $row->id));
				if(empty($assignpermissions) || empty($assignreportid)){
					$row->{$data->column} = $row->assignments;
				} else{
					$row->{$data->column} = html_writer::tag('a', $row->assignments,
					array('href' => $assignmenturl));
				}
	            break;
			case 'quizes':
        		$quizpermissions = empty($quizreportid) ? false : (new reportbase($quizreportid))->check_permissions($USER->id, $context);
				$quizurl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $quizreportid, 'filter_users' => $row->id));
				if(empty($quizpermissions) || empty($quizreportid)){
					$row->{$data->column} = $row->quizes;
				} else{
					$row->{$data->column} = html_writer::tag('a', $row->quizes,
					array('href' => $quizurl));
				}

	            break;
			case 'badges':
			     if(!isset($row->badges)){
		            $badges =  $DB->get_field_sql($data->subquery);
		         }else{
		            $badges = $row->{$data->column};
		         }
        		$badgepermissions = empty($userbadgeid) ? false : (new reportbase($userbadgeid))->check_permissions($USER->id, $context);
				$badgeurl = new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $userbadgeid, 'filter_users' => $row->id));
				if(empty($badgepermissions) || empty($userbadgeid)){
					$row->{$data->column} = $badges;
				} else{
						$row->{$data->column} = html_writer::tag('a', $badges,
					array('href' => $badgeurl));
				}
	            break;
	        case 'grade':
	        	if(!isset($row->grade)){
		            $grade =  $DB->get_field_sql($data->subquery);
		         }else{
		            $grade = $row->{$data->column};
		         }
				$row->{$data->column} = (isset($grade) && $grade != ' / ')? $grade : '--';
	            break; 
	        case 'progress':
	        	if(!isset($row->progress)){
		            $progress =  $DB->get_field_sql($data->subquery);
		         }else{
		            $progress = $row->{$data->column};
		         }
		         $progress = empty($progress) ? 0 : $progress;
				return "<div class='spark-report' id='".html_writer::random_id()."' style='width:100px;' data-sparkline='$progress; progressbar' 
						data-labels = 'progress' >" . $progress . "</div>";
			break;
			 case 'status':
			 	$userstatus = $DB->get_record_sql('SELECT suspended, deleted FROM {user} WHERE id = :id', ['id' => $row->id]);

			    if($userstatus->suspended){
			    	$userstaus = '<span class="badge badge-warning">' . get_string('suspended') .'</span>';
			    } else if($userstatus->deleted){
			    	$userstaus = '<span class="badge badge-warning">' . get_string('deleted') .'</span>';
			    } else{
			    	$userstaus =  '<span class="badge badge-success">' . get_string('active') .'</span>';
			    }
                $row->{$data->column} = $userstaus;

            break;
		}
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
