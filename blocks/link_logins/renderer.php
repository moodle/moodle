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
 * @package    block_link_logins
 * @copyright  2023 onwards Louisiana State University
 * @copyright  2023 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_link_logins_renderer extends plugin_renderer_base {
    public function users_form(moodle_url $formtarget, $existingusername, $prospectiveemail) {
        $content = html_writer::start_tag('form', array('class'=>'linkusers', 'method'=>'get', 'action'=>$formtarget));
        $content .= html_writer::start_tag('div');
        $content .= html_writer::tag('label', get_string('existingusername', 'block_link_logins'));
        $content .= html_writer::empty_tag('input', array('id'=>'existingusername', 'type'=>'text', 'name'=>'existingusername', 'value'=>s($existingusername)));
        $content .= html_writer::empty_tag('br', null);
        $content .= html_writer::tag('label', get_string('prospectiveemail', 'block_link_logins'));
        $content .= html_writer::empty_tag('input', array('id'=>'prospectiveemail', 'type'=>'text', 'name'=>'prospectiveemail', 'value'=>s($prospectiveemail)));
        $content .= html_writer::empty_tag('br', null);
        $content .= html_writer::empty_tag('input', array('class'=> 'btn btn-primary', 'type'=>'submit', 'value'=>s(get_string('link', 'block_link_logins'))));
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('form');
        return $content;
    }
}
