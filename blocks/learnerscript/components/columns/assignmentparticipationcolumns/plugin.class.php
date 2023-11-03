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
use moodle_url;
use DateTime;
class plugin_assignmentparticipationcolumns extends pluginbase{
  public function init(){
    $this->fullname = get_string('assignmentparticipationcolumns','block_learnerscript');
    $this->type = 'undefined';
    $this->form = true;
    $this->reporttypes = array('assignmentparticipation');
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
        case 'username':
                if(isset($row->userid) && $row->userid){
                  $row->{$data->column} =  $DB->get_field('user', 'username', array('id' => $row->userid));
                }else{
                  $row->{$data->column} = 'NA';
                }
            break;
        case 'submitteddate':
            $submitteddate =  $DB->get_field_sql("SELECT asb.timemodified FROM {qbassign} a JOIN {qbassign_submission} asb ON asb.qbassignment = a.id WHERE asb.userid = :userid AND a.id = :assignmentid AND asb.status = 'submitted'", ['userid' => $row->userid,'assignmentid' => $row->id]);

            $row->{$data->column} =   $submitteddate ? userdate($submitteddate) : 'NA';
            break;
        case 'finalgrade':
            $module = $DB->get_field('modules', 'name', array('id' => $row->module));
            $finalgrade = $DB->get_field_sql("SELECT gg.finalgrade AS finalgrade 
                            FROM {grade_grades} gg  
                            JOIN {grade_items} gi ON gg.itemid = gi.id  
                           WHERE 1 = 1 AND gi.itemmodule = 'qbassign' AND gg.userid = :userid AND gi.iteminstance = :assignid",['userid' => $row->userid,'assignid' => $row->id]);
              if(!empty($finalgrade)){
                  $row->{$data->column} =  ROUND($finalgrade, 2);
              }else{
                $url = new moodle_url('/mod/'.$module.'/view.php',
                                   array('id' => $row->activityid,'action' => 'grader','userid' => $row->userid));
                  $row->{$data->column} =  '<a href="'.$url.'"><button type="button" class="btn btn-primary">Grade</button></a>';
              }
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
          // if(isset($this->reportfilterparams['filter_status']) && $this->reportfilterparams['filter_status'] == 'inprogress'){
          //   $row->{$data->column} = '<span class="completed">Submitted</span>';
          // }else if(isset($this->reportfilterparams['filter_status']) && $this->reportfilterparams['filter_status'] == 'completed'){
          //   $row->{$data->column} = 'Completed' ;
          // }else{
          //   $row->{$data->column} = $row->{$data->column};
          // }
        break;
        case 'noofdaysdelayed':
            $latedaydifference = $row->overduedate - $row->due_date;
            $latedaydays = format_time($latedaydifference);
              if($latedaydifference > 0 && $row->submissionstatus == 'submitted' && $row->due_date !=0){
                  $noofdaysdelayedstatus = 'Assignment was submitted : '.$latedaydays.' late';             
                $row->{$data->column} = !empty($noofdaysdelayedstatus) ? $noofdaysdelayedstatus : '--'; 
              }elseif($latedaydifference < 0 && $row->submissionstatus == 'submitted' && $row->due_date !=0){
                  $noofdaysdelayedstatus = 'NA';             
                $row->{$data->column} = !empty($noofdaysdelayedstatus) ? $noofdaysdelayedstatus : '--'; 
              }elseif($latedaydifference >= 0 && ($row->submissionstatus == 'new' || $row->submissionstatus == '') && $row->due_date !=0){
                $date = new DateTime();
                $timestamp = $date->getTimestamp();
                $latedaydifference = $timestamp - $row->due_date;
                  $noofdaysdelayedstatus = 'Assignment is overdue by : '.format_time($latedaydifference).' late';             
                $row->{$data->column} = !empty($noofdaysdelayedstatus) ? $noofdaysdelayedstatus : '--'; 
              }else {
                $row->{$data->column} = '--';
              }
                  break;
            case 'duedate':
                if($row->due_date &&  $row->due_date !=0){
                $row->{$data->column} = $row->due_date ? userdate($row->due_date) : '--' ;
                } else {
                  $row->{$data->column} = '--';
                }
            break;
    }
    return (isset($row->{$data->column}))? $row->{$data->column} : '';
  }
}
