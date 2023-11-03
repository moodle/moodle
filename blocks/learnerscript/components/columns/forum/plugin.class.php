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
  * @author eAbyas Info Solutions
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls;
use block_learnerscript\local\querylib;

class plugin_forum extends pluginbase{
	public function init(){
		$this->fullname = get_string('forum','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('forum');
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
		global $DB, $CFG;
		switch ($data->column) {
            case 'discussionscount':
                if (!isset($row->discussionscount)) {
                    $discussionscount =  $DB->get_field_sql($data->subquery);
                } else {
                    $discussionscount = $row->{$data->column};
                }
                $row->{$data->column} = !empty($discussionscount) ? $discussionscount : '--';
                break;
            case 'posts':
                if (!isset($row->posts)) {
                    $posts =  $DB->get_field_sql($data->subquery);
                } else {
                    $posts = $row->{$data->column};
                }
                $row->{$data->column} = !empty($posts) ? $posts : '--';
                break;
            case 'replies':
                if (!isset($row->replies)) {
                    $replies =  $DB->get_field_sql($data->subquery);
                } else {
                    $replies = $row->{$data->column};
                }
                $row->{$data->column} = !empty($replies) ? $replies : '--';
                break;
            case 'wordscount':
                if (!isset($row->wordscount)) {
                    $wordscount =  $DB->get_field_sql($data->subquery);
                } else {
                    $wordscount = $row->{$data->column};
                }
                $row->{$data->column} = !empty($wordscount) ? $wordscount : '--';
                break;
		}
		return (isset($row->{$data->column})) ? $row->{$data->column} : ' ';
	}
}
