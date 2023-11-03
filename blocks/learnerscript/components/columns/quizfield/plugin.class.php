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
use context_system;
use html_writer;

class plugin_quizfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('quizfield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array('myquizs', 'quizzes');
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
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        $quizrecord = $DB->get_record('quiz', array('id' => $row->id));
        if (isset($quizrecord->{$data->column})) {
            switch ($data->column) {
                case 'name':
                    $module = $DB->get_field_sql('SELECT name FROM {modules} AS m JOIN {course_modules} AS cm ON m.id = cm.module WHERE cm.id = :activityid', ['activityid' => $row->activityid]);
                    $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                    $quizrecord->{$data->column} = $activityicon . html_writer::link("$CFG->wwwroot/mod/$module/view.php?id=$row->activityid", $quizrecord->{$data->column},array("target" => "_blank"));
                break;
                case 'course':
                    $coursename = $DB->get_field('course', 'fullname', array('id'=>$quizrecord->{$data->column}));
                    $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                    $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                    if(empty($reportid) || empty($checkpermissions)){
                        $quizrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$quizrecord->course.'" target="_blank" class="edit">'.$coursename.'</a>';
                    } else{
                        $quizrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$quizrecord->course.'" target="_blank" class="edit">'.$coursename.'</a>';
                   }
                break;
                case 'timecreated':
                    $quizrecord->{$data->column} = $quizrecord->{$data->column} ? userdate($quizrecord->{$data->column}) : 'N/A';
                break;
                case 'timemodified':
                    $quizrecord->{$data->column} = $quizrecord->{$data->column} ? userdate($quizrecord->{$data->column}) : 'N/A';
                break;
                case 'timeclose':
                    $quizrecord->{$data->column} = $quizrecord->{$data->column} ? userdate($quizrecord->{$data->column}) : 'N/A';
                break;
                case 'timeopen':
                    $quizrecord->{$data->column} = $quizrecord->{$data->column} ? userdate($quizrecord->{$data->column}) : 'N/A';
                break;
                case 'timelimit':
                    $quizrecord->{$data->column} = $quizrecord->{$data->column} ? gmdate("H:i:s", $quizrecord->{$data->column}) : 'N/A';
                break;
            }
        }
       return (isset($quizrecord->{$data->column})) ? $quizrecord->{$data->column} : 'N/A';
    }

}
