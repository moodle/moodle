<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the instance add/edit form.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_customcert\certificate;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Instance add/edit form.
 *
 * @package    mod_customcert
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_customcert_mod_form extends moodleform_mod {

    /**
     * Form definition.
     */
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name', 'customcert'), ['size' => '64']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('description', 'customcert'));

        $mform->addElement('header', 'options', get_string('options', 'customcert'));

        $deliveryoptions = [
            certificate::DELIVERY_OPTION_INLINE => get_string('deliveryoptioninline', 'customcert'),
            certificate::DELIVERY_OPTION_DOWNLOAD => get_string('deliveryoptiondownload', 'customcert'),
        ];
        $mform->addElement('select', 'deliveryoption', get_string('deliveryoptions', 'customcert'), $deliveryoptions);
        $mform->setDefault('deliveryoption', certificate::DELIVERY_OPTION_INLINE);

        if (has_capability('mod/customcert:manageemailstudents', $this->get_context())) {
            $mform->addElement('selectyesno', 'emailstudents', get_string('emailstudents', 'customcert'));
            $mform->setDefault('emailstudents', get_config('customcert', 'emailstudents'));
            $mform->addHelpButton('emailstudents', 'emailstudents', 'customcert');
            $mform->setType('emailstudents', PARAM_INT);
        }

        if (has_capability('mod/customcert:manageemailteachers', $this->get_context())) {
            $mform->addElement('selectyesno', 'emailteachers', get_string('emailteachers', 'customcert'));
            $mform->setDefault('emailteachers', get_config('customcert', 'emailteachers'));
            $mform->addHelpButton('emailteachers', 'emailteachers', 'customcert');
            $mform->setType('emailteachers', PARAM_INT);
        }

        if (has_capability('mod/customcert:manageemailothers', $this->get_context())) {
            $mform->addElement('text', 'emailothers', get_string('emailothers', 'customcert'), ['size' => '40']);
            $mform->addHelpButton('emailothers', 'emailothers', 'customcert');
            $mform->setDefault('emailothers', get_config('customcert', 'emailothers'));
            $mform->setType('emailothers', PARAM_TEXT);
        }

        if (has_capability('mod/customcert:manageverifyany', $this->get_context())) {
            $mform->addElement('selectyesno', 'verifyany', get_string('verifycertificateanyone', 'customcert'));
            $mform->addHelpButton('verifyany', 'verifycertificateanyone', 'customcert');
            $mform->setDefault('verifyany', get_config('customcert', 'verifyany'));
            $mform->setType('verifyany', PARAM_INT);
        }

        if (has_capability('mod/customcert:managerequiredtime', $this->get_context())) {
            $mform->addElement('text', 'requiredtime', get_string('coursetimereq', 'customcert'), ['size' => '3']);
            $mform->addHelpButton('requiredtime', 'coursetimereq', 'customcert');
            $mform->setDefault('requiredtime', get_config('customcert', 'requiredtime'));
            $mform->setType('requiredtime', PARAM_INT);
        }

        if (has_capability('mod/customcert:manageprotection', $this->get_context())) {
            $mform->addElement('checkbox', 'protection_print', get_string('setprotection', 'customcert'),
                get_string('print', 'customcert'));
            $mform->addElement('checkbox', 'protection_modify', '', get_string('modify', 'customcert'));
            $mform->addElement('checkbox', 'protection_copy', '', get_string('copy', 'customcert'));
            $mform->addHelpButton('protection_print', 'setprotection', 'customcert');
            $mform->setType('protection_print', PARAM_BOOL);
            $mform->setType('protection_modify', PARAM_BOOL);
            $mform->setType('protection_copy', PARAM_BOOL);
        }

        // Create an element for language selector.
        if (has_capability('mod/customcert:managelanguages', $this->get_context())) {
            $languages = get_string_manager()->get_list_of_translations();
            $languages = ['' => get_string('userlanguage', 'customcert')] + $languages;
            $mform->addElement('select', 'language', get_string('languageoptions', 'customcert'), $languages);
            $mform->addHelpButton('language', 'userlanguage', 'customcert');
        }

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    /**
     * Any data processing needed before the form is displayed.
     *
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        // Set the values in the form to what has been set in database if updating
        // or set default configured values if creating.
        if (!empty($defaultvalues['update'])) {
            if (!empty($defaultvalues['protection'])) {
                $protection = $this->build_protection_data($defaultvalues['protection']);

                $defaultvalues['protection_print'] = $protection->protection_print;
                $defaultvalues['protection_modify'] = $protection->protection_modify;
                $defaultvalues['protection_copy'] = $protection->protection_copy;
            }
        } else {
            $defaultvalues['protection_print'] = get_config('customcert', 'protection_print');
            $defaultvalues['protection_modify'] = get_config('customcert', 'protection_modify');
            $defaultvalues['protection_copy'] = get_config('customcert', 'protection_copy');
        }
    }

    /**
     * Post process form data.
     *
     * @param \stdClass $data
     *
     * @throws \dml_exception
     */
    public function data_postprocessing($data) {
        global $DB;

        parent::data_postprocessing($data);

        // If creating a new activity.
        if (!empty($data->add)) {
            foreach ($this->get_options_elements_with_required_caps() as $name => $capability) {
                if (!isset($data->$name) && !has_capability($capability, $this->get_context())) {
                    $data->$name = get_config('customcert', $name);
                }
            }
        } else {
            // If updating, but a user can't manage protection, then get data from database.
            if (!has_capability('mod/customcert:manageprotection', $this->get_context())) {
                $customcert = $DB->get_record('customcert', ['id' => $data->instance]);

                $protection = $this->build_protection_data($customcert->protection);
                $data->protection_print = $protection->protection_print;
                $data->protection_modify = $protection->protection_modify;
                $data->protection_copy = $protection->protection_copy;
            }
        }
    }

    /**
     * Some basic validation.
     *
     * @param array $data
     * @param array $files
     * @return array the errors that were found
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check that the required time entered is valid if it was entered at all.
        if (!empty($data['requiredtime'])) {
            if ((!is_number($data['requiredtime']) || $data['requiredtime'] < 0)) {
                $errors['requiredtime'] = get_string('requiredtimenotvalid', 'customcert');
            }
        }

        return $errors;
    }

    /**
     * Get a list of all options form elements with required capabilities for managing each element.
     *
     * @return array
     */
    protected function get_options_elements_with_required_caps() {
        return [
            'emailstudents' => 'mod/customcert:manageemailstudents',
            'emailteachers' => 'mod/customcert:manageemailteachers',
            'emailothers' => 'mod/customcert:manageemailothers',
            'verifyany' => 'mod/customcert:manageverifyany',
            'requiredtime' => 'mod/customcert:managerequiredtime',
            'protection_print' => 'mod/customcert:manageprotection',
            'protection_modify' => 'mod/customcert:manageprotection',
            'protection_copy' => 'mod/customcert:manageprotection',
        ];
    }

    /**
     * Build a protection data to be able to set to the form.
     *
     * @param string $protection Protection sting from database.
     *
     * @return \stdClass
     */
    protected function build_protection_data($protection) {
        $data = new stdClass();

        $data->protection_print = 0;
        $data->protection_modify = 0;
        $data->protection_copy = 0;

        $protection = explode(', ', $protection);

        if (in_array(certificate::PROTECTION_PRINT, $protection)) {
            $data->protection_print = 1;
        }
        if (in_array(certificate::PROTECTION_MODIFY, $protection)) {
            $data->protection_modify = 1;
        }
        if (in_array(certificate::PROTECTION_COPY, $protection)) {
            $data->protection_copy = 1;
        }

        return $data;
    }

}
