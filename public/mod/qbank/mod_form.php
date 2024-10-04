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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * mod_qbank settings form definition.
 *
 * @package    mod_qbank
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qbank_mod_form extends moodleform_mod {
    #[\Override]
    protected function definition(): void {
        global $CFG;

        $mform = $this->_form;
        $striptags = !empty($CFG->formatstringstriptags);
        $this->standard_hidden_coursemodule_elements();

        // We need to force visibility on this here as we don't need the other standard course elements.
        $mform->addElement('hidden', 'visible', 0);
        $mform->setType('visible', PARAM_INT);

        $mform->addElement('hidden', 'type');
        $mform->setDefaults('type', \core_question\local\bank\question_bank_helper::TYPE_STANDARD);
        $mform->setType('type', PARAM_TEXT);

        $mform->addElement('header', 'generalhdr', get_string('general'));

        // Add element for name.
        $mform->addElement('text', 'name', get_string('qbankname', 'mod_qbank'), ['size' => '64']);
        if ($striptags) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addHelpButton('name', 'qbankname', 'mod_qbank');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', null, 'maxlength', \core_question\local\bank\question_bank_helper::BANK_NAME_MAX_LENGTH, 'client');

        // Add intro editor.
        $mform->addElement('editor', 'introeditor', get_string('moduleintro'), ['rows' => 10], [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true,
            'context' => $this->context,
            'subdirs' => true,
        ]);
        $mform->setType('introeditor', PARAM_RAW); // No XSS prevention here, users must be trusted.
        if ($CFG->requiremodintro) {
            $mform->addRule('introeditor', get_string('required'), 'required', null, 'client');
        }

        // Add show description checkbox.
        $mform->addElement('advcheckbox', 'showdescription', get_string('showdescription', 'mod_qbank'));
        $mform->addHelpButton('showdescription', 'showdescription', 'mod_qbank');

        $mform->addElement('header', 'modstandardelshdr', get_string('modstandardels', 'form'));

        // Add idnumber element.
        $mform->addElement('text', 'cmidnumber', get_string('idnumbermod'));
        if ($striptags) {
            $mform->setType('cmidnumber', PARAM_TEXT);
        } else {
            $mform->setType('cmidnumber', PARAM_CLEANHTML);
        }
        $mform->addHelpButton('cmidnumber', 'idnumbermod');

        // Add our submission buttons.
        $buttonarray[] = $mform->createElement('submit', 'submitbutton2', get_string('saveandreturn', 'mod_qbank'));
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('saveanddisplay', 'mod_qbank'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->setType('buttonar', PARAM_RAW);
    }

    #[\Override]
    public function validation($data, $files): array {
        global $DB;
        $mform = $this->_form;

        // We don't want the parent validation as it has completion settings which we don't use.
        // Best call the grandparent though in case it changes in the future.
        $errors = moodleform::validation($data, $files);

        if ($mform->elementExists('name')) {
            $name = trim($data['name']);
            if ($name === '') {
                $errors['name'] = get_string('required');
            }
        }

        if (!empty($data['cmidnumber'])) {
            $idnumexists = $DB->record_exists_select(
                'course_modules',
                'id <> :id AND course = :course AND idnumber = :idnumber',
                ['course' => $data['course'], 'idnumber' => $data['cmidnumber'], 'id' => $data['coursemodule'] ?? null]
            );
            if ($idnumexists) {
                $errors['cmidnumber'] = get_string('idnumbertaken');
            }
        }

        if (!empty($data['type']) && !in_array($data['type'], core_question\local\bank\question_bank_helper::SHARED_TYPES, true)) {
            $errors['type'] = get_string('unknownbanktype', 'mod_qbank', $data['type']);
        }

        return $errors;
    }
}
