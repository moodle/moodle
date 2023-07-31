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
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

/**
 * qbassignment grading options form
 *
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qbassign_grading_options_form extends moodleform {
    /**
     * Define this form - called from the parent constructor.
     */
    public function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        $mform->disable_form_change_checker();

        $mform->addElement('header', 'general', get_string('gradingoptions', 'qbassign'));
        // Visible elements.
        $options = array(10 => '10', 20 => '20', 50 => '50', 100 => '100', -1 => get_string('all'));
        $maxperpage = get_config('qbassign', 'maxperpage');
        if (isset($maxperpage) && $maxperpage != -1) {
            unset($options[-1]);
            foreach ($options as $val) {
                if ($val > $maxperpage) {
                    unset($options[$val]);
                }
            }
        }
        $mform->addElement('select', 'perpage', get_string('qbassignmentsperpage', 'qbassign'), $options);
        $options = array('' => get_string('filternone', 'qbassign'),
                         qbassign_FILTER_NOT_SUBMITTED => get_string('filternotsubmitted', 'qbassign'),
                         qbassign_FILTER_DRAFT => get_string('filterdraft', 'qbassign'),
                         qbassign_FILTER_SUBMITTED => get_string('filtersubmitted', 'qbassign'),
                         qbassign_FILTER_REQUIRE_GRADING => get_string('filterrequiregrading', 'qbassign'),
                         qbassign_FILTER_GRANTED_EXTENSION => get_string('filtergrantedextension', 'qbassign'));
        if ($instance['submissionsenabled']) {
            $mform->addElement('select', 'filter', get_string('filter', 'qbassign'), $options);
        }
        if (!empty($instance['markingallocationopt'])) {
            $markingfilter = get_string('markerfilter', 'qbassign');
            $mform->addElement('select', 'markerfilter', $markingfilter, $instance['markingallocationopt']);
        }
        if (!empty($instance['markingworkflowopt'])) {
            $workflowfilter = get_string('workflowfilter', 'qbassign');
            $mform->addElement('select', 'workflowfilter', $workflowfilter, $instance['markingworkflowopt']);
        }
        // Quickgrading.
        if ($instance['showquickgrading']) {
            $mform->addElement('checkbox', 'quickgrading', get_string('quickgrading', 'qbassign'));
            $mform->addHelpButton('quickgrading', 'quickgrading', 'qbassign');
            $mform->setDefault('quickgrading', $instance['quickgrading']);
        }

        // Show active/suspended user option.
        if ($instance['showonlyactiveenrolopt']) {
            $mform->addElement('checkbox', 'showonlyactiveenrol', get_string('showonlyactiveenrol', 'grades'));
            $mform->addHelpButton('showonlyactiveenrol', 'showonlyactiveenrol', 'grades');
            $mform->setDefault('showonlyactiveenrol', $instance['showonlyactiveenrol']);
        }

        // Place student downloads in seperate folders.
        if ($instance['submissionsenabled']) {
            $mform->addElement('checkbox', 'downloadasfolders', get_string('downloadasfolders', 'qbassign'));
            $mform->addHelpButton('downloadasfolders', 'downloadasfolders', 'qbassign');
            $mform->setDefault('downloadasfolders', $instance['downloadasfolders']);
        }

        // Hidden params.
        $mform->addElement('hidden', 'contextid', $instance['contextid']);
        $mform->setType('contextid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['cm']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'userid', $instance['userid']);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'saveoptions');
        $mform->setType('action', PARAM_ALPHA);

        // Buttons.
        $this->add_action_buttons(false, get_string('updatetable', 'qbassign'));
    }
}

