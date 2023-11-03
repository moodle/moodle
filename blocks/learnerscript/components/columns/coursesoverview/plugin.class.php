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
use moodle_url;
use html_writer;

class plugin_coursesoverview extends pluginbase{
	public function init(){
		$this->fullname = get_string('coursesoverview','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('coursesoverview');
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
		global $DB, $USER;
        $systemcontext = context_system::instance();
        $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'courseprofile'), IGNORE_MULTIPLE);
        $courseprofilepermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $systemcontext);
        if (empty($reportid) || empty($courseprofilepermissions)) {
            $url = new moodle_url('/course/view.php', array('id' => $row->id));
        } else {
            $url = new moodle_url('/blocks/learnerscript/viewreport.php', array('id' => $reportid,'filter_courses' => $row->id));
        }
        $activityinfoid = $DB->get_field('block_learnerscript', 'id',array('type' => 'useractivities'), IGNORE_MULTIPLE);
        $reportpermissions = empty($activityinfoid) ? false : (new reportbase($activityinfoid))->check_permissions($USER->id, $systemcontext);
        $this->reportfilterparams['filter_modules'] = isset($this->reportfilterparams['filter_modules']) ? $this->reportfilterparams['filter_modules'] : 0;
        $allactivityurl = new moodle_url('/blocks/learnerscript/viewreport.php', 
                        array('id' => $activityinfoid, 'filter_courses' => $row->id, 
                               'filter_modules' => $this->reportfilterparams['filter_modules'], 'filter_users' => $this->reportfilterparams['filter_users']));
        $inprogressactivityurl = new moodle_url('/blocks/learnerscript/viewreport.php', array('id' => $activityinfoid, 'filter_courses' => $row->id, 'filter_status' => 'notcompleted', 'filter_modules' => $this->reportfilterparams['filter_modules'], 'filter_users' => $this->reportfilterparams['filter_users']));
        $completedactivityurl = new moodle_url('/blocks/learnerscript/viewreport.php', array('id' => $activityinfoid, 'filter_courses' => $row->id, 'filter_status' => 'completed', 'filter_modules' => $this->reportfilterparams['filter_modules'], 'filter_users' => $this->reportfilterparams['filter_users']));
        switch ($data->column) {
            case 'coursename':
                if (!isset($row->coursename)) {
                    $coursename =  $DB->get_field_sql($data->subquery);
                } else {
                    $coursename = $row->{$data->column};
                }
                $row->{$data->column} = !empty($coursename) ? html_writer::tag('a', $coursename, array('href' => $url)) : '--';
                break;
            case 'totalactivities':
                if (!isset($row->totalactivities)) {
                    $totalactivities =  $DB->get_field_sql($data->subquery);
                } else {
                    $totalactivities = $row->{$data->column};
                }
                if (empty($activityinfoid) || empty($reportpermissions)) {
                    $row->{$data->column} = !empty($totalactivities) ? $totalactivities : '--';
                } else {
                   $row->{$data->column} = !empty($totalactivities) ? html_writer::tag('a', $totalactivities, array('href' => $allactivityurl)) : '--';  
                }
                break;
            case 'inprogressactivities':
                if (!isset($row->inprogressactivities)) {
                    $inprogressactivities =  $DB->get_field_sql($data->subquery);
                } else {
                    $inprogressactivities = $row->{$data->column};
                }
                if (empty($activityinfoid) || empty($reportpermissions)) {
                    $row->{$data->column} = !empty($inprogressactivities) ? $inprogressactivities : '--'; 
                } else {
                    $row->{$data->column} = !empty($inprogressactivities) ? html_writer::tag('a', $inprogressactivities, array('href' => $inprogressactivityurl)) : '--';
                }
                break;
            case 'completedactivities':
                if (!isset($row->completedactivities)) {
                    $completedactivities =  $DB->get_field_sql($data->subquery);
                } else {
                    $completedactivities = $row->{$data->column};
                }
                if (empty($activityinfoid) || empty($reportpermissions)) {
                    $row->{$data->column} = !empty($completedactivities) ? $completedactivities : '--';
                } else {
                    $row->{$data->column} = !empty($completedactivities) ? html_writer::tag('a', $completedactivities, array('href' => $completedactivityurl)) : '--'; 
                }
                break;
            case 'grades':
                if (!isset($row->grades)) {
                    $grades =  $DB->get_field_sql($data->subquery);
                } else {
                    $grades = $row->{$data->column};
                }
                if($reporttype == 'table'){
                        $row->{$data->column} = !empty($grades) ? $grades : '--';
                }else{
                        $row->{$data->column} = !empty($grades) ? $grades : 0;
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
            
        }
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
