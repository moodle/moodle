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

class plugin_activityfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('activityfield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array('users', 'usercourses', 'grades');
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
    // Row -> Complet user row c->id, c->fullname, etc...
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        if(isset($row->activityid)) {
            $activityid = $row->activityid;
        }else{
            $activityid = $row->id;
        }
        $courserecord = $DB->get_record('course_modules',array('id'=>$activityid));
            switch ($data->column) {
                case 'groupmode':
                    if($courserecord->{$data->column} == 0){
                        $courserecord->{$data->column} = get_string('groupsnone');
                    } else if ($courserecord->{$data->column} == 1){
                        $courserecord->{$data->column} = get_string('groupsseparate');
                    } else if ($courserecord->{$data->column} == 2){
                        $courserecord->{$data->column} = get_string('groupsvisible');
                    }
                break;
                case 'completion':
                    $courserecord->{$data->column} = $courserecord->{$data->column} > 0 ?
                                                    '<span class="label label-success">' .  get_string('enabled', 'block_learnerscript') . '</span>' :
                                                    '<span class="label label-warning">' . get_string('disabled', 'block_learnerscript') . '</span>';
                break;
                case 'completiongradeitemnumber':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? ($courserecord->{$data->column}) : 'N/A';
                break;
                case 'course':
                    $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                    $coursename = $DB->get_field('course', 'fullname', array('id' => $courserecord->course));

                    $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                    if(empty($reportid) || empty($checkpermissions)){
                         $courserecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courserecord->course.'" />'.$coursename.'</a>';
                    } else{
                        $courserecord->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$courserecord->course.'" />'.$coursename.'</a>';
                    }
                break;
                case 'visible':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ?
                                                    '<span class="label label-success">' . get_string('active') .'</span>':
                                                    '<span class="label label-warning">' . get_string('no'). '</span>';
                break;
                case 'module':
                    $moduletype = $DB->get_field('modules', 'name', array('id' => $courserecord->module));
                    $activityicon1 = $OUTPUT->pix_icon('icon', ucfirst($moduletype), $moduletype, array('class' => 'icon'));
                    $courserecord->{$data->column} = $activityicon1 . get_string('pluginname', $moduletype);;
                break;
                case 'section':
                    $format = \course_get_format($courserecord->course);
                    $modinfo = \get_fast_modinfo($courserecord->course);
                    $modules = $modinfo->get_used_module_names();
                    $sections = array();
                    if ($format->uses_sections()) {
                        foreach ($modinfo->get_section_info_all() as $section) {
                            if ($section->uservisible) {
                                $sections[] = $format->get_section_name($section);
                            }
                        }
                    }
                    $sectionid = $DB->get_field_sql("SELECT section FROM {course_sections} WHERE id = :sectionid", ['sectionid' => $courserecord->section]); 
                    foreach($sections as $k => $value){
                        if ($k == $sectionid) {
                            $courserecord->{$data->column} = $value;
                        }
                    }
                break;
                case 'added':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? userdate($courserecord->{$data->column}) : '--';
                break;
                case 'idnumber':
                    $courserecord->{$data->column} = !empty($courserecord->{$data->column}) ? ($courserecord->{$data->column}) : '--';
                break;
                case 'availability':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? ($courserecord->{$data->column}) : '--';
                break;
                case 'completionexpected':
                    $courserecord->{$data->column} = ($courserecord->{$data->column}) ? userdate($courserecord->{$data->column}) : '--';
                break;
                case 'showdescription':
                    if ($courserecord->{$data->column} == 0) {
                        $courserecord->{$data->column} = get_string('hide');
                    } else {
                        $courserecord->{$data->column} = get_string('show');
                    }
                    break;
            }
        return (isset($courserecord->{$data->column})) ? $courserecord->{$data->column} : '';
    }

}
