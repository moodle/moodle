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
use block_learnerscript\local\ls;
use block_learnerscript\local\reportbase;
use context_system;
use html_writer;
class plugin_scorm extends pluginbase {

    public function init() {
        $this->fullname = get_string('scormfield', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('courses', 'activitystatus', 'courseaverage',
            'popularresources','scorm');
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
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0,$reporttype) {
        global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        $scormreportid = $DB->get_field('block_learnerscript', 'id', array('type'=>'scormparticipation'), IGNORE_MULTIPLE);
        $checkpermissions = empty($scormreportid) ? false : (new reportbase($scormreportid))->check_permissions($USER->id, $context);
        switch ($data->column) {
            case 'noofattempts':
                if (!isset($row->noofattempts)) {
                    $noofattempts =  $DB->get_field_sql($data->subquery);
                } else {
                    $noofattempts = $row->{$data->column};
                }
                $row->{$data->column} = !empty($noofattempts) ? $noofattempts : '--';
                break;
            case 'noofcompletions':
                if (!isset($row->noofcompletions)) {
                    $noofcompletions =  $DB->get_field_sql($data->subquery);
                } else {
                    $noofcompletions = $row->{$data->column};
                }
                if(empty($scormreportid) || empty($checkpermissions)){
                    $row->{$data->column} = !empty($noofcompletions) ? $noofcompletions : '--';
                }else{
                     $row->{$data->column} = !empty($noofcompletions) ? html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$scormreportid&filter_courses=$row->course&filter_scorm=$row->id&filter_activity=$row->activityid&filter_status=completed", $noofcompletions) : '--';
                }
               
                break;
            case 'highestgrade':
                if (!isset($row->highestgrade)) {
                    $highestgrade =  $DB->get_field_sql($data->subquery);
                } else {
                    $highestgrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : 0;
                }
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
            case 'lowestgrade':
                if (!isset($row->lowestgrade)) {
                    $lowestgrade =  $DB->get_field_sql($data->subquery);
                } else {
                    $lowestgrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : 0;
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
                         $row->{$data->column} = html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_courses=$row->course&filter_activities=$row->activityid", get_string('numviews', 'report_outline', $numviews), array("target" => "_blank"));
                    } 
                break;
            case 'status':
              $row->{$data->column}= ($row->{$data->column}) ?
                                                    '<span class="label label-success">' . get_string('active') .'</span>':
                                                    '<span class="label label-warning">' . get_string('inactive'). '</span'  ;
            break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : '';
    }

}
