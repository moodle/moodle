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

class plugin_gradedactivity extends pluginbase {

    public function init() {
        $this->fullname = get_string('listofactivities', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('courses', 'grades','activities');
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

    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0,$reporttype) {
        global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        switch($data->column){

                case 'modulename':
                    if(!isset($row->{$data->column})){
                        $modulename =  $DB->get_field_sql($data->subquery);
                     }else{
                        $modulename = $row->{$data->column};
                     }
                    $module = $DB->get_field_sql('select m.name from {modules} as m JOIN {course_modules} as cm on m.id=cm.module
                                                   where cm.id= :activityid', ['activityid' => $row->activityid]);
                    $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                    $row->{$data->column} = $activityicon . html_writer::link("$CFG->wwwroot/mod/$module/view.php?id=$row->activityid", $modulename,array("target" => "_blank"));
                break;
                case 'highestgrade':
                case 'lowestgrade':
                case 'averagegrade':
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
                case 'totaltimespent':
                    if(!isset($row->totaltimespent)){
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
                    if ($this->colformat) {
                        return $row->numviews;
                    } else {
                        if(!isset($row->numviews)){
                            $numviews =  $DB->get_record_sql($data->subquery);
                         }else{
                            $numviews = $row->{$data->column};
                         }
                        $reportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'noofviews'), IGNORE_MULTIPLE);
                        $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                    if(empty($reportid) || empty($checkpermissions)){
                          $row->{$data->column} = get_string('numviews', 'report_outline', $numviews);
                    } else{
                         $row->{$data->column} = html_writer::link("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=$reportid&filter_courses=$row->courseid&filter_activities=$row->activityid", get_string('numviews', 'report_outline', $numviews), array("target" => "_blank"));
                    }
                    }
                    // $row->{$data->column} = get_string('numviews', 'report_outline', $row);
                break;
                case 'description':
                    $row->{$data->column} = $row->description ? $row->description : '--';
                break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : ' -- ';
    }
}
