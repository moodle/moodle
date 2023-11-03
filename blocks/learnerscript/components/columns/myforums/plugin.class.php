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
  * @author: sowmya<sowmya@eabyas.in>
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use context_system;
use moodle_url;
use html_writer;

class plugin_myforums extends pluginbase{
	public function init(){
		$this->fullname = get_string('myforums','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('myforums');
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
	public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0){
		global $DB, $CFG, $OUTPUT, $USER;
        $context = context_system::instance();
        require_once($CFG->libdir . '/completionlib.php');
		$params = array();
		$params['userid'] = $user;
		switch ($data->column) {
			case 'forumname':
                    if(!isset($row->{$data->column})){
                        $forumname =  $DB->get_field_sql($data->subquery);
                     }else{
                        $forumname = $row->{$data->column};
                    }
	                $module = 'forum';
	                $forumicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
	                $url = new moodle_url('/mod/'.$module.'/view.php',
	                             array('id' => $row->activityid));
	                $row->{$data->column} = $forumicon . html_writer::tag('a', $row->forumname, array('href' => $url));
            break;
            case 'coursename':
                    if(!isset($row->{$data->column})){
                        $coursename =  $DB->get_field_sql($data->subquery);
                     }else{
                        $coursename = $row->{$data->column};
                    }
					$reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
					$checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($user, $context);
					if(empty($reportid) || empty($checkpermissions)){
					$row->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$row->courseid.'" />'.$row->{$data->column}.'</a>';
					} else{
					$row->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$row->courseid.'" />'.$row->{$data->column}.'</a>';
					}
            break;
            case 'noofdisscussions':
            case 'noofreplies':
            case 'wordcount':
                    if(!isset($row->{$data->column})){
                        $discussions =  $DB->get_field_sql($data->subquery);
                     }else{
                        $discussions = $row->{$data->column};
                     }
                    $row->{$data->column} = $discussions == null ? '--' : $discussions;
            break;
		}
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
