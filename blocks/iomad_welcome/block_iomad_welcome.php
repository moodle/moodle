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

class block_iomad_welcome extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_iomad_welcome');
    }

    public function hide_header() {
        return false;
    }

    public function applicable_formats() {
        return array('my' => true);
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        // Empty by default;
        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text = '';

        // Only display if you have the correct capability.
        if (!iomad::has_capability('block/iomad_welcome:view', context_system::instance())) {
            return;
        }

        // Only display until companies have been created
        if ($DB->record_exists('company', array())) {
            return;
        }

        $message = get_string('message', 'block_iomad_welcome');
        $dashboardlink = new moodle_url('/my');
        $dashboardtext = get_string('dashboardtext', 'block_iomad_welcome');
        $this->content->text = '<p><center>' . $message . '</center></p>';
        $this->content->text .= '<p><center><a href="' . $dashboardlink . '">' . $dashboardtext . '</a></center></p>';
        $this->content->footer = '';

        return $this->content;
    }
}
