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
use context_system;
use moodle_url;
use html_writer;
use DateTime;
class plugin_pendingactivities extends pluginbase{
	public function init(){
		$this->fullname = get_string('pendingactivities','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('pendingactivities');
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
		global $CFG, $DB, $OUTPUT, $USER;
    $date = new DateTime();
    $timestamp = $date->getTimestamp();
		switch ($data->column) {
      			
      			case 'activityname':
                  $row->activity = $row->activityname;
                  $module = $DB->get_field('modules','name',array('id'=>$row->moduleid));
                  $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                  $url = new moodle_url('/mod/'.$module.'/view.php',
                                   array('id' => $row->id));
                  $row->{$data->column} = $activityicon . html_writer::tag('a', $row->activityname, array('href' => $url));
                break;

            case 'course':
                    $course = $DB->get_field('course','fullname',array('id'=>$row->course));
                      $row->{$data->column} = $course ? $course : '--';
                break;

            case 'startdate':
            
                $row->{$data->column} = $row->{$data->column} ? userdate($row->{$data->column}) : 'NA';
                break;

            case 'enddate':
            
                $row->{$data->column} = $row->lastdate ? userdate($row->lastdate) : 'NA';
                break;
            case 'attempt':
                $module = $DB->get_field('modules','name',array('id'=>$row->moduleid));
                      $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                $url = new moodle_url('/mod/'.$module.'/view.php',
                                   array('id' => $row->id));
                $daydifference = $timestamp - $row->lastdate;
                $latedaydays = format_time($daydifference);
                $activity = '<b>'.$row->activity.'</b>'." is overdue by : ".$latedaydays;
               $row->{$data->column} =  '<a href="'.$url.'"><button type="button" class="btn btn-primary">Submit</button></a><br><br>'. $activity;
                break;
            }
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
