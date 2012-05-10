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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   tool_assignmentupgrade
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


/** Include formslib.php */
require_once ($CFG->libdir.'/formslib.php');

/**
 * Assignment upgrade table display options
 *
 * @package   tool_assignmentupgrade
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_assignmentupgrade_pagination_form extends moodleform {
    /**
     * Define this form - called from the parent constructor
     */
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->addElement('header', 'general', get_string('assignmentsperpage', 'tool_assignmentupgrade'));
        // visible elements
        $options = array(10=>'10', 20=>'20', 50=>'50', 100=>'100');
        $mform->addElement('select', 'perpage', get_string('assignmentsperpage', 'assign'), $options);

        // hidden params
        $mform->addElement('hidden', 'action', 'saveoptions');
        $mform->setType('action', PARAM_ALPHA);

        // buttons
        $this->add_action_buttons(false, get_string('updatetable', 'tool_assignmentupgrade'));
    }
}

