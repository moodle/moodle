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
 * Assignment grading options form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_grading_batch_operations_form extends moodleform {
    /**
     * Define this form - called by the parent constructor
     */
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;

        // visible elements
        $options = array();
        $options['lock'] = get_string('locksubmissions', 'assign');
        $options['unlock'] = get_string('unlocksubmissions', 'assign');
        if ($instance['submissiondrafts']) {
            $options['reverttodraft'] = get_string('reverttodraft', 'assign');
        }
        if ($instance['duedate'] && has_capability('mod/assign:grantextension', $instance['context'])) {
            $options['grantextension'] = get_string('grantextension', 'assign');
        }

        foreach ($instance['feedbackplugins'] as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                foreach ($plugin->get_grading_batch_operations() as $action => $description) {
                    $options['plugingradingbatchoperation_' . $plugin->get_type() . '_' . $action] = $description;
                }
            }
        }

        $mform->addElement('hidden', 'action', 'gradingbatchoperation');
        $mform->addElement('hidden', 'id', $instance['cm']);
        $mform->addElement('hidden', 'selectedusers', '', array('class'=>'selectedusers'));
        $mform->addElement('hidden', 'returnaction', 'grading');

        $objs = array();
        $objs[] =& $mform->createElement('select', 'operation', get_string('chooseoperation', 'assign'), $options);
        $objs[] =& $mform->createElement('submit', 'submit', get_string('go'));
        $mform->addElement('group', 'actionsgrp', get_string('batchoperationsdescription', 'assign'), $objs, ' ', false);

    }

}

