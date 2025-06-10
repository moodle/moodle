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
 * @package    block_migrate_users
 * @copyright  2019 onwards Louisiana State University
 * @copyright  2019 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_migrate_users_renderer extends plugin_renderer_base {
    public function users_form(moodle_url $formtarget, $userfrom, $userto, $courseid) {
        $content = html_writer::start_tag('form', array('class'=>'migrateusers', 'method'=>'get', 'action'=>$formtarget));
        $content .= html_writer::start_tag('div');
        $content .= html_writer::tag('label', get_string('userfrom', 'block_migrate_users'));
        $content .= html_writer::empty_tag('input', array('id'=>'userfrom', 'type'=>'text', 'name'=>'userfrom', 'value'=>s($userfrom)));
        $content .= html_writer::empty_tag('br', null);
        $content .= html_writer::tag('label', get_string('userto', 'block_migrate_users'));
        $content .= html_writer::empty_tag('input', array('id'=>'userto', 'type'=>'text', 'name'=>'userto', 'value'=>s($userto)));
        $content .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'courseid', 'value'=>s($courseid)));
        $content .= html_writer::empty_tag('br', null);
        $content .= html_writer::empty_tag('input', array('class'=> 'btn btn-primary', 'type'=>'submit', 'value'=>s(get_string('migrate', 'block_migrate_users'))));
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('form');
        return $content;
    }
}
