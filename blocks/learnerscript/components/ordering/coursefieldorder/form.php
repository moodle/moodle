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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
if (!defined('MOODLE_INTERNAL')) {
    die(get_string('nodirectaccess','block_learnerscript'));    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class coursefieldorder_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG;

        $mform = & $this->_form;

        $columns = $DB->get_columns('course');

        $coursecolumns = array();
        foreach ($columns as $c)
            $coursecolumns[$c->name] = $c->name;

        $mform->addElement('select', 'column', get_string('column', 'block_learnerscript'), $coursecolumns);

        $directions = array('asc' => 'ASC', 'desc' => 'DESC');
        $mform->addElement('select', 'direction', get_string('direction', 'block_learnerscript'), $directions);

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }

}
