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
 * @package    workshopallocation
 * @subpackage random
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Allocator settings form
 *
 * This is used by {@see workshop_random_allocator::ui()} to set up allocation parameters.
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class workshop_random_allocator_form extends moodleform {

    /**
     * Definition of the setting form elements
     */
    public function definition() {
        $mform          = $this->_form;
        $workshop       = $this->_customdata['workshop'];
        $plugindefaults = get_config('workshopallocation_random');

        $mform->addElement('header', 'randomallocationsettings', get_string('allocationsettings', 'workshopallocation_random'));

        $gmode = groups_get_activity_groupmode($workshop->cm, $workshop->course);
        switch ($gmode) {
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

        $options_numper = array(
            workshop_random_allocator_setting::NUMPER_SUBMISSION => get_string('numperauthor', 'workshopallocation_random'),
            workshop_random_allocator_setting::NUMPER_REVIEWER   => get_string('numperreviewer', 'workshopallocation_random')
        );
        $grpnumofreviews = array();
        $grpnumofreviews[] = $mform->createElement('text', 'numofreviews', '', array('size' => 5, 'maxlength' => 20));
        $mform->setType('numofreviews', PARAM_INT);
        $mform->setDefault('numofreviews', $plugindefaults->numofreviews);
        $grpnumofreviews[] = $mform->createElement('select', 'numper', '', $options_numper);
        $mform->setDefault('numper', workshop_random_allocator_setting::NUMPER_SUBMISSION);
        $mform->addGroup($grpnumofreviews, 'grpnumofreviews', get_string('numofreviews', 'workshopallocation_random'),
                array(' '), false);

        if (VISIBLEGROUPS == $gmode) {
            $mform->addElement('checkbox', 'excludesamegroup', get_string('excludesamegroup', 'workshopallocation_random'));
            $mform->setDefault('excludesamegroup', 0);
        } else {
            $mform->addElement('hidden', 'excludesamegroup', 0);
            $mform->setType('excludesamegroup', PARAM_BOOL);
        }

        $mform->addElement('checkbox', 'removecurrent', get_string('removecurrentallocations', 'workshopallocation_random'));
        $mform->setDefault('removecurrent', 0);

        $mform->addElement('checkbox', 'assesswosubmission', get_string('assesswosubmission', 'workshopallocation_random'));
        $mform->setDefault('assesswosubmission', 0);

        if (empty($workshop->useselfassessment)) {
            $mform->addElement('static', 'addselfassessment', get_string('addselfassessment', 'workshopallocation_random'),
                                                                 get_string('selfassessmentdisabled', 'workshop'));
        } else {
            $mform->addElement('checkbox', 'addselfassessment', get_string('addselfassessment', 'workshopallocation_random'));
        }

        $this->add_action_buttons();
    }

    /**
     * Validate the allocation settings.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {

        $errors = parent::validation($data, $files);

        if ($data['numofreviews'] < 0) {
            $errors['grpnumofreviews'] = get_string('invalidnum', 'core_error');
        }

        return $errors;
    }
}
