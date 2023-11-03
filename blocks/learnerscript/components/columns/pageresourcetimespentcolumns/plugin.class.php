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
use block_learnerscript\local\ls;
use moodle_url;
use html_writer;

class plugin_pageresourcetimespentcolumns extends pluginbase{
	public function init(){
		$this->fullname = get_string('pageresourcetimespentcolumns','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('pageresourcetimespent');
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
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0,$reporttype){
		global $DB, $CFG, $OUTPUT;
        switch($data->column){
            case 'name':
                $module = $DB->get_field('modules','name',array('id'=>$row->module));
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $row->id, 'action' => 'grading'));
                $row->{$data->column} = $activityicon . html_writer::tag('a', $row->name, array('href' => $url));
            break;
            case 'totaltimespent':
              if($reporttype == 'table'){
                    $row->{$data->column} = $row->{$data->column} ? (new ls)->strTime($row->{$data->column}) : '--';
              }else{
                $row->{$data->column} = $row->{$data->column} ? $row->{$data->column} : '--';
              }
                
            break;
        }
        return (isset($row->{$data->column})) ? $row->{$data->column} : '';
    }
}
