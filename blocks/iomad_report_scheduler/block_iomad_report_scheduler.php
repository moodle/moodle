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

class block_iomad_report_scheduler extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_iomad_report_scheduler');
    }

    public function hide_header() {
        return true;
    }

    public function applicable_formats() {
        return array('site' => true);
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        // Only display if you have the correct capability.
        if (!has_capability('block/iomad_report_scheduler:view', context_system::instance())) {
            return;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '<div id="iomad_report_scheduler" style="width: 100%; height: 200px; position:relative;
                                margin: 0 auto; overflow: hidden">';
        $this->content->text .= '<div id="iomad_report_scheduler_main1" style="width: 100%; height: 200px; position:relative;
                                margin: 0 auto; ">
                                <h3>Iomad Report Scheduler</h3></br>';
        $this->content->text .= '<a id="ELDMSRS" href="'.
                                    new moodle_url('/blocks/iomad_report_scheduler/reports_view.php'). '"><img src="'.
                                    new moodle_url('/blocks/iomad_report_scheduler/images/report.png') .
                                    '"></br>View Reports</a>';
        return $this->content;
    }
}


