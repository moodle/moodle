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
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir.'/formslib.php');

/**
 * Upload modified grading worksheet
 *
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_offline_upload_grades_form extends moodleform {
    /**
     * Define this form - called by the parent constructor
     */
    public function definition() {
        global $COURSE, $USER;

        $mform = $this->_form;
        $params = $this->_customdata;

        $mform->addElement('header', '', get_string('uploadgrades', 'assignfeedback_offline'));

        $fileoptions = array('subdirs'=>0,
                                'maxbytes'=>$COURSE->maxbytes,
                                'accepted_types'=>'csv',
                                'maxfiles'=>1,
                                'return_types'=>FILE_INTERNAL);

        $mform->addElement('filepicker', 'gradesfile', get_string('uploadafile'), null, $fileoptions);
        $mform->addRule('gradesfile', get_string('uploadnofilefound'), 'required', null, 'client');
        $mform->addHelpButton('gradesfile', 'gradesfile', 'assignfeedback_offline');

        $mform->addElement('checkbox', 'ignoremodified', '', get_string('ignoremodified', 'assignfeedback_offline'));
        $mform->addHelpButton('ignoremodified', 'ignoremodified', 'assignfeedback_offline');

        $mform->addElement('hidden', 'id', $params['cm']);
        $mform->addElement('hidden', 'action', 'viewpluginpage');
        $mform->addElement('hidden', 'pluginaction', 'uploadgrades');
        $mform->addElement('hidden', 'plugin', 'offline');
        $mform->addElement('hidden', 'pluginsubtype', 'assignfeedback');
        $this->add_action_buttons(true, get_string('uploadgrades', 'assignfeedback_offline'));

    }

}

