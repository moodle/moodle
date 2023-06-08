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

require_once($CFG->libdir.'/formslib.php');

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

/**
 * Form for copying and pasting from a spreadsheet.
 *
 * @package   gradeimport_direct
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradeimport_direct_import_form extends moodleform {

    /**
     * Definition method.
     */
    public function definition() {
        global $COURSE;

        $mform = $this->_form;

        if (isset($this->_customdata)) {  // Hardcoding plugin names here is hacky.
            $features = $this->_customdata;
        } else {
            $features = array();
        }

        // Course id needs to be passed for auth purposes.
        $mform->addElement('hidden', 'id', optional_param('id', 0, PARAM_INT));
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'general', get_string('pluginname', 'gradeimport_direct'));
        // Data upload from copy/paste.
        $mform->addElement('textarea', 'userdata', get_string('importdata', 'core_grades'),
            array('rows' => 10, 'class' => 'gradeimport_data_area'));
        $mform->addHelpButton('userdata', 'importdata', 'core_grades');
        $mform->addRule('userdata', null, 'required');
        $mform->setType('userdata', PARAM_RAW);

        $encodings = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'grades'), $encodings);
        $mform->addHelpButton('encoding', 'encoding', 'grades');

        if (!empty($features['verbosescales'])) {
            $options = array(1 => get_string('yes'), 0 => get_string('no'));
            $mform->addElement('select', 'verbosescales', get_string('verbosescales', 'grades'), $options);
            $mform->addHelpButton('verbosescales', 'verbosescales', 'grades');
        }

        $options = array('10' => 10, '20' => 20, '100' => 100, '1000' => 1000, '100000' => 100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'grades'), $options);
        $mform->addHelpButton('previewrows', 'rowpreviewnum', 'grades');
        $mform->setType('previewrows', PARAM_INT);
        $mform->addElement('hidden', 'groupid', groups_get_course_group($COURSE));
        $mform->setType('groupid', PARAM_INT);
        $mform->addElement('advcheckbox', 'forceimport', get_string('forceimport', 'grades'));
        $mform->addHelpButton('forceimport', 'forceimport', 'grades');
        $mform->setDefault('forceimport', false);
        $this->add_sticky_action_buttons(false, get_string('uploadgrades', 'grades'));
    }
}
