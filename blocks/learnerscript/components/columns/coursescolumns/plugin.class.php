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
use block_learnerscript\local\ls;
use context_system;
use html_writer;

class plugin_coursescolumns extends pluginbase{
	public function init(){
		$this->fullname = get_string('coursescolumns', 'block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('courses');
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
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0,$reporttype = 'table'){
		global $DB, $CFG, $USER;
        $context = context_system::instance();
		$usercoursesReportID = $DB->get_field('block_learnerscript', 'id', array('type' => 'usercourses'), IGNORE_MULTIPLE);
        $competencyreportid =  $DB->get_field('block_learnerscript', 'id', array('type' => 'coursecompetency'), IGNORE_MULTIPLE);
  	switch($data->column){
			case 'progress':
            if(!isset($row->progress) && isset($data->subquery)){
                $progress =  $DB->get_field_sql($data->subquery);
             }else{
                $progress = $row->{$data->column};
             }
             if($progress == ""){
                    $progress = '0.00';
             } 
             $progress = ROUND($progress, 2);
			 	$progresscheckpermissions =  empty($usercoursesReportID) ? false : 
                                    (new reportbase($usercoursesReportID))->check_permissions($this->reportclass->userid, $context);
			    if(empty($usercoursesReportID) || empty($progresscheckpermissions)){
					$avgcompletedlink = $progress;
				} else{
					$avgcompletedlink = html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$usercoursesReportID
														&filter_courses=$row->id&filter_status=all", $progress,
														array("target" => "_blank"));
				}
				return "<div class='spark-report' id='".html_writer::random_id()."' data-sparkline='$progress; progressbar'
						data-labels = 'inprogress, completed' data-link='$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$usercoursesReportID&filter_courses=$row->id&filter_status=all' >" . $avgcompletedlink . "</div>";
			break;
			case 'activities':
          if(!isset($row->activities) && isset($data->subquery)){
              $activities = $DB->get_field_sql($data->subquery);
          }else{
              $activities = $row->{$data->column};
          }
				$listofactivitiesReportID = $DB->get_field('block_learnerscript', 'id', array('type' => 'courseactivities'), IGNORE_MULTIPLE);
                $checkpermissions = empty($listofactivitiesReportID) ? false : (new reportbase($listofactivitiesReportID))->check_permissions($USER->id, $context);
			    if(empty($listofactivitiesReportID) || empty($checkpermissions)){
                    return $activities ;
                } else{
                    return html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$listofactivitiesReportID&filter_courses=$row->id",$activities,array("target" => "_blank"));
                }

			break;
            case 'competencies':
                if(!isset($row->competencies) && isset($data->subquery)){
                   $competencies = $DB->get_field_sql($data->subquery);
               }else{
                    $competencies = $row->{$data->column};
               }
                $enrolcheckpermissions = empty($competencyreportid) ? false : (new reportbase($competencyreportid))->check_permissions($USER->id, $context);
                if(empty($competencyreportid) || empty($enrolcheckpermissions)){
                    return $enrolments ;
                } else{
                    return html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$competencyreportid&filter_courses=$row->id&filter_status=all", $competencies, array("target" => "_blank"));
                }

            break;
			case 'enrolments':
                if(!isset($row->enrolments) && isset($data->subquery)){
                   $enrolments = $DB->get_field_sql($data->subquery);
               }else{
                    $enrolments = $row->{$data->column};
               }
				$enrolcheckpermissions = empty($usercoursesReportID) ? false : (new reportbase($usercoursesReportID))->check_permissions($USER->id, $context);
			    if(empty($usercoursesReportID) || empty($enrolcheckpermissions)){
                    return $enrolments ;
                } else{
                    return html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$usercoursesReportID&filter_courses=$row->id&filter_status=all", $enrolments, array("target" => "_blank"));
                }

			break;
			case 'completed':
                if(!isset($row->completed) && isset($data->subquery)){
                    $completed =  $DB->get_field_sql($data->subquery);
                 }else{
                    $completed = $row->{$data->column};
                 }

				$comcheckpermissions = empty($usercoursesReportID) ? false : (new reportbase($usercoursesReportID))->check_permissions($USER->id, $context);
			    if(empty($usercoursesReportID) || empty($comcheckpermissions)){
                    return $completed ;
                } else{
                    return html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$usercoursesReportID&filter_courses=$row->id&filter_status=completed", $completed , array("target" => "_blank"));
                }
			break;
            case 'badges':
                if(!isset($row->badges) && isset($data->subquery)){
                    $badges =  $DB->get_field_sql($data->subquery);
                 }else{
                    $badges = $row->{$data->column};
                 }
                 $row->{$data->column} = !empty($badges) ? $badges : 0;
            break;
			case 'highgrade':
                if(!isset($row->highgrade) && isset($data->subquery)){
                    $highgrade =  $DB->get_field_sql($data->subquery);
                 }else{
                    $highgrade = $row->{$data->column};
                 }
              if($reporttype == 'table'){
                $row->{$data->column} = !empty($highgrade) ? ROUND($highgrade, 2) : '--';
              }else{
                $row->{$data->column} = !empty($highgrade) ? ROUND($highgrade, 2) : 0;
              }
			break;
			case 'lowgrade':
                if(!isset($row->lowgrade) && isset($data->subquery)){
                    $lowgrade =  $DB->get_field_sql($data->subquery);
                 }else{
                    $lowgrade = $row->{$data->column};
                 }
              if($reporttype == 'table'){
                $row->{$data->column} = !empty($lowgrade) ? ROUND($lowgrade, 2) : '--';
              }else{
                $row->{$data->column} = !empty($lowgrade) ? ROUND($lowgrade, 2) : 0;
              }
			break;
			case 'avggrade':
                if(!isset($row->avggrade) && isset($data->subquery)){
                     $avggrade =  $DB->get_field_sql($data->subquery);
                 }else{
                    $avggrade = $row->{$data->column};
                 }
              if($reporttype == 'table'){
                $row->{$data->column} = !empty($avggrade) ? ROUND($avggrade, 2) : '--';
              }else{
                $row->{$data->column} = !empty($avggrade) ? ROUND($avggrade, 2) : 0;
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
      case 'numviews':
          // $numviews = $DB->get_record_sql($data->subquery);   
          $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'courseviews'), IGNORE_MULTIPLE);
          $comcheckpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
          if(empty($reportid) || empty($comcheckpermissions)){
            $row->{$data->column} = '--';
          }else{
            return html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_courses=$row->id", '<img src="'.$CFG->wwwroot.'/blocks/reportdashboard/pix/views.png" />', array("target" => "_blank"));
          }
      break;
      case 'status':
          $coursestatus = $DB->get_field_sql('SELECT visible FROM {course} WHERE id = :rowid', ['rowid' => $row->id]);
          if($coursestatus == 1){
              $coursestat = '<span class="label label-success">' . get_string('active') .'</span>';
          } else if($coursestatus == 0){
              $coursestat = '<span class="label label-warning">' . get_string('inactive') .'</span>';
          }
          $row->{$data->column} = $coursestat;
        break;
        case 'enrolmethods':
            if(!isset($row->enrolmethods) && isset($data->subquery)){
                $enrolmethods =  $DB->get_field_sql($data->subquery);
            }else{
                $enrolmethods = $row->{$data->column};
            }
            $row->{$data->column} = !empty($enrolmethods) ? $enrolmethods : '--';
        break;
			default:
				return (isset($row->{$data->column}))? $row->{$data->column} : '--';
			break;

		}
		return (isset($row->{$data->column}))? $row->{$data->column} : '--';
	}
}
