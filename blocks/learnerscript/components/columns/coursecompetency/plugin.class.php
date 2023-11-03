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

/**
 * LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2020
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use html_writer;
use moodle_url;

class plugin_coursecompetency extends pluginbase {

    public function init() {
        $this->fullname = get_string('coursecompetency', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = array('coursecompetency');
    }

    public function summary($data) {
        return format_string($data->columname);
    }

    public function colformat($data) {
        $align = (isset($data->align)) ? $data->align : '';
        $size = (isset($data->size)) ? $data->size : '';
        $wrap = (isset($data->wrap)) ? $data->wrap : '';
        return array($align, $size, $wrap);
    }

    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $OUTPUT, $CFG;
        switch($data->column) { 
            case 'competency':
                $compurl = $CFG->wwwroot . '/admin/tool/lp/user_competency_in_course.php?courseid='.$row->courseid.'&competencyid='.$row->id; 
                $competency = html_writer::tag('a', $row->competency, array('href' => $compurl)); 
                $row->{$data->column} = !empty($competency) ? $competency : '--';
            break;
            case 'activity': 
                $modules = $DB->get_fieldset_select('modules', 'name', '', array('visible' => 1));
                foreach ($modules as $modulename) {
                    $aliases[] = $modulename;
                    $activities[] = "'$modulename'";
                    $fields1[] = "COALESCE($modulename.name,'')";
                } 
                $activitynames = implode(',', $fields1);
                $sql = " SELECT cm.id, CONCAT($activitynames) AS activityname, m.id AS moduleid 
                            FROM {course_modules} cm 
                            JOIN {modules} m ON m.id = cm.module 
                            LEFT JOIN {competency_modulecomp} mcom ON mcom.cmid = cm.id ";
                foreach ($aliases as $alias) {
                    $sql .= " LEFT JOIN {".$alias."} AS $alias ON $alias.id = cm.instance AND m.name = '$alias' ";
                } 
                $sql .= " WHERE m.visible = :visible AND mcom.competencyid = :rowid"; 
                $activitieslist = $DB->get_records_sql($sql, ['visible' => 1, 'rowid' => $row->id]);
                foreach ($activitieslist as $activity) { 
                    $module = $DB->get_field('modules','name',array('id'=>$activity->moduleid));
                    $activityicon = $OUTPUT->pix_icon('icon', ucfirst($module), $module, array('class' => 'icon'));
                    $url = new moodle_url('/mod/'.$module.'/view.php',
                             array('id' => $activity->id));
                    $activityname = $activityicon . html_writer::tag('a', $activity->activityname, array('href' => $url));
                    $data1[] = $activityname;
                }
                $activitiesd = implode(', ', $data1);
                $row->{$data->column} = !empty($activitiesd) ? $activitiesd : '--';
            break;
        }
        return (isset($row->{$data->column}))? $row->{$data->column} : ' -- ';
    }
}
