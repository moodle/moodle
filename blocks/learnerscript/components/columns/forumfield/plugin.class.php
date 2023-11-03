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

class plugin_forumfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('forumfield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array('forum');
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
        $forumrecord = $DB->get_record('forum', array('id' => $row->id));
        $activityid = $DB->get_field_sql("SELECT cm.id FROM {course_modules} cm JOIN {modules} m ON m.id = cm.module AND m.name = 'forum' AND cm.instance = $row->id");
        switch($data->column){
            case 'completionposts':
                 $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'forcesubscribe':
                 $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'grade_forum':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'scale':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'cutoffdate':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'assesstimestart':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'trackingtype':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? $forumrecord->{$data->column} : '--';
            break;
            case 'name':
                $module = 'forum';
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                if(is_siteadmin()){
                    $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $activityid, 'action' => 'grading'));
                }else {
                    $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $activityid));
                }
                $forumrecord->{$data->column} = $activityicon . html_writer::tag('a', $forumrecord->name, array('href' => $url));
            break;
            case 'course':
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                $coursename = $DB->get_field('course', 'fullname', array('id'=>$forumrecord->course));
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                if(empty($reportid) || empty($checkpermissions)){
                    $forumrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$forumrecord->course.'" />'.$coursename.'</a>';
                } else{
                    $forumrecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$forumrecord->course.'" />'.$coursename.'</a>';
                }
            break;
            case 'timemodified':
                $forumrecord->{$data->column} = ($forumrecord->{$data->column}) ? userdate($forumrecord->{$data->column}) : '--';
            break;

        }
        return (isset($forumrecord->{$data->column})) ? $forumrecord->{$data->column} : '';
    }

}
