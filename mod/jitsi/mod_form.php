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
 * The main jitsi configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_jitsi
 * @copyright  2019 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Jitsi settings form.
 *
 * @package   mod_jitsi
 * @copyright  2019 Sergio Comerón Sánchez-Paniagua <sergiocomeron@icloud.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_jitsi_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('jitsiname', 'jitsi'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'modulename', 'jitsi');

        $this->standard_intro_elements();

        $mform->addElement('header', 'availability', get_string('availability', 'assign'));

        $name = get_string('allow', 'jitsi');
        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'timeopen', $name, $options);

        $name = get_string('close', 'jitsi');
        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'timeclose', $name, $options);

        $choicesminspre = array(
            5 => 5,
            10 => 10,
            15 => 15,
            20 => 20,
            30 => 30
        );
        $mform->addElement('select', 'minpretime', get_string('minpretime', 'jitsi'), $choicesminspre);
        $mform->disabledIf('minpretime', 'timeopen[enabled]');
        $mform->addHelpButton('minpretime', 'minpretime', 'jitsi');

        if ($CFG->jitsi_invitebuttons == 1) {
            $optionsinvitation = array('defaulttime' => time() + 86400, 'optional' => true);
            $mform->addElement('header', 'invitations', get_string('invitations', 'jitsi'));
            $options = array('optional' => true);
            $mform->addElement('date_time_selector', 'validitytime', get_string('finishinvitation', 'jitsi'), $optionsinvitation);
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Add elements for setting the custom completion rules.
     *
     * @category completion
     * @return array List of added element names, or names of wrapping group elements.
     */
    public function add_completion_rules() {

        $mform =&  $this->_form;

        $group = [
            $mform->createElement('checkbox', 'completionminutesenabled', ' ', get_string('completionminutesex', 'jitsi')),
            $mform->createElement('text', 'completionminutes', ' ', ['size' => 3]),
        ];
        $mform->setType('completionminutes', PARAM_INT);
        $mform->addGroup($group, 'completionminutesgroup', get_string('completionminutes', 'jitsi'), [' '], false);
        $mform->addHelpButton('completionminutesgroup', 'completionminutes', 'jitsi');
        $mform->disabledIf('completionminutes', 'completionminutesenabled', 'notchecked');

        return ['completionminutesgroup'];
    }

    /**
     * Called during validation to see whether some module-specific completion rules are selected.
     *
     * @param array $data Input data not yet validated.
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        return (!empty($data['completionminutesenabled']) && $data['completionminutes'] != 0);
    }

    /**
     * Get Data
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return $data;
        }
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionminutesenabled) || !$autocompletion) {
                $data->completionminutes = 0;
            }
        }
        return $data;
    }

    /**
     * Data validation
     *
     * @param array $data Input data to validated.
     * @param array $files Files uploaded.
     * @return String error message, if any.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check open and close times are consistent.
        if ($data['timeopen'] != 0 && $data['timeclose'] != 0 &&
                $data['timeclose'] < $data['timeopen']) {
            $errors['timeclose'] = get_string('closebeforeopen', 'jitsi');
        }

        // Check validitity time is consistent with open and close times.
        if (($data['validitytime'] != 0 && $data['timeopen'] != 0 && $data['validitytime'] < $data['timeopen']) ||
                ($data['validitytime'] != 0 && $data['timeclose'] != 0 && $data['validitytime'] > $data['timeclose'])) {
            $errors['validitytime'] = get_string('validitytimevalidation', 'jitsi');
        }

        return $errors;
    }

    /**
     * Processing data
     * @param array $defaultvalues - default values
     */
    public function data_preprocessing(&$defaultvalues) {
        $defaultvalues['completionminutesenabled'] =
            !empty($defaultvalues['completionminutes']) ? 1 : 0;
        if (empty($defaultvalues['completionminutes'])) {
            $defaultvalues['completionminutes'] = 1;
        }
    }
}
