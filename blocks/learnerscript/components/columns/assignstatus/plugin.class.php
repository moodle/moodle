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

class plugin_assignstatus extends pluginbase{
	public function init(){
		$this->fullname = get_string('assignstatus','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('assignstatus');
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
		$datesql = '';
		switch ($data->column) {
            case 'total':
            case 'completed':
            case 'pending':
            case 'overdue':
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
