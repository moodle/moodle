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

require_once(dirname(__FILE__) . '/../../config.php'); // Creates $PAGE.
require_once($CFG->dirroot.'/blocks/iomad_microlearning/lib.php');

/**
 *
 */

class block_iomad_microlearning extends block_base {
    public function init() {
        $this->title = get_string('blocktitle', 'block_iomad_microlearning');
    }

    public function hide_header() {
        return false;
    }

    public function get_content() {
        global $CFG, $USER, $DB;

        $this->content = new stdClass;
        $this->content->footer = '';

        $this->content->text = "";

        return $this->content;
    }

    function has_config() {
        return true;
    }
}
