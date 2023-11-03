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

/** Learner Script
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use block_learnerscript\local\ls as ls;
class plotoption implements renderable, templatable {
    /** @var string $sometext Some text to show how to pass data to a template. */
    var $plots = array();
    var $reportid;
    var $calcbutton;
    var $active;
    var $reports;

    public function __construct($plots,$reportid,$calcbutton,$active) {
        global $DB;
        $this->plots = $plots;
        $this->reportid = $reportid;
        $this->calcbutton = $calcbutton;
        $this->active = $active;
        if(!empty($_SESSION['role']) && ($_SESSION['role'] != 'manager')){
            $reports = (new \block_learnerscript\local\ls)->listofreportsbyrole();
        } else {
            $reportlist = $DB->get_records_sql("SELECT * FROM {block_learnerscript}
                                                 WHERE global = 1 AND visible = 1 AND type != 'statistics'");
            $reports = array();
            foreach ($reportlist as $report) {
                $reports[] = ['id'=> $report->id, 'name' => $report->name];
            }
        }
        $this->reports = $reports;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;
        $data = new stdClass();
        $ls = new ls();
        if ($this->active == 'viewreport') {
            $data->searchenable = true;
        }
        $activetab = $this->active;
        $data->plots=$this->plots;
        $data->reportid = $this->reportid;
        $data->calcbutton = $this->calcbutton;
        $data->permissions = 'permissions';
        $data->editicon = 'edit_icon';
        $data->schreportform ='schreportform';
        $data->addgraphs = 'addgraphs';
        $data->design = 'design';
        $data->viewreport = 'viewreport';
        $data->searchreport = 'searchreport';
        $data->reports = $this->reports;
        $data->params = $_SERVER['QUERY_STRING'];
        unset($data->{$activetab});
        $data->{$activetab} = $activetab.'-active';
            $properties = new stdClass();
            $properties->courseid = SITEID;
            $properties->cmid = 0;
        $reportclass = $ls->create_reportclass($this->reportid, $properties);
        $data->permissionsavailable = false;
        if (in_array('permissions',$reportclass->components)) {
            $data->permissionsavailable = true;
        }
	if(isset($reportclass->componentdata['customsql']['config']->type) && (($reportclass->componentdata['customsql']['config']->type == 'sql') || ($reportclass->componentdata['customsql']['config']->type == 'statistics'))){
           $data->permissionsavailable = false;
        }
        $data->enableschedule = ($reportclass->parent === true && $reportclass->config->type != 'statistics')? true : false ;
        return $data;
    }
}
