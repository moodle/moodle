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
 * Random allocator settings form
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Allocator settings form
 *
 * This is used by {@see workshop_random_allocator::ui()} to set up allocation paramters.
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workshop_random_allocator_form extends moodleform {

    /**
     * Definition of the setting form elements
     */
    public function definition() {
        $mform      = $this->_form;
        $workshop   = $this->_customdata['workshop'];

        $mform->addElement('header', 'settings', get_string('allocationsettings', 'workshop'));

        switch ($workshop->cm->groupmode) {
        case NOGROUPS:
            $grouplabel = get_string('groupsnone', 'group');
            break;
        case VISIBLEGROUPS:
            $grouplabel = get_string('groupsvisible', 'group');
            break;
        case SEPARATEGROUPS:
            $grouplabel = get_string('groupsseparate', 'group');
            break;
        }
        $mform->addElement('static', 'groupmode', get_string('groupmode', 'group'), $grouplabel);

        $options_numofreviewes = array(0=>0,1=>1, 2=>2, 3=>3, 4=>4);
        $options_numper = array(WORKSHOP_USERTYPE_AUTHOR    => get_string('numperauthor', 'workshop'),
                                WORKSHOP_USERTYPE_REVIEWER  => get_string('numperreviewer', 'workshop'));
        $grpnumofreviews = array();
        $grpnumofreviews[] =& $mform->createElement('select', 'numofreviews', '', $options_numofreviewes);
        $mform->setDefault('numofreviews', 4);
        $grpnumofreviews[] =& $mform->createElement('select', 'numper', '', $options_numper);
        $mform->setDefault('numper', WORKSHOP_USERTYPE_AUTHOR);
        $mform->addGroup($grpnumofreviews, 'grpnumofreviews', get_string('numofreviews', 'workshop'), array(' '), false);

        $mform->addElement('advcheckbox', 'removecurrent', get_string('removecurrentallocations', 'workshop'));
        $mform->setDefault('removecurrent', 0);

        $mform->addElement('advcheckbox', 'assesswosubmission', get_string('assesswosubmission', 'workshop'));
        $mform->setDefault('assesswosubmission', 0);

        $mform->addElement('advcheckbox', 'addselfassessment', get_string('addselfassessment', 'workshop'));
        $mform->setDefault('addselfassessment', 0);

        $this->add_action_buttons();
    }
}
