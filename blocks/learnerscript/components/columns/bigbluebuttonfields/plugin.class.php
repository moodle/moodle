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
use block_learnerscript\local\querylib;
use block_learnerscript\local\ls;
use context_system;
use html_writer;
use moodle_url;
class plugin_bigbluebuttonfields extends pluginbase {

    public function init() {
        $this->fullname = get_string('bigbluebuttonfields', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('bigbluebutton');
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

    public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0,$reporttype=null) {
        global $DB, $OUTPUT, $USER, $CFG; 
        $context = context_system::instance();
        switch($data->column) { 
            case 'session':
                $module = 'bigbluebuttonbn';
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                $url = new moodle_url('/mod/'.$module.'/view.php',
                         array('id' => $row->activityid));
                $row->{$data->column} = $activityicon . html_writer::tag('a', $row->session, array('href' => $url));
            break; 
            case 'course': 
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                $coursename = $DB->get_field('course', 'fullname', array('id'=>$row->courseid));
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($USER->id, $context);
                if(empty($reportid) || empty($checkpermissions)){
                    $row->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$row->courseid.'" />'.$row->course.'</a>';
                } else{
                    $row->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$row->courseid.'" />'.$row->course.'</a>';
                }
            break; 
            case 'timestart': 
                if (!isset($row->timestart)) {
                    $timestart =  $DB->get_field_sql($data->subquery);
                } else {
                    $timestart = $row->{$data->column};
                }
                $row->{$data->column} = !empty($timestart) ? userdate($timestart) : '--';
            break;
            case 'sessionjoinedat': 
                if (!isset($row->sessionjoinedat)) {
                    $sessionjoinedat =  $DB->get_field_sql($data->subquery);
                } else {
                    $sessionjoinedat = $row->{$data->column};
                }
                $row->{$data->column} = !empty($sessionjoinedat) ? userdate($sessionjoinedat) : '--';
            break; 
            case 'duration': 
                if (!isset($row->duration)) {
                    $duration =  $DB->get_field_sql($data->subquery);
                } else {
                    $duration = $row->{$data->column};
                }
                $duration = ($duration > 0) ? $duration : '';
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($duration) ? (new ls)->strTime($duration) : '--'; 
                } else {
                    $row->{$data->column} = !empty($duration) ? $duration : 0;
                }
                break; 
            case 'inactivestudents':  
                $learnersql  = (new querylib)->get_learners('', $row->courseid);
                $activeusers = $DB->get_record_sql("SELECT COUNT(DISTINCT bbbl.userid) AS active
                            FROM {user} u 
                            JOIN {bigbluebuttonbn_logs} bbbl ON bbbl.userid = u.id
                            JOIN {bigbluebuttonbn} bbb ON bbb.id = bbbl.bigbluebuttonbnid
                            JOIN {course} as c ON c.id = bbb.course
                            JOIN {user_enrolments} ue ON ue.userid = bbbl.userid AND bbbl.log = 'Join'
                            JOIN {enrol} e ON e.id = ue.enrolid 
                            JOIN {role_assignments} ra ON ra.userid = ue.userid
                            JOIN {context} ct ON ct.id = ra.contextid
                            JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'student'
                            WHERE bbbl.bigbluebuttonbnid = $row->id AND ct.instanceid = c.id 
                            AND u.confirmed = 1 AND u.deleted = 0 AND ra.userid IN ($learnersql) AND c.id = ".$row->courseid );
                $enrolledusers = $DB->get_records_sql($learnersql);
                $inactiveusers = COUNT($enrolledusers) - $activeusers->active;
                $row->{$data->column} = !empty($inactiveusers) ? $inactiveusers : 0; 
            break;
            case 'learner': 
                $userprofilereport = $DB->get_field('block_learnerscript', 'id', array('type'=> 'userprofile'), IGNORE_MULTIPLE);
                $checkpermissions = empty($userprofilereport) ? false : (new reportbase($userprofilereport))->check_permissions($USER->id, $context);
                if ($this->report->type == 'userprofile' || empty($userprofilereport) || empty($checkpermissions)) {
                    $row->{$data->column} = html_writer::tag('a', $row->learner,
                        array('href' => $CFG->wwwroot.'/user/profile.php?id='.$row->id.''));
                }else {
                    $row->{$data->column} = html_writer::tag('a', $row->learner,
                                    array('href' => $CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$userprofilereport.'&filter_users='.$row->id.''));
                }
            break; 
            case 'activestudents': 
                $userprofilereport = $DB->get_field('block_learnerscript', 'id', array('type'=> 'activestudents'), IGNORE_MULTIPLE);
                $checkpermissions = empty($userprofilereport) ? false : (new reportbase($userprofilereport))->check_permissions($USER->id, $context);
                if (empty($userprofilereport) || empty($checkpermissions)) {
                    $row->{$data->column} = $row->{$data->column};
                }else {
                    $row->{$data->column} = html_writer::tag('a', $row->activestudents,
                                    array('href' => $CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$userprofilereport.'&filter_session='.$row->id.''));
                }
            break;
            case 'sessionsduration': 
                if($reporttype == 'table'){
                  $row->{$data->column} = !empty($row->sessionsduration) ? (new ls)->strTime($row->sessionsduration) : '--';
                }else{
                  $row->{$data->column} = !empty($row->{$data->column}) ? $row->{$data->column} : 0;
                }

            break; 

        }
        return (isset($row->{$data->column}))? $row->{$data->column} : ' -- ';
    }
}
