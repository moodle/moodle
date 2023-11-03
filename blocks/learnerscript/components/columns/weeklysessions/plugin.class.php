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
  * @author: jahnavi<jahnavi@eabyas.com>
  * @date: 2022
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;

class plugin_weeklysessions extends pluginbase{
	public function init(){
		$this->fullname = get_string('weeklysessions','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('weeklysessions');
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
		global $DB;
		switch ($data->column) {
            case 'timespent':
                if (!isset($row->timespent) && $data->subquery) {
                    $timespent =  $DB->get_field_sql($data->subquery);
                } else {
                    $timespent = $row->{$data->column};
                }
                $row->{$data->column} = !empty($timespent) ? (new ls)->strTime($timespent) : '--';
                break;
            }
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
