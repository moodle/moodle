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

class plugin_userquizzes extends pluginbase{
    public function init(){
        $this->fullname = get_string('userquizzes','block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('userquizzes');
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
        global $DB, $CFG, $USER;
        $context = context_system::instance();
        $myquizreport = $DB->get_field('block_learnerscript','id',array('type' => 'myquizs', 'visible' => 1), IGNORE_MULTIPLE);
        $link = $CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$myquizreport.'&filter_users='.$row->userid.'&filter_courses='.$row->course.'';
        $myquizpermissions = empty($myquizreport) ? false : (new reportbase($myquizreport))->check_permissions($USER->id, $context);
        switch ($data->column) {
           case 'totalquizs':
                $total = html_writer::tag('a', 'Total', array('class'=> 'btn', 'href' => $link));
                if(empty($myquizpermissions) || empty($myquizreport)){
                    $row->{$data->column} = '--';
                } else{
                    $row->{$data->column} =  $total;
                }
            break;
             case 'inprogressquizs':
                if(!isset($row->inprogressquizs) && isset($data->subquery)){
                    $inprogressquizs =  $DB->get_field_sql($data->subquery);
                }else{
                    $inprogressquizs = $row->{$data->column};
                }
                if(empty($myquizpermissions) || empty($myquizreport)){
                     $row->{$data->column} = !empty($inprogressquizs) ? $inprogressquizs : '--';
                } else{
                    $row->{$data->column} =  html_writer::link($link.'&filter_status=inprogress',$inprogressquizs,array('target'=>'_blank'));
                }
            break;
            case 'finishedquizs':
                if(!isset($row->finishedquizs) && isset($data->subquery)){
                    $finishedquizs =  $DB->get_field_sql($data->subquery);
                }else{
                    $finishedquizs = $row->{$data->column};
                }
                $row->{$data->column} =  $finishedquizs;
            break;
            case 'completedquizs':
                if(!isset($row->completedquizs) && isset($data->subquery)){
                    $completedquizs =  $DB->get_field_sql($data->subquery);
                }else{
                    $completedquizs = $row->{$data->column};
                }
                if(empty($myquizpermissions) || empty($myquizreport)){
                    $row->{$data->column} = !empty($completedquizs) ? $completedquizs : '--';
                } else{
                    $row->{$data->column} =  html_writer::link($link.'&filter_status=completed',$completedquizs,array('target'=>'_blank'));
                }
            break;
             case 'notattemptedquizs':
                if(!isset($row->notattemptedquizs) && isset($data->subquery)){
                    $notattemptedquizs =  $DB->get_field_sql($data->subquery);
                }else{
                    $notattemptedquizs = $row->{$data->column};
                }
                if(empty($myquizpermissions) || empty($myquizreport)){
                    $row->{$data->column} = !empty($notattemptedquizs) ? $notattemptedquizs : '--';
                } else{
                    // $row->{$data->column} = $notattemptedquizs;
                    $row->{$data->column} =  html_writer::link($link.'&filter_status=notattempted',$notattemptedquizs,array('target'=>'_blank'));
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
                if(!isset($row->numviews) && isset($data->subquery)){
                    $numviews =  $DB->get_field_sql($data->subquery);
                }else{
                    $numviews = $row->{$data->column};
                }
                $row->{$data->column} = !empty($numviews) ? $numviews : '--';
                break;
        }
    return (isset($row->{$data->column})) ? $row->{$data->column} : '';
    }
}
