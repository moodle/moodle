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
  * @author eAbyas Info Solutions
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;
use block_learnerscript\local\reportbase;
use context_system;
use html_writer;

class plugin_assignment extends pluginbase{
    public function init(){
        $this->fullname = get_string('assignment','block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('assignment');
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
    public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0,$reporttype=null){
        global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        $gradereportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'grades'), IGNORE_MULTIPLE);
        $activityid = $DB->get_field_sql("SELECT cm.id FROM {course_modules} cm JOIN {modules} m ON m.id = cm.module AND m.name = 'qbassign' AND cm.instance = :rowid", ['rowid' => $row->id]); 
        $assignmentid = $DB->get_field('block_learnerscript', 'id', array('type'=>'assignmentparticipation'), IGNORE_MULTIPLE);
        $checkpermissions = empty($assignmentid) ? false : (new reportbase($assignmentid))->check_permissions($USER->id, $context);
        switch ($data->column) {
            case 'submittedusers':
                if (!isset($row->submittedusers)) {
                    $submittedusers =  $DB->get_field_sql($data->subquery);
                } else {
                    $submittedusers = $row->{$data->column};
                }
                
                if(empty($assignmentid) || empty($checkpermissions)){
                    $row->{$data->column} = !empty($submittedusers) ? $submittedusers : '--';
                }else{
                    $row->{$data->column} = !empty($submittedusers) ? html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$assignmentid&filter_courses=$row->course&filter_assignment=$row->id&&filter_status=inprogress", $submittedusers) : '--';
                }
                break;
            case 'completedusers':
                if (!isset($row->completedusers)) {
                    $completedusers =  $DB->get_field_sql($data->subquery);
                } else {
                    $completedusers = $row->{$data->column};
                }
              if(empty($assignmentid) || empty($checkpermissions)){
                    $row->{$data->column} = !empty($completedusers) ? $completedusers : '--';
                }else{
                   $row->{$data->column} = !empty($completedusers) ? html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$assignmentid&filter_courses=$row->course&filter_assignment=$row->id&&filter_status=completed", $completedusers) : '--';
                }
                
                break;
            case 'needgrading':
                if (!isset($row->needgrading)) {
                    $needgrading =  $DB->get_field_sql($data->subquery);
                } else {
                    $needgrading = $row->{$data->column};
                }
                $row->{$data->column} = !empty($needgrading) ? $needgrading : '--';
                break;
            case 'avggrade':
                if (!isset($row->avggrade)) {
                    $avggrade =  $DB->get_field_sql($data->subquery);
                } else {
                    $avggrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($avggrade) ? ROUND($avggrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($avggrade) ? ROUND($avggrade, 2) : 0;
                }
                break;
            case 'gradepass':
                if (!isset($row->gradepass)) {
                    $gradepass =  $DB->get_field_sql($data->subquery);
                } else {
                    $gradepass = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($gradepass) ? ROUND($gradepass, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($gradepass) ? ROUND($gradepass, 2) : 0;
                }
                break;
            case 'grademax':
                if (!isset($row->grademax)) {
                    $grademax =  $DB->get_field_sql($data->subquery);
                } else {
                    $grademax = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($grademax) ? ROUND($grademax, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($grademax) ? ROUND($grademax, 2) : 0;
                }
                break;
            case 'totaltimespent':
                if (!isset($row->totaltimespent)) {
                    $totaltimespent =  $DB->get_field_sql($data->subquery);
                } else {
                    $totaltimespent = $row->{$data->column};
                }
                if($reporttype == 'table'){
                  $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strTime($totaltimespent) : '--';
                }else{
                  $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
                break;
            case 'numviews':
                if(!isset($row->numviews)){
                    $numviews = $DB->get_record_sql($data->subquery);
                }
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'noofviews'), IGNORE_MULTIPLE);
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                    if(empty($reportid) || empty($checkpermissions)){
                          $row->{$data->column} = get_string('numviews', 'report_outline', $numviews);
                    } else{
                         $row->{$data->column} = html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_courses=$row->course&filter_activities=$activityid", get_string('numviews', 'report_outline', $numviews), array("target" => "_blank"));
                    } 
                break;
        }
        return (isset($row->{$data->column})) ? $row->{$data->column} : ' ';
    }
}
