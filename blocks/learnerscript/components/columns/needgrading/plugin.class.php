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
use DateTime;
use moodle_url;
use html_writer;
class plugin_needgrading extends pluginbase{
	public function init(){
		$this->fullname = get_string('needgrading','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('needgrading');
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
        $date = new DateTime();
    	$timestamp = $date->getTimestamp();
		switch ($data->column) {
			case 'module':
				$row->modulename = $row->module;
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($row->module), $row->module, array('class' => 'icon'));
            	$row->{$data->column} =  ($activityicon.get_string('pluginname', $row->module));
            	break;
            case 'datesubmitted':
            	$row->{$data->column} = $row->timecreated ? userdate($row->timecreated) : 'NA';
            	break;
            case 'delay':
            	$delay = $timestamp - $row->timecreated;
            	$row->{$data->column} = $delay!=0 ? format_time($delay) : 'NA';
            	break;
            case 'grade':
                 $url = new moodle_url('/mod/'.$row->modulename.'/view.php',
                                   array('id' => $row->cmd,'action' => 'grader','userid' => $row->userid));
                  $row->{$data->column} =  '<a href="'.$url.'"><button type="button" class="btn btn-primary">Grade</button></a>';
            	break;
		}
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
