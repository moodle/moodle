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

class reportarea implements renderable, templatable {
    public $reportid;
    public $instanceid;
    public $reportcontenttype;
    public $disableheader;
    public $stylecolorpicker;
    public $reportduration;
    public function __construct($data) {
        $this->reportid = $data->reportid;
        $this->instanceid = $data->instanceid;
        $this->reportcontenttype = $data->reportcontenttype;
        $this->disableheader = $data->disableheader;
        $this->stylecolorpicker = $data->stylecolorpicker;
        $this->reportduration = $data->reportduration;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;
        $data = array();
        $data['reportid'] = $this->reportid;
        $data['instanceid'] = $this->instanceid;
        $data['reportinstance'] = $this->instanceid ? $this->instanceid : $this->reportid;
        $data['reportcontenttype'] = $this->reportcontenttype;
        $data['loading'] = $OUTPUT->image_url('loading', 'block_learnerscript');
        $data['reportduration'] = $this->reportduration;
        $data['disableheader'] = $this->disableheader;
        $data['stylecolorpicker'] = $this->stylecolorpicker;
        return $data;
    }
}