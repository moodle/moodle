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

class plugin_userfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('userfield', 'block_learnerscript');
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
        $row->id = isset($row->userid) ? $row->userid : $row->id;
        if (strpos($data->column, 'profile_') === 0) {
            $sql = "SELECT d.*, f.shortname, f.datatype
                      FROM {user_info_data} d ,{user_info_field} f
                     WHERE f.id = d.fieldid AND d.userid = ?";
            if ($profiledata = $DB->get_records_sql($sql, array($row->id))) {
                foreach ($profiledata as $p) {
                    if ($p->datatype == 'checkbox') {
                        $p->data = ($p->data) ? get_string('yes') : get_string('no');
                    }
                    if ($p->datatype == 'datetime') {
                        $p->data = userdate($p->data);
                    }
                    $row->{'profile_' . $p->shortname} = $p->data;
                }
            }
        }
        $userprofilereport = $DB->get_field('block_learnerscript', 'id', array('type'=> 'userprofile'), IGNORE_MULTIPLE);
        $userrecord = $DB->get_record('user',array('id'=>$row->id));
        $userrecord->fullname = '<span class = "userdp_name">';
        $userrecord->fullname .= $OUTPUT->user_picture($userrecord);
        $checkpermissions = empty($userprofilereport) ? false : (new reportbase($userprofilereport))->check_permissions($USER->id, $context);
        if ($this->report->type == 'userprofile' || empty($userprofilereport) || empty($checkpermissions)) {
            $userrecord->fullname .= html_writer::tag('a', fullname($userrecord),
                        array('href' => $CFG->wwwroot.'/user/profile.php?id='.$row->id.''));
        }else {
            $userrecord->fullname .= html_writer::tag('a', fullname($userrecord),
                                    array('href' => $CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$userprofilereport.'&filter_users='.$row->id.''));
        }
        $userrecord->fullname .= '</span>';
        $userfullname = $userrecord->fullname;
        if($CFG->messaging){
            //commented by Raghuvaran
            // $userrecord->fullname .= '<sup id="communicate">';
            // $userrecord->fullname .= $OUTPUT->pix_icon('message', 'message', 'block_learnerscript',
            //                                        array('class' => 'icon sendsms','id'=>"sendsms_" . $this->reportinstance . "_" . $row->id,
            //                                             'onclick'=>'(function(e){
            //                                                require("block_learnerscript/report").sendmessage({userid: '.$row->id.', reportinstance: ' . $this->reportinstance . '}) })(event)')).
            //                     '</sup>';
            //added by Raghuvaran
            $userrecord->fullname .= "<sup id='communicate'>";
            $userrecord->fullname .= html_writer::start_span('ls icon sendsms', array('id'=>"sendsms_" . $this->reportinstance . "_" . $row->id,
                                                         'onclick'=>'(function(e){
                                                            require("block_learnerscript/helper").sendmessage({userid: '.$row->id.', reportinstance: ' . $this->reportinstance . '}, \''.$userfullname.'\'); e.stopImmediatePropagation(); }) (event)'));
            $userrecord->fullname .= html_writer::end_span();
            $userrecord->fullname .='</sup>';
        }
        if (isset($userrecord->{$data->column})) {
            switch ($data->column) {
                case 'email':
                    $userrecord->{$data->column} = html_writer::tag('a', $userrecord->{$data->column},
                                                                        array('href' => 'mailto:'.$userrecord->{$data->column}.'' ));
                break;
                case 'firstaccess':
                case 'lastaccess':
                case 'currentlogin':
                case 'timemodified':
                case 'lastlogin':
                case 'timecreated':
                    $userrecord->{$data->column} = ($userrecord->{$data->column}) ? userdate($userrecord->{$data->column}) : '--';
                    break;
                case 'url':
                case 'description':
                case 'imagealt':
                case 'lastnamephonetic':
                case 'firstnamephonetic':
                case 'middlename':
                case 'alternatename':
                case 'secret':
                case 'lang':
                case 'theme':
                case 'icq':
                case 'skype':
                case 'yahoo':
                case 'aim':
                case 'msn':
                case 'phone1':
                case 'phone2':
                case 'department':
                case 'address':
                case 'institution':
                case 'idnumber':
                    if($userrecord->{$data->column} == NULL){
                        $userrecord->{$data->column} = "--";
                    }else if($userrecord->{$data->column}){
                        $userrecord->{$data->column} = $userrecord->{$data->column};
                    }else{
                        $userrecord->{$data->column} = "--";
                    }
                    // $userrecord->{$data->column} = ($userrecord->{$data->column}) ? ($userrecord->{$data->column}) : '--';
                    break;
                case 'country':
                    if($userrecord->{$data->column} == NULL){
                        $userrecord->{$data->column} = "--";
                    }else if($userrecord->{$data->column}){
                        $userrecord->{$data->column} = get_string(strtoupper($userrecord->{$data->column}), 'countries');
                    }else{
                        $userrecord->{$data->column} = "--";
                    }
                    break;
                case 'confirmed':
                case 'policyagreed':
                case 'maildigest':
                case 'ajax':
                case 'autosubscribe':
                case 'trackforums':
                case 'screenreader':
                case 'emailstop':
                case 'picture':
                    $userrecord->{$data->column} = ($userrecord->{$data->column}) ? get_string('yes') : get_string('no');
                    break;
                case 'description':
                    $userrecord->{$data->column} = $userrecord->{$data->column} ? $userrecord->{$data->column} : '--';
                break;
                case 'deleted':
                case 'suspended':
                    $userrecord->{$data->column} = $userrecord->{$data->column} > 0 ?
                                                    '<span class="label label-warning">' .  get_string('yes') . '</span>' :
                                                    '<span class="label label-success">' . get_string('no') . '</span>';
                break;
            }
        } else {
            $columnshortname = str_replace("profile_","",$data->column);
            $result = $DB->get_record_sql("SELECT uid.data, uif.datatype
                                FROM {user_info_data} uid
                                join {user_info_field} as uif on uif.id = uid.fieldid
                                WHERE uif.shortname = '{$columnshortname}' and uid.userid = ". $row->id);
            if($result->datatype == 'datetime' && $result->data > 0) {
                $advdata = userdate($result->data, get_string('strftimedaydate', 'core_langconfig'));
            } else {
                $advdata = $result->data;
            }
            $userrecord->{$data->column} = !empty($advdata) ? $advdata : '--';
        }
        return (isset($userrecord->{$data->column})) ? $userrecord->{$data->column} : '';
    }
}
