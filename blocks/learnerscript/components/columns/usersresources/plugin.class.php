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
class plugin_usersresources extends pluginbase {
	public function init(){
		$this->fullname = get_string('usersresources','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('usersresources');
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
		global $DB;
		switch($data->column){
			case 'totalresources':
                if (!isset($row->totalresources)) {
                    $totalresources =  $DB->get_field_sql($data->subquery);
                } else {
                    $totalresources = $row->{$data->column};
                }
                $row->{$data->column} = !empty($totalresources) ? $totalresources : '--';
                break;
            case 'totaltimespent':
                if (!isset($row->totaltimespent)) {
                    $totaltimespent =  $DB->get_field_sql($data->subquery);
                } else {
                    $totaltimespent = $row->{$data->column};
                }
                if($reporttype == 'table'){
                  $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strTime($totaltimespent) : '--';
                }else{
                  $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
                break;
            case 'numviews':
                if(!isset($row->numviews)){
                    $numviews = $DB->get_field_sql($data->subquery);
                }else {
                    $numviews = $row->{$data->column};
                }
                $row->{$data->column} = $numviews;
                break;
        }
		return (isset($row->{$data->column}))? $row->{$data->column} : '';
	}
}
