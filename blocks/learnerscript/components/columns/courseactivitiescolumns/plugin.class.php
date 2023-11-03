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

/**
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls;
use context_system;
use moodle_url;
use html_writer;

class plugin_courseactivitiescolumns extends pluginbase {

	public function init() {
		$this->fullname = get_string('courseactivitiescolumns', 'block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('courseactivities');
	}

	public function summary($data) {
		return format_string($data->columname);
	}

	public function colformat($data) {
		$align = (isset($data->align)) ? $data->align : '';
		$size = (isset($data->size)) ? $data->size : '';
		$wrap = (isset($data->wrap)) ? $data->wrap : '';
		return array($align, $size, $wrap);
	}

	// Data -> Plugin configuration data.
	// Row -> Complet course row c->id, c->fullname, etc...
	public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0, $reporttype) {
        global $CFG, $DB, $OUTPUT, $USER;
        $context = context_system::instance();
		switch($data->column){
            case 'activityname':
                $module = $DB->get_field('modules','name',array('id'=>$row->moduleid));
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $row->id));
                // if($module == "workshop"){
                //     $row->{$data->column} = $activityicon . html_writer::tag('a', $row->modulename, array('href' => $url));
                // }else{
                    $row->{$data->column} = $activityicon . html_writer::tag('a', $row->activityname, array('href' => $url));
                // }
            break;
            case 'highestgrade':
            case 'lowestgrade':
            case 'averagegrade':
            case 'grademax':
            case 'gradepass':
                if(!isset($row->{$data->column})){
                    $grade =  $DB->get_field_sql($data->subquery);
                 }else{
                    $grade = $row->{$data->column};
                 }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($grade) ? ROUND($grade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($grade) ? ROUND($grade, 2) : 0;
                }
            break;
            case 'learnerscompleted':
                if(!isset($row->{$data->column})){
                    $learnerscompleted =  $DB->get_field_sql($data->subquery);
                 }else{
                    $learnerscompleted = $row->{$data->column};
                 }
                $row->{$data->column} = $learnerscompleted;
            break;
            case 'progress':
                if(!isset($row->{$data->column})){
                    $progress =  $DB->get_field_sql($data->subquery);
                 }else{
                    $progress = $row->{$data->column};
                 }
				$row->{$data->column} =  "<div class='spark-report' id='spark-report$row->id' data-sparkline='$progress; progressbar' data-labels = 'progress' >" . $progress . "</div>";
            break;
            case 'grades';
    			$gradesReportID = $DB->get_field('block_learnerscript', 'id', array('type' => 'grades'), IGNORE_MULTIPLE);
                $checkpermissions =  empty($gradesReportID) ? false : (new reportbase($gradesReportID))->check_permissions($USER->id, $context);
                if(empty($gradesReportID) || empty($checkpermissions)){
                    $row->{$data->column} = 'N/A';
                } else{
                    $row->{$data->column} =  html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$gradesReportID&filter_courses=$row->course&filter_activities=$row->id", 'Grades');
                }
            break;
            case 'totaltimespent':
                if(!isset($row->{$data->column})){
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
                if(!isset($row->{$data->column})){
                    $numviews =  $DB->get_record_sql($data->subquery);
                 }else{
                    $numviews = $row->{$data->column};
                 }
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'noofviews'), IGNORE_MULTIPLE);
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                    if(empty($reportid) || empty($checkpermissions)){
                          $row->{$data->column} = get_string('numviews', 'report_outline', $numviews);
                    } else{
                         $row->{$data->column} = html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_courses=$row->course&filter_activities=$row->id", get_string('numviews', 'report_outline', $numviews), array("target" => "_blank"));
                    } 
            break;
            case 'description':
                $row->{$data->column} = $row->description ? $row->description : '--';
                break;
            
        }
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}

}
