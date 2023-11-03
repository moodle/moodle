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
  * @author: arun<arun@eabyas.in>
  * @date: 2016
  */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls;
use context_system;
use moodle_url;
use html_writer;
use completion_info;
use stdClass;
class plugin_myscormcolumns extends pluginbase{
	public function init(){
		$this->fullname = get_string('myscormcolumns','block_learnerscript');
		$this->type = 'undefined';
		$this->form = true;
		$this->reporttypes = array('myscorm');
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
	public function execute($data,$row,$user,$courseid,$starttime=0,$endtime=0, $reporttype){
		global $DB, $CFG, $OUTPUT, $USER;
    $context = context_system::instance();
    require_once($CFG->libdir . '/completionlib.php');
        switch ($data->column) {
            case 'course':
                $reportid = $DB->get_field('block_learnerscript', 'id', array('type'=> 'courseprofile'), IGNORE_MULTIPLE);
                $checkpermissions = empty($reportid) ? false : (new reportbase($reportid))->check_permissions($user, $context);
                if(empty($reportid) || empty($checkpermissions)){
                    $row->{$data->column} = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$row->courseid.'" />'.$row->{$data->column}.'</a>';
                } else{
                    $row->{$data->column} = '<a href="'.$CFG->wwwroot.'/blocks/learnerscript/viewreport.php?id='.$reportid.'&filter_courses='.$row->courseid.'" />'.$row->{$data->column}.'</a>';
                }
            break;
            case 'scormname':
                $module = $DB->get_field('modules','name',array('id'=>$row->moduleid));

                $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                if(is_siteadmin()){
                  $url = new moodle_url('/mod/scorm/report.php',
                             array('id' => $row->cmid));
                }else {
                  $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $row->cmid));
                }
                $row->{$data->column} = $activityicon . html_writer::tag('a', $row->scormname, array('href' => $url));
            break;
            case 'lastaccess':
                if (!empty($row->attempt)) {
                    $value = $DB->get_field_sql("SELECT timemodified FROM {scorm_scoes_track} WHERE attempt = :attempt 
                              AND element = :element AND scormid = :scormid AND userid = :userid", ['attempt' => $row->attempt, 'element' => 'cmi.core.total_time', 'scormid' => $row->scormid, 'userid' => $row->userid]);
                    if (empty($value)) {
                        $lastaccess = $DB->get_field_sql("SELECT timemodified FROM {scorm_scoes_track} WHERE attempt = $row->attempt 
                                                AND element = :element AND scormid = :scormid AND userid = :userid", ['element' => 'x.start.time', 'scormid' => $row->scormid, 'userid' => $row->userid]);
                        $row->{$data->column} = $lastaccess ? userdate($lastaccess) : '--';
                    } else {
                        $row->{$data->column} = $value ? userdate($value) : '--';
                    }
                }
            break;
            case 'activitystate':
                $courserecord = $DB->get_record('course',array('id'=>$row->courseid));
                $completion_info = new completion_info($courserecord);
                if ($CFG->dbtype == 'sqlsrv') {
                    $scormattemptstatus = $DB->get_field_sql("SELECT TOP 1 value FROM {scorm_scoes_track}
                                                            WHERE scormid = :id AND userid = :userid
                                                            ORDER BY id DESC ", ['id' => $row->id, 'userid' => $row->userid]);
                    $scormcomppletion = $DB->get_field_sql("SELECT TOP 1 id FROM {course_modules_completion}
                                                            WHERE coursemoduleid = :cmid AND userid = :userid
                                                            AND completionstate <> :completionstate ORDER BY id DESC ", ['cmid' => $row->cmid, 'userid' => $row->userid, 'completionstate' => 0]);
                } else {
                    $scormattemptstatus = $DB->get_field_sql("SELECT value FROM {scorm_scoes_track}
                                                            WHERE scormid = :id AND userid = :userid
                                                            ORDER BY id DESC LIMIT 1 ", ['id' => $row->id, 'userid' => $row->userid]);
                    $scormcomppletion = $DB->get_field_sql("SELECT id FROM {course_modules_completion}
                                                            WHERE coursemoduleid = :cmid AND userid = :userid
                                                            AND completionstate <> :completionstate ORDER BY id DESC LIMIT 1 ", ['cmid' => $row->cmid, 'userid' => $row->userid, 'completionstate' => 0]);
                }

                if(empty($scormattemptstatus) && empty($scormcomppletion)) {
                    $completionstatus = '<span class="notyetstart">Not Yet Started</span>';
                } else if(empty($scormattemptstatus) && !empty($scormcomppletion)) {
                  $completionstatus = '<span class="finished">Completed</span>';
                } else if(!empty($scormattemptstatus) && empty($scormcomppletion)) {
                    $completionstatus = '<span class="finished">In-Progress</span>';
                } else if (!empty($scormcomppletion)){
                    $cm = new stdClass();
                    $cm->id = $row->cmid;
                    $completion = $completion_info->get_data($cm, false, $row->userid);
                    switch($completion->completionstate) {
                        case COMPLETION_INCOMPLETE :
                            $completionstatus = 'In-Progress';
                        break;
                        case COMPLETION_COMPLETE :
                            $completionstatus = 'Completed';
                        break;
                        case COMPLETION_COMPLETE_PASS :
                            $completionstatus = 'Completed (achieved pass grade)';
                        break;
                        case COMPLETION_COMPLETE_FAIL :
                            $completionstatus = 'Fail';
                        break;
                    }
                }
                $row->{$data->column} =  !empty($completionstatus) ? $completionstatus : '--';
            break;
            case 'attempt':
                if(!isset($row->attempt) && isset($data->subquery)){
                    $attempt =  $DB->get_field_sql($data->subquery);
                }else{
                    $attempt = $row->{$data->column};
                }
                $row->{$data->column} = !empty($attempt) ? $attempt : 0;
                break;
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
            case 'firstaccess':
                if(!isset($row->firstaccess) && isset($data->subquery)){
                    $firstaccess =  $DB->get_field_sql($data->subquery);
                }else{
                    $firstaccess = $row->{$data->column};
                }
                $row->{$data->column} = !empty($firstaccess) ? userdate($firstaccess) : '--';
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
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : '--';
	}
}
