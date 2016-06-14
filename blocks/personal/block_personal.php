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
 * Personal block
 *
 * @package    block_personal
 * @copyright  2016 HsuanTang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_personal extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_personal');
    }

//     function specialization() {
//     }

//     function applicable_formats() {
//         return array('all' => true);
//     }

//     function instance_allow_multiple() {
//         return false;
//     }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $QQ = get_string('assignlist', 'block_personal');
        $this->content = new stdClass();
        $this->content->text = html_writer::link(
        	new moodle_url("$CFG->wwwroot/blocks/personal/assignlist.php"),
        	get_string('assignlist', 'block_personal')
        );
        $this->content->footer = '';
        
        return $this->content;
    }
}
