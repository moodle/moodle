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
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


/** Include formslib.php */
require_once ($CFG->libdir.'/formslib.php');
/** Include locallib.php */
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Assignment grading actions form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_grading_actions_form extends moodleform {
    /**
     * The definition for this form (called by the parent constructor)
     */
    function definition() {
        $mform = $this->_form;
        $data = $this->_customdata;
        $links = $data['links'];
        $cm = $data['cm'];
        $mform->addElement('header', 'general', get_string('gradingactions', 'assign'));
        // visible elements
        $autosubmit = array('onchange'=>'form.submit();');
        $mform->addElement('select', 'url', '', $links, $autosubmit);

        // hidden params
        $mform->addElement('hidden', 'id', $cm);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'action', 'redirect');
        $mform->setType('action', PARAM_ALPHA);

        // buttons
        $this->add_action_buttons(false, get_string('submitaction', 'assign'));
    }
}
