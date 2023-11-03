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
use context_system;
use moodle_url;
use html_writer;

class plugin_useractivitiescolumns extends pluginbase{
    public function init(){
        $this->fullname = get_string('useractivities','block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('useractivities', 'popularresources');
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
        global $CFG,$DB,$OUTPUT;
        switch($data->column){
            case 'finalgrade':
                if(!isset($row->finalgrade) && isset($data->subquery)){
                    $finalgrade =  $DB->get_field_sql($data->subquery);
                }else{
                    $finalgrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($finalgrade) ? ROUND($finalgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($finalgrade) ? ROUND($finalgrade, 2) : 0;
                }
                break;
            case 'highestgrade':
                if(!isset($row->highestgrade) && isset($data->subquery)){
                    $highestgrade =  $DB->get_field_sql($data->subquery);
                }else{
                    $highestgrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($highestgrade) ? ROUND($highestgrade, 2) : 0;
                }
                break;
            case 'lowestgrade':
                if(!isset($row->lowestgrade) && isset($data->subquery)){
                    $lowestgrade =  $DB->get_field_sql($data->subquery);
                }else{
                    $lowestgrade = $row->{$data->column};
                }
                if($reporttype == 'table'){
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : '--';
                }else{
                    $row->{$data->column} = !empty($lowestgrade) ? ROUND($lowestgrade, 2) : 0;
                }
                break;
            case 'totaltimespent':
                if(!isset($row->totaltimespent) && isset($data->subquery)){
                    $totaltimespent =  $DB->get_field_sql($data->subquery);
                }else{
                    $totaltimespent = $row->{$data->column};
                }
                if($reporttype == 'table'){
                  $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strTime($totaltimespent) : '--';
                }else{
                  $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
                break;
            case 'numviews':
                if(!isset($row->numviews) && isset($data->subquery)){
                    $numviews =  $DB->get_field_sql($data->subquery);
                }else{
                    $numviews = $row->{$data->column};
                }
                $row->{$data->column} = !empty($numviews) ? $numviews : 0;
                break;
            case 'firstaccess':
            case 'lastaccess':
            case 'completedon':
                $row->{$data->column} = (isset($row->{$data->column})) ? userdate($row->{$data->column}) : '--';
            break;
            case 'modulename':
                $module = $DB->get_field('modules','name',array('id' => $row->module));
                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $row->id));
                $row->{$data->column} = $activityicon . html_writer::tag('a', $row->modulename, array('href' => $url));
            break;
            case 'moduletype':
                $activityicon1 = $OUTPUT->pix_icon('icon', ucfirst($row->moduletype), $row->moduletype, array('class' => 'icon'));
                $row->{$data->column} = $activityicon1 . ucfirst($row->moduletype);
            break;
            case 'completionstatus':
                switch($row->completionstatus) {
                    case 0 : $completiontype='n'; break;
                    case 1 : $completiontype='y'; break;
                    case 2 : $completiontype='pass'; break;
                    case 3 : $completiontype='fail'; break;
                }

                $row->completionstatus = $completiontype ? get_string('completion-' . $completiontype, 'completion') : 'N/A';
            break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : '--';
    }
}
