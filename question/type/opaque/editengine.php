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
 * @package qtype
 * @subpackage opaque
 * @copyright 2006 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
include_once($CFG->libdir . '/validateurlsyntax.php');
require_once(dirname(__FILE__) . '/locallib.php');


$engineid = optional_param('engineid', 0, PARAM_INT);

// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypesettingopaque', '', null,
        new moodle_url('/question/type/opaque/editengine.php', array('engineid' => $engineid)));
$PAGE->set_title(get_string('editquestionengine', 'qtype_opaque'));
$PAGE->navbar->add(get_string('editquestionengineshort', 'qtype_opaque'));


/**
 * Form definition class.
 *
 * @copyright 2006 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_engine_edit_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'enginename', get_string('enginename', 'qtype_opaque'));
        $mform->addRule('enginename', get_string('missingenginename', 'qtype_opaque'), 'required', null, 'client');
        $mform->setType('enginename', PARAM_MULTILANG);

        $mform->addElement('textarea', 'questionengineurls', get_string('questionengineurls', 'qtype_opaque'),
                'rows="5" cols="80"');
        $mform->addRule('questionengineurls', get_string('missingengineurls', 'qtype_opaque'), 'required', null, 'client');
        $mform->setType('questionengineurls', PARAM_RAW);

        $mform->addElement('textarea', 'questionbankurls', get_string('questionbankurls', 'qtype_opaque'),
                'rows="5" cols="80"');
        $mform->setType('questionbankurls', PARAM_RAW);

        $mform->addElement('text', 'passkey', get_string('passkey', 'qtype_opaque'));
        $mform->setType('passkey', PARAM_MULTILANG);
        $mform->addHelpButton('passkey', 'passkey', 'qtype_opaque');

        $mform->addElement('hidden', 'engineid');
        $mform->setType('engineid', PARAM_INT);

        $this->add_action_buttons();
    }

    /**
     * Validate the contents of a textarea field, which should be a newline-separated list of URLs.
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
     * Extract the contents of a textarea field, which should be a newline-separated list of URLs.
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

    public function validation($data) {
        $errors = parent::validation($data, $files);
        $this->validateurllist($data, 'questionengineurls', $errors);
        $this->validateurllist($data, 'questionbankurls', $errors);
        return $errors;
    }
}

$mform = new qtype_opaque_engine_edit_form('editengine.php');

if ($mform->is_cancelled()){
    redirect(new moodle_url('/question/type/opaque/engines.php'));

} else if ($data = $mform->get_data()){
    $engine = new stdClass;
    if (!empty($data->engineid)) {
        $engine->id = $data->engineid;
    }
    $engine->name = $data->enginename;
    $engine->passkey = trim($data->passkey);
    $engine->questionengines = $mform->extracturllist($data, 'questionengineurls');
    $engine->questionbanks = $mform->extracturllist($data, 'questionbankurls');
    save_engine_def($engine);
    redirect(new moodle_url('/question/type/opaque/engines.php'));
}

// Prepare defaults.
$defaults = new stdClass;
$defaults->engineid = $engineid;
if ($engineid) {
    $engine = load_engine_def($engineid);
    $defaults->enginename = $engine->name;
    $defaults->questionengineurls = implode("\n", $engine->questionengines);
    $defaults->questionbankurls = implode("\n", $engine->questionbanks);
    $defaults->passkey = $engine->passkey;
}
$mform->set_data($defaults);

// Display the form.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('editquestionengine', 'qtype_opaque'),
        'editquestionengine', 'qtype_opaque');
$mform->display();
echo $OUTPUT->footer();
