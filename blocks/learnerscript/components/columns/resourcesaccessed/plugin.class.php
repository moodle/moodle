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
class plugin_resourcesaccessed extends pluginbase{
	public function init(){
		$this->fullname = get_string('resourcesaccessed','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('resources_accessed');
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
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0){
        global $DB, $CFG, $OUTPUT, $USER;
    	$context = context_system::instance();
        switch ($data->column) {
            case 'coursefullname':
                $coursereportid = $DB->get_field('block_learnerscript', 'id',  array('type' => 'courseprofile'), IGNORE_MULTIPLE);
                $checkpermissions = empty($coursereportid) ? false :  (new reportbase($coursereportid))->check_permissions($USER->id, $context);
                if(empty($coursereportid) || empty($checkpermissions)){
                	$row->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$row->courseid.'" target="_blank" class="edit">'.$row->coursefullname.'</a>';
                } else{
	                $row->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$coursereportid.'&filter_courses='.$row->courseid.'" />'.$row->coursefullname.'</a>';
	            }
            break;
        }
		return (isset($row->{$data->column}))? $row->{$data->column} : '--';
	}
}
