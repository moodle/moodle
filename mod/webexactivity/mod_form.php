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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Class the creates the mod_form.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_webexactivity_mod_form extends \moodleform_mod {
    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Types cannot be changed. Check if it's already set.
        if (!empty($this->current->id)) {
            $typename = \mod_webexactivity\meeting::get_meeting_type_name($this->current->type);
            $mform->addElement('static', 'typestatic', get_string('meetingtype', 'webexactivity'), $typename);
        } else {
            $meetingtypes = \mod_webexactivity\meeting::get_available_types($this->context);
            if (count($meetingtypes) == 0) {
                throw new \coding_exception('There are no valid meeting types for this user. Admin must fix.');
            } else if (count($meetingtypes) == 1) {
                $keys = array_keys($meetingtypes);
                $type = array_pop($keys);
                $mform->addElement('static', 'typestatic', get_string('meetingtype', 'webexactivity'), $meetingtypes[$type]);
                $mform->addElement('hidden', 'type', $type);
            } else {
                $mform->addElement('select', 'type', get_string('meetingtype', 'webexactivity'), $meetingtypes);
                $mform->setDefault('type', get_config('webexactivity', 'defaultmeetingtype'));
            }
            $mform->setType('type', PARAM_INT);
        }

        $mform->addElement('text', 'name', get_string('webexactivityname', 'webexactivity'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'webexactivity'));
        $mform->addRule('starttime', null, 'required', null, 'client');

        $duration = array();
        $duration[] =& $mform->createElement('text', 'duration', '', array('size' => '4'));
        $duration[] =& $mform->createElement('static', 'durationname', '', '('.get_string('minutes').')');
        $mform->addGroup($duration, 'durationgroup', get_string('duration', 'webexactivity'), array(' '), false);
        $mform->setType('duration', PARAM_INT);
        $mform->addRule('durationgroup', null, 'required', null, 'client');
        $mform->setDefault('duration', 20);
        $mform->addHelpButton('durationgroup', 'duration', 'webexactivity');

        $mform->addElement('passwordunmask', 'password', get_string('meetingpassword', 'webexactivity'));
        $mform->setType('password', PARAM_TEXT);
        $mform->addRule('password', null, 'maxlength', 16, 'client');
        $req = get_config('webexactivity', 'requiremeetingpassword');
        if ($req) {
            $mform->addRule('password', null, 'required', null, 'client');
        }

        $mform->addElement('header', 'additionalsettings', get_string('additionalsettings', 'webexactivity'));

        $mform->addElement('checkbox', 'studentdownload', get_string('studentdownload', 'webexactivity'));
        $mform->setDefault('studentdownload', 1);
        $mform->addHelpButton('studentdownload', 'studentdownload', 'webexactivity');

        $mform->addElement('checkbox', 'calpublish', get_string('calpublish', 'webexactivity'));
        $mform->setDefault('calpublish', 1);
        $mform->addHelpButton('calpublish', 'calpublish', 'webexactivity');
        $mform->disabledIf('calpublish', 'longavailability', 'checked');

        $mform->addElement('checkbox', 'longavailability', get_string('longavailability', 'webexactivity'));
        $mform->setDefault('longavailability', 0);
        $mform->addHelpButton('longavailability', 'longavailability', 'webexactivity');

        $mform->addElement('date_time_selector', 'endtime', get_string('availabilityendtime', 'webexactivity'));
        $mform->setDefault('endtime', (time() + (3600 * 24 * 14)));
        $mform->addRule('starttime', null, 'required', null, 'client');
        $mform->disabledIf('endtime', 'longavailability');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    /**
     * Any data processing needed before the form is displayed.
     *
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$data) {
        if (isset($data['endtime'])) {
            $data['longavailability'] = 1;
        } else {
            $data['longavailability'] = 0;
            $data['endtime'] = time() + (3600 * 24 * 14);
        }
    }

    /**
     * Perform minimal validation on the settings form.
     *
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['instance'] == 0) {
            // Check that the passed type is valid.
            if (!isset($data['type']) || (!\mod_webexactivity\meeting::is_valid_type($data['type'], $this->context))) {
                $errors['type'] = get_string('invalidtype', 'webexactivity');
            }
        }

        return $errors;
    }
}
