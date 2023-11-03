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
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use context_system;
class plugin_competencycolumns extends pluginbase {

    public function init() {
        $this->fullname = get_string('competencycolumns', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('competency');
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

    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $CFG, $USER; 
        $context = context_system::instance();
        $coursereportid = $DB->get_field('block_learnerscript', 'id', array('type'=>'coursecompetency'), IGNORE_MULTIPLE);
        switch($data->column) { 
            // case 'competency':
            //     $compurl = $CFG->wwwroot . '/admin/tool/lp/user_competency_in_course.php?courseid='.$row->courseid.'&competencyid='.$row->id; 
            //     $competency = html_writer::tag('a', $row->competency, array('href' => $compurl)); 
            //     $row->{$data->column} = !empty($competency) ? $competency : '--';
            // break;
            case 'course':  
                $checkpermissions = empty($coursereportid) ? false : (new reportbase($coursereportid))->check_permissions($USER->id, $context);
                $sql = " SELECT c.id, c.fullname 
                        FROM {course} c 
                        JOIN {competency_coursecomp} comc ON comc.courseid = c.id 
                        WHERE c.visible = :visible AND comc.competencyid = :rowid"; 
                $courseslist = $DB->get_records_sql($sql, ['visible' => 1, 'rowid' => $row->id]);
                foreach ($courseslist as $course) {
                    if($this->report->type == 'courseprofile' || empty($coursereportid) || empty($checkpermissions)){
                        $row->{$data->column} .= '<li><a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'" />'.$course->fullname.'</a></li>';
                    }else if($coursereportid){ 
                        $row->{$data->column} .= '<li><a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$coursereportid.'&filter_courses='. $course->id .'" />'.$course->fullname.'</a>, </li>';
                    } 
                    // $row->{$data->column} .= '<li>' . $course->fullname . '</li>';
                }
                // $activitiesd = implode(', ', $data1);
                // $row->{$data->column} = !empty($activitiesd) ? $activitiesd : '--';
            break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : ' -- ';
    }
}
