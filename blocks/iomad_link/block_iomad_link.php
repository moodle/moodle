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

class block_iomad_link extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_iomad_link');
    }

    public function hide_header() {
        return true;
    }

    public function applicable_formats() {
        return array('site' => true, 'my' => true);
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        // Only display if you have the correct capability.
        if (!iomad::has_capability('block/iomad_link:view', context_system::instance())) {
            return;
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $strlink = get_string('link', 'block_iomad_link');
        $this->content = new stdClass;
        $this->content->text = "<center><a href=\"{$CFG->wwwroot}/my/index.php\">
                                <img src='" . $OUTPUT->image_url('iomad_logo', 'block_iomad_link') ."' /></a></center>";
        $this->content->text .= "<center><b><a href=\"{$CFG->wwwroot}/my/index.php\">$strlink</a></b></center>";
        $this->content->footer = '';

        return $this->content;
    }
}
