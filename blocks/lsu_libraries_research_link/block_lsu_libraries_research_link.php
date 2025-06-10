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

defined('MOODLE_INTERNAL') || die();

class block_lsu_libraries_research_link extends block_base {

    public function init() {
        $this->title = get_string('lsu_libraries_research_link', 'block_lsu_libraries_research_link');
    }

    public function applicable_formats() {
        return array('site' => false, 'my' => false, 'course-view' => true);
    }

    public function get_content() {
        global $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '<a href="http://www.lib.lsu.edu" target="_new">'
                        . '<img src="'. $CFG->wwwroot. '/blocks/lsu_libraries_research_link/pix/icon1.svg" width="76" '
                        . 'height="76" alt="' . get_string('icon_alt', 'block_lsu_libraries_research_link') . '">'
                        . '</a><br><br><a href="http://www.lib.lsu.edu" target="_new">LSU Libraries Homepage</a><br>'
                        . '<a href="http://search.ebscohost.com/login.aspx?authtype=ip,guest&custid=s8491974&groupid=main&profid=eds-main" '
                        . 'target="_new">Discovery Search</a><br><a href="http://askus.lib.lsu.edu" '
                        . 'target="_new">Research Support</a>';

        $this->content->footer = '';

        return $this->content;
    }
}
