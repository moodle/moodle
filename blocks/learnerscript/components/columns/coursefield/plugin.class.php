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

class plugin_coursefield extends pluginbase {

    public function init() {
        $this->fullname = get_string('coursefield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array('courses', 'activitystatus',
            'popularresources');
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
        global $DB, $CFG, $USER;
        $context = context_system::instance();
        $courserecord = $DB->get_record('course',array('id'=>$row->id));
        $coursereportid = $DB->get_field('block_learnerscript', 'id', array('type'=>'courseprofile'), IGNORE_MULTIPLE);
        if (isset($courserecord->{$data->column})) {
            switch ($data->column) {
                case 'enrolments':
                    $courserecord->{$data->column} = '--';
                    break;
                case 'enrolstartdate':
                case 'enrolenddate':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? userdate($courserecord->{$data->column}) : '--';
                    break;
                case 'lang':
                case 'calendartype':
                case 'theme':
                case 'summary':
                case 'idnumber':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? ($courserecord->{$data->column}) : '--';
                    break;
                case 'visibleold':
                case 'visible':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ?
                                                    '<span class="label label-success">' . get_string('active') .'</span>':
                                                    '<span class="label label-warning">' . get_string('no'). '</span'  ;
                    break;
                case 'enrollable':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ?
                                                    '<span class="label label-success">' .  get_string('yes') . '</span>' :
                                                    '<span class="label label-warning">' . get_string('no') . '</span>';
                    break;
                case 'enablecompletion':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ?
                                                    '<span class="label label-success">' .  get_string('enabled', 'block_learnerscript') . '</span>' :
                                                    '<span class="label label-warning">' . get_string('disabled', 'block_learnerscript') . '</span>';
                    break;
                case 'fullname':
                    $checkpermissions = empty($coursereportid) ? false : (new reportbase($coursereportid))->check_permissions($USER->id, $context);
                    if($this->report->type == 'courseprofile' || empty($coursereportid) || empty($checkpermissions)){
                        $courserecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courserecord->id.'" />'.$courserecord->{$data->column}.'</a>';
                    }else if($coursereportid){
                        $courserecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$coursereportid.'&filter_courses='.$courserecord->id.'" />'.$courserecord->{$data->column}.'</a>';
                    }
                break;
                case 'category':
                    if($courserecord->{$data->column} > 0) {
                        $courserecord->{$data->column} = $DB->get_field('course_categories', 'name', array('id' => $courserecord->{$data->column}));
                    } else {
                        $courserecord->{$data->column};
                    }
                break;
                case 'timecreated':
                case 'timemodified';
                case 'cacherev':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? userdate($courserecord->{$data->column}) : '--';
                break;
                case 'groupmode':
                    if($courserecord->{$data->column} == 0){
                        $courserecord->{$data->column} = get_string('groupsnone');
                    } else if ($courserecord->{$data->column} == 1){
                        $courserecord->{$data->column} = get_string('groupsseparate');
                    } else if ($courserecord->{$data->column} == 2){
                        $courserecord->{$data->column} = get_string('groupsvisible');
                    }
                break;
                case 'startdate':
                case 'enddate':
                    if ($coursereportid) {
                        $courserecord->{$data->column} = ($courserecord->{$data->column}) ?
                        date("l, d F Y", $courserecord->{$data->column}) : '--';
                    } else {
                        $courserecord->{$data->column} = ($courserecord->{$data->column}) ? userdate($courserecord->{$data->column}) : '--';
                    }
                break;
            }
        }
       return (isset($courserecord->{$data->column})) ? $courserecord->{$data->column} : '--';
    }

}
