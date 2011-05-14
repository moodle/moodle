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
 * Page for editing the configuration of a particular Opaque engine.
 *
 * @package    qtype
 * @subpackage opaque
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/validateurlsyntax.php');


/**
 * Form definition class.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_engine_edit_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'enginename', get_string('enginename', 'qtype_opaque'));
        $mform->addRule('enginename', get_string('missingenginename', 'qtype_opaque'),
                'required', null, 'client');
        $mform->setType('enginename', PARAM_MULTILANG);

        $mform->addElement('textarea', 'questionengineurls',
                get_string('questionengineurls', 'qtype_opaque'), 'rows="5" cols="80"');
        $mform->addRule('questionengineurls', get_string('missingengineurls', 'qtype_opaque'),
                'required', null, 'client');
        $mform->setType('questionengineurls', PARAM_RAW);

        $mform->addElement('textarea', 'questionbankurls',
                get_string('questionbankurls', 'qtype_opaque'), array('rows' => 5, 'cols' => 80));
        $mform->setType('questionbankurls', PARAM_RAW);

        $mform->addElement('text', 'passkey', get_string('passkey', 'qtype_opaque'));
        $mform->setType('passkey', PARAM_MULTILANG);
        $mform->addHelpButton('passkey', 'passkey', 'qtype_opaque');

        $mform->addElement('hidden', 'engineid');
        $mform->setType('engineid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Validate the contents of a textarea field, which should be a
     * newline-separated list of URLs.
     *
     * @param $data the form data.
     * @param $field the field to validate.
     * @param $errors any error messages are added to this array.
     */
    protected function validateurllist(&$data, $field, &$errors) {
        $urls = preg_split('/[\r\n]+/', $data[$field]);
        foreach ($urls as $url) {
            $url = trim($url);
            if ($url && !validateUrlSyntax($url, 's?H?S?u-P-a?I?p?f?q?r?')) {
                $errors[$field] = get_string('urlsinvalid', 'qtype_opaque');
            }
        }
    }

    /**
     * Extract the contents of a textarea field, which should be a
     * newline-separated list of URLs.
     *
     * @param $data the form data.
     * @param $field the field to extract.
     * @param @return array those lines from the form field that are valid URLs.
     */
    public function extracturllist($data, $field) {
        $rawurls = preg_split('/[\r\n]+/', $data->$field);
        $urls = array();
        foreach ($rawurls as $url) {
            $url = clean_param(trim($url), PARAM_URL);
            if ($url) {
                $urls[] = $url;
            }
        }
        return $urls;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $this->validateurllist($data, 'questionengineurls', $errors);
        $this->validateurllist($data, 'questionbankurls', $errors);
        return $errors;
    }
}
