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
 * Form for editing LearnerScript report dashboard block instances.
 * @package  block_reportdashboard
 * @author Naveen kumar <naveen@eabyas.in>
 */
namespace block_reportdashboard\output;
defined('MOODLE_INTERNAL') || die();
use renderable;
use renderer_base;
use templatable;
use stdClass;
use block_learnerscript\local\ls as ls;

class widgetheader implements renderable, templatable {
    /** @var string $sometext Some text to show how to pass data to a template. */
    public $methodname = null;
    public $reportid;
    public $instanceid;
    public $reportvisible;
    public $reportcontenttype;
    public $reportnameheading;
    public $reporttitle;
    public $reportcontenttypes;
    public $editactions;
    public $durations;
    public $exportparams;
    public $startduration;
    public $endduration;
    public $currenttime;
    public function __construct($data) {
        $this->methodname = $data->methodname;
        $this->reportid = $data->reportid;
        $this->instanceid = $data->instanceid;
        $this->exports = $data->exports;
        $this->reportvisible = $data->reportvisible;
        $this->reportcontenttype = $data->reportcontenttype;
        $this->reportnameheading = substr($data->reportname, 0, 35);
        $this->reporttitle = $data->reportname;
        $this->reportcontenttypes = $data->reportcontenttypes;
        $this->editactions = $data->editactions;
        $this->disableheader = $data->disableheader;
        $this->exportparams = $data->exportparams;
        $this->durations = $data->durations;
        $this->startduration = $data->startduration;
        $this->endduration = $data->endduration;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT, $USER, $PAGE, $CFG, $DB;
        $data = array();
        $helpimg = $CFG->wwwroot."/pix/help.png";
        if (!empty($this->methodname)) {
            foreach ($this->methodname as $method) {
                $data['action'][] = ["method" => $method,
                                   "icon" => $OUTPUT->image_url($method, "block_reportdashboard"),
                                   "title" => get_string($method, 'block_reportdashboard')
                                  ];
            }
        }      
        $report = $DB->get_record('block_learnerscript', array('id' => $this->reportid));
        $data['editicon'] = $OUTPUT->image_url('edit_icon', 'block_reportdashboard');
        $data['edittitle'] = get_string('edit');
        $data['reportid'] = $this->reportid;
        $data['instanceid'] = $this->instanceid;
        $data['showhide'] = (!empty($this->reportvisible)) ? get_string('hide') : get_string('show');
        $data['sesskey'] = $USER->sesskey;
        $data['exports'] = $this->exports;
        $data['download_icon'] = $OUTPUT->image_url('download_icon', 'block_reportdashboard');
        $data['designreportcap'] = (has_capability('block/learnerscript:designreport', \context_system::instance())
                                    && !$this->disableheader) ? true : false;
        $data['is_siteadmin'] = is_siteadmin();
        $data['widgetheader'] = (!empty($this->methodname) || !empty($this->exports) || $data['designreportcap']) ? true : false;
        $data['reportcontenttype'] = $this->reportcontenttype;
        $data['reportinstance'] = $this->instanceid ? $this->instanceid : $this->reportid;
        $data['report_option_innerstatus'] = $data['designreportcap'] || !empty($data['action']) ? true : false;
        $hideurl = new \moodle_url($PAGE->url, array('sesskey' => sesskey(), 'bui_hideid' => $this->instanceid));
        $data['hideactionurl'] = $hideurl;
        $data['reportnameheading'] = $this->reportnameheading;
        if (strlen($this->reportnameheading) != strlen($this->reporttitle)) {
            $data['reportnameheading'] .= "...";
        }
        $data['reporttitle'] = $this->reporttitle;
        $data['reportcontenttypes'] = $this->reportcontenttypes;
        $data['reportcontenttypeslist'] = count($this->reportcontenttypes) > 1 ? true : false;
        $data['editactions'] = $this->editactions;
        $data['disableheader'] = $this->disableheader;
        $data['exportparams'] = $this->exportparams;
        $data['dashboardrole'] = isset($_SESSION['role']) ? $_SESSION['role'] : '';
        $data['dashboardcontextlevel'] = isset($_SESSION['ls_contextlevel']) ? $_SESSION['ls_contextlevel'] : 10;
        $data['dashboardurl'] = $PAGE->subpage;
        $data['durations'] = $this->durations;
        $data['endduration'] = $this->endduration;
        $data['startduration'] = $this->startduration;
        if($report->type == 'sql'){
            $data['helpimgsql'] = "1";
            $data['helpimg'] = $helpimg; 
        }
        return $data;
    }
}
