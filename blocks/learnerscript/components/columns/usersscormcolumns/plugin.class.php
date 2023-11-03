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
  * @author: arun<arun@eabyas.in>
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls;
use context_system;
use html_writer;
use moodle_url;
class plugin_usersscormcolumns extends pluginbase{
    public function init(){
        $this->fullname = get_string('usersscormcolumns','block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('usersscorm');
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
        global $CFG, $DB, $OUTPUT, $USER;
        $context = context_system::instance();
        $myscormreportid = $DB->get_field('block_learnerscript', 'id', array('type' => 'myscorm'), IGNORE_MULTIPLE);
        switch($data->column){
            case 'inprogress':
                if (!isset($row->inprogress)) {
                    $inprogress =  $DB->get_field_sql($data->subquery);
                } else {
                    $inprogress = $row->{$data->column};
                }
                $row->{$data->column} = !empty($inprogress) ? $inprogress : '--';
                break;
            case 'notattempted':
                if (!isset($row->notattempted)) {
                    $notattempted =  $DB->get_field_sql($data->subquery);
                } else {
                    $notattempted = $row->{$data->column};
                }
                $row->{$data->column} = !empty($notattempted) ? $notattempted : '--';
                break;
            case 'completed':
                if (!isset($row->completed)) {
                    $completed =  $DB->get_field_sql($data->subquery);
                } else {
                    $completed = $row->{$data->column};
                }
                $row->{$data->column} = !empty($completed) ? $completed : '--';
                break;
            case 'firstaccess':
                if (!isset($row->firstaccess)) {
                    $firstaccess =  $DB->get_field_sql($data->subquery);
                } else {
                    $firstaccess = $row->{$data->column};
                }
                $row->{$data->column} = !empty($firstaccess) ? userdate($firstaccess) : '--';
                break;
            case 'lastaccess':
                $attempt = $DB->get_field_sql("SELECT MAX(sst.attempt) FROM {scorm_scoes_track} sst 
                    JOIN {scorm} s ON s.id = sst.scormid WHERE sst.userid = :id AND s.course = :courseid ", ['id' => $row->id, 'courseid' => $courseid]);
                if (!empty($attempt)) {
                    $value = $DB->get_field_sql("SELECT sst.timemodified FROM {scorm_scoes_track} sst 
                                            JOIN {scorm} s ON s.id = sst.scormid 
                                            WHERE sst.element = :element 
                                            AND sst.userid = :id AND s.course = :courseid 
                                            AND sst.attempt = :attempt", ['element' => 'cmi.core.total_time', 'id' => $row->id, 'courseid' => $courseid, 'attempt' => $attempt]);
                    if (empty($value)) {
                        $lastaccess = $DB->get_field_sql("SELECT sst.timemodified FROM {scorm_scoes_track} sst 
                                                JOIN {scorm} s ON s.id = sst.scormid 
                                                WHERE sst.userid = :id AND s.course = :courseid 
                                                AND sst.element = :element AND sst.attempt = :attempt
                                                ", ['id' => $row->id, 'courseid' => $courseid, 'element' => 'x.start.time', 'attempt' => $attempt]);
                        $row->{$data->column} = $lastaccess ? userdate($lastaccess) : '--';
                    } else {
                        $row->{$data->column} = $value ? userdate($value) : '--';
                    }
                }
            break;
            case 'total':
                $myscormpermissions = empty($myscormreportid) ? false : (new reportbase($myscormreportid))->check_permissions($USER->id, $context);
                $url = new moodle_url('/blocks/learnerscript/viewreport.php',
                                array('id' => $myscormreportid, 'filter_users' => $row->id ,'filter_courses' => $courseid));
                $total = html_writer::tag('a', 'Total', array('class'=> 'btn', 'href' => $url));

                if (empty($myscormpermissions) || empty($myscormreportid)) {
                    $row->{$data->column} = 'Total';
                } else{
                    $row->{$data->column} = $total;
                }
            break;
            case 'totaltimespent':
                if (!isset($row->totaltimespent)) {
                    $totaltimespent =  $DB->get_field_sql($data->subquery);
                } else {
                    $totaltimespent = $row->{$data->column};
                }
                if ($reporttype == 'table') {
                  $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strTime($totaltimespent) : '--';
                } else {
                  $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
                break;
            case 'numviews':
                if (!isset($row->numviews) && isset($data->subquery)) {
                    $numviews =  $DB->get_field_sql($data->subquery);
                } else {
                    $numviews = $row->{$data->column};
                }
                $row->{$data->column} = !empty($numviews) ? $numviews : '--';
                break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : '--';
    }
}
