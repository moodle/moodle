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
 * LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use context_system;
use moodle_url;
use html_writer;
class plugin_assignmentfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('assignmentfield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array('assignment', 'myassignments');
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
        $assignmentrecord = $DB->get_record('qbassign', array('id' => $row->id));
        $activityid = $DB->get_field_sql("SELECT cm.id FROM {course_modules} cm JOIN {modules} m ON m.id = cm.module AND m.name = 'qbassign' AND cm.instance = $row->id");
        switch($data->column){
            case 'name':
                $module = 'qbassign';
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                if(is_siteadmin()){
                    $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $activityid, 'action' => 'grading'));
                }else {
                    $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $activityid));
                }
                $assignmentrecord->{$data->column} = $activityicon . html_writer::tag('a', $assignmentrecord->name, array('href' => $url));
            break;
            case 'course':
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                $coursename = $DB->get_field('course', 'fullname', array('id'=>$assignmentrecord->course));
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                if(empty($reportid) || empty($checkpermissions)){
                    $assignmentrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$assignmentrecord->course.'" />'.$coursename.'</a>';
                } else{
                    $assignmentrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$assignmentrecord->course.'" />'.$coursename.'</a>';
                }
            break;
            case 'duedate':
            case 'allowsubmissionsfromdate':
            case 'timemodified':
            case 'gradingduedate':
            case 'cutoffdate':
                $assignmentrecord->{$data->column} = ($assignmentrecord->{$data->column}) ? userdate($assignmentrecord->{$data->column}) : '--';
            break;
            case 'intro':
                $assignmentrecord->{$data->column} = !empty($assignmentrecord->{$data->column}) ? $assignmentrecord->{$data->column} : '--';
		    break;
            case 'maxattempts':
              if ($assignmentrecord->{$data->column} == -1) {
                $assignmentrecord->{$data->column} = get_string('unlimited');
              } else {
                $assignmentrecord->{$data->column} = $assignmentrecord->{$data->column};
              }
              break;

        }
        return (isset($assignmentrecord->{$data->column})) ? $assignmentrecord->{$data->column} : '';
    }

}
