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

use block_learnerscript\local\ls;

require_once($CFG->libdir . '/formslib.php');

class parentcategory_form extends moodleform {

    function definition() {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        $mform = & $this->_form;

        $mform->addElement('header', 'crformheader', get_string('coursefield', 'block_learnerscript'), '');

        $options = array(get_string('top'));
        $parents = array();
        (new ls)->cr_make_categories_list($options, $parents);
        $mform->addElement('select', 'categoryid', get_string('category'), $options);

        $mform->addElement('checkbox', 'includesubcats', get_string('includesubcats', 'block_learnerscript'));

        // buttons
        $this->add_action_buttons(true, get_string('add'));
    }

}
