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

namespace mod_questionnaire;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The questionnaire settings form.
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_form extends \moodleform {

    /**
     * Defines the form.
     */
    public function definition() {
        global $questionnaire, $questionnairerealms;

        $mform    =& $this->_form;

        $mform->addElement('header', 'contenthdr', get_string('contentoptions', 'questionnaire'));

        $capabilities = questionnaire_load_capabilities($questionnaire->cm->id);
        if (!$capabilities->createtemplates) {
            unset($questionnairerealms['template']);
        }
        if (!$capabilities->createpublic) {
            unset($questionnairerealms['public']);
        }
        if (isset($questionnairerealms['public']) || isset($questionnairerealms['template'])) {
            $mform->addElement('select', 'realm', get_string('realm', 'questionnaire'), $questionnairerealms);
            $mform->setDefault('realm', $questionnaire->survey->realm);
            $mform->addHelpButton('realm', 'realm', 'questionnaire');
        } else {
            $mform->addElement('hidden', 'realm', 'private');
        }
        $mform->setType('realm', PARAM_RAW);

        $mform->addElement('text', 'title', get_string('title', 'questionnaire'), array('size' => '60'));
        $mform->setDefault('title', $questionnaire->survey->title);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');
        $mform->addHelpButton('title', 'title', 'questionnaire');

        $mform->addElement('text', 'subtitle', get_string('subtitle', 'questionnaire'), array('size' => '60'));
        $mform->setDefault('subtitle', $questionnaire->survey->subtitle);
        $mform->setType('subtitle', PARAM_TEXT);
        $mform->addHelpButton('subtitle', 'subtitle', 'questionnaire');

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true);
        $mform->addElement('editor', 'info', get_string('additionalinfo', 'questionnaire'), null, $editoroptions);
        $mform->setDefault('info', $questionnaire->survey->info);
        $mform->setType('info', PARAM_RAW);
        $mform->addHelpButton('info', 'additionalinfo', 'questionnaire');

        $mform->addElement('header', 'submithdr', get_string('submitoptions', 'questionnaire'));

        $mform->addElement('text', 'thanks_page', get_string('url', 'questionnaire'), array('size' => '60'));
        $mform->setType('thanks_page', PARAM_TEXT);
        $mform->setDefault('thanks_page', $questionnaire->survey->thanks_page);
        $mform->addHelpButton('thanks_page', 'url', 'questionnaire');

        $mform->addElement('static', 'confmes', get_string('confalts', 'questionnaire'));
        $mform->addHelpButton('confmes', 'confpage', 'questionnaire');

        $mform->addElement('text', 'thank_head', get_string('headingtext', 'questionnaire'), array('size' => '30'));
        $mform->setType('thank_head', PARAM_TEXT);
        $mform->setDefault('thank_head', $questionnaire->survey->thank_head);

        $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'trusttext' => true);
        $mform->addElement('editor', 'thank_body', get_string('bodytext', 'questionnaire'), null, $editoroptions);
        $mform->setType('thank_body', PARAM_RAW);
        $mform->setDefault('thank_body', $questionnaire->survey->thank_body);

        $allowemailreporting = get_config('questionnaire', 'allowemailreporting');
        if (!$allowemailreporting) {
            $attributes = ['size' => '75', 'disabled' => 'disabled'];
        } else {
            $attributes = ['size' => '75'];
        }
        $mform->addElement('text', 'email', get_string('email', 'questionnaire'), $attributes);
        $mform->setType('email', PARAM_TEXT);
        $mform->setDefault('email', $questionnaire->survey->email);
        $mform->addHelpButton('email', 'sendemail', 'questionnaire');

        // Hidden fields.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'sid', 0);
        $mform->setType('sid', PARAM_INT);
        $mform->addElement('hidden', 'name', '');
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('hidden', 'courseid', '');
        $mform->setType('courseid', PARAM_RAW);

        // Buttons.

        $submitlabel = get_string('savechangesanddisplay');
        $submit2label = get_string('savechangesandreturntocourse');
        $mform = $this->_form;

        // Elements in a row need a group.
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', $submit2label);
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');

    }

    /**
     * Validation rules for form.
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
